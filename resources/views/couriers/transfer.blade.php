@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-4 space-y-4">

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Courier Header -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028] flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">Transfer Courier</h1>
                <p class="text-sm text-white mt-0.5 font-mono">{{ $courier->tracking_id }} &mdash; {{ $courier->sender_name }} ({{ $courier->courier_company }})</p>
            </div>
            @php
                $sc = ['pending'=>'bg-yellow-100 text-yellow-800','transferred'=>'bg-blue-100 text-blue-800','received'=>'bg-green-100 text-green-800','dispatched'=>'bg-purple-100 text-purple-800'];
            @endphp
            <span class="px-3 py-1 text-sm font-bold rounded-full {{ $sc[$courier->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($courier->status) }}
            </span>
        </div>
        <div class="px-6 py-3 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm border-b border-gray-100">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Center</p>
                <p class="font-medium text-gray-800">{{ $courier->center->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Received By (R&amp;I)</p>
                <p class="font-medium text-gray-800">{{ $courier->receivedBy->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Currently With</p>
                <p class="font-medium text-gray-800">{{ $courier->department->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Type</p>
                <p class="font-medium text-gray-800">{{ ucfirst(str_replace('_',' ', $courier->type ?? '—')) }}</p>
            </div>
        </div>
    </div>

    <!-- Transfer History Table -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center gap-2">
            <i class="fa-solid fa-route text-indigo-500"></i>
            <h2 class="text-base font-bold text-gray-800">Transfer History</h2>
            <span class="ml-auto text-xs text-gray-400">{{ $courier->transfers->count() }} transfer(s)</span>
        </div>

        @if($courier->transfers->isEmpty())
            <div class="px-6 py-8 text-center text-gray-400 text-sm">
                <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                No transfers yet. Use the form below to forward this courier.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Transferred To</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">By / Notes</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Acknowledged</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @php
                            $isReverted = !is_null($courier->reverted_by_user_id);
                            $transfers = $courier->transfers->sortBy('created_at')->values();
                            $lastTransfer = $transfers->last();
                        @endphp
                        @foreach($transfers as $i => $transfer)
                            @php $isCurrent = $loop->last && $courier->status !== 'dispatched'; @endphp
                            <tr class="{{ $isCurrent ? 'bg-indigo-50' : '' }}">
                                <td class="px-5 py-3 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-5 py-3 font-medium text-gray-900">
                                    {{ $transfer->transferable->name ?? 'R&I Staff' }}
                                    @if($isCurrent)
                                        <span class="ml-1 text-xs bg-indigo-100 text-indigo-700 font-semibold px-1.5 py-0.5 rounded">current</span>
                                    @endif
                                    @if($transfer->is_for_dispatch)
                                        <span class="ml-1 text-xs bg-orange-100 text-orange-700 font-semibold px-1.5 py-0.5 rounded"><i class="fa-solid fa-paper-plane mr-0.5"></i> for dispatch</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <span class="text-gray-600">{{ $transfer->transferredBy->name ?? '—' }}</span>
                                    @if($transfer->notes)
                                        <p class="text-xs text-indigo-600 mt-0.5 italic"><i class="fa-solid fa-quote-left text-indigo-300 mr-1"></i>{{ $transfer->notes }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-gray-500">{{ $transfer->created_at->format('d M Y, h:i A') }}</td>
                                <td class="px-5 py-3">
                                    @if($transfer->received_at)
                                        <span class="inline-flex items-center text-xs text-green-700 font-medium">
                                            <i class="fa-solid fa-check mr-1"></i> {{ \Carbon\Carbon::parse($transfer->received_at)->format('d M Y, h:i A') }}
                                        </span>
                                    @elseif($transfer->is_reverted)
                                        <span class="inline-flex items-center text-xs text-red-600 font-medium">
                                            <i class="fa-solid fa-rotate-left mr-1"></i> Reverted to R&amp;I
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-xs text-amber-600 font-medium">
                                            <i class="fa-regular fa-clock mr-1"></i> Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if($isReverted)
                            <tr class="bg-red-50">
                                <td class="px-5 py-3 text-gray-500">—</td>
                                <td class="px-5 py-3 font-medium text-red-800" colspan="2">
                                    <i class="fa-solid fa-rotate-left mr-1"></i> Reverted to R&amp;I
                                    @if($courier->comments) <span class="text-xs text-red-500 font-normal italic">"{{ $courier->comments }}"</span> @endif
                                </td>
                                <td class="px-5 py-3 text-red-600 text-xs">{{ $courier->updated_at->format('d M Y, h:i A') }}</td>
                                <td class="px-5 py-3 text-red-600 text-xs">By: {{ $courier->revertedBy->name ?? '—' }}</td>
                            </tr>
                        @elseif($lastTransfer && $lastTransfer->received_at && is_null($courier->department_id) && $courier->status === 'transferred')
                            <tr class="bg-amber-50">
                                <td class="px-5 py-3 text-gray-500">—</td>
                                <td class="px-5 py-3 font-medium text-amber-800" colspan="3">
                                    <i class="fa-solid fa-reply mr-1"></i> Returned to R&amp;I (after processing)
                                    @if($lastTransfer->is_for_dispatch) <span class="ml-1 text-xs bg-orange-100 text-orange-700 font-semibold px-1.5 py-0.5 rounded"><i class="fa-solid fa-paper-plane mr-0.5"></i> for dispatch</span> @endif
                                </td>
                                <td class="px-5 py-3 text-amber-600 text-xs">{{ $courier->updated_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        @elseif($courier->status === 'dispatched')
                            <tr class="bg-purple-50">
                                <td class="px-5 py-3 text-gray-500">—</td>
                                <td class="px-5 py-3 font-medium text-purple-800" colspan="3">
                                    <i class="fa-solid fa-paper-plane mr-1"></i> Dispatched (outgoing processed)
                                </td>
                                <td class="px-5 py-3 text-purple-600 text-xs">{{ $courier->updated_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Transfer Form (only if not dispatched) -->
    @if($courier->status !== 'dispatched')
    @php
        $authUser = Auth::user();
        $isOfficer = $authUser->role->slug === 'officer';
        $showReturnTab = $isOfficer && $courier->status === 'received';
        $defaultTab = $showReturnTab ? 'return' : 'user';
    @endphp
    <div x-data="{ transferMode: '{{ $defaultTab }}' }" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-share text-blue-500"></i>
                <h2 class="text-base font-bold text-gray-800">Transfer Courier</h2>
            </div>
            <div class="flex bg-gray-200 rounded-lg p-1 text-sm flex-wrap gap-1">
                @if($showReturnTab)
                    <button @click="transferMode = 'return'" :class="{ 'bg-white shadow-sm font-semibold': transferMode === 'return', 'text-gray-500': transferMode !== 'return' }" class="px-3 py-1 rounded-md transition-all text-amber-700">
                        <i class="fa-solid fa-reply mr-1"></i> Return to R&amp;I
                    </button>
                @endif
                <button @click="transferMode = 'user'" :class="{ 'bg-white shadow-sm font-semibold': transferMode === 'user', 'text-gray-500': transferMode !== 'user' }" class="px-3 py-1 rounded-md transition-all">To Officer/Staff</button>
                @if(!$isOfficer)
                <button @click="transferMode = 'department'" :class="{ 'bg-white shadow-sm font-semibold': transferMode === 'department', 'text-gray-500': transferMode !== 'department' }" class="px-3 py-1 rounded-md transition-all">To Department</button>
                @endif
            </div>
        </div>
        <div class="p-6">

            {{-- Return to R&I tab (officers who received the courier) --}}
            @if($showReturnTab)
            <form x-show="transferMode === 'return'" method="POST" action="{{ route('couriers.returnToRI', $courier->id) }}">
                @csrf
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-amber-800 font-medium mb-1"><i class="fa-solid fa-info-circle mr-1"></i> Returning to R&amp;I</p>
                    <p class="text-xs text-amber-700">Use this when you have processed the courier and need to send it back to R&amp;I. If it is ready to be dispatched as outgoing, check the box below so R&amp;I knows to dispatch it.</p>
                </div>
                <label class="flex items-center gap-3 cursor-pointer mb-4 p-3 border border-orange-200 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                    <input type="checkbox" name="is_for_dispatch" value="1" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-4 h-4">
                    <span class="text-sm font-semibold text-orange-800"><i class="fa-solid fa-paper-plane mr-1"></i> Courier is ready for dispatch (outgoing)</span>
                </label>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fa-solid fa-comment-dots mr-1 text-gray-400"></i> Notes / Comments <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea name="notes" rows="2" placeholder="e.g. Processed and signed. Ready to go." class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm"></textarea>
                </div>
                <button type="submit" class="inline-flex items-center px-5 py-2 bg-amber-600 hover:bg-amber-700 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md transition-all">
                    <i class="fa-solid fa-reply mr-2"></i> Return to R&amp;I
                </button>
            </form>
            @endif

            <!-- Transfer to Officer/Staff Form -->
            <div x-show="transferMode === 'user'" {{ $showReturnTab ? 'x-cloak' : '' }}
                 x-data="{
                     selectedDept: '',
                     selectedUser: '',
                     usersByDept: {{ $usersForTransfer->toJson() }},
                     get officers() {
                         return this.selectedDept ? (this.usersByDept[this.selectedDept] || []) : [];
                     }
                 }">
                <form method="POST" action="{{ route('couriers.transfer', $courier->id) }}">
                    @csrf
                    <input type="hidden" name="transferable_type" value="user">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1">1. Select Department</label>
                            <select x-model="selectedDept" @change="selectedUser = ''"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">— Choose Department —</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1">2. Select Officer</label>
                            <select name="transferable_id" x-model="selectedUser"
                                :disabled="!selectedDept || officers.length === 0"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-400"
                                required>
                                <option value="">— Choose Officer —</option>
                                <template x-for="officer in officers" :key="officer.id">
                                    <option :value="officer.id" x-text="officer.name"></option>
                                </template>
                            </select>
                            <p x-show="selectedDept && officers.length === 0" class="text-xs text-amber-600 mt-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i> No officers in this department.</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fa-solid fa-comment-dots mr-1 text-gray-400"></i> Notes / Comments <span class="text-gray-400 font-normal">(optional)</span></label>
                        <textarea name="notes" rows="2" placeholder="e.g. Please review and process this courier." class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        <i class="fa-solid fa-user-check mr-2"></i> Transfer to Officer/Staff
                    </button>
                </form>
            </div>

            <!-- Transfer to Department Form -->
            <form x-show="transferMode === 'department'" x-cloak method="POST" action="{{ route('couriers.transfer', $courier->id) }}">
                @csrf
                <input type="hidden" name="transferable_type" value="department">

                <div class="mb-4">
                    <label class="block font-medium text-sm text-gray-700 mb-1">Select Department</label>
                    <select id="transferable_dept_id" name="transferable_id"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('transferable_id') border-red-500 @enderror"
                        required x-bind:disabled="transferMode !== 'department'">
                        <option value="">— Choose Department —</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('transferable_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                                @if($dept->id === $courier->department_id) (current) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('transferable_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fa-solid fa-comment-dots mr-1 text-gray-400"></i> Notes / Comments <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea name="notes" rows="2" placeholder="e.g. Forwarding to this department for action." class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>
                <button type="submit"
                    class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                    <i class="fa-solid fa-building mr-2"></i> Transfer to Department
                </button>
            </form>
        </div>
    </div>
    @endif

    <div>
        <a href="{{ url()->previous() !== route('couriers.transferForm', $courier->id) ? url()->previous() : route('incoming') }}"
           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 transition">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back
        </a>
    </div>

</div>
@endsection
