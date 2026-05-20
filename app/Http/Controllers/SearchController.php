<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Dispatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $results = collect();
        $totalCount = 0;

        if (strlen($q) >= 2) {
            $user = Auth::user();

            $courierQuery = Courier::with(['center', 'department', 'assignedUser'])
                ->where(function ($query) use ($q) {
                    $query->where('tracking_id', 'like', "%{$q}%")
                          ->orWhere('sender_name', 'like', "%{$q}%")
                          ->orWhere('courier_company', 'like', "%{$q}%")
                          ->orWhere('city', 'like', "%{$q}%");
                });

            $dispatchQuery = Dispatch::with(['center', 'dispatchedBy', 'requestedBy'])
                ->where(function ($query) use ($q) {
                    $query->where('case_number', 'like', "%{$q}%")
                          ->orWhere('applicant_name', 'like', "%{$q}%")
                          ->orWhere('tracking_id', 'like', "%{$q}%")
                          ->orWhere('dispatched_from', 'like', "%{$q}%");
                });

            if ($user->role->slug === 'officer') {
                $courierQuery->where('department_id', $user->department_id);
                $dispatchQuery->where('requested_by_user_id', $user->id);
            } elseif (in_array($user->role->slug, ['staff-user', 'center-admin'])) {
                $courierQuery->where('center_id', $user->center_id);
                $dispatchQuery->where('center_id', $user->center_id);
            }

            $couriers   = $courierQuery->latest()->limit(20)->get();
            $dispatches = $dispatchQuery->latest()->limit(20)->get();
            $totalCount = $couriers->count() + $dispatches->count();

            $results = [
                'couriers'   => $couriers,
                'dispatches' => $dispatches,
            ];
        }

        return view('search.index', compact('q', 'results', 'totalCount'));
    }
}
