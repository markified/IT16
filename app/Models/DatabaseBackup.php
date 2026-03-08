<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatabaseBackup extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'path',
        'size',
        'type',
        'status',
        'created_by',
        'notes',
        'error_message',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * Get the user who created the backup.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for completed backups.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for failed backups.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Get human readable file size.
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get status badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'failed' => '<span class="badge bg-danger">Failed</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Get type badge HTML.
     */
    public function getTypeBadgeAttribute()
    {
        $badges = [
            'manual' => '<span class="badge bg-primary">Manual</span>',
            'scheduled' => '<span class="badge bg-info">Scheduled</span>',
            'auto' => '<span class="badge bg-secondary">Auto</span>',
        ];

        return $badges[$this->type] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Check if backup file exists.
     */
    public function fileExists()
    {
        return file_exists($this->path);
    }

    /**
     * Delete the backup file.
     */
    public function deleteFile()
    {
        if ($this->fileExists()) {
            return unlink($this->path);
        }

        return false;
    }
}
