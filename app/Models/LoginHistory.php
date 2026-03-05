<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'browser',
        'platform',
        'device_type',
        'status',
        'failure_reason',
        'login_at',
        'logout_at',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    /**
     * Get the user that owns the login history.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for successful logins.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed logins.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for blocked logins.
     */
    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    /**
     * Log a successful login.
     */
    public static function logSuccess($user, $request)
    {
        return self::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'browser' => self::getBrowser($request->userAgent()),
            'platform' => self::getPlatform($request->userAgent()),
            'device_type' => self::getDeviceType($request->userAgent()),
            'status' => 'success',
            'login_at' => now(),
        ]);
    }

    /**
     * Log a failed login.
     */
    public static function logFailed($email, $request, $reason = null)
    {
        return self::create([
            'email' => $email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'browser' => self::getBrowser($request->userAgent()),
            'platform' => self::getPlatform($request->userAgent()),
            'device_type' => self::getDeviceType($request->userAgent()),
            'status' => 'failed',
            'failure_reason' => $reason,
            'login_at' => now(),
        ]);
    }

    /**
     * Log a blocked login attempt.
     */
    public static function logBlocked($email, $request, $reason = 'Account locked')
    {
        return self::create([
            'email' => $email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'browser' => self::getBrowser($request->userAgent()),
            'platform' => self::getPlatform($request->userAgent()),
            'device_type' => self::getDeviceType($request->userAgent()),
            'status' => 'blocked',
            'failure_reason' => $reason,
            'login_at' => now(),
        ]);
    }

    /**
     * Get browser from user agent.
     */
    protected static function getBrowser($userAgent)
    {
        if (strpos($userAgent, 'Edg') !== false) return 'Edge';
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Opera') !== false || strpos($userAgent, 'OPR') !== false) return 'Opera';
        if (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) return 'Internet Explorer';
        return 'Unknown';
    }

    /**
     * Get platform from user agent.
     */
    protected static function getPlatform($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Mac') !== false) return 'Mac OS';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) return 'iOS';
        return 'Unknown';
    }

    /**
     * Get device type from user agent.
     */
    protected static function getDeviceType($userAgent)
    {
        if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false) {
            if (strpos($userAgent, 'Tablet') !== false || strpos($userAgent, 'iPad') !== false) {
                return 'Tablet';
            }
            return 'Mobile';
        }
        return 'Desktop';
    }

    /**
     * Get status badge attribute.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'success' => '<span class="badge bg-success">Success</span>',
            'failed' => '<span class="badge bg-danger">Failed</span>',
            'blocked' => '<span class="badge bg-warning">Blocked</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
}
