@if(!Auth::user()->role || Auth::user()->role->slug === 'officer')
@else
@php
    $pendingCount    = $forDispatchCouriers->whereNotIn('status', ['dispatched'])->count();
    $dispatchedCount = $forDispatchCouriers->where('status', 'dispatched')->count();
@endphp
@if($forDispatchCouriers->isNotEmpty())
<div class="mx-6 mb-6 rounded-xl border border-orange-200 overflow-hidden shadow-sm">
    <div class="flex items-center gap-3 px-5 py-3 bg-orange-50 border-b border-orange-200">
        <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-paper-plane text-orange-600 text-xs"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-orange-800">Couriers Returned for Dispatch (by Officers)</h3>
            <p class="text-xs text-orange-500">These incoming couriers were processed by officers and sent back to R&amp;I for outgoing dispatch.</p>
        </div>
        <div class="ml-auto flex items-center gap-2">
            @if($pendingCount > 0)
            <span class="text-xs font-bold bg-orange-600 text-white px-2.5 py-1 rounded-full">{{ $pendingCount }} pending</span>
            @endif
            @if($dispatchedCount > 0)
            <span class="text-xs font-semibold bg-green-100 text-green-700 px-2.5 py-1 rounded-full"><i class="fa-solid fa-check mr-1"></i>{{ $dispatchedCount }} dispatched</span>
            @endif
        </div>
    </div>
    <div class="overflow-x-auto bg-white">
        <table class="min-w-full divide-y divide-orange-100 text-sm">
            <thead class="bg-orange-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tracking ID</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sender</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Company</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Returned By</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Notes</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider" colspan="2">Step 1 — Receive</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Step 2 — Dispatch</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-orange-50">
                @foreach($forDispatchCouriers as $courier)
                @php
                    $dispatchTransfer = $courier->transfers->where('is_for_dispatch', true)->sortByDesc('created_at')->first();
                    $returnedByUser = $dispatchTransfer?->transferredBy;
                    $isDispatched = $courier->status === 'dispatched';
                    $isReceived   = $courier->status === 'received';
                    $isPending    = $courier->status === 'transferred';
                @endphp
                <tr class="{{ $isDispatched ? 'bg-green-50' : 'hover:bg-orange-50' }} transition-colors">
                    <td class="px-5 py-3 whitespace-nowrap">
                        <a href="{{ route('couriers.show', $courier->id) }}" class="font-mono font-bold text-orange-700 text-sm hover:underline">{{ $courier->tracking_id }}</a>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-gray-700">
                        {{ $courier->sender_name }}
                        @if($courier->sender_contact)<span class="block text-xs text-gray-400">{{ $courier->sender_contact }}</span>@endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-gray-600">{{ $courier->courier_company }}</td>
                    <td class="px-5 py-3 whitespace-nowrap text-gray-700">{{ $returnedByUser->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500 max-w-[160px]">
                        @if($dispatchTransfer?->notes)
                            <span class="italic text-xs text-indigo-600">"{{ $dispatchTransfer->notes }}"</span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-gray-500 text-xs">{{ $courier->updated_at->format('d M Y, h:i A') }}</td>

                    {{-- Step 1: Receive --}}
                    @if($isDispatched)
                        <td class="px-5 py-3 text-center" colspan="2">
                            <span class="inline-flex items-center text-xs font-semibold text-green-700"><i class="fa-solid fa-check-double mr-1"></i> Done</span>
                        </td>
                    @elseif($isPending)
                        <td class="px-5 py-3 text-center" colspan="2">
                            <form action="{{ route('couriers.markReceivedDirect', $courier->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                    <i class="fa-solid fa-inbox mr-1.5"></i> Mark Received
                                </button>
                            </form>
                        </td>
                    @else {{-- received --}}
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <span class="inline-flex items-center text-xs font-semibold text-green-700 bg-green-50 border border-green-200 px-2 py-1 rounded-lg">
                                <i class="fa-solid fa-check mr-1"></i> Received
                            </span>
                        </td>
                        <td class="px-4 py-3 text-left whitespace-nowrap">
                            <i class="fa-solid fa-arrow-right text-gray-300 text-xs mx-1"></i>
                        </td>
                    @endif

                    {{-- Step 2: Dispatch --}}
                    <td class="px-5 py-3 text-center whitespace-nowrap">
                        @if($isDispatched)
                            <span class="inline-flex items-center text-xs font-semibold text-green-700"><i class="fa-solid fa-check-double mr-1"></i> Dispatched</span>
                        @elseif($isReceived)
                            <form action="{{ route('couriers.markDispatched', $courier->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white text-xs font-bold rounded-lg hover:bg-orange-700 transition-colors shadow-sm">
                                    <i class="fa-solid fa-paper-plane mr-1.5"></i> Mark Dispatched
                                </button>
                            </form>
                        @else
                            <span class="inline-flex items-center text-xs text-gray-400 border border-dashed border-gray-300 px-2 py-1 rounded-lg">
                                <i class="fa-solid fa-lock mr-1"></i> Receive first
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endif
