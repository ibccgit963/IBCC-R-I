<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DispatchLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DispatchLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = DispatchLog::with('center', 'creator');

        if (Auth::user()->role->slug !== 'super-admin') {
            $query->where('center_id', Auth::user()->center_id);
        }

        $centerId = Auth::user()->center_id;
        $maxSerial = $centerId
            ? DispatchLog::where('center_id', $centerId)->max('serial_number')
            : DispatchLog::max('serial_number');
        $nextSerial = $maxSerial ? (int)$maxSerial + 1 : 1;

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('file_no', 'like', '%' . $request->search . '%');
            });
        }

        $logs = $query->latest()->paginate(20);
        return view('dispatch_logs.index', compact('logs', 'nextSerial'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'subject' => 'required|string|max:255',
            'file_no' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $validated['center_id'] = Auth::user()->center_id;
        $validated['created_by'] = Auth::id();

        DB::transaction(function () use ($validated) {
            $lastLog = DispatchLog::where('center_id', $validated['center_id'])
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();
            $validated['serial_number'] = $lastLog ? (int)$lastLog->serial_number + 1 : 1;
            DispatchLog::create($validated);
        });

        return redirect()->route('dispatch-logs.index')->with('success', 'Dispatch record saved successfully.');
    }

    public function export(Request $request)
    {
        $query = DispatchLog::with('center', 'creator');

        if (Auth::user()->role->slug !== 'super-admin') {
            $query->where('center_id', Auth::user()->center_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="dispatch_records_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Serial No', 'Name', 'Address', 'Subject', 'File No', 'Remarks', 'Center', 'Created By', 'Created At']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->serial_number,
                    $log->name,
                    $log->address,
                    $log->subject,
                    $log->file_no,
                    $log->remarks,
                    $log->center->name ?? 'N/A',
                    $log->creator->name ?? 'N/A',
                    $log->created_at,
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
