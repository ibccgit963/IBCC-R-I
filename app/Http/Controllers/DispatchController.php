<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\Center;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispatchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin|center-admin|staff-user|officer');
    }

    public function index()
    {
        $user = Auth::user();
        $dispatches = Dispatch::with('center', 'dispatchedBy', 'requestedBy');

        if ($user->role->slug === 'officer') {
            $dispatches->where('requested_by_user_id', $user->id);
        } elseif ($user->role->slug === 'center-admin' || $user->role->slug === 'staff-user') {
            $dispatches->where('center_id', $user->center_id);
        }

        $dispatches = $dispatches->latest()->get();
        return view('dispatches.index', compact('dispatches'));
    }

    public function create()
    {
        $centers = Center::all();
        return view('dispatches.create', compact('centers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'applicant_name'          => 'required|string|max:255',
            'father_name'             => 'nullable|string|max:255',
            'applicant_contact'       => 'nullable|string|max:255',
            'case_number'             => 'required|string|max:255',
            'dispatch_courier_company'=> 'nullable|string|max:255',
            'dispatched_from'         => 'required|string|max:255',
            'type'                    => 'required|in:applicant,internal,sub_office,ministry',
        ]);

        $defaultCompany = $user->center?->default_courier_company ?? '';

        if ($user->role->slug === 'officer') {
            $dispatch = Dispatch::create([
                'applicant_name'           => $request->applicant_name,
                'father_name'              => $request->father_name,
                'applicant_contact'        => $request->applicant_contact,
                'case_number'              => $request->case_number,
                'dispatch_courier_company' => $request->dispatch_courier_company ?? '',
                'dispatched_from'          => $request->dispatched_from,
                'type'                     => $request->type,
                'status'                   => 'pending',
                'center_id'                => $user->center_id,
                'requested_by_user_id'     => $user->id,
            ]);
        } else {
            $dispatch = Dispatch::create([
                'applicant_name'           => $request->applicant_name,
                'father_name'              => $request->father_name,
                'applicant_contact'        => $request->applicant_contact,
                'case_number'              => $request->case_number,
                'dispatch_courier_company' => $request->dispatch_courier_company ?: $defaultCompany,
                'dispatched_from'          => $request->dispatched_from,
                'type'                     => $request->type,
                'status'                   => 'dispatched',
                'center_id'                => $user->center_id,
                'dispatched_by_user_id'    => $user->id,
            ]);
        }

        ActivityLog::record('created', $dispatch, 'Dispatch created');

        return redirect()->back()->with('success', 'Dispatch request submitted successfully.');
    }

    public function show(Dispatch $dispatch)
    {
        $this->authorizeCenter($dispatch);
        $dispatch->load('attachments.uploader');
        return view('dispatches.show', compact('dispatch'));
    }

    public function edit(Dispatch $dispatch)
    {
        $this->authorizeCenter($dispatch);
        $centers = Center::all();
        return view('dispatches.edit', compact('dispatch', 'centers'));
    }

    public function update(Request $request, Dispatch $dispatch)
    {
        $this->authorizeCenter($dispatch);

        $request->validate([
            'applicant_name'          => 'required|string|max:255',
            'father_name'             => 'nullable|string|max:255',
            'applicant_contact'       => 'nullable|string|max:255',
            'case_number'             => 'required|string|max:255',
            'dispatch_courier_company'=> 'nullable|string|max:255',
            'dispatched_from'         => 'required|string|max:255',
            'tracking_id'             => 'nullable|string|max:255',
            'type'                    => 'required|in:applicant,internal,sub_office,ministry',
        ]);

        $dispatch->update([
            'applicant_name'           => $request->applicant_name,
            'father_name'              => $request->father_name,
            'applicant_contact'        => $request->applicant_contact,
            'case_number'              => $request->case_number,
            'dispatch_courier_company' => $request->dispatch_courier_company,
            'dispatched_from'          => $request->dispatched_from,
            'tracking_id'              => $request->tracking_id ?? $dispatch->tracking_id,
            'type'                     => $request->type,
        ]);

        ActivityLog::record('updated', $dispatch, 'Dispatch details updated');
        return redirect()->back()->with('success', 'Dispatch updated successfully.');
    }

    public function destroy(Dispatch $dispatch)
    {
        $this->authorizeCenter($dispatch);
        ActivityLog::record('deleted', $dispatch, 'Dispatch deleted');
        $dispatch->delete();

        return redirect()->back()->with('success', 'Dispatch deleted successfully.');
    }

    public function markReceived(Dispatch $dispatch)
    {
        $user = Auth::user();
        if (!in_array($user->role->slug, ['super-admin', 'center-admin', 'staff-user'])) {
            abort(403, 'Unauthorized action.');
        }
        if ($user->role->slug !== 'super-admin' && $dispatch->center_id !== $user->center_id) {
            abort(403, 'Unauthorized action.');
        }
        if ($dispatch->received_at) {
            return redirect()->back()->with('error', 'Already marked as received.');
        }

        $dispatch->received_at = now();
        $dispatch->save();
        ActivityLog::record('received', $dispatch, 'Dispatch received by R&I');

        return redirect()->back()->with('success', 'Dispatch marked as received by R&I.');
    }

    public function markDispatched(Request $request, Dispatch $dispatch)
    {
        $user = Auth::user();
        if (!in_array($user->role->slug, ['super-admin', 'center-admin', 'staff-user'])) {
            abort(403, 'Unauthorized action.');
        }
        if ($user->role->slug !== 'super-admin' && $dispatch->center_id !== $user->center_id) {
            abort(403, 'Unauthorized action.');
        }
        if ($dispatch->status === 'dispatched') {
            return redirect()->back()->with('error', 'Already dispatched.');
        }

        $request->validate([
            'tracking_id' => 'nullable|string|max:255',
        ]);

        $dispatch->status = 'dispatched';
        $dispatch->dispatched_by_user_id = $user->id;
        if ($request->filled('tracking_id')) {
            $dispatch->tracking_id = $request->tracking_id;
        }
        if (!$dispatch->received_at) {
            $dispatch->received_at = now();
        }
        $dispatch->save();
        ActivityLog::record('dispatched', $dispatch, 'Dispatch finalized and sent out');

        return redirect()->back()->with('success', 'Dispatch marked as dispatched.');
    }

    private function authorizeCenter(Dispatch $dispatch, bool $allowFinalized = false): void
    {
        $user = Auth::user();
        if ($user->role->slug === 'officer') {
            if ($dispatch->requested_by_user_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
            // Officers cannot edit/delete records that have already been dispatched
            if (!$allowFinalized && $dispatch->status === 'dispatched') {
                abort(403, 'This dispatch has been finalized and cannot be modified.');
            }
            return;
        }
        if ($user->role->slug !== 'super-admin' && $dispatch->center_id !== $user->center_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
