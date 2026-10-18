<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('table_name')) {
            $query->where('table_name', $request->table_name);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('keyword')) {
            $keyword = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $request->keyword);
            $query->where('description', 'like', '%'.$keyword.'%');
        }

        $logs = $query->latest()->paginate(50)->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name']);
        $actions = ActivityLog::query()->distinct()->orderBy('action')->pluck('action');
        $tables = ActivityLog::query()->distinct()->orderBy('table_name')->pluck('table_name');

        return view('activity-logs.index', compact('logs', 'users', 'actions', 'tables'));
    }
}
