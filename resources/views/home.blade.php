@extends('layouts.app')

@section('content')

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Dashboard Overview</h1>
            <span class="text-sm text-gray-500 bg-white px-3 py-1 rounded-full shadow-sm border border-gray-200">
                <i class="fa-regular fa-calendar mr-2"></i> {{ now()->format('F d, Y') }}
            </span>
        </div>

        <!-- Navigation Buttons Card Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 mb-8 max-w-5xl mx-auto mt-12 px-6" style="gap: 40px;">

            <!-- Incoming Button Card -->
            <a href="{{ route('incoming') }}"
                class="incoming group relative rounded-3xl pt-6 pb-12 px-8 shadow-xl hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 overflow-hidden text-center border-2 border-blue-100 block"
                style="background-color: #DBEAFE;">
                <div class="relative z-10 space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-blue-200/50 flex items-center justify-center text-[#1E3A8A] mx-auto shadow-sm group-hover:scale-110 transition-transform duration-500">
                        <i class="fa-solid fa-inbox text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-3xl font-extrabold mb-1 tracking-tight uppercase" style="color: #1E3A8A;">Receive</h3>
                        <p class="text-blue-700/80 text-sm font-medium">Review recently received courier records</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center rounded-xl text-white text-sm font-bold shadow-lg transform hover:scale-105 transition-all duration-200"
                              style="background-color: #3B82F6; padding: 12px 40px;">
                            Manage Records <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </div>
                </div>
            </a>

            <!-- Outgoing Button Card -->
            <a href="{{ route('outgoing') }}"
                class="outgoing group relative rounded-3xl pt-6 pb-12 px-8 shadow-xl hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 overflow-hidden text-center border-2 border-amber-100 block"
                style="background-color: #FEF3C7;">
                <div class="relative z-10 space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-amber-200/50 flex items-center justify-center text-[#92400E] mx-auto shadow-sm group-hover:scale-110 transition-transform duration-500">
                        <i class="fa-solid fa-paper-plane text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-3xl font-extrabold mb-1 tracking-tight uppercase" style="color: #92400E;">Dispatch</h3>
                        <p class="text-amber-800/80 text-sm font-medium">Review recently dispatched records</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center rounded-xl text-white text-sm font-bold shadow-lg transform hover:scale-105 transition-all duration-200"
                              style="background-color: #F59E0B; padding: 12px 40px;">
                            Manage Records <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </div>
                </div>
            </a>

        </div>

        {{-- ============================================================ --}}
        {{-- REVERTED DHAKS ALERT — visible to staff-user / center-admin  --}}
        {{-- ============================================================ --}}
        @if(in_array(Auth::user()->role->slug, ['staff-user','center-admin','super-admin']) && $revertedCouriers->isNotEmpty())
        <div class="max-w-5xl mx-auto px-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg border-l-4 border-red-500 overflow-hidden">
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 bg-red-50 border-b border-red-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fa-solid fa-rotate-left text-red-600 text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-red-800">Reverted Dhaks — Action Required</h2>
                            <p class="text-xs text-red-500">These couriers were returned by officers. Please review and re-assign.</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-600 text-white shadow">
                        {{ $revertedCouriers->count() }} pending
                    </span>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tracking ID</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sender</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reverted By</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Officer's Comments</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date Reverted</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($revertedCouriers as $rc)
                            <tr class="hover:bg-red-50 transition-colors">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-rose-600 text-sm">{{ $rc->tracking_id }}</span>
                                    <span class="block text-xs text-gray-400 mt-0.5">{{ $rc->courier_company }}</span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $rc->sender_name }}
                                    <span class="block text-xs text-gray-400">{{ $rc->sender_contact }}</span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-user text-orange-600 text-xs"></i>
                                        </div>
                                        <span class="font-medium text-gray-800">{{ $rc->revertedBy->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600 max-w-xs">
                                    <p class="truncate italic text-gray-500" title="{{ $rc->comments }}">
                                        "{{ $rc->comments ?? '—' }}"
                                    </p>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $rc->updated_at->format('d M Y') }}
                                    <span class="block text-xs text-gray-400">{{ $rc->updated_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('couriers.show', $rc->id) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-600 hover:bg-blue-600 hover:text-white transition-all duration-200"
                                           title="View Details">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('couriers.transferForm', $rc->id) }}"
                                           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-all shadow-sm"
                                           title="Re-assign to officer/department">
                                            <i class="fa-solid fa-share mr-1.5"></i> Re-assign
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if(in_array(Auth::user()->role->slug, ['staff-user', 'center-admin', 'super-admin']))
        <!-- Stats Cards -->
        <div class="max-w-5xl mx-auto px-6 mb-12">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Incoming (Receive) Overview</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <a href="{{ route('incoming1') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center">
                            <i class="fa-regular fa-clock text-yellow-500 text-lg"></i>
                        </div>
                        <span class="text-xs font-medium text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full">Pending</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['totalPending'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Awaiting assignment</p>
                </a>
                <a href="{{ route('incoming1') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                            <i class="fa-solid fa-check text-green-500 text-lg"></i>
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Received</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['totalReceived'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Accepted by officer</p>
                </a>
                <a href="{{ route('incoming1') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                            <i class="fa-solid fa-inbox text-blue-500 text-lg"></i>
                        </div>
                        <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Today</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['todayReceived'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Registered today</p>
                </a>
                <a href="{{ route('incoming1') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center">
                            <i class="fa-solid fa-rotate-left text-orange-500 text-lg"></i>
                        </div>
                        <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full">Reverted</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['totalReverted'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Returned by officers</p>
                </a>
            </div>

            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Outgoing (Dispatch) Overview</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <a href="{{ route('outgoing1') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                            <i class="fa-solid fa-paper-plane text-indigo-500 text-lg"></i>
                        </div>
                        <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">Total</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['totalOutgoing'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">All dispatch records</p>
                </a>
                <a href="{{ route('outgoing1') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                            <i class="fa-solid fa-check-double text-green-500 text-lg"></i>
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Dispatched</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['outgoingDispatched'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Successfully dispatched</p>
                </a>
                <a href="{{ route('outgoing1') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                            <i class="fa-regular fa-clock text-amber-500 text-lg"></i>
                        </div>
                        <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Pending</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['outgoingPending'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Not yet dispatched</p>
                </a>
            </div>
        </div>
        @endif

    </div>
    @endsection
