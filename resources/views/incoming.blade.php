@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Incoming Couriers</h1>
            </div>
            <span class="text-sm text-gray-500 bg-white px-3 py-1 rounded-full shadow-sm border border-gray-200">
                <i class="fa-regular fa-calendar mr-2"></i> {{ now()->format('F d, Y') }}
            </span>
        </div>

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- Pending -->
            <div onclick="window.location.href='?tab=pending'"
                class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl p-6 shadow-md hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden text-white cursor-pointer {{ request('tab') !== 'transferred' ? 'ring-4 ring-amber-300' : '' }}">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-sm font-medium text-amber-50 mb-1">Pending</p>
                        <h3 class="text-3xl font-bold text-white">{{ $pendingCouriers }}</h3>
                    </div>
                    <div
                        class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center text-white group-hover:scale-110 transition-transform backdrop-blur-sm">
                        <i class="fa-regular fa-clock text-2xl"></i>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
            </div>

            <!-- Transferred -->
            <div onclick="window.location.href='?tab=transferred'"
                class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-xl p-6 shadow-md hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden text-white cursor-pointer {{ request('tab') === 'transferred' ? 'ring-4 ring-purple-300' : '' }}">
                <div class="flex justify-between items-start relative z-10">
                    <div>
                        <p class="text-sm font-medium text-indigo-100 mb-1">Transferred</p>
                        <h3 class="text-3xl font-bold text-white">{{ $transferredCouriers }}</h3>
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

            <form method="GET" action="{{ route('incoming') }}">
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
                        <button type="submit" class="flex-1 h-[42px] text-white rounded-lg font-semibold hover:opacity-90 transition-all" style="background-color: #4F46E5;">
                            Apply Filter
                        </button>
                        <a href="{{ route('incoming') }}" class="flex-1 h-[42px] inline-flex items-center justify-center bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-all">
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

        <!-- Recent Incoming Couriers Table -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8 border border-gray-100">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fa-solid {{ request('tab') === 'transferred' ? 'fa-share text-purple-600' : 'fa-inbox text-rose-600' }} mr-2"></i> 
                    {{ request('tab') === 'transferred' ? 'Transferred Couriers History' : 'Recent Incoming Couriers' }}
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tracking ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sender</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Forwarded To / Assigned</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($recentCouriers as $courier)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="block font-mono font-medium text-rose-600 font-bold">{{ $courier->tracking_id ?? 'N/A' }}</span>
                                        <span class="block text-xs text-gray-500 mt-1">{{ $courier->courier_company ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $courier->sender_name }}
                                        <span class="block text-xs text-gray-500">{{ $courier->sender_contact }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div class="font-medium text-gray-800">{{ $courier->department->name ?? '—' }}</div>
                                        @if($courier->assignedUser)
                                            <div class="text-xs text-indigo-600 mt-1"><i class="fa-solid fa-user-tie mr-1"></i>{{ $courier->assignedUser->name }}</div>
                                        @else
                                            <div class="text-xs text-gray-400 mt-1 italic"><i class="fa-solid fa-users mr-1"></i>Unassigned</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $sc = ['pending'=>'bg-yellow-100 text-yellow-800','transferred'=>'bg-blue-100 text-blue-800','received'=>'bg-green-100 text-green-800','dispatched'=>'bg-purple-100 text-purple-800','reverted'=>'bg-orange-100 text-orange-800'];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sc[$courier->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            @if($courier->status === 'reverted')
                                                <i class="fa-solid fa-rotate-left mr-1"></i> Reverted
                                            @else
                                                {{ ucfirst($courier->status) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $courier->created_at->format('d M Y') }}
                                        <span class="text-xs text-gray-400 block">{{ $courier->created_at->format('h:i A') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-1">
                                        <a href="{{ route('couriers.show', $courier->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-600 hover:bg-blue-600 hover:text-white transition-all duration-200" title="View Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        @if(request('tab') !== 'transferred')
                                            @if(Auth::user()->role->slug === 'officer')
                                            @if($courier->status === 'transferred')
                                                @if($courier->transfers->isNotEmpty())
                                                    <form action="{{ route('courier-transfers.markReceived', $courier->transfers->last()->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs font-bold rounded hover:bg-green-700">
                                                            <i class="fa-solid fa-check mr-1"></i> Received
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('couriers.markReceivedDirect', $courier->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs font-bold rounded hover:bg-green-700">
                                                            <i class="fa-solid fa-check mr-1"></i> Received
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                            @if(in_array($courier->status, ['transferred','received']))
                                                <a href="{{ route('couriers.transferForm', $courier->id) }}" class="inline-flex items-center px-2 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700">
                                                    <i class="fa-solid fa-share mr-1"></i> Transfer
                                                </a>
                                            @endif
                                            <!-- Revert: only if no one has received this courier before -->
                                            @if($courier->status === 'transferred' && $courier->transfers->whereNotNull('received_at')->isEmpty())
                                                <button type="button" @click="$dispatch('open-revert-modal', { id: {{ $courier->id }}, trackingId: '{{ $courier->tracking_id }}' })" class="inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs font-bold rounded hover:bg-red-700 transition-colors shadow-sm ml-1" title="Revert to R&I">
                                                    <i class="fa-solid fa-rotate-left mr-1"></i> Revert
                                                </button>
                                            @endif
                                        @elseif(Auth::user()->role->slug === 'staff-user')
                                            @if(!is_null($courier->reverted_by_user_id))
                                                <a href="{{ route('couriers.transferForm', $courier->id) }}" class="inline-flex items-center px-2 py-1 bg-indigo-600 text-white text-xs font-bold rounded hover:bg-indigo-700 transition-colors shadow-sm">
                                                    <i class="fa-solid fa-share mr-1"></i> Re-assign
                                                </a>
                                            @elseif($courier->status === 'transferred' && $courier->department_id === null)
                                                @php $latestTransferRI = $courier->transfers->sortByDesc('created_at')->first(); @endphp
                                                @if($latestTransferRI && $latestTransferRI->is_for_dispatch)
                                                    <span class="inline-flex items-center px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-semibold rounded"><i class="fa-solid fa-paper-plane mr-1"></i> For dispatch</span>
                                                @endif
                                                <form action="{{ route('couriers.markReceivedDirect', $courier->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs font-bold rounded hover:bg-green-700">
                                                        <i class="fa-solid fa-inbox mr-1"></i> Confirm Receipt
                                                    </button>
                                                </form>
                                            @elseif($courier->status === 'received' && $courier->department_id === null)
                                                <a href="{{ route('couriers.transferForm', $courier->id) }}" class="inline-flex items-center px-2 py-1 bg-indigo-600 text-white text-xs font-bold rounded hover:bg-indigo-700">
                                                    <i class="fa-solid fa-share mr-1"></i> Transfer
                                                </a>
                                                <form action="{{ route('couriers.markDispatched', $courier->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-orange-600 text-white text-xs font-bold rounded hover:bg-orange-700">
                                                        <i class="fa-solid fa-paper-plane mr-1"></i> Mark Dispatched
                                                    </button>
                                                </form>
                                            @endif
                                        @elseif(in_array(Auth::user()->role->slug, ['center-admin','super-admin']))
                                            @if($courier->status !== 'dispatched')
                                                <a href="{{ route('couriers.transferForm', $courier->id) }}" class="inline-flex items-center px-2 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700">
                                                    <i class="fa-solid fa-share mr-1"></i> Transfer
                                                </a>
                                            @endif
                                        @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 italic">No recent incoming couriers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($recentCouriers instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-4">
                        {{ $recentCouriers->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Revert Modal Component using Alpine.js -->
    <div x-data="{ open: false, courierId: null, trackingId: '', comments: '' }"
         @open-revert-modal.window="open = true; courierId = $event.detail.id; trackingId = $event.detail.trackingId; comments = ''; setTimeout(() => $refs.commentInput.focus(), 100)"
         @keydown.escape.window="open = false"
         class="relative z-50"
         style="display: none;"
         x-show="open">
        
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                    
                    <!-- Decorative Header -->
                    <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex items-center gap-3">
                        <div class="mx-auto flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-bold leading-6 text-red-900" id="modal-title">Revert Courier to R&I</h3>
                    </div>

                    <form :action="`/couriers/${courierId}/revert`" method="POST">
                        @csrf
                        <div class="bg-white px-6 py-5">
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">
                                    You are about to return <span class="font-bold text-gray-900 font-mono" x-text="trackingId"></span> back to the R&I staff. 
                                    Please provide a mandatory reason or comment for this action.
                                </p>
                            </div>
                            
                            <div class="mt-2">
                                <label for="revert_comments" class="block text-sm font-semibold text-gray-700 mb-1">Reason for Revert <span class="text-red-500">*</span></label>
                                <textarea x-ref="commentInput" id="revert_comments" name="comments" x-model="comments" rows="4" 
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-3 bg-gray-50" 
                                    placeholder="e.g., Wrong department, missing documents, etc." required></textarea>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100">
                            <button type="button" @click="open = false" class="inline-flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 sm:mt-0 sm:w-auto transition-colors">
                                Cancel
                            </button>
                            <button type="submit" :disabled="comments.trim().length === 0" class="inline-flex w-full justify-center rounded-lg border border-transparent bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <i class="fa-solid fa-rotate-left mr-2 mt-0.5"></i> Confirm Revert
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection