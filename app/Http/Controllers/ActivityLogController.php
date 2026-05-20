<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin|center-admin');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ActivityLog::with('user', 'center')->latest();

        if ($user->role->slug !== 'super-admin') {
            $query->where('center_id', $user->center_id);
        } elseif ($request->filled('center_id')) {
            $query->where('center_id', $request->center_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject_label', 'like', '%' . $request->search . '%')
                  ->orWhere('notes', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $logs = $query->paginate(30);
        $centers = $user->role->slug === 'super-admin' ? Center::all() : collect();
        $actions = ['created', 'updated', 'deleted', 'transferred', 'received', 'dispatched', 'reverted'];

        return view('activity_logs.index', compact('logs', 'centers', 'actions'));
    }
}
