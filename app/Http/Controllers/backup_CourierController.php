<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Department;
use App\Models\Officer;

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
        // Add authorization check here later
        // Only show departments that have at least one user in the same center
        $departments = Department::where('center_id', $courier->center_id)
            ->whereHas('users', function ($query) use ($courier) {
                $query->where('center_id', $courier->center_id);
            })
            ->get();
        $officers = Officer::where('center_id', $courier->center_id)->get();
        return view('couriers.transfer', compact('courier', 'departments', 'officers'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $couriers = Courier::with('center', 'receivedBy');

        // If user is a Department User (Officer), show only couriers forwarded to their department
        if (Auth::user()->role->slug === 'officer') {
            $couriers->where('department_id', Auth::user()->department_id)
                     ->where('status', 'transferred');
        } elseif (Auth::user()->role->slug === 'center-admin' || Auth::user()->role->slug === 'staff-user') {
            $couriers->where('center_id', Auth::user()->center_id);
        }

        $couriers = $couriers->get();
        // Data for the create form
        $centers = Center::all(); 
        $departments = Department::where('center_id', Auth::user()->center_id)->get();
        
        return view('couriers.index', compact('couriers', 'centers', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $centers = Center::all(); // Potentially filtered by user's center for staff
        $centers = Center::all(); 
        $departments = Department::where('center_id', Auth::user()->center_id)->get();
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
            'department_id' => 'required|exists:departments,id',
        ]);

        // Save courier company for suggestions
        \App\Models\CourierCompany::firstOrCreate(['name' => $request->courier_company]);

        $department = Department::find($request->department_id);

        Courier::create([
            'tracking_id' => $request->tracking_id,
            'courier_company' => $request->courier_company,
            'sender_name' => $request->sender_name,
            'sender_cnic' => $request->sender_cnic,
            'sender_contact' => $request->sender_contact,
            'category' => $department->name, // Keeping category as department name for backward compatibility or display
            'department_id' => $request->department_id,
            'center_id' => Auth::user()->center_id, // Auto-populate from logged-in user
            'received_by_user_id' => Auth::id(), // Auto-populate from logged-in user
            'status' => 'transferred', // Auto-forward to Department
        ]);

        return redirect()->route('couriers.index')
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
        // Add authorization check here later
        $request->validate([
            'tracking_id' => 'required|string|max:255|unique:couriers,tracking_id,' . $courier->id,
            'courier_company' => 'required|string|max:255',
            'sender_name' => 'required|string|max:255',
            'sender_cnic' => 'nullable|string|max:255',
            'sender_contact' => 'nullable|string|max:255',
            'category' => 'required|in:department-officer,attestation,equivalence,others',
        ]);

        // Save courier company for suggestions
        \App\Models\CourierCompany::firstOrCreate(['name' => $request->courier_company]);

        $courier->update([
            'tracking_id' => $request->tracking_id,
            'courier_company' => $request->courier_company,
            'sender_name' => $request->sender_name,
            'sender_cnic' => $request->sender_cnic,
            'sender_contact' => $request->sender_contact,
            'category' => $request->category,
            // 'center_id' and 'received_by_user_id' are generally not updated after creation
        ]);

        return redirect()->route('couriers.index')
                         ->with('success', 'Incoming courier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Courier $courier)
    {
        // Add authorization check here later
        $courier->delete();

        return redirect()->route('couriers.index')
                         ->with('success', 'Incoming courier deleted successfully.');
    }

    public function getCompanySuggestions(Request $request)
    {
        $search = $request->get('search');
        $companies = \App\Models\CourierCompany::where('name', 'LIKE', "%{$search}%")
            ->limit(10)
            ->get(['name']); // Select only the name column

        return response()->json($companies);
    }

    public function markReceived(Courier $courier)
    {
        // Check if user is authorized (must be a department user/officer of the same department)
        if (Auth::user()->role->slug !== 'officer' || Auth::user()->department_id !== $courier->department_id) {
            abort(403, 'Unauthorized action.');
        }

        $courier->update([
            'status' => 'received',
            'received_by_user_id' => Auth::id(), // Allow department user to "receive" it
        ]);

        return redirect()->route('couriers.index')
                         ->with('success', 'Courier marked as received.');
    }
}
