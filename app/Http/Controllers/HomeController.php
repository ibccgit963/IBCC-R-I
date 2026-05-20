<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Dispatch;
use App\Models\Center;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\HttpFoundation\StreamedResponse;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $centers = $user->role->slug === 'super-admin' ? Center::all() : collect();

        // Fetch couriers that were reverted back to R&I
        $revertedCouriers = collect();
        if (in_array($user->role->slug, ['staff-user', 'center-admin', 'super-admin'])) {
            $revertedQuery = Courier::with(['revertedBy', 'department', 'center'])
                ->whereNotNull('reverted_by_user_id')
                ->where('status', 'pending');
            if (in_array($user->role->slug, ['staff-user', 'center-admin'])) {
                $revertedQuery->where('center_id', $user->center_id);
            }
            $revertedCouriers = $revertedQuery->latest('updated_at')->get();
        }

        // Stats
        $courierBase = Courier::query();
        $dispatchBase = Dispatch::query();
        if (in_array($user->role->slug, ['staff-user', 'center-admin'])) {
            $courierBase->where('center_id', $user->center_id);
            $dispatchBase->where('center_id', $user->center_id);
        } elseif ($user->role->slug === 'officer') {
            $courierBase->where('department_id', $user->department_id);
            $dispatchBase->where('requested_by_user_id', $user->id);
        }

        // Couriers returned to R&I flagged for dispatch (courier-flow, not Dispatch model)
        $riForDispatchBase = Courier::whereNull('department_id')
            ->whereHas('transfers', fn($q) => $q->where('is_for_dispatch', true));
        if (in_array($user->role->slug, ['center-admin', 'staff-user'])) {
            $riForDispatchBase->where('center_id', $user->center_id);
        }

        $stats = [
            'totalPending'   => (clone $courierBase)->where('status', 'pending')->count(),
            'totalReceived'  => (clone $courierBase)->where('status', 'received')->count(),
            'totalDispatched'=> (clone $courierBase)->where('status', 'dispatched')->count(),
            'totalReverted'  => (clone $courierBase)->whereNotNull('reverted_by_user_id')->where('status', 'pending')->count(),
            'todayReceived'  => (clone $courierBase)->whereDate('created_at', today())->count(),
            'totalOutgoing'      => (clone $dispatchBase)->count()
                                   + (clone $riForDispatchBase)->count(),
            'outgoingDispatched' => (clone $dispatchBase)->where('status', 'dispatched')->count()
                                   + (clone $riForDispatchBase)->where('status', 'dispatched')->count(),
            // Pending = Dispatch requests not yet received by R&I + couriers returned for dispatch not yet dispatched
            'outgoingPending' => (clone $dispatchBase)->whereNull('received_at')->count()
                                + (clone $riForDispatchBase)->whereIn('status', ['transferred', 'received'])->count(),
        ];

        return view('home', compact('centers', 'revertedCouriers', 'stats'));
    }

    public function incoming(Request $request)
    {
        $user = Auth::user();
        $queryCouriers = Courier::with(['department', 'assignedUser', 'transfers']);

        // Scope by role
        if ($user->role->slug === 'officer') {
            $deptHasFocalPerson = \App\Models\User::where('department_id', $user->department_id)
                ->where('is_focal_person', true)->exists();
            $queryCouriers->where('department_id', $user->department_id)
                          ->where(function ($query) use ($user, $deptHasFocalPerson) {
                              $query->where('assigned_user_id', $user->id);
                              if ($user->is_focal_person || !$deptHasFocalPerson) {
                                  $query->orWhereNull('assigned_user_id');
                              }
                          });
        } elseif (in_array($user->role->slug, ['center-admin', 'staff-user'])) {
            $queryCouriers->where('center_id', $user->center_id)
                          ->whereNull('reverted_by_user_id'); // reverted ones shown separately on home dashboard
        } elseif ($request->filled('center_id')) {
            $queryCouriers->where('center_id', $request->center_id);
        }

        // Apply date range filter
        if ($request->filled('start_date')) {
            $queryCouriers->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $queryCouriers->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $queryCouriers->where(function($q) use ($request) {
                $q->where('tracking_id', 'like', '%' . $request->search . '%')
                  ->orWhere('sender_name', 'like', '%' . $request->search . '%')
                  ->orWhere('sender_cnic', 'like', '%' . $request->search . '%')
                  ->orWhere('courier_company', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }

        if ($user->role->slug === 'officer') {
            // Pending = in officer's inbox, not yet acknowledged
            $pendingCouriers = (clone $queryCouriers)->where('status', 'transferred')->count();
            // Transferred = couriers this officer personally forwarded to another dept/R&I
            $transferredCouriers = \App\Models\CourierTransfer::where('transferred_by_user_id', $user->id)
                ->when($request->filled('start_date'), fn($q) => $q->whereDate('created_at', '>=', $request->start_date))
                ->when($request->filled('end_date'),   fn($q) => $q->whereDate('created_at', '<=', $request->end_date))
                ->distinct('courier_id')
                ->count('courier_id');
                
            if ($request->tab === 'transferred') {
                $transferCourierIds = \App\Models\CourierTransfer::where('transferred_by_user_id', $user->id)
                    ->distinct('courier_id')
                    ->pluck('courier_id');
                $recentCouriers = Courier::with(['department', 'assignedUser', 'transfers', 'center'])
                    ->whereIn('id', $transferCourierIds)
                    ->latest()
                    ->paginate(20);
            } else {
                $recentCouriers = (clone $queryCouriers)->with('transfers', 'department')->latest()->paginate(20);
            }
        } else {
            $pendingCouriers    = (clone $queryCouriers)->where('status', 'pending')->count();
            $transferredCouriers = (clone $queryCouriers)->where('status', 'transferred')->count();
            $recentCouriers = (clone $queryCouriers)->with('transfers', 'department')->latest()->paginate(20);
        }

        $centers = $user->role->slug === 'super-admin' ? Center::all() : collect();

        return view('incoming', compact(
            'pendingCouriers',
            'transferredCouriers',
            'centers',
            'recentCouriers'
        ));
    }

    public function incoming1(Request $request)
    {
        $couriers = $this->buildCourierQuery('applicant', $request);
        $centers = Center::all();
        $departments = $this->eligibleDepartments();
        return view('incoming1', compact('couriers', 'centers', 'departments'));
    }

    public function incoming2(Request $request)
    {
        $couriers = $this->buildCourierQuery('internal', $request);
        $centers = Center::all();
        $departments = $this->eligibleDepartments();
        return view('incoming2', compact('couriers', 'centers', 'departments'));
    }

    public function incoming3(Request $request)
    {
        $couriers = $this->buildCourierQuery('sub_office', $request);
        $centers = Center::all();
        $departments = $this->eligibleDepartments();
        return view('incoming3', compact('couriers', 'centers', 'departments'));
    }

    public function incoming4(Request $request)
    {
        $couriers = $this->buildCourierQuery('ministry', $request);
        $centers = Center::all();
        $departments = $this->eligibleDepartments();
        $ministries = Courier::whereNotNull('ministry_department')
            ->where('ministry_department', '!=', '')
            ->distinct()
            ->pluck('ministry_department');
        return view('incoming4', compact('couriers', 'centers', 'departments', 'ministries'));
    }

    private function buildCourierQuery(string $type, ?Request $request = null)
    {
        $user = Auth::user();
        $q = Courier::with('center', 'receivedBy', 'assignedUser', 'department', 'transfers')->where('type', $type);

        if ($user->role->slug === 'officer') {
            $deptHasFocalPerson = \App\Models\User::where('department_id', $user->department_id)
                ->where('is_focal_person', true)->exists();
            $q->where('department_id', $user->department_id)
              ->where(function ($query) use ($user, $deptHasFocalPerson) {
                  $query->where('assigned_user_id', $user->id);
                  if ($user->is_focal_person || !$deptHasFocalPerson) {
                      $query->orWhereNull('assigned_user_id');
                  }
              })
              ->whereIn('status', ['transferred', 'received']);
        } elseif (in_array($user->role->slug, ['center-admin', 'staff-user'])) {
            $q->where('center_id', $user->center_id);
        } elseif ($user->role->slug === 'super-admin' && $request && $request->filled('center_id')) {
            $q->where('center_id', $request->center_id);
        }

        return $q->latest()->paginate(25);
    }

    private function eligibleDepartments()
    {
        return Department::where('center_id', Auth::user()->center_id)
            ->whereHas('users', fn($q) => $q->whereHas('role', fn($r) => $r->where('slug', 'officer')))
            ->get();
    }

    public function outgoing1(Request $request)
    {
        $dispatches = $this->buildDispatchQuery('applicant', $request);
        $forDispatchCouriers = $this->buildForDispatchCouriersQuery('applicant', $request);
        $defaultCompany = auth()->user()->center?->default_courier_company ?? '';
        $centers = auth()->user()->role->slug === 'super-admin' ? Center::all() : collect();
        return view('outgoing1', compact('dispatches', 'forDispatchCouriers', 'defaultCompany', 'centers'));
    }

    public function outgoing2(Request $request)
    {
        $dispatches = $this->buildDispatchQuery('internal', $request);
        $forDispatchCouriers = $this->buildForDispatchCouriersQuery('internal', $request);
        $defaultCompany = auth()->user()->center?->default_courier_company ?? '';
        $centers = auth()->user()->role->slug === 'super-admin' ? Center::all() : collect();
        return view('outgoing2', compact('dispatches', 'forDispatchCouriers', 'defaultCompany', 'centers'));
    }

    public function outgoing3(Request $request)
    {
        $dispatches = $this->buildDispatchQuery('sub_office', $request);
        $forDispatchCouriers = $this->buildForDispatchCouriersQuery('sub_office', $request);
        $defaultCompany = auth()->user()->center?->default_courier_company ?? '';
        $centers = auth()->user()->role->slug === 'super-admin' ? Center::all() : collect();
        return view('outgoing3', compact('dispatches', 'forDispatchCouriers', 'defaultCompany', 'centers'));
    }

    public function outgoing4(Request $request)
    {
        $dispatches = $this->buildDispatchQuery('ministry', $request);
        $forDispatchCouriers = $this->buildForDispatchCouriersQuery('ministry', $request);
        $defaultCompany = auth()->user()->center?->default_courier_company ?? '';
        $centers = auth()->user()->role->slug === 'super-admin' ? Center::all() : collect();
        return view('outgoing4', compact('dispatches', 'forDispatchCouriers', 'defaultCompany', 'centers'));
    }

    private function buildDispatchQuery(string $type, ?Request $request = null)
    {
        $user = Auth::user();
        $q = \App\Models\Dispatch::with('center', 'dispatchedBy', 'requestedBy')->where('type', $type);

        if ($user->role->slug === 'officer') {
            $q->where('requested_by_user_id', $user->id);
        } elseif (in_array($user->role->slug, ['center-admin', 'staff-user'])) {
            $q->where('center_id', $user->center_id);
        } elseif ($user->role->slug === 'super-admin' && $request && $request->filled('center_id')) {
            $q->where('center_id', $request->center_id);
        }

        // Filter by status tab if provided
        if ($request && $request->filled('tab') && $request->tab !== 'all') {
            if ($request->tab === 'pending') {
                $q->where('status', 'pending');
            } elseif ($request->tab === 'dispatched') {
                $q->where('status', 'dispatched');
            }
        }

        // Pending records on top, then by latest
        return $q->orderByRaw("CASE status WHEN 'pending' THEN 0 ELSE 1 END")->orderBy('created_at', 'desc')->paginate(25);
    }

    private function buildForDispatchCouriersQuery(string $type, ?Request $request = null)
    {
        $user = Auth::user();
        if ($user->role->slug === 'officer') {
            return collect();
        }

        $q = Courier::with('center', 'receivedBy', 'transfers.transferredBy')
            ->where('type', $type)
            ->whereNull('department_id')
            ->whereHas('transfers', fn($tq) => $tq->where('is_for_dispatch', true));

        if (in_array($user->role->slug, ['center-admin', 'staff-user'])) {
            $q->where('center_id', $user->center_id);
        } elseif ($user->role->slug === 'super-admin' && $request && $request->filled('center_id')) {
            $q->where('center_id', $request->center_id);
        }

        // Pending and ready first, dispatched last
        return $q->orderByRaw("CASE status WHEN 'transferred' THEN 0 WHEN 'received' THEN 1 ELSE 2 END")
                 ->orderBy('updated_at', 'desc')
                 ->get();
    }

    public function outgoing(Request $request)
    {
        $user = Auth::user();
        $queryDispatches = Dispatch::query();

        // Apply center/department filter based on user role
        if ($user->role->slug === 'center-admin' || $user->role->slug === 'staff-user') {
            $queryDispatches->where('center_id', $user->center_id);
        } elseif ($user->role->slug === 'officer') {
            $queryDispatches->where('requested_by_user_id', $user->id);
        } elseif ($request->has('center_id') && $request->center_id != '') {
            $queryDispatches->where('center_id', $request->center_id);
        }

        // Apply date range filter
        if ($request->has('start_date') && $request->start_date != '') {
            $queryDispatches->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $queryDispatches->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $queryDispatches->where(function($q) use ($request) {
                $q->where('applicant_name', 'like', '%' . $request->search . '%')
                  ->orWhere('father_name', 'like', '%' . $request->search . '%')
                  ->orWhere('case_number', 'like', '%' . $request->search . '%')
                  ->orWhere('dispatch_courier_company', 'like', '%' . $request->search . '%')
                  ->orWhere('dispatched_from', 'like', '%' . $request->search . '%');
            });
        }

        $pendingDispatches = (clone $queryDispatches)->where('status', 'pending')->count();
        $transferredDispatches = (clone $queryDispatches)->where('status', 'dispatched')->count();

        $recentDispatches = (clone $queryDispatches)->with('requestedBy', 'dispatchedBy')->latest()->take(20)->get();

        $centers = Auth::user()->role->slug === 'super-admin' ? Center::all() : collect();

        return view('outgoing', compact(
            'pendingDispatches',
            'transferredDispatches',
            'centers',
            'recentDispatches'
        ));
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $queryCouriers = Courier::query();
        $queryDispatches = Dispatch::query();

        // Apply center filter based on user role
        // Apply center/department filter based on user role
        if ($user->role->slug === 'center-admin' || $user->role->slug === 'staff-user') {
            $queryCouriers->where('center_id', $user->center_id);
            $queryDispatches->where('center_id', $user->center_id);
        } elseif ($user->role->slug === 'officer') {
            $deptHasFocalPerson = \App\Models\User::where('department_id', $user->department_id)
                ->where('is_focal_person', true)->exists();
            $queryCouriers->where(function($q) use ($user, $deptHasFocalPerson) {
                $q->where(function ($query) use ($user, $deptHasFocalPerson) {
                      $query->where('department_id', $user->department_id)
                            ->where(function ($inner) use ($user, $deptHasFocalPerson) {
                                $inner->where('assigned_user_id', $user->id);
                                if ($user->is_focal_person || !$deptHasFocalPerson) {
                                    $inner->orWhereNull('assigned_user_id');
                                }
                            });
                  })
                  ->orWhereHas('transfers', function($q2) use ($user) {
                      $q2->where('transferred_by_user_id', $user->id);
                  });
            });
            $queryDispatches->where('center_id', $user->center_id);
        } elseif ($request->has('center_id') && $request->center_id != '') {
            $queryCouriers->where('center_id', $request->center_id);
            $queryDispatches->where('center_id', $request->center_id);
        }

        // Apply date range filter
        if ($request->has('start_date') && $request->start_date != '') {
            $queryCouriers->whereDate('created_at', '>=', $request->start_date);
            $queryDispatches->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $queryCouriers->whereDate('created_at', '<=', $request->end_date);
            $queryDispatches->whereDate('created_at', '<=', $request->end_date);
        }

        $incomingData = $queryCouriers->get();
        $dispatchData = $queryDispatches->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="dashboard_export_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($incomingData, $dispatchData) {
            $file = fopen('php://output', 'w');

            // Incoming Couriers
            fputcsv($file, ['Incoming Couriers']);
            fputcsv($file, [
                'ID', 'Tracking ID', 'Company', 'Sender Name', 'Sender CNIC',
                'Sender Contact', 'Category', 'Center', 'Received By', 'Status', 'Created At'
            ]);
            foreach ($incomingData as $courier) {
                fputcsv($file, [
                    $courier->id,
                    $courier->tracking_id,
                    $courier->courier_company,
                    $courier->sender_name,
                    $courier->sender_cnic,
                    $courier->sender_contact,
                    $courier->category,
                    $courier->center->name ?? 'N/A',
                    $courier->receivedBy->name ?? 'N/A',
                    $courier->status,
                    $courier->created_at,
                ]);
            }

            fputcsv($file, []); // Empty line for separation

            // Outgoing Dispatches
            fputcsv($file, ['Outgoing Dispatches']);
            fputcsv($file, [
                'ID', 'Applicant Name', 'Father Name', 'Applicant Contact',
                'Case Number', 'Dispatch Company', 'Dispatched From', 'Center', 'Dispatched By', 'Created At'
            ]);
            foreach ($dispatchData as $dispatch) {
                fputcsv($file, [
                    $dispatch->id,
                    $dispatch->applicant_name,
                    $dispatch->father_name,
                    $dispatch->applicant_contact,
                    $dispatch->case_number,
                    $dispatch->dispatch_courier_company,
                    $dispatch->dispatched_from,
                    $dispatch->center->name ?? 'N/A',
                    $dispatch->dispatchedBy->name ?? 'N/A',
                    $dispatch->created_at,
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
