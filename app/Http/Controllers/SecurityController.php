<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\LoginHistory;
use App\Models\SecuritySetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SecurityController extends Controller
{
    /**
     * Display security dashboard.
     */
    public function index()
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        // Get recent login attempts
        $recentLogins = LoginHistory::with('user')
            ->orderBy('login_at', 'desc')
            ->take(10)
            ->get();

        // Get login statistics
        $loginStats = [
            'total_today' => LoginHistory::whereDate('login_at', today())->count(),
            'successful_today' => LoginHistory::successful()->whereDate('login_at', today())->count(),
            'failed_today' => LoginHistory::failed()->whereDate('login_at', today())->count(),
            'blocked_today' => LoginHistory::blocked()->whereDate('login_at', today())->count(),
        ];

        // Get user statistics
        $userStats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'locked_users' => User::whereNotNull('locked_until')->where('locked_until', '>', now())->count(),
            'admins' => User::where('role', 'superadmin')->count(),
        ];

        // Get security settings
        $settings = SecuritySetting::getGroupedSettings();

        // Get recent security-related audit logs
        $securityLogs = AuditLog::whereIn('action', [
            'login', 'logout', 'failed_login', 'password_changed',
            'user_locked', 'user_unlocked', 'settings_updated',
        ])->orderBy('created_at', 'desc')->take(10)->get();

        return view('security.index', compact(
            'recentLogins',
            'loginStats',
            'userStats',
            'settings',
            'securityLogs'
        ));
    }

    /**
     * Display login history.
     */
    public function loginHistory(Request $request)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        $query = LoginHistory::with('user');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('login_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('login_at', '<=', $request->date_to);
        }

        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        $loginHistories = $query->orderBy('login_at', 'desc')->paginate(20);

        $users = User::orderBy('name')->get();
        $statuses = ['success', 'failed', 'blocked'];

        return view('security.login-history', compact('loginHistories', 'users', 'statuses'));
    }

    /**
     * Display security settings.
     */
    public function settings()
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        $settings = SecuritySetting::getGroupedSettings();

        return view('security.settings', compact('settings'));
    }

    /**
     * Update security settings.
     */
    public function updateSettings(Request $request)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'password_min_length' => 'required|integer|min:6|max:32',
            'password_require_uppercase' => 'boolean',
            'password_require_lowercase' => 'boolean',
            'password_require_numbers' => 'boolean',
            'password_require_symbols' => 'boolean',
            'max_login_attempts' => 'required|integer|min:3|max:10',
            'lockout_duration' => 'required|integer|min:30|max:3600',
            'session_timeout' => 'required|integer|min:15|max:480',
            'single_session' => 'boolean',
            'audit_log_retention_days' => 'required|integer|min:30|max:365',
        ]);

        $settingsToUpdate = [
            'password_min_length' => $request->password_min_length,
            'password_require_uppercase' => $request->boolean('password_require_uppercase') ? '1' : '0',
            'password_require_lowercase' => $request->boolean('password_require_lowercase') ? '1' : '0',
            'password_require_numbers' => $request->boolean('password_require_numbers') ? '1' : '0',
            'password_require_symbols' => $request->boolean('password_require_symbols') ? '1' : '0',
            'max_login_attempts' => $request->max_login_attempts,
            'lockout_duration' => $request->lockout_duration,
            'session_timeout' => $request->session_timeout,
            'single_session' => $request->boolean('single_session') ? '1' : '0',
            'audit_log_retention_days' => $request->audit_log_retention_days,
        ];

        try {
            foreach ($settingsToUpdate as $key => $value) {
                SecuritySetting::where('key', $key)->update(['value' => $value]);
            }

            // Clear settings cache
            SecuritySetting::clearCache();

            // Log the action
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'settings_updated',
                'model_type' => 'SecuritySetting',
                'model_id' => 0,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => 'Security settings updated',
            ]);

            return redirect()->route('security.settings')
                ->with('success', 'Security settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update security settings. Please try again.');
        }
    }

    /**
     * Display active sessions.
     */
    public function activeSessions()
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        // Get active sessions from database session driver
        $sessions = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select(
                'sessions.id',
                'sessions.user_id',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->whereNotNull('sessions.user_id')
            ->orderBy('sessions.last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                $session->last_activity = \Carbon\Carbon::createFromTimestamp($session->last_activity);
                $session->browser = $this->getBrowser($session->user_agent);
                $session->platform = $this->getPlatform($session->user_agent);

                return $session;
            });

        return view('security.active-sessions', compact('sessions'));
    }

    /**
     * Terminate a specific session.
     */
    public function terminateSession(Request $request, $sessionId)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        try {
            DB::table('sessions')->where('id', $sessionId)->delete();

            // Log the action
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'session_terminated',
                'model_type' => 'Session',
                'model_id' => 0,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => 'User session terminated: ' . $sessionId,
            ]);

            return redirect()->route('security.active-sessions')
                ->with('success', 'Session terminated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('security.active-sessions')
                ->with('error', 'Failed to terminate session. Please try again.');
        }
    }

    /**
     * Unlock a user account.
     */
    public function unlockUser(Request $request, $userId)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        try {
            $user = User::findOrFail($userId);
            $user->resetFailedAttempts();

            // Log the action
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'user_unlocked',
                'model_type' => 'User',
                'model_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => "User account unlocked: {$user->email}",
            ]);

            return redirect()->back()
                ->with('success', "User {$user->name}'s account has been unlocked.");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'User not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to unlock user account. Please try again.');
        }
    }

    /**
     * Toggle user active status.
     */
    public function toggleUserStatus(Request $request, $userId)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        try {
            $user = User::findOrFail($userId);

            // Prevent deactivating own account
            if ($user->id === Auth::id()) {
                return redirect()->back()
                    ->with('error', 'You cannot deactivate your own account.');
            }

            $user->update(['is_active' => ! $user->is_active]);

            $status = $user->is_active ? 'activated' : 'deactivated';

            // Log the action
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => "user_{$status}",
                'model_type' => 'User',
                'model_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => "User account {$status}: {$user->email}",
            ]);

            return redirect()->back()
                ->with('success', "User {$user->name}'s account has been {$status}.");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'User not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update user status. Please try again.');
        }
    }

    /**
     * Force password change for a user.
     */
    public function forcePasswordChange(Request $request, $userId)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        try {
            $user = User::findOrFail($userId);
            $user->update(['force_password_change' => true]);

            // Log the action
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'force_password_change',
                'model_type' => 'User',
                'model_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => "Forced password change for user: {$user->email}",
            ]);

            return redirect()->back()
                ->with('success', "User {$user->name} will be required to change password on next login.");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'User not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to force password change. Please try again.');
        }
    }

    /**
     * Export security logs.
     */
    public function exportLogs(Request $request)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        $type = $request->get('type', 'login');
        $format = $request->get('format', 'csv');
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        if ($type === 'login') {
            $data = LoginHistory::whereBetween('login_at', [$dateFrom, $dateTo . ' 23:59:59'])
                ->with('user:id,name,email')
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'user' => $log->user->name ?? 'N/A',
                        'email' => $log->email,
                        'ip_address' => $log->ip_address,
                        'browser' => $log->browser,
                        'platform' => $log->platform,
                        'status' => $log->status,
                        'failure_reason' => $log->failure_reason,
                        'login_at' => $log->login_at->format('Y-m-d H:i:s'),
                    ];
                });
            $filename = "login_history_{$dateFrom}_to_{$dateTo}";
        } else {
            $data = AuditLog::whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
                ->with('user:id,name,email')
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'user' => $log->user->name ?? 'System',
                        'action' => $log->action,
                        'model_type' => $log->model_type,
                        'model_id' => $log->model_id,
                        'description' => $log->description,
                        'ip_address' => $log->ip_address,
                        'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            $filename = "audit_logs_{$dateFrom}_to_{$dateTo}";
        }

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            ];

            $callback = function () use ($data) {
                $file = fopen('php://output', 'w');

                if ($data->count() > 0) {
                    fputcsv($file, array_keys($data->first()));
                }

                foreach ($data as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return response()->json($data)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.json\"");
    }

    /**
     * Get browser from user agent.
     */
    protected function getBrowser($userAgent)
    {
        if (strpos($userAgent, 'Edg') !== false) {
            return 'Edge';
        }
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        }
        if (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        }
        if (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        }
        if (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) {
            return 'Opera';
        }

        return 'Unknown';
    }

    /**
     * Get platform from user agent.
     */
    protected function getPlatform($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows';
        }
        if (strpos($userAgent, 'Mac') !== false) {
            return 'Mac OS';
        }
        if (strpos($userAgent, 'Linux') !== false) {
            return 'Linux';
        }
        if (strpos($userAgent, 'Android') !== false) {
            return 'Android';
        }
        if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            return 'iOS';
        }

        return 'Unknown';
    }
}
