@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-5 bg-white border-b border-gray-200">
            <div class="mb-2 border-b border-gray-200 pb-2">
                <h1 class="text-2xl font-bold text-gray-800">Edit Officer: {{ $officer->name }}</h1>
                <p class="text-sm text-gray-500 mt-1">Modify the details for this officer.</p>
            </div>

            <form method="POST" action="{{ route('officers.update', $officer->id) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">{{ __('Name') }}</label>
                            <input id="name" type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-500 @enderror"
                                name="name" value="{{ old('name', $officer->name) }}" required autofocus placeholder="Enter officer's name">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Designation -->
                        <div>
                            <label for="designation" class="block font-medium text-sm text-gray-700">{{ __('Designation (Optional)') }}</label>
                            <input id="designation" type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('designation') border-red-500 @enderror"
                                name="designation" value="{{ old('designation', $officer->designation) }}" placeholder="e.g. Manager, Clerk">
                            @error('designation')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact -->
                        <div>
                            <label for="contact" class="block font-medium text-sm text-gray-700">{{ __('Contact Number (Optional)') }}</label>
                            <input id="contact" type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('contact') border-red-500 @enderror"
                                name="contact" value="{{ old('contact', $officer->contact) }}" placeholder="Enter contact number">
                            @error('contact')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <!-- Center -->
                        <div>
                            <label for="center_id" class="block font-medium text-sm text-gray-700">{{ __('Center') }}</label>
                            <select id="center_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('center_id') border-red-500 @enderror"
                                name="center_id" required>
                                <option value="">Select Center</option>
                                @foreach ($centers as $center)
                                    <option value="{{ $center->id }}"
                                        {{ old('center_id', $officer->center_id) == $center->id ? 'selected' : '' }}>
                                        {{ $center->name }}</option>
                                @endforeach
                            </select>
                            @error('center_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Department -->
                        <div>
                            <label for="department_id" class="block font-medium text-sm text-gray-700">{{ __('Department') }}</label>
                            <select id="department_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('department_id') border-red-500 @enderror"
                                name="department_id" required>
                                <option value="">Select Department</option>
                                {{-- Options will be loaded dynamically --}}
                            </select>
                            @error('department_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Start of new section for User Account Details -->
                <div class="mt-8 mb-4 border-b border-gray-200 pb-2">
                    <h2 class="text-xl font-bold text-gray-800">User Account Details</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage the associated user account for this officer.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Left Column (User fields) -->
                    <div class="space-y-4">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block font-medium text-sm text-gray-700">{{ __('Email') }}</label>
                            <input id="email" type="email"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('email') border-red-500 @enderror"
                                name="email" value="{{ old('email', $officer->user->email ?? '') }}" required placeholder="Enter user's email">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block font-medium text-sm text-gray-700">{{ __('New Password (Optional)') }}</label>
                            <input id="password" type="password"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('password') border-red-500 @enderror"
                                name="password" autocomplete="new-password" placeholder="Leave blank to keep current password">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block font-medium text-sm text-gray-700">{{ __('Confirm New Password') }}</label>
                            <input id="password_confirmation" type="password"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                name="password_confirmation" autocomplete="new-password" placeholder="Confirm new password">
                        </div>
                    </div>

                    <!-- Right Column (User fields) -->
                    <div class="space-y-4">
                        <!-- Employee ID (Optional) -->
                        <div>
                            <label for="employee_id" class="block font-medium text-sm text-gray-700">{{ __('Employee ID (Optional)') }}</label>
                            <input id="employee_id" type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('employee_id') border-red-500 @enderror"
                                name="employee_id" value="{{ old('employee_id', $officer->user->employee_id ?? '') }}" placeholder="Enter employee ID">
                            @error('employee_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role (Hidden, enforced to Officer) -->
                        <div>
                            <input type="hidden" name="role_id" value="{{ $officerRoleId }}">
                            <p class="block font-medium text-sm text-gray-700">{{ __('Role: Officer') }}</p>
                            <p class="text-sm text-gray-500">Automatically assigned to Officer role.</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4 pt-3 border-t border-gray-200 space-x-4">
                    <a href="{{ route('officers.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                        {{ __('Update Officer') }}
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
        console.log('Officer Edit Script loaded and DOM content loaded.'); // Added log
        const centerSelect = document.getElementById('center_id');
        const departmentSelect = document.getElementById('department_id');
        const oldDepartmentId = "{{ old('department_id', $officer->department_id) }}";
        const initialCenterId = "{{ old('center_id', $officer->center_id) }}";

        function fetchDepartments(centerId, selectedDepartmentId = null) {
            console.log('Fetching departments for centerId:', centerId); // Added log
            departmentSelect.innerHTML = '<option value="">Loading Departments...</option>';
            departmentSelect.disabled = true;

            if (!centerId) {
                departmentSelect.innerHTML = '<option value="">Select Department</option>';
                departmentSelect.disabled = false;
                return;
            }

            fetch(`/api/departments-by-center/${centerId}`)
                .then(response => {
                    console.log('Fetch response received.'); // Added log
                    return response.json();
                })
                .then(data => {
                    console.log('Departments data:', data); // Added log
                    departmentSelect.innerHTML = '<option value="">Select Department</option>';
                    if (data.length > 0) {
                        data.forEach(department => {
                            const option = document.createElement('option');
                            option.value = department.id;
                            option.textContent = department.name;
                            if (department.id == selectedDepartmentId) {
                                option.selected = true;
                            }
                            departmentSelect.appendChild(option);
                        });
                    } else {
                        departmentSelect.innerHTML = '<option value="">No Departments Found</option>';
                    }
                    departmentSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching departments:', error);
                    departmentSelect.innerHTML = '<option value="">Error Loading Departments</option>';
                    departmentSelect.disabled = false;
                });
        }

        // Event listener for center selection change
        if (centerSelect) { // Added check
            centerSelect.addEventListener('change', function () {
                console.log('Center changed:', this.value); // Added log
                fetchDepartments(this.value);
            });
        } else {
            console.error('center_id element not found.');
        }

        // Initial fetch on page load with the officer's current center or old input
        if (centerSelect && initialCenterId) { // Added check
            console.log('Initial fetch for center:', initialCenterId); // Added log
            fetchDepartments(initialCenterId, oldDepartmentId);
        } else if (centerSelect) { // Only if centerSelect exists
            departmentSelect.innerHTML = '<option value="">Select Center First</option>';
            departmentSelect.disabled = true;
        }
    });
</script>
@endpush
