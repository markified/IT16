<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = AuditLog::with('user')
                ->orderBy('created_at', 'desc');

            // Filters
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }
            if ($request->filled('model_type')) {
                $query->where('model_type', $request->model_type);
            }
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $logs = $query->paginate(50);

            $actions = AuditLog::select('action')->distinct()->pluck('action');
            $modelTypes = AuditLog::select('model_type')->distinct()->pluck('model_type');
            $users = \App\Models\User::orderBy('name')->get();

            return view('audit-logs.index', compact('logs', 'actions', 'modelTypes', 'users'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load audit logs. Please try again.');
        }
    }

    public function show(string $id)
    {
        try {
            $log = AuditLog::with('user')->findOrFail($id);

            return view('audit-logs.show', compact('log'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('audit-logs.index')->with('error', 'Audit log not found.');
        } catch (\Exception $e) {
            return redirect()->route('audit-logs.index')->with('error', 'Failed to load audit log details.');
        }
    }
}
