<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DatabaseBackup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DatabaseManagementController extends Controller
{
    /**
     * Display database overview and statistics.
     */
    public function index()
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        // Get database name
        $databaseName = config('database.connections.mysql.database');

        // Get table information
        $tables = $this->getTableStatistics();

        // Get recent backups
        $recentBackups = DatabaseBackup::latest()->take(5)->get();

        // Calculate total database size
        $totalSize = collect($tables)->sum('size_bytes');
        $totalSizeFormatted = $this->formatBytes($totalSize);
        $totalRows = collect($tables)->sum(function ($table) {
            return (int) str_replace(',', '', $table['rows']);
        });

        return view('database-management.index', compact(
            'databaseName',
            'tables',
            'recentBackups',
            'totalSize',
            'totalSizeFormatted',
            'totalRows'
        ));
    }

    /**
     * Get statistics for all tables.
     */
    protected function getTableStatistics()
    {
        $databaseName = config('database.connections.mysql.database');

        $tables = DB::select('
            SELECT 
                table_name as `name`,
                table_rows as `row_count`,
                (data_length + index_length) as `size`,
                update_time as `last_updated`
            FROM information_schema.tables 
            WHERE table_schema = ?
            ORDER BY table_name
        ', [$databaseName]);

        return collect($tables)->map(function ($table) {
            return [
                'name' => $table->name,
                'rows' => number_format($table->row_count ?? 0),
                'size' => $this->formatBytes($table->size ?? 0),
                'size_bytes' => $table->size ?? 0,
                'last_updated' => $table->last_updated,
            ];
        });
    }

    /**
     * Display backup management page.
     */
    public function backups()
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        $backups = DatabaseBackup::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('database-management.backups', compact('backups'));
    }

    /**
     * Create a new database backup.
     */
    public function createBackup(Request $request)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $databaseName = config('database.connections.mysql.database');
        $filename = "backup_{$databaseName}_" . date('Y-m-d_His') . '.sql';
        $backupPath = storage_path('app/backups');

        // Create backup directory if it doesn't exist
        if (! is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $fullPath = $backupPath . '/' . $filename;

        // Create backup record
        $backup = DatabaseBackup::create([
            'filename' => $filename,
            'path' => $fullPath,
            'type' => 'manual',
            'status' => 'pending',
            'created_by' => Auth::id(),
            'notes' => $request->notes,
        ]);

        try {
            // Build mysqldump command
            $command = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s %s > "%s"',
                escapeshellarg(config('database.connections.mysql.host')),
                escapeshellarg(config('database.connections.mysql.port')),
                escapeshellarg(config('database.connections.mysql.username')),
                escapeshellarg(config('database.connections.mysql.password')),
                escapeshellarg($databaseName),
                $fullPath
            );

            // Execute backup
            exec($command . ' 2>&1', $output, $returnCode);

            if ($returnCode !== 0 || ! file_exists($fullPath)) {
                throw new \Exception('Backup command failed: ' . implode("\n", $output));
            }

            // Update backup record with file size
            $backup->update([
                'status' => 'completed',
                'size' => filesize($fullPath),
            ]);

            // Log the action
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'backup_created',
                'model_type' => 'DatabaseBackup',
                'model_id' => $backup->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => "Database backup created: {$filename}",
            ]);

            return redirect()->route('database.backups')
                ->with('success', "Database backup created successfully: {$filename}");
        } catch (\Exception $e) {
            $backup->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->route('database.backups')
                ->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file.
     */
    public function downloadBackup($id)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        $backup = DatabaseBackup::findOrFail($id);

        if (! $backup->fileExists()) {
            return redirect()->route('database.backups')
                ->with('error', 'Backup file not found.');
        }

        return response()->download($backup->path, $backup->filename);
    }

    /**
     * Delete a backup.
     */
    public function deleteBackup(Request $request, $id)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        $backup = DatabaseBackup::findOrFail($id);

        // Delete the file
        $backup->deleteFile();

        // Log the action
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'backup_deleted',
            'model_type' => 'DatabaseBackup',
            'model_id' => $backup->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Database backup deleted: {$backup->filename}",
        ]);

        $backup->delete();

        return redirect()->route('database.backups')
            ->with('success', 'Backup deleted successfully.');
    }

    /**
     * Show restore confirmation page.
     */
    public function showRestore($id)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        $backup = DatabaseBackup::findOrFail($id);

        if (! $backup->fileExists()) {
            return redirect()->route('database.backups')
                ->with('error', 'Backup file not found.');
        }

        return view('database-management.restore', compact('backup'));
    }

    /**
     * Restore database from backup.
     */
    public function restoreBackup(Request $request, $id)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'confirmation' => 'required|in:RESTORE',
        ]);

        $backup = DatabaseBackup::findOrFail($id);

        if (! $backup->fileExists()) {
            return redirect()->route('database.backups')
                ->with('error', 'Backup file not found.');
        }

        try {
            $databaseName = config('database.connections.mysql.database');

            // Build mysql restore command
            $command = sprintf(
                'mysql --host=%s --port=%s --user=%s --password=%s %s < "%s"',
                escapeshellarg(config('database.connections.mysql.host')),
                escapeshellarg(config('database.connections.mysql.port')),
                escapeshellarg(config('database.connections.mysql.username')),
                escapeshellarg(config('database.connections.mysql.password')),
                escapeshellarg($databaseName),
                $backup->path
            );

            exec($command . ' 2>&1', $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('Restore command failed: ' . implode("\n", $output));
            }

            // Log the action
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'backup_restored',
                'model_type' => 'DatabaseBackup',
                'model_id' => $backup->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => "Database restored from backup: {$backup->filename}",
            ]);

            return redirect()->route('database.index')
                ->with('success', 'Database restored successfully from backup.');
        } catch (\Exception $e) {
            return redirect()->route('database.backups')
                ->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    /**
     * Optimize database tables.
     */
    public function optimize(Request $request)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        try {
            $databaseName = config('database.connections.mysql.database');

            $tables = DB::select('
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = ?
            ', [$databaseName]);

            $optimized = [];
            foreach ($tables as $table) {
                DB::statement("OPTIMIZE TABLE `{$table->table_name}`");
                $optimized[] = $table->table_name;
            }

            // Log the action
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'database_optimized',
                'model_type' => 'Database',
                'model_id' => 0,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => 'Database tables optimized: ' . count($optimized) . ' tables',
            ]);

            return redirect()->route('database.index')
                ->with('success', 'Successfully optimized ' . count($optimized) . ' tables.');
        } catch (\Exception $e) {
            return redirect()->route('database.index')
                ->with('error', 'Optimization failed: ' . $e->getMessage());
        }
    }

    /**
     * Show table details.
     */
    public function showTable($tableName)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        $databaseName = config('database.connections.mysql.database');

        // Get table structure
        $columns = DB::select("DESCRIBE `{$tableName}`");

        // Get table indexes
        $indexes = DB::select("SHOW INDEX FROM `{$tableName}`");

        // Get row count
        $rowCount = DB::table($tableName)->count();

        // Get sample data (first 10 rows)
        $sampleData = DB::table($tableName)->limit(10)->get();

        return view('database-management.table', compact(
            'tableName',
            'columns',
            'indexes',
            'rowCount',
            'sampleData'
        ));
    }

    /**
     * Export table data.
     */
    public function exportTable(Request $request, $tableName)
    {
        // Allow superadmin and security roles (middleware already protects this route)
        if (! Auth::user()->isAdmin() && ! Auth::user()->isSecurity()) {
            abort(403, 'Unauthorized access.');
        }

        $format = $request->get('format', 'csv');
        $data = DB::table($tableName)->get();

        if ($format === 'csv') {
            $filename = "{$tableName}_" . date('Y-m-d_His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($data) {
                $file = fopen('php://output', 'w');

                // Add headers
                if ($data->count() > 0) {
                    fputcsv($file, array_keys((array) $data->first()));
                }

                // Add rows
                foreach ($data as $row) {
                    fputcsv($file, (array) $row);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        if ($format === 'json') {
            $filename = "{$tableName}_" . date('Y-m-d_His') . '.json';

            return response()->json($data)
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        }

        return redirect()->back()->with('error', 'Invalid export format.');
    }

    /**
     * Format bytes to human readable format.
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
    }
}
