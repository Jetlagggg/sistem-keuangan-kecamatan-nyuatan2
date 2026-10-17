<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')->latest()->paginate(50)->withQueryString();

        return view('activity-logs.index', compact('logs'));
    }
}
