@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-4">

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-4 rounded-r shadow-sm">
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r shadow-sm">
            <p class="text-sm">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-4">
        <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028] flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">Courier Details</h1>
                <p class="text-sm text-white mt-0.5 font-mono">{{ $courier->tracking_id }}</p>
            </div>
            @php
                $sc=['pending'=>'bg-yellow-100 text-yellow-800','transferred'=>'bg-blue-100 text-blue-800','received'=>'bg-green-100 text-green-800','dispatched'=>'bg-purple-100 text-purple-800','reverted'=>'bg-orange-100 text-orange-800'];
            @endphp
            <span class="px-3 py-1 text-sm font-bold rounded-full {{ $sc[$courier->status] ?? 'bg-gray-100 text-gray-800' }}">
                @if($courier->status === 'reverted')<i class="fa-solid fa-rotate-left mr-1"></i>@endif{{ ucfirst($courier->status) }}
            </span>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Courier Company</p>
                    <p class="font-medium text-gray-800">{{ $courier->courier_company }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Sender Name</p>
                    <p class="font-medium text-gray-800">{{ $courier->sender_name }}</p>
                </div>
                @if($courier->sender_cnic)
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Sender CNIC</p>
                    <p class="font-medium text-gray-800">{{ $courier->sender_cnic }}</p>
                </div>
                @endif
                @if($courier->sender_contact)
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Contact</p>
                    <p class="font-medium text-gray-800">{{ $courier->sender_contact }}</p>
                </div>
                @endif
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Type</p>
                    <p class="font-medium text-gray-800">{{ ucfirst(str_replace('_', ' ', $courier->type ?? '—')) }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Center</p>
                    <p class="font-medium text-gray-800">{{ $courier->center->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">First Received By (R&amp;I)</p>
                    <p class="font-medium text-gray-800">{{ $courier->receivedBy->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Currently With</p>
                    <p class="font-medium text-gray-800">{{ $courier->department->name ?? '—' }}</p>
                </div>
                @if($courier->branch)
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Branch</p>
                    <p class="font-medium text-gray-800">{{ $courier->branch }}</p>
                </div>
                @endif
                @if($courier->ministry_department)
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Ministry / Department</p>
                    <p class="font-medium text-gray-800">{{ $courier->ministry_department }}</p>
                </div>
                @endif
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Registered On</p>
                    <p class="font-medium text-gray-800">{{ $courier->created_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Trail -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-4">
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
            <h2 class="text-base font-bold text-gray-800 flex items-center">
                <i class="fa-solid fa-route mr-2 text-indigo-500"></i> Transfer Trail
            </h2>
        </div>
        <div class="p-6">
            @php
                $transfers = $courier->transfers()->with('transferable', 'transferredBy')->orderBy('created_at')->get();
                $lastTransfer = $transfers->last();
                $isReverted = !is_null($courier->reverted_by_user_id);
                // returnToRI now creates a transfer record; detect by: previous transfers received, last not, dept=null
                $hasAnyReceived = $transfers->whereNotNull('received_at')->isNotEmpty();
                $isReturnedToRI = !$isReverted && $lastTransfer && !$lastTransfer->received_at
                    && $hasAnyReceived && is_null($courier->department_id);
            @endphp
            @if($transfers->isEmpty())
                <p class="text-sm text-gray-500 italic">No transfers recorded — registered directly to department.</p>
            @else
                <ol class="relative border-l border-gray-200 ml-3">
                    @foreach($transfers as $transfer)
                        @php $isLast = $transfer->id === $lastTransfer->id; @endphp
                        <li class="mb-6 ml-6">
                            <span class="absolute -left-3 flex items-center justify-center w-6 h-6 bg-indigo-100 rounded-full ring-2 ring-white">
                                <i class="fa-solid fa-share text-indigo-600 text-xs"></i>
                            </span>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <p class="text-sm font-semibold text-gray-800">
                                    Transferred to: <span class="text-indigo-700">{{ $transfer->transferable->name ?? 'R&amp;I Staff' }}</span>
                                    @if($transfer->is_for_dispatch)
                                        <span class="ml-2 text-xs bg-orange-100 text-orange-700 font-semibold px-1.5 py-0.5 rounded"><i class="fa-solid fa-paper-plane mr-0.5"></i> for dispatch</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    By: {{ $transfer->transferredBy->name ?? '—' }} &middot; {{ $transfer->created_at->format('d M Y, h:i A') }}
                                </p>
                                @if($transfer->notes)
                                    <p class="text-xs text-indigo-600 mt-1 italic bg-indigo-50 rounded px-2 py-1"><i class="fa-solid fa-quote-left text-indigo-300 mr-1"></i>{{ $transfer->notes }}</p>
                                @endif
                                @if($transfer->received_at)
                                    <p class="text-xs text-green-700 mt-1">
                                        <i class="fa-solid fa-check mr-1"></i> Acknowledged received on {{ \Carbon\Carbon::parse($transfer->received_at)->format('d M Y, h:i A') }}
                                    </p>
                                @elseif($transfer->is_reverted)
                                    <p class="text-xs text-red-600 mt-1">
                                        <i class="fa-solid fa-rotate-left mr-1"></i> Reverted to R&amp;I without receiving
                                    </p>
                                @elseif($isLast && $isReturnedToRI)
                                    <p class="text-xs text-amber-600 mt-1">
                                        <i class="fa-solid fa-reply mr-1"></i> Returned to R&amp;I {{ $transfer->is_for_dispatch ? '(for dispatch)' : '(for reassignment)' }} — awaiting R&amp;I confirmation
                                    </p>
                                @else
                                    <p class="text-xs text-amber-600 mt-1">
                                        <i class="fa-regular fa-clock mr-1"></i> Awaiting acknowledgement
                                    </p>
                                @endif
                            </div>
                        </li>
                    @endforeach

                    {{-- Reverted trail entry --}}
                    @if($isReverted)
                        <li class="mb-6 ml-6">
                            <span class="absolute -left-3 flex items-center justify-center w-6 h-6 bg-red-100 rounded-full ring-2 ring-white">
                                <i class="fa-solid fa-rotate-left text-red-600 text-xs"></i>
                            </span>
                            <div class="bg-red-50 rounded-lg p-3 border border-red-200">
                                <p class="text-sm font-semibold text-red-800">Reverted to R&amp;I (needs reassignment)</p>
                                <p class="text-xs text-red-600 mt-0.5">
                                    By: {{ $courier->revertedBy->name ?? '—' }} &middot; {{ $courier->updated_at->format('d M Y, h:i A') }}
                                </p>
                                @if($courier->comments)
                                    <p class="text-xs text-red-500 mt-1 italic">"{{ $courier->comments }}"</p>
                                @endif
                            </div>
                        </li>
                    @endif

                    @if($courier->status === 'dispatched')
                        <li class="ml-6">
                            <span class="absolute -left-3 flex items-center justify-center w-6 h-6 bg-purple-100 rounded-full ring-2 ring-white">
                                <i class="fa-solid fa-paper-plane text-purple-600 text-xs"></i>
                            </span>
                            <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                                <p class="text-sm font-semibold text-purple-800">Dispatched (outgoing processed)</p>
                                <p class="text-xs text-purple-600 mt-0.5">Updated: {{ $courier->updated_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </li>
                    @endif
                </ol>
            @endif
        </div>
    </div>

    <!-- Attachments -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-4">
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-800 flex items-center">
                <i class="fa-solid fa-paperclip mr-2 text-indigo-500"></i> Attachments
                <span class="ml-2 text-xs font-normal text-gray-500">({{ $courier->attachments->count() }} file(s))</span>
            </h2>
        </div>
        <div class="p-6">
            @if($courier->attachments->isEmpty())
                <p class="text-sm text-gray-500 italic mb-4">No attachments yet.</p>
            @else
                <ul class="divide-y divide-gray-100 mb-4">
                    @foreach($courier->attachments as $att)
                        <li class="flex items-center justify-between py-2">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-file text-indigo-400"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $att->original_name }}</p>
                                    <p class="text-xs text-gray-400">{{ number_format($att->size / 1024, 1) }} KB &middot; {{ $att->uploader->name ?? '—' }} &middot; {{ $att->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ Storage::disk('public')->url($att->stored_name) }}" target="_blank"
                                   class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-600 text-xs font-medium rounded hover:bg-blue-600 hover:text-white transition-colors">
                                    <i class="fa-solid fa-download mr-1"></i> Download
                                </a>
                                @if($att->uploaded_by === Auth::id() || in_array(Auth::user()->role->slug, ['center-admin', 'super-admin']))
                                    <form action="{{ route('attachments.destroy', $att->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this attachment?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-50 text-red-600 text-xs font-medium rounded hover:bg-red-600 hover:text-white transition-colors">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
            <form action="{{ route('attachments.store') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
                @csrf
                <input type="hidden" name="attachable_type" value="courier">
                <input type="hidden" name="attachable_id" value="{{ $courier->id }}">
                <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" required
                    class="block w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded hover:bg-indigo-700 transition-colors whitespace-nowrap">
                    <i class="fa-solid fa-upload mr-1"></i> Upload
                </button>
            </form>
            <p class="text-xs text-gray-400 mt-2">Allowed: PDF, images, Word, Excel. Max 10MB.</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 transition">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back
        </a>
        <div class="flex items-center gap-2">
            <label class="flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer select-none">
                <input type="checkbox" id="print-include-attachments" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                Include attachments list
            </label>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 shadow-sm transition">
                <i class="fa-solid fa-print mr-2"></i> Print Receipt
            </button>
        </div>

        @php $user = Auth::user(); @endphp

        @if($courier->status !== 'dispatched')
            @if($user->role->slug === 'officer' && $courier->status === 'transferred' && $courier->department_id === $user->department_id)
                @if($latestTransfer && !$latestTransfer->received_at)
                    @php
                        $canReceive = false;
                        if ($latestTransfer->transferable_type === 'user' && $latestTransfer->transferable_id === $user->id) {
                            $canReceive = true;
                        } elseif ($latestTransfer->transferable_type === 'department' && $latestTransfer->transferable_id === $user->department_id) {
                            $canReceive = true;
                        }
                    @endphp
                    @if($canReceive)
                        <form action="{{ route('courier-transfers.markReceived', $latestTransfer->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 shadow-sm transition">
                                <i class="fa-solid fa-check mr-2"></i> Mark Received
                            </button>
                        </form>
                    @endif
                @endif
            @endif

            @php
                $canOfficerTransfer = $user->role->slug === 'officer' 
                    && $courier->department_id === $user->department_id 
                    && ($courier->assigned_user_id === $user->id || is_null($courier->assigned_user_id))
                    && in_array($courier->status, ['transferred','received']);
            @endphp

            @php
                $canStaffTransfer = $user->role->slug === 'staff-user'
                    && $courier->center_id === $user->center_id
                    && (is_null($courier->department_id) || !is_null($courier->reverted_by_user_id));
                $canAdminTransfer = in_array($user->role->slug, ['super-admin', 'center-admin']);
            @endphp
            @if($canAdminTransfer || $canOfficerTransfer || $canStaffTransfer)
                <a href="{{ route('couriers.transferForm', $courier->id) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md transition">
                    <i class="fa-solid fa-share mr-2"></i> Transfer
                </a>
            @endif

            {{-- Return to R&I is now handled via the Transfer form's "Return to R&I" tab --}}
            @if(in_array($user->role->slug, ['super-admin', 'center-admin', 'staff-user']) && $courier->status === 'transferred' && is_null($courier->department_id))
                <form action="{{ route('couriers.markReceivedDirect', $courier->id) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 shadow-sm transition">
                        <i class="fa-solid fa-inbox mr-2"></i> Confirm Receipt
                    </button>
                </form>
            @endif
            @if(in_array($user->role->slug, ['super-admin', 'center-admin', 'staff-user']) && $courier->status === 'received' && is_null($courier->department_id))
                <form action="{{ route('couriers.markDispatched', $courier->id) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 shadow-sm transition">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Mark Dispatched
                    </button>
                </form>
            @endif
        @endif
    </div>
</div>

@push('scripts')
<style>
#print-receipt { display: none; }
#print-attachments-section { display: none; }
@@media print {
    body * { visibility: hidden !important; }
    #print-receipt { display: block !important; visibility: visible !important; position: absolute; top: 0; left: 0; width: 100%; background: white; z-index: 9999; padding: 30px; }
    #print-receipt * { visibility: visible !important; }
    @@page { margin: 0.5cm; size: A4; }
}
</style>
<div id="print-receipt">
    <div style="font-family: Arial, sans-serif; max-width: 700px; margin: 0 auto; border: 1px solid #ccc; padding: 24px;">
        <div style="text-align:center; border-bottom: 2px solid #333; padding-bottom: 12px; margin-bottom: 16px;">
            <h1 style="font-size: 17px; font-weight: bold; margin: 0; letter-spacing: 0.5px;">Inter Boards Coordination Commission</h1>
            <p style="font-size: 13px; margin: 4px 0; color: #444;">Courier Receipt</p>
            <p style="font-size: 11px; color: #666; margin: 0;">{{ $courier->center->name ?? '' }} &mdash; Printed: {{ now()->format('d M Y, h:i A') }}</p>
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:13px; margin-bottom:16px;">
            <tr><td style="padding:5px 8px; font-weight:bold; width:38%; border-bottom:1px solid #eee; color:#555;">Tracking ID</td><td style="padding:5px 8px; border-bottom:1px solid #eee; font-family:monospace; font-weight:bold;">{{ $courier->tracking_id }}</td></tr>
            <tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Status</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ ucfirst($courier->status) }}</td></tr>
            <tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Courier Company</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $courier->courier_company }}</td></tr>
            <tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Type</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ ucfirst(str_replace('_', ' ', $courier->type ?? '—')) }}</td></tr>
            <tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Sender Name</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $courier->sender_name }}</td></tr>
            @if($courier->sender_contact)<tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Contact</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $courier->sender_contact }}</td></tr>@endif
            <tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Received By (R&amp;I)</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $courier->receivedBy->name ?? '—' }}</td></tr>
            <tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Currently With</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $courier->department->name ?? '—' }}</td></tr>
            <tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Date Registered</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $courier->created_at->format('d M Y, h:i A') }}</td></tr>
        </table>

        @php $printTransfers = $courier->transfers()->with('transferable', 'transferredBy')->orderBy('created_at')->get(); @endphp
        @if($printTransfers->isNotEmpty())
        <div style="margin-bottom:16px;">
            <h2 style="font-size:13px; font-weight:bold; border-bottom:1px solid #333; padding-bottom:4px; margin-bottom:8px; color:#333;">Transfer Trail</h2>
            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead><tr style="background:#f5f5f5;"><th style="padding:4px 8px; text-align:left; font-weight:bold; border-bottom:1px solid #ddd;">#</th><th style="padding:4px 8px; text-align:left; font-weight:bold; border-bottom:1px solid #ddd;">Transferred To</th><th style="padding:4px 8px; text-align:left; font-weight:bold; border-bottom:1px solid #ddd;">By / Notes</th><th style="padding:4px 8px; text-align:left; font-weight:bold; border-bottom:1px solid #ddd;">Date</th><th style="padding:4px 8px; text-align:left; font-weight:bold; border-bottom:1px solid #ddd;">Acknowledged</th></tr></thead>
                <tbody>
                    @foreach($printTransfers as $i => $tr)
                    <tr><td style="padding:4px 8px; border-bottom:1px solid #eee;">{{ $i+1 }}</td><td style="padding:4px 8px; border-bottom:1px solid #eee;">{{ $tr->transferable->name ?? 'R&amp;I Staff' }}{{ $tr->is_for_dispatch ? ' ★dispatch' : '' }}</td><td style="padding:4px 8px; border-bottom:1px solid #eee;">{{ $tr->transferredBy->name ?? '—' }}{{ $tr->notes ? ' — "'.$tr->notes.'"' : '' }}</td><td style="padding:4px 8px; border-bottom:1px solid #eee; white-space:nowrap;">{{ $tr->created_at->format('d M Y, h:i A') }}</td><td style="padding:4px 8px; border-bottom:1px solid #eee;">{{ $tr->received_at ? \Carbon\Carbon::parse($tr->received_at)->format('d M Y') : ($tr->is_reverted ? 'Reverted to R&I' : 'Pending') }}</td></tr>
                    @endforeach
                    @if($courier->status === 'dispatched')
                    <tr style="background:#f3e8ff;"><td style="padding:4px 8px;">—</td><td style="padding:4px 8px; font-weight:bold;" colspan="3">✈ Dispatched (outgoing processed)</td><td style="padding:4px 8px; white-space:nowrap;">{{ $courier->updated_at->format('d M Y, h:i A') }}</td></tr>
                    @elseif($courier->reverted_by_user_id)
                    <tr style="background:#fee2e2;"><td style="padding:4px 8px;">—</td><td style="padding:4px 8px; font-weight:bold;" colspan="3">↩ Reverted to R&amp;I — {{ $courier->revertedBy->name ?? '—' }}{{ $courier->comments ? ': "'.$courier->comments.'"' : '' }}</td><td style="padding:4px 8px; white-space:nowrap;">{{ $courier->updated_at->format('d M Y, h:i A') }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        @endif

        <div id="print-attachments-section" style="margin-bottom:16px;">
            <h2 style="font-size:13px; font-weight:bold; border-bottom:1px solid #333; padding-bottom:4px; margin-bottom:8px; color:#333;">Attachments</h2>
            @if($courier->attachments->isNotEmpty())
            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead><tr style="background:#f5f5f5;"><th style="padding:4px 8px; text-align:left; border-bottom:1px solid #ddd;">File Name</th><th style="padding:4px 8px; text-align:left; border-bottom:1px solid #ddd;">Uploaded By</th><th style="padding:4px 8px; text-align:left; border-bottom:1px solid #ddd;">Date</th></tr></thead>
                <tbody>
                    @foreach($courier->attachments as $att)
                    <tr><td style="padding:4px 8px; border-bottom:1px solid #eee;">{{ $att->original_name }}</td><td style="padding:4px 8px; border-bottom:1px solid #eee;">{{ $att->uploader->name ?? '—' }}</td><td style="padding:4px 8px; border-bottom:1px solid #eee;">{{ $att->created_at->format('d M Y') }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="font-size:12px; color:#888;">No attachments.</p>
            @endif
        </div>
    </div>
</div>
<script>
document.getElementById('print-include-attachments').addEventListener('change', function() {
    document.getElementById('print-attachments-section').style.display = this.checked ? 'block' : 'none';
});
</script>
@endpush
@endsection
