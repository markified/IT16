<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helpers\IpHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'failed_login_attempts',
        'locked_until',
        'last_login_at',
        'last_login_ip',
        'password_changed_at',
        'force_password_change',
        'is_active',
        'is_approved',
        'approved_at',
        'approved_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
            'locked_until' => 'datetime',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'force_password_change' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the login history for the user.
     */
    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    /**
     * Check if the user account is locked.
     */
    public function isLocked()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Increment failed login attempts.
     */
    public function incrementFailedAttempts()
    {
        $this->increment('failed_login_attempts');

        $maxAttempts = SecuritySetting::get('max_login_attempts', 5);
        $lockoutDuration = SecuritySetting::get('lockout_duration', 900); // Default 15 minutes = 900 seconds

        if ($this->failed_login_attempts >= $maxAttempts) {
            $this->update([
                'locked_until' => now()->addSeconds($lockoutDuration),
            ]);
        }
    }

    /**
     * Reset failed login attempts.
     */
    public function resetFailedAttempts()
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Record successful login.
     */
    public function recordLogin($request)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => IpHelper::getClientIp($request),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if user is regular admin (limited access).
     */
    public function isRegularAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is inventory manager.
     */
    public function isInventory()
    {
        return $this->role === 'inventory';
    }

    /**
     * Check if user is security personnel.
     */
    public function isSecurity()
    {
        return $this->role === 'security';
    }

    /**
     * Check if user is employee.
     */
    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    /**
     * Check if user has one of the specified roles.
     *
     * @param  array|string  $roles
     * @return bool
     */
    public function hasRole($roles)
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }

        return in_array($this->role, $roles);
    }

    /**
     * Check if user has any of the specified roles.
     *
     * @return bool
     */
    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }

    /**
     * Check if user can manage inventory.
     *
     * @return bool
     */
    public function canManageInventory()
    {
        return in_array($this->role, ['superadmin', 'inventory']);
    }

    /**
     * Check if user can manage security.
     *
     * @return bool
     */
    public function canManageSecurity()
    {
        return in_array($this->role, ['superadmin', 'security']);
    }

    /**
     * Check if user can access admin features (users, audit logs, reports).
     *
     * @return bool
     */
    public function canAccessAdminFeatures()
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    /**
     * Get status badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        if (! $this->is_active) {
            return '<span class="badge bg-secondary">Inactive</span>';
        }
        if ($this->isLocked()) {
            return '<span class="badge bg-warning">Locked</span>';
        }

        return '<span class="badge bg-success">Active</span>';
    }
}
