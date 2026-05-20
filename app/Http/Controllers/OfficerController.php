<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use App\Models\Center;
use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OfficerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin|center-admin');
    }

    public function index()
    {
        $officers = Officer::with('center', 'department', 'user')->when(
            Auth::user()->role->slug === 'center-admin',
            fn($q) => $q->where('center_id', Auth::user()->center_id)
        )->get();

        return view('officers.index', compact('officers'));
    }

    public function create()
    {
        $officerRoleId = Role::where('slug', 'officer')->value('id');
        if (!$officerRoleId) {
            return redirect()->back()->with('error', 'Officer role not found in the system.');
        }

        $centers = Auth::user()->role->slug === 'super-admin'
            ? Center::all()
            : Center::where('id', Auth::user()->center_id)->get();

        $departments = Auth::user()->role->slug === 'super-admin'
            ? Department::all()
            : Department::where('center_id', Auth::user()->center_id)->get();

        $roles = Role::where('slug', '!=', 'super-admin')->get();

        return view('officers.create', compact('centers', 'departments', 'roles', 'officerRoleId'));
    }

    public function store(Request $request)
    {
        $officerRoleId = Role::where('slug', 'officer')->value('id');
        if (!$officerRoleId) {
            return redirect()->back()->with('error', 'Officer role not found in the system.')->withInput();
        }

        $centerId = Auth::user()->role->slug === 'center-admin'
            ? Auth::user()->center_id
            : $request->center_id;

        $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'center_id' => 'required|exists:centers,id',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'employee_id' => 'nullable|string|max:255|unique:users',
        ]);

        // Ensure center-admin can only assign departments within their own center
        if (Auth::user()->role->slug === 'center-admin') {
            $dept = Department::findOrFail($request->department_id);
            if ($dept->center_id !== $centerId) {
                abort(403, 'Unauthorized: department does not belong to your center.');
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'employee_id' => $request->employee_id,
            'center_id' => $centerId,
            'role_id' => $officerRoleId,
            'department_id' => $request->department_id,
        ]);

        Officer::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'designation' => $request->designation,
            'contact' => $request->contact,
            'department_id' => $request->department_id,
            'center_id' => $centerId,
        ]);

        return redirect()->route('officers.index')
                         ->with('success', 'Officer and User account created successfully.');
    }

    public function show(Officer $officer)
    {
        $this->authorizeCenter($officer);
        $officer->load('user');
        return view('officers.show', compact('officer'));
    }

    public function edit(Officer $officer)
    {
        $this->authorizeCenter($officer);
        $officer->load('user');

        $officerRoleId = Role::where('slug', 'officer')->value('id');
        if (!$officerRoleId) {
            return redirect()->back()->with('error', 'Officer role not found in the system.');
        }

        $centers = Auth::user()->role->slug === 'super-admin'
            ? Center::all()
            : Center::where('id', Auth::user()->center_id)->get();

        $departments = Auth::user()->role->slug === 'super-admin'
            ? Department::all()
            : Department::where('center_id', Auth::user()->center_id)->get();

        $roles = Role::where('slug', '!=', 'super-admin')->get();

        return view('officers.edit', compact('officer', 'centers', 'departments', 'roles', 'officerRoleId'));
    }

    public function update(Request $request, Officer $officer)
    {
        $this->authorizeCenter($officer);

        $officerRoleId = Role::where('slug', 'officer')->value('id');
        if (!$officerRoleId) {
            return redirect()->back()->with('error', 'Officer role not found in the system.')->withInput();
        }

        $centerId = Auth::user()->role->slug === 'center-admin'
            ? Auth::user()->center_id
            : $request->center_id;

        $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'center_id' => 'required|exists:centers,id',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($officer->user_id)],
            'employee_id' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($officer->user_id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!$officer->user) {
            return redirect()->back()->withErrors('No associated user found for this officer.')->withInput();
        }

        $officer->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'employee_id' => $request->employee_id,
            'center_id' => $centerId,
            'role_id' => $officerRoleId,
            'department_id' => $request->department_id,
        ]);

        if ($request->filled('password')) {
            $officer->user->update(['password' => Hash::make($request->password)]);
        }

        $officer->update([
            'name' => $request->name,
            'designation' => $request->designation,
            'contact' => $request->contact,
            'department_id' => $request->department_id,
            'center_id' => $centerId,
        ]);

        return redirect()->route('officers.index')
                         ->with('success', 'Officer and User account updated successfully.');
    }

    public function destroy(Officer $officer)
    {
        $this->authorizeCenter($officer);

        if ($officer->user) {
            $officer->user->delete();
        }
        $officer->delete();

        return redirect()->route('officers.index')
                         ->with('success', 'Officer and associated User account deleted successfully.');
    }

    private function authorizeCenter(Officer $officer): void
    {
        $user = Auth::user();
        if ($user->role->slug !== 'super-admin' && $officer->center_id !== $user->center_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
