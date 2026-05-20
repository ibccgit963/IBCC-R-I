@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028]">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">Incoming Couriers</h1>
                    <p class="text-sm text-white mt-1">Manage all incoming couriers and their details.</p>
                </div>
            </div>
            </div>

            <!-- Create Courier Form Section -->
            <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                <div class="mb-4 border-b border-gray-200 pb-2">
                    <h2 class="text-lg font-bold text-gray-800">Register New Incoming Courier</h2>
                    <p class="text-sm text-gray-500">Enter the details of the incoming courier package.</p>
                </div>

                <form method="POST" action="{{ route('couriers.store') }}">
                    @csrf

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
                            <!-- Sender CNIC -->
                            <div>
                                <label for="sender_cnic" class="block font-medium text-sm text-gray-700">{{ __('Sender CNIC (Optional)') }}</label>
                                <input id="sender_cnic" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('sender_cnic') border-red-500 @enderror" name="sender_cnic" value="{{ old('sender_cnic') }}" placeholder="Enter CNIC (without dashes)">
                                @error('sender_cnic')
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
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-200">
                        <button type="submit" formnovalidate class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                            {{ __('Register Courier') }}
                        </button>
                    </div>
                </form>
            </div>

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

            <div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#007bff] text-white">
                        <tr>
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
                                Category
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Center
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Received By
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
                                    {{ $courier->courier_category }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $courier->center->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $courier->receivedBy->name }}
                                </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        @if($courier->status === 'transferred' && $courier->department)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Forwarded to {{ $courier->department->name }}
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $courier->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($courier->status === 'received' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($courier->status) }}
                                            </span>
                                        @endif
                                    </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                    @if(Auth::user()->role->slug === 'officer' && $courier->status === 'transferred')
                                        <form action="{{ route('couriers.transfer', $courier->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <!-- Assuming reusing transfer endpoint or creating a new one for mark received. 
                                                 For now, I'll use a placeholder route or logic if exact route isn't defined yet.
                                                 Wait, the plan mentioned updateStatus logic. 
                                                 Let's check routes. marking received usually happens via transfer controller 'markReceived' 
                                                 but that takes a courierTransfer object. 
                                                 Here we are interacting with the courier directly. 
                                                 
                                                 I'll add a specific route/method for this or reuse transfer form if applicable. 
                                                 Actually, the user asked for "Mark as Received". 
                                                 I will assume there's a need for a new route or I can use an existing compatible one.
                                                 
                                                 Let's look at web.php again. 
                                                 Route::post('courier-transfers/{courierTransfer}/mark-received', ...);
                                                 This expects a CourierTransfer.
                                                 
                                                 Since we are auto-forwarding, we might not have a CourierTransfer record yet, OR we should have created one.
                                                 AH! The `store` method just updates status to 'transferred' and sets `department_id`.
                                                 It does NOT create a `CourierTransfer` record.
                                                 So I need a new route/method to mark the COURIER as received directly by the department user.
                                            -->
                                            <!-- I will implement a new route in the next step. For now, I'll put a placeholder form/button. -->
                                            <button type="submit" form="mark-received-{{ $courier->id }}" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700 transition-colors shadow-sm">
                                                <i class="fa-solid fa-check mr-1"></i> Mark Received
                                            </button>
                                        </form>
                                        <form id="mark-received-{{ $courier->id }}" action="{{ route('couriers.markReceived', $courier->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('PATCH')
                                        </form>
                                    @endif

                                    @if(Auth::user()->role->slug !== 'officer')
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
                                <td colspan="9" class="px-6 py-10 text-center text-gray-500">
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
            
             <div class="mt-4">
                 {{-- Pagination links if needed --}}
                 {{-- {{ $couriers->links() }} --}}
            </div>
        </div>
    </div>
</div>
@endsection