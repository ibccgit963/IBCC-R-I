@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Outgoing Dispatches</h1>
            </div>
            <span class="text-sm text-gray-500 bg-white px-3 py-1 rounded-full shadow-sm border border-gray-200">
                <i class="fa-regular fa-calendar mr-2"></i> {{ now()->format('F d, Y') }}
            </span>
        </div>

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- Pending -->
            <div
                class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl p-6 shadow-md hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden text-white cursor-pointer">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-sm font-medium text-amber-50 mb-1">{{ Auth::user()->role->slug === 'officer' ? 'My Requests' : 'Pending' }}</p>
                        <h3 class="text-3xl font-bold text-white">{{ $pendingDispatches }}</h3>
                    </div>
                    <div
                        class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center text-white group-hover:scale-110 transition-transform backdrop-blur-sm">
                        <i class="fa-regular fa-clock text-2xl"></i>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
            </div>

            <!-- Transferred -->
            <div
                class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-xl p-6 shadow-md hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden text-white cursor-pointer">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-sm font-medium text-purple-100 mb-1">{{ Auth::user()->role->slug === 'officer' ? 'Dispatched' : 'Transferred' }}</p>
                        <h3 class="text-3xl font-bold text-white">{{ $transferredDispatches }}</h3>
                    </div>
                    <div
                        class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center text-white group-hover:scale-110 transition-transform backdrop-blur-sm">
                        <i class="fa-solid fa-share text-2xl"></i>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
            </div>

        </div>

        <!-- Filters Section -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8 transition-all duration-300 hover:shadow-md">
            <div class="flex items-center mb-4 text-gray-800">
                <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 mr-3">
                    <i class="fa-solid fa-filter text-sm"></i>
                </div>
                <h2 class="text-lg font-semibold">Filter Data</h2>
            </div>

            <form method="GET" action="{{ route('outgoing') }}">
                <div class="flex flex-wrap lg:flex-nowrap gap-4 items-end">
                    @if (Auth::user()->role->slug === 'super-admin')
                        <div class="w-full lg:w-1/6">
                            <label for="center_id" class="block text-sm font-medium text-gray-700 mb-2">Center</label>
                            <select name="center_id" id="center_id" class="w-full h-[42px] px-4 rounded-lg border-gray-300 bg-gray-50 text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="">All Centers</option>
                                @foreach ($centers as $center)
                                    <option value="{{ $center->id }}" {{ request('center_id') == $center->id ? 'selected' : '' }}>
                                        {{ $center->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="w-full lg:w-1/6">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="w-full h-[42px] px-3 rounded-lg border-gray-300 bg-gray-50 text-sm" value="{{ request('start_date') }}">
                    </div>

                    <div class="w-full lg:w-1/6">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="w-full h-[42px] px-3 rounded-lg border-gray-300 bg-gray-50 text-sm" value="{{ request('end_date') }}">
                    </div>

                    <div class="flex-1 flex space-x-2">
                        <button type="submit" class="flex-1 h-[42px] text-white rounded-lg font-semibold hover:opacity-90 transition-all" style="background-color: #E11D48;">
                            Apply Filter
                        </button>
                        <a href="{{ route('outgoing') }}" class="flex-1 h-[42px] inline-flex items-center justify-center bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-all">
                            Clear
                        </a>
                    </div>

                    <div class="w-full lg:w-1/6">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Records</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs"></i>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="block w-full h-[42px] pl-12 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Recent Outgoing Dispatches Table -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8 border border-gray-100">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fa-solid fa-paper-plane mr-2 text-rose-600"></i> Recent Outgoing Dispatches
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name / Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case / Ref No.</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Company</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tracking ID</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">From</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                @if(in_array(Auth::user()->role->slug, ['staff-user', 'center-admin', 'super-admin']))
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($recentDispatches as $dispatch)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $dispatch->applicant_name }}
                                        @if($dispatch->father_name)
                                            <span class="block text-xs text-gray-500">{{ $dispatch->father_name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">{{ $dispatch->case_number }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">{{ $dispatch->dispatch_courier_company }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-purple-600">{{ $dispatch->tracking_id ?? '—' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-600">{{ $dispatch->dispatched_from }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-1.5 py-0.5 text-xs rounded bg-gray-100 text-gray-600">{{ ucfirst(str_replace('_', ' ', $dispatch->type)) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($dispatch->status === 'dispatched')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Dispatched</span>
                                        @elseif($dispatch->received_at)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Rcvd by R&amp;I</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $dispatch->created_at->format('d M Y') }}
                                        <span class="text-xs text-gray-400 block">{{ $dispatch->created_at->format('h:i A') }}</span>
                                    </td>
                                    @if(in_array(Auth::user()->role->slug, ['staff-user', 'center-admin', 'super-admin']))
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-1">
                                            @if(!$dispatch->received_at)
                                                <form action="{{ route('dispatches.markReceived', $dispatch->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700 transition-colors">
                                                        <i class="fa-solid fa-inbox mr-1"></i> Received
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
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ in_array(Auth::user()->role->slug, ['staff-user', 'center-admin', 'super-admin']) ? 7 : 6 }}" class="px-6 py-4 text-center text-gray-500 italic">No recent dispatches found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
