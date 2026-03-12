<?php

namespace App\Http\Middleware;

use App\Models\SecuritySetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SessionSecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Apply session security settings from database:
     * - Session timeout based on security_settings
     * - Single session enforcement (optional)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Check session timeout from security settings
        if ($this->isSessionExpired($request)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('warning', 'Your session has expired due to inactivity. Please log in again.');
        }

        // Update last activity timestamp
        $request->session()->put('last_activity', time());

        // Enforce single session if enabled
        if ($this->isSingleSessionEnabled()) {
            $this->enforceSingleSession($request, $user);
        }

        return $next($request);
    }

    /**
     * Check if the session has expired based on security settings.
     */
    protected function isSessionExpired(Request $request): bool
    {
        $lastActivity = $request->session()->get('last_activity');

        if (! $lastActivity) {
            // First request in this session, set the activity timestamp
            $request->session()->put('last_activity', time());

            return false;
        }

        // Get timeout from security settings (in minutes)
        $timeoutMinutes = (int) SecuritySetting::get('session_timeout', 120);
        $timeoutSeconds = $timeoutMinutes * 60;

        return (time() - $lastActivity) > $timeoutSeconds;
    }

    /**
     * Check if single session mode is enabled.
     */
    protected function isSingleSessionEnabled(): bool
    {
        return (bool) SecuritySetting::get('single_session', false);
    }

    /**
     * Enforce single session per user.
     *
     * When a user logs in, this will invalidate all their other sessions.
     */
    protected function enforceSingleSession(Request $request, $user): void
    {
        $currentSessionId = $request->session()->getId();

        // Check if this is the user's current active session
        $sessionKey = 'user_active_session_' . $user->id;
        $activeSession = cache()->get($sessionKey);

        if ($activeSession && $activeSession !== $currentSessionId) {
            // This session is not the active one - terminate other sessions
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $currentSessionId)
                ->delete();
        }

        // Mark this session as the active one
        cache()->put($sessionKey, $currentSessionId, now()->addMinutes(
            (int) SecuritySetting::get('session_timeout', 120)
        ));
    }
}
