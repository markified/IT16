<?php

namespace App\Models;

use App\Helpers\IpHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log($action, $model, $oldValues = null, $newValues = null, $description = null)
    {
        return self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => IpHelper::getClientIp(),
            'user_agent' => Request::userAgent(),
            'description' => $description,
        ]);
    }

    public static function logStockIn($stockIn)
    {
        return self::log(
            'stock_in',
            $stockIn,
            null,
            [
                'product' => $stockIn->product->name,
                'quantity' => $stockIn->quantity,
                'supplier' => $stockIn->supplier_name,
            ],
            "Added {$stockIn->quantity} units of {$stockIn->product->name}"
        );
    }

    public static function logStockOut($inventoryIssue)
    {
        return self::log(
            'stock_out',
            $inventoryIssue,
            null,
            [
                'product' => $inventoryIssue->product->name,
                'quantity' => $inventoryIssue->quantity_issued,
                'recipient' => $inventoryIssue->recipient,
            ],
            "Issued {$inventoryIssue->quantity_issued} units of {$inventoryIssue->product->name}"
        );
    }

    public static function logAdjustment($adjustment)
    {
        return self::log(
            'adjustment',
            $adjustment,
            ['quantity' => $adjustment->quantity_before],
            ['quantity' => $adjustment->quantity_after],
            "Adjusted {$adjustment->product->name}: {$adjustment->quantity_before} → {$adjustment->quantity_after} ({$adjustment->reason_label})"
        );
    }

    public static function logAuth($action, $user, $description = null)
    {
        return self::create([
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => 'User',
            'model_id' => $user->id,
            'old_values' => null,
            'new_values' => null,
            'ip_address' => IpHelper::getClientIp(),
            'user_agent' => Request::userAgent(),
            'description' => $description ?? ($action === 'login' ? "User {$user->name} logged in" : "User {$user->name} logged out"),
        ]);
    }

    public static function logLogin($user)
    {
        return self::logAuth('login', $user);
    }

    public static function logLogout($user)
    {
        return self::logAuth('logout', $user);
    }

    public static function logLoginFailed($email)
    {
        return self::create([
            'user_id' => null,
            'action' => 'login_failed',
            'model_type' => 'User',
            'model_id' => null,
            'old_values' => null,
            'new_values' => ['email' => $email],
            'ip_address' => IpHelper::getClientIp(),
            'user_agent' => Request::userAgent(),
            'description' => "Failed login attempt for email: {$email}",
        ]);
    }

    public function getActionBadgeAttribute()
    {
        return match ($this->action) {
            'created' => '<span class="badge bg-success">Created</span>',
            'updated' => '<span class="badge bg-info">Updated</span>',
            'deleted' => '<span class="badge bg-danger">Deleted</span>',
            'stock_in' => '<span class="badge bg-primary">Stock In</span>',
            'stock_out' => '<span class="badge bg-warning">Stock Out</span>',
            'adjustment' => '<span class="badge bg-secondary">Adjustment</span>',
            'login' => '<span class="badge bg-success"><i class="fas fa-sign-in-alt mr-1"></i>Login</span>',
            'logout' => '<span class="badge bg-secondary"><i class="fas fa-sign-out-alt mr-1"></i>Logout</span>',
            'login_failed' => '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle mr-1"></i>Failed Login</span>',
            default => '<span class="badge bg-dark">' . ucfirst($this->action) . '</span>',
        };
    }
}
