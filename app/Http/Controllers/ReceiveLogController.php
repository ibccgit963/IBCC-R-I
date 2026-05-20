<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ReceiveLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceiveLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = ReceiveLog::with('center', 'creator');

        if (Auth::user()->role->slug !== 'super-admin') {
            $query->where('center_id', Auth::user()->center_id);
        }

        $maxSerial = ReceiveLog::where('center_id', Auth::user()->center_id)->max('sr_no');
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
                  ->orWhere('sr_no', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('organization_name', 'like', '%' . $request->search . '%');
            });
        }

        $logs = $query->latest()->paginate(20);
        return view('receive_logs.index', compact('logs', 'nextSerial'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'nullable|date',
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'organization_name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'signature' => 'nullable|string|max:255',
        ]);

        if (!isset($validated['date'])) {
            $validated['date'] = now()->toDateString();
        }

        $validated['center_id'] = Auth::user()->center_id;
        $validated['created_by'] = Auth::id();

        DB::transaction(function () use ($validated) {
            $maxSerial = ReceiveLog::where('center_id', $validated['center_id'])
                ->lockForUpdate()
                ->max('sr_no');
            $validated['sr_no'] = $maxSerial ? (int)$maxSerial + 1 : 1;
            ReceiveLog::create($validated);
        });

        return redirect()->route('receive-logs.index')->with('success', 'Receive record saved successfully.');
    }

    public function export(Request $request)
    {
        $query = ReceiveLog::with('center', 'creator');

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
            'Content-Disposition' => 'attachment; filename="receive_records_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Sr No', 'Date', 'Name', 'Designation', 'Organization', 'Subject', 'Signature', 'Center', 'Created By', 'Created At']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->sr_no,
                    $log->date,
                    $log->name,
                    $log->designation,
                    $log->organization_name,
                    $log->subject,
                    $log->signature,
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
