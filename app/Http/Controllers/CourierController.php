<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Center;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Department;
use App\Models\Officer;
use App\Models\User;

class CourierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin|center-admin|staff-user|officer');
    }

    // ... (existing methods)

    public function transferForm(Courier $courier)
    {
        $user = Auth::user();
        if ($user->role->slug !== 'super-admin' && $courier->center_id !== $user->center_id) {
            abort(403, 'Unauthorized action.');
        }
        $courier->load('center', 'receivedBy', 'department', 'transfers.transferable', 'transfers.transferredBy');
        $departments = Department::where('center_id', $courier->center_id)->get();

        // Build department→users map for Alpine.js dynamic officer dropdown
        $usersForTransfer = \App\Models\User::whereNotNull('department_id')
            ->where('center_id', $courier->center_id)
            ->get(['id', 'name', 'department_id'])
            ->groupBy('department_id')
            ->map(fn($users) => $users->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->values());

        return view('couriers.transfer', compact('courier', 'departments', 'usersForTransfer'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $couriers = Courier::with('center', 'receivedBy', 'assignedUser', 'department', 'transfers');

        // If user is a Department User (Officer), show only couriers forwarded to their department AND assigned to them (or no one if fallback)
        if (Auth::user()->role->slug === 'officer') {
            $user = Auth::user();
            $deptHasFocalPerson = \App\Models\User::where('department_id', $user->department_id)
                ->where('is_focal_person', true)->exists();
            $couriers->where('department_id', $user->department_id)
                     ->where(function ($q) use ($user, $deptHasFocalPerson) {
                         $q->where('assigned_user_id', $user->id);
                         // Null-assigned couriers: focal person sees them to distribute;
                         // if no focal person in dept, all officers see them (prevents invisibility)
                         if ($user->is_focal_person || !$deptHasFocalPerson) {
                             $q->orWhereNull('assigned_user_id');
                         }
                     })
                     ->where('status', 'transferred');
        } elseif (Auth::user()->role->slug === 'center-admin' || Auth::user()->role->slug === 'staff-user') {
            $couriers->where('center_id', Auth::user()->center_id);
        }

        $couriers = $couriers->get();
        $centers = Center::all();
        $departments = Department::where('center_id', Auth::user()->center_id)
            ->whereHas('users', fn($q) => $q->whereHas('role', fn($r) => $r->where('slug', 'officer')))
            ->get();
        
        return view('couriers.index', compact('couriers', 'centers', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $centers = Center::all();
        $departments = Department::where('center_id', Auth::user()->center_id)
            ->whereHas('users', fn($q) => $q->whereHas('role', fn($r) => $r->where('slug', 'officer')))
            ->get();
            
        return view('couriers.create', compact('centers', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tracking_id' => 'required|string|max:255|unique:couriers,tracking_id',
            'courier_company' => 'required|string|max:255',
            'sender_name' => 'required|string|max:255',
            'sender_cnic' => 'nullable|string|max:255',
            'sender_contact' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'internal_branch' => 'nullable|string|max:255',
            'branch' => 'nullable|string|max:255',
            'ministry_department' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'type' => 'required|in:applicant,internal,sub_office,ministry',
        ]);

        \App\Models\CourierCompany::firstOrCreate(['name' => $request->courier_company]);

        $department = Department::find($request->department_id);
        $assignedUserId = $request->assigned_user_id;

        // If no user selected, fallback to the department's focal person
        if (!$assignedUserId) {
            $focalPerson = User::where('department_id', $department->id)->where('is_focal_person', true)->first();
            if ($focalPerson) {
                $assignedUserId = $focalPerson->id;
            }
        }

        $courier = Courier::create([
            'tracking_id' => $request->tracking_id,
            'courier_company' => $request->courier_company,
            'sender_name' => $request->sender_name,
            'sender_cnic' => $request->sender_cnic,
            'sender_contact' => $request->sender_contact,
            'city' => $request->city,
            'address' => $request->address,
            'internal_branch' => $request->internal_branch,
            'branch' => $request->branch,
            'ministry_department' => $request->ministry_department,
            'category' => $department->name,
            'type' => $request->type,
            'department_id' => $request->department_id,
            'assigned_user_id' => $assignedUserId,
            'center_id' => Auth::user()->center_id,
            'received_by_user_id' => Auth::id(),
            'status' => 'transferred',
        ]);

        \App\Models\CourierTransfer::create([
            'courier_id' => $courier->id,
            'transferable_type' => \App\Models\Department::class,
            'transferable_id' => $request->department_id,
            'transferred_by_user_id' => Auth::id(),
        ]);

        ActivityLog::record('created', $courier, 'New courier registered');

        return redirect()->back()
                         ->with('success', 'Incoming courier registered successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Courier $courier)
    {
        $user = Auth::user();
        if ($user->role->slug !== 'super-admin' && $courier->center_id !== $user->center_id) {
            abort(403, 'Unauthorized action.');
        }
        $latestTransfer = $courier->transfers()->latest()->first();
        return view('couriers.show', compact('courier', 'latestTransfer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Courier $courier)
    {
        $user = Auth::user();
        if ($user->role->slug !== 'super-admin' && $courier->center_id !== $user->center_id) {
            abort(403, 'Unauthorized action.');
        }
        // Add authorization check here later
        $centers = Center::all(); // Potentially filtered
        $categories = ['department-officer', 'attestation', 'equivalence', 'others'];
        return view('couriers.edit', compact('courier', 'centers', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Courier $courier)
    {
        $user = Auth::user();
        if ($user->role->slug !== 'super-admin' && $courier->center_id !== $user->center_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'tracking_id' => 'required|string|max:255|unique:couriers,tracking_id,' . $courier->id,
            'courier_company' => 'required|string|max:255',
            'sender_name' => 'required|string|max:255',
            'sender_cnic' => 'nullable|string|max:255',
            'sender_contact' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'internal_branch' => 'nullable|string|max:255',
            'branch' => 'nullable|string|max:255',
            'ministry_department' => 'nullable|string|max:255',
            'type' => 'required|in:applicant,internal,sub_office,ministry',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        \App\Models\CourierCompany::firstOrCreate(['name' => $request->courier_company]);
        
        $assignedUserId = $request->assigned_user_id;
        $departmentId = $request->department_id ?? $courier->department_id;

        // If no user selected, fallback to the department's focal person
        if (!$assignedUserId && $departmentId) {
            $focalPerson = User::where('department_id', $departmentId)->where('is_focal_person', true)->first();
            if ($focalPerson) {
                $assignedUserId = $focalPerson->id;
            }
        }

        // If status was reverted, updating it should change it back to transferred so it goes back to the officer
        $status = $courier->status;
        if ($status === 'reverted') {
            $status = 'transferred';
        }

        $courier->update([
            'tracking_id' => $request->tracking_id,
            'courier_company' => $request->courier_company,
            'sender_name' => $request->sender_name,
            'sender_cnic' => $request->sender_cnic,
            'sender_contact' => $request->sender_contact,
            'city' => $request->city,
            'address' => $request->address,
            'internal_branch' => $request->internal_branch,
            'branch' => $request->branch,
            'ministry_department' => $request->ministry_department,
            'type' => $request->type,
            'department_id' => $departmentId,
            'assigned_user_id' => $assignedUserId,
            'status' => $status,
        ]);

        ActivityLog::record('updated', $courier, 'Courier details updated');

        return redirect()->route('couriers.index')
                         ->with('success', 'Incoming courier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Courier $courier)
    {
        $user = Auth::user();
        if ($user->role->slug !== 'super-admin' && $courier->center_id !== $user->center_id) {
            abort(403, 'Unauthorized action.');
        }

        ActivityLog::record('deleted', $courier, 'Courier soft-deleted');
        $courier->delete();

        return redirect()->route('couriers.index')
                         ->with('success', 'Incoming courier deleted successfully.');
    }

    public function returnToRI(\Illuminate\Http\Request $request, Courier $courier)
    {
        $user = Auth::user();
        if ($user->role->slug !== 'officer' || $user->department_id !== $courier->department_id) {
            abort(403, 'Unauthorized action.');
        }
        if ($courier->status !== 'received') {
            return redirect()->back()->with('error', 'You can only return a courier after marking it received.');
        }

        $isForDispatch = $request->boolean('is_for_dispatch');

        // Find the original R&I receiver to use as transferable target
        $riUser = $courier->receivedBy;

        \App\Models\CourierTransfer::create([
            'courier_id'          => $courier->id,
            'transferred_by_user_id' => $user->id,
            'transferable_type'   => $riUser ? \App\Models\User::class : null,
            'transferable_id'     => $riUser ? $riUser->id : null,
            'is_for_dispatch'     => $isForDispatch,
            'notes'               => $request->filled('notes') ? trim($request->notes) : null,
        ]);

        $courier->status = 'transferred';
        $courier->department_id = null;
        $courier->assigned_user_id = $riUser ? $riUser->id : null;
        $courier->save();

        $intent = $isForDispatch ? 'for dispatch' : 'for reassignment';
        ActivityLog::record('returned_to_ri', $courier, "Courier returned to R&I by officer ({$intent})");
        return redirect()->back()->with('success', 'Courier returned to R&I (' . ($isForDispatch ? 'ready for dispatch' : 'needs reassignment') . ').');
    }

    public function receiveBack(Courier $courier)
    {
        // Kept for backward compatibility — use returnToRI + markReceivedDirect instead
        abort(404);
    }

    public function markDispatched(Courier $courier)
    {
        $user = Auth::user();
        if (!in_array($user->role->slug, ['super-admin', 'center-admin', 'staff-user'])) {
            abort(403, 'Unauthorized action.');
        }
        if ($user->role->slug === 'staff-user' && $courier->center_id !== $user->center_id) {
            abort(403, 'Unauthorized action.');
        }
        if ($courier->status !== 'received' || $courier->department_id !== null) {
            return redirect()->back()->with('error', 'Courier must be received back by R&I before it can be dispatched.');
        }
        $courier->status = 'dispatched';
        $courier->save();
        ActivityLog::record('dispatched', $courier, 'Courier marked as dispatched');
        return redirect()->back()->with('success', 'Courier marked as dispatched.');
    }

    public function markReceivedDirect(Courier $courier)
    {
        $user = Auth::user();
        if ($user->role->slug === 'officer' && $user->department_id !== $courier->department_id) {
            abort(403, 'Unauthorized action.');
        }
        if ($courier->status !== 'transferred') {
            return redirect()->back()->with('error', 'Courier is not in a transferable state.');
        }
        $courier->status = 'received';
        if ($courier->assigned_user_id === null && $user->role->slug === 'officer') {
            $courier->assigned_user_id = $user->id;
        }
        $courier->save();
        ActivityLog::record('received', $courier, 'Courier acknowledged as received');
        return redirect()->back()->with('success', 'Courier marked as received.');
    }

    public function revert(Request $request, Courier $courier)
    {
        $user = Auth::user();
        if ($user->role->slug !== 'officer' || $user->department_id !== $courier->department_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'comments' => 'required|string|max:1000'
        ]);

        // Mark the latest unacknowledged transfer as reverted (persists in history even after re-assignment)
        $courier->transfers()
            ->whereNull('received_at')
            ->latest()
            ->first()?->update(['is_reverted' => true]);

        // Reset courier back to R&I pending queue
        $courier->status = 'pending';
        $courier->comments = $request->comments;
        $courier->reverted_by_user_id = $user->id;
        $courier->department_id = null;
        $courier->assigned_user_id = null;
        $courier->save();

        ActivityLog::record('reverted', $courier, 'Reverted to R&I: ' . $request->comments);
        return redirect()->route('incoming')->with('success', 'Courier has been reverted to R&I staff with your comments.');
    }

    public function getCompanySuggestions(Request $request)
    {
        $search = $request->get('search');
        $companies = \App\Models\CourierCompany::where('name', 'LIKE', "%{$search}%")
            ->limit(10)
            ->get(['name']); // Select only the name column

        return response()->json($companies);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action'      => 'required|in:mark_received,delete',
            'courier_ids' => 'required|array|min:1',
            'courier_ids.*' => 'integer|exists:couriers,id',
        ]);

        $user = Auth::user();
        $ids  = $request->courier_ids;

        $query = Courier::whereIn('id', $ids);
        if ($user->role->slug !== 'super-admin') {
            $query->where('center_id', $user->center_id);
        }
        $couriers = $query->get();

        if ($request->action === 'mark_received') {
            foreach ($couriers as $courier) {
                if ($courier->status === 'pending') {
                    $courier->update(['status' => 'received']);
                    ActivityLog::record('received', $courier, 'Bulk mark received');
                }
            }
            return back()->with('success', $couriers->count() . ' courier(s) marked as received.');
        }

        if ($request->action === 'delete' && in_array($user->role->slug, ['super-admin', 'center-admin'])) {
            foreach ($couriers as $courier) {
                ActivityLog::record('deleted', $courier, 'Bulk delete');
                $courier->delete();
            }
            return back()->with('success', $couriers->count() . ' courier(s) deleted.');
        }

        return back()->with('error', 'Invalid action or insufficient permissions.');
    }

}
