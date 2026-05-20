<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\CourierTransfer;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Officer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourierTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin|center-admin|staff-user|officer');
    }

    public function transfer(Request $request, Courier $courier)
    {
        $user = Auth::user();
        if ($user->role->slug !== 'super-admin' && $courier->center_id !== $user->center_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($courier->status === 'dispatched') {
            return redirect()->back()->with('error', 'This courier has already been dispatched.');
        }

        $request->validate([
            'transferable_type' => 'required|in:department,user',
            'transferable_id' => 'required|integer',
        ]);

        if ($request->transferable_type === 'department') {
            $transferable = Department::findOrFail($request->transferable_id);
            if ($transferable->users()->count() === 0) {
                return redirect()->back()->with('error', 'Cannot transfer to a department with no active users.');
            }
            if ($transferable->center_id !== $courier->center_id) {
                return redirect()->back()->with('error', 'Cannot transfer courier to a different center\'s department.');
            }
            $courier->department_id = $request->transferable_id;
            // Assign to focal person so only they see the courier; falls back to null if none set
            $focalPerson = $transferable->users()->where('is_focal_person', true)->first();
            $courier->assigned_user_id = $focalPerson ? $focalPerson->id : null;
            $transferableTypeClass = \App\Models\Department::class;
        } else {
            $transferable = User::findOrFail($request->transferable_id);
            $isTargetStaffUser = $transferable->role && $transferable->role->slug === 'staff-user';
            $courier->assigned_user_id = $request->transferable_id;
            $courier->department_id = $transferable->department_id;
            $transferableTypeClass = \App\Models\User::class;
        }

        CourierTransfer::create([
            'courier_id' => $courier->id,
            'transferable_type' => $transferableTypeClass,
            'transferable_id' => $request->transferable_id,
            'transferred_by_user_id' => Auth::id(),
            'is_for_dispatch' => $request->boolean('is_for_dispatch'),
            'notes' => $request->filled('notes') ? trim($request->notes) : null,
        ]);

        $courier->status = 'transferred';
        // If this courier was previously reverted, clear the revert trail so it leaves the reverted list
        $courier->reverted_by_user_id = null;
        $courier->comments = null;
        $courier->save();

        ActivityLog::record('transferred', $courier, 'Courier transferred to ' . ($transferable->name ?? 'unknown'));
        return redirect()->route('couriers.transferForm', $courier->id)->with('success', 'Courier transferred successfully.');
    }

    public function markReceived(CourierTransfer $courierTransfer)
    {
        // Ensure the courier transfer is loaded with its transferable (Officer or Department) and courier
        $courierTransfer->load('transferable', 'courier');

        if ($courierTransfer->received_at) {
            return redirect()->back()->with('error', 'Courier already marked as received.');
        }

        $user = Auth::user();
        $isAuthorized = false;

        // Super Admin and Center Admin can mark as received (within their center for Center Admin)
        if ($user->role->slug === 'super-admin') {
            $isAuthorized = true;
        } elseif ($user->role->slug === 'center-admin') {
            if ($courierTransfer->courier->center_id === $user->center_id) {
                $isAuthorized = true;
            }
        } elseif ($user->role->slug === 'officer') {
            // Officer can mark received if the courier's current department is theirs
            if ($courierTransfer->courier->department_id === $user->department_id &&
                $courierTransfer->courier->center_id === $user->center_id) {
                $isAuthorized = true;
            }
        }

        if (!$isAuthorized) {
            return redirect()->back()->with('error', 'You are not authorized to mark this courier as received.');
        }

        // Proceed to mark as received
        $courierTransfer->received_at = now();
        $courierTransfer->save();

        $courier = $courierTransfer->courier;
        $courier->status = 'received';
        if ($courier->assigned_user_id === null && $user->role->slug === 'officer') {
            $courier->assigned_user_id = $user->id;
        }
        $courier->save();

        return redirect()->back()->with('success', 'Courier marked as received.');
    }
}
