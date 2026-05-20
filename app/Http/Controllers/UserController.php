<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Center;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super-admin|center-admin');
    }

    public function index(Request $request)
    {
        $users = User::with('center', 'role', 'department');

        $allCenters = Center::all();
        $allRoles = Role::all();
        $allDepartments = Auth::user()->role->slug === 'super-admin'
            ? Department::all()
            : Department::where('center_id', Auth::user()->center_id)->get();

        if (Auth::user()->role->slug === 'super-admin') {
            if ($request->filled('center_id')) {
                $users->where('center_id', $request->center_id);
            }
            if ($request->filled('role_id')) {
                $users->where('role_id', $request->role_id);
            }
            if ($request->filled('department_id')) {
                $users->where('department_id', $request->department_id);
            }
            if ($request->filled('search')) {
                $searchTerm = '%' . $request->search . '%';
                $users->where(fn($q) => $q->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('employee_id', 'like', $searchTerm));
            }
        } elseif (Auth::user()->role->slug === 'center-admin') {
            $users->where('center_id', Auth::user()->center_id)
                  ->whereHas('role', fn($q) => $q->whereNotIn('slug', ['super-admin', 'center-admin']));

            if ($request->filled('department_id')) {
                $users->where('department_id', $request->department_id);
            }
        }

        $users = $users->get();

        return view('users.index', compact('users', 'allCenters', 'allRoles', 'allDepartments'));
    }

    public function create()
    {
        $centers = Center::all();
        $roles = Role::all();
        $departments = Auth::user()->role->slug === 'super-admin'
            ? Department::all()
            : Department::where('center_id', Auth::user()->center_id)->get();

        return view('users.create', compact('centers', 'roles', 'departments'));
    }

    public function store(Request $request)
    {
        $isOfficerRole = $this->requestedRoleIsOfficer($request->role_id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'employee_id' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'department_id' => $isOfficerRole
                ? 'required|integer|exists:departments,id'
                : 'nullable|integer|exists:departments,id',
            'is_focal_person' => 'nullable|boolean',
        ];

        if (Auth::user()->role->slug === 'super-admin') {
            $rules['center_id'] = 'required|integer|exists:centers,id';
            $rules['role_id'] = 'required|integer|exists:roles,id';
        } elseif (Auth::user()->role->slug === 'center-admin') {
            $forbiddenRoleSlugs = ['super-admin', 'center-admin'];
            $allowedRoleIds = Role::whereNotIn('slug', $forbiddenRoleSlugs)->pluck('id')->toArray();
            $rules['role_id'] = ['required', 'integer', Rule::in($allowedRoleIds)];
        }

        $validatedData = $request->validate($rules);
        $validatedData['password'] = Hash::make($validatedData['password']);

        if (Auth::user()->role->slug === 'center-admin') {
            $validatedData['center_id'] = Auth::user()->center_id;
        }
        
        $isFocalPerson = $request->boolean('is_focal_person');
        $validatedData['is_focal_person'] = $isFocalPerson && $isOfficerRole;

        if ($validatedData['is_focal_person']) {
            User::where('department_id', $validatedData['department_id'])
                ->update(['is_focal_person' => false]);
        }

        User::create($validatedData);

        return redirect()->route('users.index')
                         ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $this->authorizeAccess($user);
        $user->load('department');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorizeAccess($user);
        $centers = Center::all();
        $roles = Role::all();
        $departments = Auth::user()->role->slug === 'super-admin'
            ? Department::all()
            : Department::where('center_id', Auth::user()->center_id)->get();

        return view('users.edit', compact('user', 'centers', 'roles', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAccess($user);
        $isOfficerRole = $this->requestedRoleIsOfficer($request->role_id ?? $user->role_id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'employee_id' => 'required|string|max:255|unique:users,employee_id,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'department_id' => $isOfficerRole
                ? 'required|integer|exists:departments,id'
                : 'nullable|integer|exists:departments,id',
            'is_focal_person' => 'nullable|boolean',
        ];

        if (Auth::user()->role->slug === 'super-admin') {
            $rules['center_id'] = 'required|integer|exists:centers,id';
            $rules['role_id'] = 'required|integer|exists:roles,id';
        }

        $validatedData = $request->validate($rules);

        if (Auth::user()->role->slug === 'center-admin') {
            $validatedData['center_id'] = Auth::user()->center_id;
            unset($validatedData['role_id']);
        }

        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            unset($validatedData['password']);
        }
        
        $isFocalPerson = $request->boolean('is_focal_person');
        $validatedData['is_focal_person'] = $isFocalPerson && $isOfficerRole;

        if ($validatedData['is_focal_person']) {
            User::where('department_id', $validatedData['department_id'])
                ->where('id', '!=', $user->id)
                ->update(['is_focal_person' => false]);
        }

        $user->update($validatedData);

        return redirect()->route('users.index')
                         ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorizeAccess($user);
        $timestamp = now()->timestamp;
        $user->update([
            'email' => "deleted_{$timestamp}_" . $user->email,
            'employee_id' => "deleted_{$timestamp}_" . $user->employee_id,
        ]);

        $user->delete();

        return redirect()->route('users.index')
                         ->with('success', 'User deleted successfully.');
    }

    private function requestedRoleIsOfficer($roleId): bool
    {
        if (!$roleId) {
            return false;
        }
        return Role::where('id', $roleId)->where('slug', 'officer')->exists();
    }

    private function authorizeAccess(User $user): void
    {
        $auth = Auth::user();
        if ($auth->role->slug === 'super-admin') {
            return;
        }
        // center-admin cannot access super-admin or other center-admin accounts,
        // and cannot access users outside their own center
        if ($user->center_id !== $auth->center_id) {
            abort(403, 'Unauthorized action.');
        }
        if (in_array($user->role->slug ?? '', ['super-admin', 'center-admin'])) {
            abort(403, 'Unauthorized action.');
        }
    }
}
