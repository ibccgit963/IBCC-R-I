@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028]">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">Sub-office Receive</h1>
                    <p class="text-sm text-white mt-1">Manage all sub-office incoming couriers and their details.</p>
                </div>
            </div>
            </div>

            <!-- Create Courier Form Section -->
            @if(Auth::user()->role->slug !== 'officer')
            <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                <div class="mb-4 border-b border-gray-200 pb-2">
                    <h2 class="text-lg font-bold text-gray-800">Register New Sub-office Receive</h2>
                    <p class="text-sm text-gray-500">Enter the details of the sub-office incoming courier package.</p>
                </div>

                <form method="POST" action="{{ route('couriers.store') }}">
                    @csrf
                    <input type="hidden" name="type" value="sub_office">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- Tracking ID -->
                            <div>
                                <label for="tracking_id" class="block font-medium text-sm text-gray-700">{{ __('Tracking ID') }}</label>
                                <input id="tracking_id" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('tracking_id') border-red-500 @enderror" name="tracking_id" value="{{ old('tracking_id') }}" required placeholder="Enter Tracking ID">
                                @error('tracking_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Courier Company -->
                            <div>
                                <label for="courier_company" class="block font-medium text-sm text-gray-700">{{ __('Courier Company') }}</label>
                                <input id="courier_company" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('courier_company') border-red-500 @enderror" name="courier_company" value="{{ old('courier_company') }}" required placeholder="e.g. TCS, DHL, Leopard">
                                @error('courier_company')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sender Name -->
                            <div>
                                <label for="sender_name" class="block font-medium text-sm text-gray-700">{{ __('Sender Name') }}</label>
                                <input id="sender_name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('sender_name') border-red-500 @enderror" name="sender_name" value="{{ old('sender_name') }}" required placeholder="Enter Sender Name">
                                @error('sender_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- Branch (Dropdown) -->
                            <div>
                                <label for="branch" class="block font-medium text-sm text-gray-700">{{ __('Branch') }}</label>
                                <select id="branch" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('branch') border-red-500 @enderror" name="branch">
                                    <option value="">Select Branch</option>
                                    @foreach ($centers as $center)
                                        <option value="{{ $center->name }}" {{ old('branch') == $center->name ? 'selected' : '' }}>{{ $center->name }}</option>
                                    @endforeach
                                </select>
                                @error('branch')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sender Contact Number -->
                            <div>
                                <label for="sender_contact" class="block font-medium text-sm text-gray-700">{{ __('Sender Contact Number (Optional)') }}</label>
                                <input id="sender_contact" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('sender_contact') border-red-500 @enderror" name="sender_contact" value="{{ old('sender_contact') }}" placeholder="Enter Contact Number">
                                @error('sender_contact')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Department Selection -->
                            <div>
                                <label for="department_id" class="block font-medium text-sm text-gray-700">{{ __('Forward To (Department)') }}</label>
                                <select id="department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('department_id') border-red-500 @enderror" name="department_id" required>
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
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
                                <div class="relative mt-1">
                                    <select id="assigned_user_id"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('assigned_user_id') border-red-500 @enderror"
                                        name="assigned_user_id" disabled>
                                        <option value="">Select Department First</option>
                                    </select>
                                    <span id="officer-loading-spinner" class="hidden absolute right-8 top-2 pointer-events-none">
                                        <svg class="animate-spin h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Leave empty to assign to the department's Focal Person.</p>
                                @error('assigned_user_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-200">
                        <button type="submit" formnovalidate class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                            {{ __('Register Courier') }}
                        </button>
                    </div>
                </form>
            </div>
            @endif

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded-r shadow-sm" role="alert">
                    <div class="flex">
                        <div class="py-1"><i class="fa-solid fa-circle-check mr-2"></i></div>
                        <div>
                            <p class="font-bold">Success</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(in_array(Auth::user()->role->slug, ['staff-user', 'center-admin', 'super-admin']))
            <div x-data="{ selected: [], selectAll: false }" class="mb-2">
                <!-- Bulk Action Bar -->
                <div x-show="selected.length > 0" x-cloak class="flex items-center gap-3 bg-indigo-50 border border-indigo-200 rounded-lg px-4 py-2 mb-2">
                    <span class="text-sm font-medium text-indigo-800"><span x-text="selected.length"></span> courier(s) selected</span>
                    <form action="{{ route('couriers.bulkAction') }}" method="POST">
                        @csrf
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="courier_ids[]" :value="id">
                        </template>
                        <input type="hidden" name="action" value="mark_received">
                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700 transition-colors">
                            <i class="fa-solid fa-inbox mr-1"></i> Mark Received
                        </button>
                    </form>
                    @if(in_array(Auth::user()->role->slug, ['center-admin', 'super-admin']))
                    <form action="{{ route('couriers.bulkAction') }}" method="POST" onsubmit="return confirm('Delete selected couriers?')">
                        @csrf
                        <template x-for="id in selected" :key="id">
                            <input type="hidden" name="courier_ids[]" :value="id">
                        </template>
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-bold rounded hover:bg-red-700 transition-colors">
                            <i class="fa-solid fa-trash mr-1"></i> Delete
                        </button>
                    </form>
                    @endif
                    <button type="button" @click="selected = []; selectAll = false" class="text-xs text-indigo-600 hover:underline ml-auto">Clear selection</button>
                </div>
            @endif

            <div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#007bff] text-white">
                        <tr>
                            @if(in_array(Auth::user()->role->slug, ['staff-user', 'center-admin', 'super-admin']))
                            <th scope="col" class="px-4 py-4 text-left text-xs font-bold uppercase">
                                <input type="checkbox" x-model="selectAll"
                                    @change="selected = selectAll ? @json($couriers->pluck('id')) : []"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            @endif
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Tracking ID
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Company
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Sender Name
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Branch
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Center
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Registered By
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Assigned Officer
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-4 text-right pr-20 text-xs font-bold uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($couriers as $courier)
                            <tr class="bg-gray-50 hover:bg-gray-100 transition-colors duration-200 border-b border-gray-200">
                                @if(in_array(Auth::user()->role->slug, ['staff-user', 'center-admin', 'super-admin']))
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input type="checkbox" :value="{{ $courier->id }}" x-model="selected"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $courier->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ $courier->tracking_id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $courier->courier_company }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $courier->sender_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $courier->branch ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $courier->center->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $courier->receivedBy->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    @if($courier->assignedUser)
                                        <span class="font-medium text-gray-800">{{ $courier->assignedUser->name }}</span>
                                    @else
                                        <span class="text-gray-400 italic">Unassigned</span>
                                    @endif
                                </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        @if($courier->status === 'transferred' && $courier->department)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Forwarded to {{ $courier->department->name }}
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $courier->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                   ($courier->status === 'received' ? 'bg-green-100 text-green-800' :
                                                   ($courier->status === 'reverted' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800')) }}">
                                                @if($courier->status === 'reverted')
                                                    <i class="fa-solid fa-rotate-left mr-1"></i> Reverted
                                                @else
                                                    {{ ucfirst($courier->status) }}
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    @if(Auth::user()->role->slug === 'officer')
                                        <a href="{{ route('couriers.show', $courier->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-200 shadow-sm" title="View">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        @if($courier->status === 'transferred')
                                            @if($courier->transfers->isNotEmpty())
                                                <form action="{{ route('courier-transfers.markReceived', $courier->transfers->last()->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-bold rounded hover:bg-green-700 transition-colors shadow-sm">
                                                        <i class="fa-solid fa-check mr-1"></i> Mark Received
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('couriers.markReceivedDirect', $courier->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-bold rounded hover:bg-green-700 transition-colors shadow-sm">
                                                        <i class="fa-solid fa-check mr-1"></i> Mark Received
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                        @if(in_array($courier->status, ['transferred','received']))
                                            <a href="{{ route('couriers.transferForm', $courier->id) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700 transition-colors shadow-sm">
                                                <i class="fa-solid fa-share mr-1"></i> Transfer
                                            </a>
                                        @endif
                                        <!-- Revert: only if no one has received this courier before -->
                                        @if($courier->status === 'transferred' && $courier->transfers->whereNotNull('received_at')->isEmpty())
                                            <button type="button" @click="$dispatch('open-revert-modal', { id: {{ $courier->id }}, trackingId: '{{ $courier->tracking_id }}' })" class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-bold rounded hover:bg-red-700 transition-colors shadow-sm ml-1" title="Revert to R&I">
                                                <i class="fa-solid fa-rotate-left mr-1"></i> Revert
                                            </button>
                                        @endif
                                    @elseif(Auth::user()->role->slug === 'staff-user')
                                        <a href="{{ route('couriers.show', $courier->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-200 shadow-sm" title="View">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        @if(!is_null($courier->reverted_by_user_id))
                                            <a href="{{ route('couriers.transferForm', $courier->id) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-xs font-bold rounded hover:bg-indigo-700 transition-colors shadow-sm">
                                                <i class="fa-solid fa-share mr-1"></i> Re-assign
                                            </a>
                                        @elseif($courier->status === 'transferred' && $courier->department_id === null)
                                            @php $latestTransferRI = $courier->transfers->sortByDesc('created_at')->first(); @endphp
                                            @if($latestTransferRI && $latestTransferRI->is_for_dispatch)
                                                <span class="inline-flex items-center px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-semibold rounded"><i class="fa-solid fa-paper-plane mr-1"></i> For dispatch</span>
                                            @endif
                                            <form action="{{ route('couriers.markReceivedDirect', $courier->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-bold rounded hover:bg-green-700 transition-colors shadow-sm">
                                                    <i class="fa-solid fa-inbox mr-1"></i> Confirm Receipt
                                                </button>
                                            </form>
                                        @elseif($courier->status === 'received' && $courier->department_id === null)
                                            <a href="{{ route('couriers.transferForm', $courier->id) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-xs font-bold rounded hover:bg-indigo-700 transition-colors shadow-sm">
                                                <i class="fa-solid fa-share mr-1"></i> Transfer
                                            </a>
                                            <form action="{{ route('couriers.markDispatched', $courier->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-orange-600 text-white text-xs font-bold rounded hover:bg-orange-700 transition-colors shadow-sm">
                                                    <i class="fa-solid fa-paper-plane mr-1"></i> Mark Dispatched
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                    <a href="{{ route('couriers.show', $courier->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('couriers.edit', $courier->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-amber-100 text-amber-600 hover:bg-amber-500 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" title="Edit">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <form action="{{ route('couriers.destroy', $courier->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" onclick="return confirm('Are you sure you want to delete this courier?')" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ in_array(Auth::user()->role->slug, ['staff-user', 'center-admin', 'super-admin']) ? 11 : 10 }}" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-box-open text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-lg font-medium">No incoming couriers found.</p>
                                        <p class="text-sm text-gray-400">Get started by registering a new courier.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($couriers->hasPages())
            <div class="mt-4 px-4 pb-4">
                {{ $couriers->withQueryString()->links() }}
            </div>
            @endif

            @if(in_array(Auth::user()->role->slug, ['staff-user', 'center-admin', 'super-admin']))
            </div>{{-- close x-data="{ selected: [], selectAll: false }" --}}
            @endif
        </div>
    </div>
</div>

<!-- Revert Modal -->
<div x-data="{ open: false, courierId: null, trackingId: '', comments: '' }"
     @open-revert-modal.window="open = true; courierId = $event.detail.id; trackingId = $event.detail.trackingId; comments = ''; setTimeout(() => $refs.commentsInput.focus(), 100)"
     class="relative z-50"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true"
     x-cloak
     x-show="open">
     
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
         x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                 @click.outside="open = false"
                 x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <form :action="'{{ url('couriers') }}/' + courierId + '/revert'" method="POST">
                    @csrf
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-rotate-left text-red-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Revert Courier to R&I</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-3">You are about to revert Courier <span class="font-mono font-bold text-gray-700" x-text="trackingId"></span> back to R&I staff. Please provide a reason.</p>
                                    
                                    <label for="comments" class="block text-sm font-medium text-gray-700 mb-1">Reason for reverting:</label>
                                    <textarea x-ref="commentsInput" id="comments" name="comments" x-model="comments" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required placeholder="e.g. This courier does not belong to our department."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">Confirm Revert</button>
                        <button type="button" @click="open = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const departmentSelect = document.getElementById('department_id');
        if (departmentSelect) {
            const assignedUserSelect = document.getElementById('assigned_user_id');
            const focalPersonBadge = document.getElementById('focal-person-badge');
            const oldAssignedUserId = "{{ old('assigned_user_id') }}";

            const spinner = document.getElementById('officer-loading-spinner');

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

                if (spinner) spinner.classList.remove('hidden');

                fetch(`/api/users-by-department/${departmentId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (spinner) spinner.classList.add('hidden');
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
                            if (oldAssignedUserId == user.id) {
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
                        if (spinner) spinner.classList.add('hidden');
                        console.error('Error fetching officers:', error);
                        assignedUserSelect.innerHTML = '<option value="">Error Loading Officers</option>';
                    });
            });

            if (departmentSelect.value) {
                departmentSelect.dispatchEvent(new Event('change'));
            }
        }
    });
</script>
@endpush

@endsection