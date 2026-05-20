@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Global Search</h1>
        <p class="text-sm text-gray-500 mt-1">Search across couriers (tracking ID, sender) and dispatches (case number, applicant).</p>
    </div>

    <!-- Search Bar -->
    <form method="GET" action="{{ route('search') }}" class="mb-8">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
            </div>
            <input type="text" name="q" value="{{ $q }}" autofocus
                placeholder="Search tracking ID, sender, case number, applicant name..."
                class="block w-full pl-11 pr-24 py-3.5 border border-gray-300 rounded-xl shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    Search
                </button>
            </div>
        </div>
    </form>

    @if(strlen($q) >= 2)
        <p class="text-sm text-gray-500 mb-6">
            Found <span class="font-semibold text-gray-900">{{ $totalCount }}</span> result(s) for "<span class="font-semibold text-indigo-600">{{ $q }}</span>"
        </p>

        @if($results['couriers']->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-base font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-inbox text-blue-500"></i>
                    Couriers ({{ $results['couriers']->count() }})
                </h2>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#007bff] text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Tracking ID</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Sender</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Company</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Center</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($results['couriers'] as $courier)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-mono font-bold text-purple-600">{{ $courier->tracking_id }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $courier->type) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800">{{ $courier->sender_name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $courier->courier_company }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $courier->center->name ?? '—' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @php
                                            $colors = ['pending'=>'bg-yellow-100 text-yellow-800','received'=>'bg-green-100 text-green-800','dispatched'=>'bg-blue-100 text-blue-800','reverted'=>'bg-orange-100 text-orange-800'];
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $colors[$courier->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ ucfirst($courier->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right">
                                        <a href="{{ route('couriers.show', $courier->id) }}" class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-600 text-xs font-medium rounded-lg hover:bg-blue-600 hover:text-white transition-colors">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($results['dispatches']->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-base font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane text-indigo-500"></i>
                    Dispatches ({{ $results['dispatches']->count() }})
                </h2>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#007bff] text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Case / Ref No.</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Applicant / Ministry</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Tracking ID</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Center</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($results['dispatches'] as $dispatch)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-800">{{ $dispatch->case_number }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $dispatch->type) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800">{{ $dispatch->applicant_name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-purple-600">{{ $dispatch->tracking_id ?? '—' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $dispatch->center->name ?? '—' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($dispatch->status === 'dispatched')
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">Dispatched</span>
                                        @elseif($dispatch->received_at)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Received by R&amp;I</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right">
                                        <a href="{{ route('dispatches.show', $dispatch->id) }}" class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-medium rounded-lg hover:bg-indigo-600 hover:text-white transition-colors">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($totalCount === 0)
            <div class="text-center py-16">
                <i class="fa-solid fa-magnifying-glass text-5xl text-gray-300 mb-4"></i>
                <p class="text-lg font-medium text-gray-600">No results found for "{{ $q }}"</p>
                <p class="text-sm text-gray-400 mt-1">Try a different search term — tracking ID, sender name, or case number.</p>
            </div>
        @endif

    @elseif(strlen($q) > 0)
        <p class="text-center text-gray-500 py-10">Please enter at least 2 characters to search.</p>
    @else
        <div class="text-center py-16 text-gray-400">
            <i class="fa-solid fa-magnifying-glass text-5xl mb-4"></i>
            <p class="text-lg font-medium">Type something to search</p>
        </div>
    @endif
</div>
@endsection
