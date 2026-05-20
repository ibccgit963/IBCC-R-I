@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028]">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">Applicant Dispatch</h1>
                    <p class="text-sm text-white mt-1">
                        @if(Auth::user()->role->slug === 'officer')
                            Submit dispatch requests for applicants to R&amp;I for processing.
                        @else
                            Manage all applicant dispatches and their details.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <div class="mb-4 border-b border-gray-200 pb-2">
                @if(Auth::user()->role->slug === 'officer')
                    <h2 class="text-lg font-bold text-gray-800">Submit Dispatch Request</h2>
                    <p class="text-sm text-gray-500">Fill in the applicant details and submit to R&amp;I for dispatch.</p>
                @else
                    <h2 class="text-lg font-bold text-gray-800">Create New Dispatch</h2>
                    <p class="text-sm text-gray-500">Enter details to register and dispatch directly.</p>
                @endif
            </div>

            <form method="POST" action="{{ route('dispatches.store') }}">
                @csrf
                <input type="hidden" name="type" value="applicant">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div>
                            <label for="applicant_name" class="block font-medium text-sm text-gray-700">Applicant Name</label>
                            <input id="applicant_name" type="text" name="applicant_name" value="{{ old('applicant_name') }}" required placeholder="Enter Applicant Name"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('applicant_name') border-red-500 @enderror">
                            @error('applicant_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="father_name" class="block font-medium text-sm text-gray-700">Father Name</label>
                            <input id="father_name" type="text" name="father_name" value="{{ old('father_name') }}" placeholder="Enter Father Name"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('father_name') border-red-500 @enderror">
                            @error('father_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="applicant_contact" class="block font-medium text-sm text-gray-700">Contact Number</label>
                            <input id="applicant_contact" type="text" name="applicant_contact" value="{{ old('applicant_contact') }}" placeholder="Enter Contact Number"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('applicant_contact') border-red-500 @enderror">
                            @error('applicant_contact')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label for="case_number" class="block font-medium text-sm text-gray-700">Case Number</label>
                            <input id="case_number" type="text" name="case_number" value="{{ old('case_number') }}" required placeholder="Enter Case Number"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('case_number') border-red-500 @enderror">
                            @error('case_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="dispatch_courier_company" class="block font-medium text-sm text-gray-700">Courier Company</label>
                            <input id="dispatch_courier_company" type="text" name="dispatch_courier_company" value="{{ old('dispatch_courier_company', $defaultCompany) }}" placeholder="e.g. TCS, DHL"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('dispatch_courier_company') border-red-500 @enderror">
                            @error('dispatch_courier_company')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="dispatched_from" class="block font-medium text-sm text-gray-700">From (Officer/Department)</label>
                            @if(Auth::user()->role->slug === 'officer')
                                <input id="dispatched_from" type="text" name="dispatched_from"
                                    value="{{ old('dispatched_from', Auth::user()->department->name ?? '') }}" readonly
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 sm:text-sm cursor-not-allowed">
                            @else
                                <input id="dispatched_from" type="text" name="dispatched_from" value="{{ old('dispatched_from') }}" required placeholder="Enter Officer or Department"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('dispatched_from') border-red-500 @enderror">
                            @endif
                            @error('dispatched_from')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-200">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        @if(Auth::user()->role->slug === 'officer')
                            <i class="fa-solid fa-paper-plane mr-2"></i> Submit Request
                        @else
                            <i class="fa-solid fa-paper-plane mr-2"></i> Create Dispatch
                        @endif
                    </button>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="mx-6 mb-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r shadow-sm text-sm">
                {{ session('error') }}
            </div>
        @endif

        @include('partials.for_dispatch_couriers')

        <!-- Status Filter Tabs -->
        <div class="mx-6 mb-3 flex items-center gap-2">
            <span class="text-xs text-gray-500 font-medium">Show:</span>
            <a href="?tab=all" class="px-3 py-1 rounded-full text-xs font-semibold transition-colors {{ request('tab', 'all') === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">All</a>
            <a href="?tab=pending" class="px-3 py-1 rounded-full text-xs font-semibold transition-colors {{ request('tab') === 'pending' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Pending</a>
            <a href="?tab=dispatched" class="px-3 py-1 rounded-full text-xs font-semibold transition-colors {{ request('tab') === 'dispatched' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Dispatched</a>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm mx-6 mb-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#007bff] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Applicant Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Case Number</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                            Company
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                            Tracking ID
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                            Dispatched From
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Center</th>
                        @if(Auth::user()->role->slug !== 'officer')
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Requested By</th>
                        @endif
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right pr-8 text-xs font-bold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dispatches as $dispatch)
                        <tr class="bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $dispatch->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                {{ $dispatch->applicant_name }}
                                @if($dispatch->father_name)
                                    <span class="block text-xs text-gray-500">{{ $dispatch->father_name }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $dispatch->case_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $dispatch->dispatch_courier_company }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-purple-600">
                                {{ $dispatch->tracking_id ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $dispatch->dispatched_from }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $dispatch->center->name ?? '—' }}</td>
                            @if(Auth::user()->role->slug !== 'officer')
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $dispatch->requestedBy->name ?? ($dispatch->dispatchedBy->name ?? '—') }}
                                </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($dispatch->status === 'dispatched')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fa-solid fa-check mr-1"></i> Dispatched
                                    </span>
                                @elseif($dispatch->received_at)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <i class="fa-solid fa-inbox mr-1"></i> Received by R&amp;I
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fa-regular fa-clock mr-1"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                @if(in_array(Auth::user()->role->slug, ['staff-user', 'center-admin', 'super-admin']))
                                    @if(!$dispatch->received_at)
                                        <form action="{{ route('dispatches.markReceived', $dispatch->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700 transition-colors shadow-sm">
                                                <i class="fa-solid fa-inbox mr-1"></i> Mark Received
                                            </button>
                                        </form>
                                    @endif
                                    @if($dispatch->status !== 'dispatched')
                                        <button type="button" 
                                                @click="$dispatch('open-dispatch-modal', { id: {{ $dispatch->id }}, url: '{{ route('dispatches.markDispatched', $dispatch->id) }}' })" 
                                                class="inline-flex items-center px-3 py-1 bg-orange-600 text-white text-xs font-bold rounded hover:bg-orange-700 transition-colors shadow-sm">
                                            <i class="fa-solid fa-paper-plane mr-1"></i> Mark Dispatched
                                        </button>
                                    @endif
                                    @if(in_array(Auth::user()->role->slug, ['center-admin', 'super-admin']))
                                        <a href="{{ route('dispatches.edit', $dispatch->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 text-amber-600 hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Edit">
                                            <i class="fa-solid fa-pencil text-xs"></i>
                                        </a>
                                        <form action="{{ route('dispatches.destroy', $dispatch->id) }}" method="POST" class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete this dispatch?')" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Delete">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">
                                        {{ $dispatch->created_at->format('d M Y') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->role->slug !== 'officer' ? 9 : 8 }}" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-paper-plane text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-lg font-medium">No applicant dispatches found.</p>
                                    <p class="text-sm text-gray-400">
                                        @if(Auth::user()->role->slug === 'officer')
                                            Submit a dispatch request using the form above.
                                        @else
                                            Create a new dispatch using the form above.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dispatches->hasPages())
        <div class="mt-4 px-6 pb-4">
            {{ $dispatches->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
