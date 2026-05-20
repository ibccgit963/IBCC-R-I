@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028]">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">Internal Dispatch</h1>
                    <p class="text-sm text-white mt-1">Manage internal dispatch logs and details.</p>
                </div>
                <div>
                    <a href="{{ route('dispatch-logs.export', request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-white text-[#00583A] rounded-lg font-semibold text-sm hover:bg-gray-100 transition-colors shadow-sm">
                        <i class="fa-solid fa-file-export mr-2"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Create Dispatch Log Form Section -->
        <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <div class="mb-4 border-b border-gray-200 pb-2">
                <h2 class="text-lg font-bold text-gray-800">Add New Internal Dispatch</h2>
                <p class="text-sm text-gray-500">Enter details and press Enter to save.</p>
            </div>

            <form method="POST" action="{{ route('dispatch-logs.store') }}" id="dispatch-form">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
                        <input id="name" type="text" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" required autofocus>
                    </div>
                    <div>
                        <label for="file_no" class="block font-medium text-sm text-gray-700">File No.</label>
                        <input id="file_no" type="text" name="file_no" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" required>
                    </div>
                    <div class="md:col-span-2">
                        <label for="address" class="block font-medium text-sm text-gray-700">Address</label>
                        <input id="address" type="text" name="address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" required>
                    </div>
                    <div>
                        <label for="subject" class="block font-medium text-sm text-gray-700">Subject</label>
                        <input id="subject" type="text" name="subject" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" required>
                    </div>
                    <div class="md:col-span-3">
                        <label for="remarks" class="block font-medium text-sm text-gray-700">Remarks</label>
                        <textarea id="remarks" name="remarks" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="hidden"></button>
                    <button type="button" onclick="document.getElementById('dispatch-form').submit()" class="inline-flex items-center px-4 py-2 bg-[#00583A] text-white rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-[#004028] transition-all">
                        Save Record
                    </button>
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="mx-6 mb-6 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r shadow-sm">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="px-6 mb-4">
            <form method="GET" action="{{ route('dispatch-logs.index') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">From Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">To Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors h-[38px]">
                    <i class="fa-solid fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('dispatch-logs.index') }}" class="inline-flex items-center px-4 py-2 text-gray-500 hover:text-gray-700 h-[38px]">
                    Reset
                </a>
            </form>
        </div>

        <div class="overflow-x-auto px-6 pb-6">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sr #</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">File No</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Center</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $log->serial_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $log->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $log->subject }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $log->file_no }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $log->center->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $log->created_at->format('d-M-Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('dispatch-form').addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            this.submit();
        }
    });
</script>
@endsection
