@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-5 bg-white border-b border-gray-200">
                <div class="mb-2 border-b border-gray-200 pb-2">
                    <h1 class="text-2xl font-bold text-gray-800">Register New Incoming Courier</h1>
                    <p class="text-sm text-gray-500 mt-1">Enter the details of the incoming courier package.</p>
                </div>

                <form method="POST" action="{{ route('couriers.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="type" class="block font-medium text-sm text-gray-700">{{ __('Courier Type') }}</label>
                        <select id="type" name="type" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('type') border-red-500 @enderror">
                            <option value="">Select Type</option>
                            <option value="applicant"  {{ old('type') === 'applicant'  ? 'selected' : '' }}>Applicant</option>
                            <option value="internal"   {{ old('type') === 'internal'   ? 'selected' : '' }}>Internal</option>
                            <option value="sub_office" {{ old('type') === 'sub_office' ? 'selected' : '' }}>Sub-office</option>
                            <option value="ministry"   {{ old('type') === 'ministry'   ? 'selected' : '' }}>Ministry / Department</option>
                        </select>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Left Column -->
                        <div class="space-y-3">
                            <!-- Tracking ID -->
                            <div>
                                <label for="tracking_id"
                                    class="block font-medium text-sm text-gray-700">{{ __('Tracking ID') }}</label>
                                <input id="tracking_id" type="text"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('tracking_id') border-red-500 @enderror"
                                    name="tracking_id" value="{{ old('tracking_id') }}" required autofocus>
                                @error('tracking_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Courier Company -->
                            <div>
                                <label for="courier_company"
                                    class="block font-medium text-sm text-gray-700">{{ __('Courier Company') }}</label>
                                <input id="courier_company" type="text"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('courier_company') border-red-500 @enderror"
                                    name="courier_company" value="{{ old('courier_company') }}"
                                    list="courierCompanySuggestions" required>
                                <datalist id="courierCompanySuggestions"></datalist>
                                @error('courier_company')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sender Name -->
                            <div>
                                <label for="sender_name"
                                    class="block font-medium text-sm text-gray-700">{{ __('Sender Name') }}</label>
                                <input id="sender_name" type="text"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('sender_name') border-red-500 @enderror"
                                    name="sender_name" value="{{ old('sender_name') }}" required>
                                @error('sender_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-3">
                            <!-- Sender CNIC (Optional) -->
                            <div>
                                <label for="sender_cnic"
                                    class="block font-medium text-sm text-gray-700">{{ __('Sender CNIC (Optional)') }}</label>
                                <input id="sender_cnic" type="text"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('sender_cnic') border-red-500 @enderror"
                                    name="sender_cnic" value="{{ old('sender_cnic') }}">
                                {{-- @error('sender_cnic')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror --}}
                            </div>

                            <!-- Sender Contact Number (Optional) -->
                            <div>
                                <label for="sender_contact"
                                    class="block font-medium text-sm text-gray-700">{{ __('Sender Contact Number (Optional)') }}</label>
                                <input id="sender_contact" type="text"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('sender_contact') border-red-500 @enderror"
                                    name="sender_contact" value="{{ old('sender_contact') }}">
                                {{-- @error('sender_contact')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror --}}
                            </div>

                            <!-- Courier Category -->
                            <!-- Department Selection -->
                            <div>
                                <label for="department_id"
                                    class="block font-medium text-sm text-gray-700">{{ __('Forward To (Department)') }}</label>
                                <select id="department_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('department_id') border-red-500 @enderror"
                                    name="department_id" required>
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Assigned Officer Selection -->
                            <div>
                                <label for="assigned_user_id"
                                    class="block font-medium text-sm text-gray-700">
                                    {{ __('Assign to Officer (Optional)') }}
                                    <span id="focal-person-badge" class="hidden ml-1 text-xs text-indigo-600 font-bold"></span>
                                </label>
                                <select id="assigned_user_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('assigned_user_id') border-red-500 @enderror"
                                    name="assigned_user_id" disabled>
                                    <option value="">Select Department First</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Leave empty to assign to the department's Focal Person.</p>
                                @error('assigned_user_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4 pt-3 border-t border-gray-200 space-x-4">
                        <a href="{{ route('couriers.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
                            Cancel
                        </a>
                        <button type="submit" formnovalidate
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                            {{ __('Register Courier') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const courierCompanyInput = document.getElementById('courier_company');
                const courierCompanyDatalist = document.getElementById('courierCompanySuggestions');

                let debounceTimer;

                courierCompanyInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function() {
                        const searchTerm = courierCompanyInput.value;

                        if (searchTerm.length > 1) { // Start searching after 2 characters
                            fetch(`/api/courier-companies/suggestions?search=${searchTerm}`)
                                .then(response => response.json())
                                .then(data => {
                                    courierCompanyDatalist.innerHTML =
                                    ''; // Clear previous suggestions
                                    data.forEach(company => {
                                        const option = document.createElement('option');
                                        option.value = company.name;
                                        courierCompanyDatalist.appendChild(option);
                                    });
                                })
                                .catch(error => console.error(
                                    'Error fetching courier company suggestions:', error));
                        } else {
                            courierCompanyDatalist.innerHTML =
                            ''; // Clear suggestions if search term is too short
                        }
                    }, 300); // Debounce time in milliseconds
                });

                // Department select change handler for loading officers
                const departmentSelect = document.getElementById('department_id');
                const assignedUserSelect = document.getElementById('assigned_user_id');
                const focalPersonBadge = document.getElementById('focal-person-badge');

                departmentSelect.addEventListener('change', function() {
                    const departmentId = this.value;
                    assignedUserSelect.innerHTML = '<option value="">Loading...</option>';
                    assignedUserSelect.disabled = true;
                    focalPersonBadge.classList.add('hidden');
                    focalPersonBadge.textContent = '';

                    if (!departmentId) {
                        assignedUserSelect.innerHTML = '<option value="">Select Department First</option>';
                        return;
                    }

                    fetch(`/api/users-by-department/${departmentId}`)
                        .then(response => response.json())
                        .then(data => {
                            assignedUserSelect.innerHTML = '<option value="">— Leave empty for Focal Person —</option>';
                            let focalPersonName = null;
                            data.forEach(user => {
                                const option = document.createElement('option');
                                option.value = user.id;
                                let nameText = user.name;
                                if (user.is_focal_person) {
                                    nameText += ' (Focal Person)';
                                    focalPersonName = user.name;
                                }
                                option.textContent = nameText;
                                // Automatically select if we have an old value
                                if ("{{ old('assigned_user_id') }}" == user.id) {
                                    option.selected = true;
                                }
                                assignedUserSelect.appendChild(option);
                            });

                            if (focalPersonName) {
                                focalPersonBadge.textContent = `(Default: ${focalPersonName})`;
                                focalPersonBadge.classList.remove('hidden');
                            }

                            if (data.length === 0) {
                                assignedUserSelect.innerHTML = '<option value="">No Officers Found</option>';
                            }
                            assignedUserSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching officers:', error);
                            assignedUserSelect.innerHTML = '<option value="">Error Loading Officers</option>';
                        });
                });

                // Trigger change event if department is already selected (e.g. old input)
                if (departmentSelect.value) {
                    departmentSelect.dispatchEvent(new Event('change'));
                }
            });
        </script>
    @endpush
@endsection
