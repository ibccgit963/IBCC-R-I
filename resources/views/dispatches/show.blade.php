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

    <!-- Header Card -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-4">
        <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028] flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white">Dispatch Details</h1>
                <p class="text-sm text-white mt-0.5 font-mono">{{ $dispatch->case_number ?? '—' }}</p>
            </div>
            @php
                $statusColor = $dispatch->status === 'dispatched'
                    ? 'bg-green-100 text-green-800'
                    : ($dispatch->received_at ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800');
                $statusLabel = $dispatch->status === 'dispatched'
                    ? 'Dispatched'
                    : ($dispatch->received_at ? 'Received by R&I' : 'Pending');
            @endphp
            <span class="px-3 py-1 text-sm font-bold rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Applicant Name</p>
                    <p class="font-medium text-gray-800">{{ $dispatch->applicant_name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Father Name</p>
                    <p class="font-medium text-gray-800">{{ $dispatch->father_name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Contact</p>
                    <p class="font-medium text-gray-800">{{ $dispatch->applicant_contact ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Case Number</p>
                    <p class="font-medium text-gray-800 font-mono">{{ $dispatch->case_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Courier Company</p>
                    <p class="font-medium text-gray-800">{{ $dispatch->dispatch_courier_company ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Dispatched From</p>
                    <p class="font-medium text-gray-800">{{ $dispatch->dispatched_from ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Tracking ID</p>
                    <p class="font-medium text-gray-800 font-mono">{{ $dispatch->tracking_id ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Center</p>
                    <p class="font-medium text-gray-800">{{ $dispatch->center->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Dispatched By</p>
                    <p class="font-medium text-gray-800">{{ $dispatch->dispatchedBy->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Created At</p>
                    <p class="font-medium text-gray-800">{{ $dispatch->created_at->format('d M Y, h:i A') }}</p>
                </div>
                @if($dispatch->received_at)
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Received At</p>
                    <p class="font-medium text-gray-800">{{ $dispatch->received_at->format('d M Y, h:i A') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Attachments -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-4">
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-800 flex items-center">
                <i class="fa-solid fa-paperclip mr-2 text-indigo-500"></i> Attachments
                <span class="ml-2 text-xs font-normal text-gray-500">({{ $dispatch->attachments->count() }} file(s))</span>
            </h2>
        </div>
        <div class="p-6">
            @if($dispatch->attachments->isEmpty())
                <p class="text-sm text-gray-500 italic mb-4">No attachments yet.</p>
            @else
                <ul class="divide-y divide-gray-100 mb-4">
                    @foreach($dispatch->attachments as $att)
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
                <input type="hidden" name="attachable_type" value="dispatch">
                <input type="hidden" name="attachable_id" value="{{ $dispatch->id }}">
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
        <a href="{{ route('dispatches.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 transition">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back to Dispatches
        </a>
        <a href="{{ route('dispatches.edit', $dispatch->id) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 shadow-sm transition">
            <i class="fa-solid fa-pencil mr-2"></i> Edit
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
            <p style="font-size: 13px; margin: 4px 0; color: #444;">Dispatch Receipt</p>
            <p style="font-size: 11px; color: #666; margin: 0;">{{ $dispatch->center->name ?? '' }} &mdash; Printed: {{ now()->format('d M Y, h:i A') }}</p>
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:13px; margin-bottom:16px;">
            <tr><td style="padding:5px 8px; font-weight:bold; width:38%; border-bottom:1px solid #eee; color:#555;">Case Number</td><td style="padding:5px 8px; border-bottom:1px solid #eee; font-family:monospace; font-weight:bold;">{{ $dispatch->case_number ?? '—' }}</td></tr>
            <tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Status</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $dispatch->status === 'dispatched' ? 'Dispatched' : ($dispatch->received_at ? 'Received by R&I' : 'Pending') }}</td></tr>
            <tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Applicant Name</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $dispatch->applicant_name }}</td></tr>
            @if($dispatch->father_name)<tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Father Name</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $dispatch->father_name }}</td></tr>@endif
            @if($dispatch->applicant_contact)<tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Contact</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $dispatch->applicant_contact }}</td></tr>@endif
            <tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Courier Company</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $dispatch->dispatch_courier_company ?? '—' }}</td></tr>
            @if($dispatch->tracking_id)<tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Tracking ID</td><td style="padding:5px 8px; border-bottom:1px solid #eee; font-family:monospace;">{{ $dispatch->tracking_id }}</td></tr>@endif
            <tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Dispatched From</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $dispatch->dispatched_from ?? '—' }}</td></tr>
            <tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Dispatched By</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $dispatch->dispatchedBy->name ?? '—' }}</td></tr>
            <tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Date Created</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $dispatch->created_at->format('d M Y, h:i A') }}</td></tr>
            @if($dispatch->received_at)<tr><td style="padding:5px 8px; font-weight:bold; border-bottom:1px solid #eee; color:#555;">Received At (R&amp;I)</td><td style="padding:5px 8px; border-bottom:1px solid #eee;">{{ $dispatch->received_at->format('d M Y, h:i A') }}</td></tr>@endif
        </table>

        <div id="print-attachments-section" style="margin-bottom:16px;">
            <h2 style="font-size:13px; font-weight:bold; border-bottom:1px solid #333; padding-bottom:4px; margin-bottom:8px; color:#333;">Attachments</h2>
            @if($dispatch->attachments->isNotEmpty())
            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead><tr style="background:#f5f5f5;"><th style="padding:4px 8px; text-align:left; border-bottom:1px solid #ddd;">File Name</th><th style="padding:4px 8px; text-align:left; border-bottom:1px solid #ddd;">Uploaded By</th><th style="padding:4px 8px; text-align:left; border-bottom:1px solid #ddd;">Date</th></tr></thead>
                <tbody>
                    @foreach($dispatch->attachments as $att)
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
