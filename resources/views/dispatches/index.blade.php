@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028]">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">Applicant Dispatch</h1>
                    <p class="text-sm text-white mt-1">Manage all applicant dispatches and their details.</p>
                </div>
            </div>
        </div>

        <!-- Create Dispatch Form Section -->
        <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <div class="mb-4 border-b border-gray-200 pb-2">
                <h2 class="text-lg font-bold text-gray-800">Create New Dispatch</h2>
                <p class="text-sm text-gray-500">Enter the details to create a new outgoing dispatch.</p>
            </div>

            <form method="POST" action="{{ route('dispatches.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <!-- Applicant Name -->
                        <div>
                            <label for="applicant_name" class="block font-medium text-sm text-gray-700">{{ __('Applicant Name') }}</label>
                            <input id="applicant_name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('applicant_name') border-red-500 @enderror" name="applicant_name" value="{{ old('applicant_name') }}" required placeholder="Enter Applicant Name">
                            @error('applicant_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Father Name -->
                        <div>
                            <label for="father_name" class="block font-medium text-sm text-gray-700">{{ __('Father Name') }}</label>
                            <input id="father_name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('father_name') border-red-500 @enderror" name="father_name" value="{{ old('father_name') }}" required placeholder="Enter Father Name">
                            @error('father_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Applicant Contact Number -->
                        <div>
                            <label for="applicant_contact" class="block font-medium text-sm text-gray-700">{{ __('Applicant Contact Number') }}</label>
                            <input id="applicant_contact" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('applicant_contact') border-red-500 @enderror" name="applicant_contact" value="{{ old('applicant_contact') }}" required placeholder="Enter Contact Number">
                            @error('applicant_contact')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <!-- Case Number -->
                        <div>
                            <label for="case_number" class="block font-medium text-sm text-gray-700">{{ __('Case Number') }}</label>
                            <input id="case_number" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('case_number') border-red-500 @enderror" name="case_number" value="{{ old('case_number') }}" required placeholder="Enter Case Number">
                            @error('case_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dispatch Courier Company -->
                        <div>
                            <label for="dispatch_courier_company" class="block font-medium text-sm text-gray-700">{{ __('Dispatch Courier Company') }}</label>
                            <input id="dispatch_courier_company" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-100 text-gray-500 cursor-not-allowed" name="dispatch_courier_company" value="TCS" readonly>
                            @error('dispatch_courier_company')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dispatched From (Officer/Department) -->
                        <div>
                            <label for="dispatched_from" class="block font-medium text-sm text-gray-700">{{ __('Dispatched From (Officer/Department)') }}</label>
                            <input id="dispatched_from" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('dispatched_from') border-red-500 @enderror" name="dispatched_from" value="{{ old('dispatched_from') }}" required placeholder="Enter Officer or Department Name">
                            @error('dispatched_from')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-200">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                        {{ __('Create Dispatch') }}
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
                                Applicant Name
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Case Number
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Company
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Tracking ID
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Dispatched From
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Center
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                Dispatched By
                            </th>
                            <th scope="col" class="px-6 py-4 text-right pr-20 text-xs font-bold uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($dispatches as $dispatch)
                            <tr class="bg-gray-50 hover:bg-gray-100 transition-colors duration-200 border-b border-gray-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $dispatch->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ $dispatch->applicant_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $dispatch->case_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $dispatch->dispatch_courier_company }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $dispatch->tracking_id ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $dispatch->dispatched_from }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $dispatch->center->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $dispatch->dispatchedBy->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                    <a href="{{ route('dispatches.show', $dispatch->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('dispatches.edit', $dispatch->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-amber-100 text-amber-600 hover:bg-amber-500 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" title="Edit">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    @if($dispatch->status !== 'dispatched')
                                        <button type="button" 
                                                @click="$dispatch('open-dispatch-modal', { id: {{ $dispatch->id }}, url: '{{ route('dispatches.markDispatched', $dispatch->id) }}' })" 
                                                class="inline-flex items-center px-3 py-1 bg-orange-600 text-white text-xs font-bold rounded hover:bg-orange-700 transition-colors shadow-sm">
                                            <i class="fa-solid fa-paper-plane mr-1"></i> Mark Dispatched
                                        </button>
                                    @endif
                                    <form action="{{ route('dispatches.destroy', $dispatch->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" onclick="return confirm('Are you sure you want to delete this dispatch?')" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-paper-plane text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-lg font-medium">No outgoing dispatches found.</p>
                                        <p class="text-sm text-gray-400">Get started by creating a new dispatch.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
             <div class="mt-4">
                 {{-- Pagination links if needed --}}
                 {{-- {{ $dispatches->links() }} --}}
            </div>
        </div>
    </div>
</div>
@endsection