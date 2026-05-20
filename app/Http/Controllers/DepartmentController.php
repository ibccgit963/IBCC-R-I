<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['getDepartmentsByCenter']);
        $this->middleware('role:super-admin|center-admin')->except(['getDepartmentsByCenter']);
    }

    public function index()
    {
        $departments = Department::with('center')->when(
            Auth::user()->role->slug === 'center-admin',
            fn($q) => $q->where('center_id', Auth::user()->center_id)
        )->get();

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $centers = Auth::user()->role->slug === 'super-admin'
            ? Center::all()
            : Center::where('id', Auth::user()->center_id)->get();

        return view('departments.create', compact('centers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'center_id' => 'required|exists:centers,id',
        ]);

        $centerId = Auth::user()->role->slug === 'center-admin'
            ? Auth::user()->center_id
            : $request->center_id;

        Department::create(['name' => $request->name, 'center_id' => $centerId]);

        return redirect()->route('departments.index')
                         ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $this->authorizeCenter($department);
        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $this->authorizeCenter($department);
        $centers = Auth::user()->role->slug === 'super-admin'
            ? Center::all()
            : Center::where('id', Auth::user()->center_id)->get();

        return view('departments.edit', compact('department', 'centers'));
    }

    public function update(Request $request, Department $department)
    {
        $this->authorizeCenter($department);

        $request->validate([
            'name' => 'required|string|max:255',
            'center_id' => 'required|exists:centers,id',
        ]);

        $centerId = Auth::user()->role->slug === 'center-admin'
            ? Auth::user()->center_id
            : $request->center_id;

        $department->update(['name' => $request->name, 'center_id' => $centerId]);

        return redirect()->route('departments.index')
                         ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $this->authorizeCenter($department);
        $department->delete();

        return redirect()->route('departments.index')
                         ->with('success', 'Department deleted successfully.');
    }

    public function getDepartmentsByCenter(Request $request, $centerId)
    {
        $user = Auth::user();

        if ($user && $user->role->slug !== 'super-admin' && (int) $centerId !== (int) $user->center_id) {
            abort(403, 'Unauthorized action.');
        }

        $departments = Department::where('center_id', $centerId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($departments);
    }

    private function authorizeCenter(Department $department): void
    {
        $user = Auth::user();
        if ($user->role->slug !== 'super-admin' && $department->center_id !== $user->center_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
