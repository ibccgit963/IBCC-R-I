@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028]">
            <h1 class="text-2xl font-bold text-white">Edit User</h1>
            <p class="text-sm text-white mt-1">Update the user's details and role.</p>
        </div>

        <div class="p-5 bg-white">

            <form method="POST" action="{{ route('users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Left Column -->
                    <div class="space-y-3">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('name', $user->name) }}" required autofocus>
                            @error('name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block font-medium text-sm text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Employee ID -->
                        <div>
                            <label for="employee_id" class="block font-medium text-sm text-gray-700">Employee ID</label>
                            <input type="text" name="employee_id" id="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('employee_id', $user->employee_id) }}" required>
                            @error('employee_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-3">
                        <!-- Center -->
                        <div>
                            <label for="center_id" class="block font-medium text-sm text-gray-700">Center</label>
                            @if (Auth::user()->role->slug === 'super-admin')
                                <select name="center_id" id="center_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="">Select Center</option>
                                    @foreach ($centers as $center)
                                        <option value="{{ $center->id }}" {{ old('center_id', $user->center_id) == $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                                    @endforeach
                                </select>
                            @else {{-- center-admin --}}
                                <input id="center_id" type="hidden" name="center_id" value="{{ Auth::user()->center_id }}">
                                <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ Auth::user()->center->name }}" disabled>
                            @endif
                            @error('center_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Role -->
                        <div>
                            <label for="role_id" class="block font-medium text-sm text-gray-700">Role</label>
                            @php
                                $forbiddenSlugs = Auth::user()->role->slug === 'super-admin' ? [] : ['super-admin', 'center-admin'];
                                $selectableRoles = $roles->filter(fn($r) => !in_array($r->slug, $forbiddenSlugs));
                            @endphp
                            <select id="role_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('role_id') border-red-500 @enderror" name="role_id" required>
                                <option value="">Select Role</option>
                                @foreach ($selectableRoles as $role)
                                    <option value="{{ $role->id }}" data-slug="{{ $role->slug }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Department -->
                        <div>
                            <label for="department_id" class="block font-medium text-sm text-gray-700">
                                {{ __('Department') }}
                                <span id="dept-required-badge" class="hidden text-red-500 text-xs font-bold ml-1">* Required for Officer</span>
                            </label>
                            <select id="department_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('department_id') border-red-500 @enderror"
                                name="department_id">
                                <option value="">Select Center First</option>
                            </select>
                            @error('department_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Is Focal Person -->
                        <div id="focal_person_container" class="hidden mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_focal_person" id="is_focal_person" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_focal_person', $user->is_focal_person) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Set as Department Focal Person</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1">If checked, this user will be the default recipient for incoming couriers assigned to their department. It will automatically unset any existing focal person for this department.</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6 pt-3 border-t border-gray-200 gap-3">
                    <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                        {{ __('Update User') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const centerEl       = document.getElementById('center_id');
    const roleSelect     = document.getElementById('role_id');
    const deptSelect     = document.getElementById('department_id');
    const deptBadge      = document.getElementById('dept-required-badge');
    const oldDeptId      = "{{ old('department_id', $user->department_id ?? '') }}";
    const departmentsUrl = @json(route('departments.by-center', ['centerId' => '__CENTER_ID__']));

    function fetchDepartments(centerId, preselect) {
        deptSelect.innerHTML = '<option value="">Loading...</option>';
        deptSelect.disabled = true;

        if (!centerId) {
            deptSelect.innerHTML = '<option value="">Select Center First</option>';
            deptSelect.disabled = false;
            return;
        }

        fetch(departmentsUrl.replace('__CENTER_ID__', encodeURIComponent(centerId)), {
            headers: { 'Accept': 'application/json' },
        })
            .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
            .then(data => {
                deptSelect.innerHTML = '<option value="">Select Department</option>';
                data.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = d.name;
                    if (d.id == (preselect || oldDeptId)) opt.selected = true;
                    deptSelect.appendChild(opt);
                });
                if (data.length === 0) {
                    deptSelect.innerHTML = '<option value="">No Departments Found</option>';
                }
                deptSelect.disabled = false;
            })
            .catch(() => {
                deptSelect.innerHTML = '<option value="">Error Loading Departments</option>';
                deptSelect.disabled = false;
            });
    }

    function updateDeptRequired() {
        const selectedOpt = roleSelect.options[roleSelect.selectedIndex];
        const isOfficer = selectedOpt && selectedOpt.dataset.slug === 'officer';
        deptSelect.required = isOfficer;
        deptBadge.classList.toggle('hidden', !isOfficer);
        
        const focalPersonContainer = document.getElementById('focal_person_container');
        if (focalPersonContainer) {
            focalPersonContainer.classList.toggle('hidden', !isOfficer);
        }
    }

    // Center change — reload departments
    if (centerEl.tagName === 'SELECT') {
        centerEl.addEventListener('change', function () {
            fetchDepartments(this.value);
        });
        if (centerEl.value) {
            fetchDepartments(centerEl.value);
        } else {
            deptSelect.innerHTML = '<option value="">Select Center First</option>';
            deptSelect.disabled = true;
        }
    } else {
        // center-admin: center is fixed, load departments immediately
        fetchDepartments(centerEl.value);
    }

    // Role change — toggle department required
    roleSelect.addEventListener('change', updateDeptRequired);
    updateDeptRequired(); // run on load in case of old() repopulation
});
</script>
@endpush
