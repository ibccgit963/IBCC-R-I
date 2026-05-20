@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028]">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">Activity Log</h1>
                    <p class="text-sm text-white mt-1">Audit trail of all actions performed in the system.</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @if(Auth::user()->role->slug === 'super-admin')
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Center</label>
                        <select name="center_id" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Centers</option>
                            @foreach($centers as $center)
                                <option value="{{ $center->id }}" {{ request('center_id') == $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Action</label>
                    <select name="action" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Subject Type</label>
                    <select name="subject_type" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Types</option>
                        <option value="Courier" {{ request('subject_type') === 'Courier' ? 'selected' : '' }}>Courier</option>
                        <option value="Dispatch" {{ request('subject_type') === 'Dispatch' ? 'selected' : '' }}>Dispatch</option>
                        <option value="User" {{ request('subject_type') === 'User' ? 'selected' : '' }}>User</option>
                        <option value="Center" {{ request('subject_type') === 'Center' ? 'selected' : '' }}>Center</option>
                        <option value="Department" {{ request('subject_type') === 'Department' ? 'selected' : '' }}>Department</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="{{ Auth::user()->role->slug === 'super-admin' ? '' : 'md:col-span-2' }} lg:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by subject, notes, or user name..." class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#00583A] text-white text-sm font-medium rounded-md hover:bg-[#004028] transition-colors shadow-sm">
                        <i class="fa-solid fa-filter mr-2"></i> Filter
                    </button>
                    <a href="{{ route('activity-logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition-colors shadow-sm">
                        <i class="fa-solid fa-xmark mr-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Results count -->
        <div class="px-6 py-2 bg-white border-b border-gray-100 text-xs text-gray-500">
            Showing {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#007bff] text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Time</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">User</th>
                        @if(Auth::user()->role->slug === 'super-admin')
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Center</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Subject Label</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Notes</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Changes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                                <span title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                    {{ $log->created_at->format('d M Y') }}<br>
                                    <span class="text-gray-400">{{ $log->created_at->format('H:i') }}</span>
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800">
                                {{ $log->user->name ?? '—' }}
                                @if($log->user)
                                    <span class="block text-xs text-gray-400">{{ $log->user->role->slug ?? '' }}</span>
                                @endif
                            </td>
                            @if(Auth::user()->role->slug === 'super-admin')
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $log->center->name ?? '—' }}</td>
                            @endif
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $actionColors = [
                                        'created'    => 'bg-green-100 text-green-800',
                                        'updated'    => 'bg-blue-100 text-blue-800',
                                        'deleted'    => 'bg-red-100 text-red-800',
                                        'transferred'=> 'bg-purple-100 text-purple-800',
                                        'received'   => 'bg-cyan-100 text-cyan-800',
                                        'dispatched' => 'bg-indigo-100 text-indigo-800',
                                        'reverted'   => 'bg-orange-100 text-orange-800',
                                    ];
                                    $actionIcons = [
                                        'created'    => 'fa-plus',
                                        'updated'    => 'fa-pen',
                                        'deleted'    => 'fa-trash',
                                        'transferred'=> 'fa-arrows-rotate',
                                        'received'   => 'fa-inbox',
                                        'dispatched' => 'fa-paper-plane',
                                        'reverted'   => 'fa-rotate-left',
                                    ];
                                    $color = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800';
                                    $icon  = $actionIcons[$log->action] ?? 'fa-circle-info';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $color }}">
                                    <i class="fa-solid {{ $icon }}"></i> {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $log->subject_type }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-800">{{ $log->subject_label }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate" title="{{ $log->notes }}">{{ $log->notes ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                @if($log->old_values || $log->new_values)
                                    <details class="cursor-pointer">
                                        <summary class="text-indigo-600 hover:text-indigo-800 font-medium select-none">View diff</summary>
                                        <div class="mt-2 space-y-1">
                                            @if($log->old_values)
                                                <div class="bg-red-50 rounded p-2">
                                                    <p class="font-semibold text-red-700 mb-1">Before:</p>
                                                    @foreach($log->old_values as $k => $v)
                                                        <div class="flex gap-1"><span class="text-red-600 font-medium">{{ $k }}:</span><span>{{ is_array($v) ? json_encode($v) : $v }}</span></div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if($log->new_values)
                                                <div class="bg-green-50 rounded p-2">
                                                    <p class="font-semibold text-green-700 mb-1">After:</p>
                                                    @foreach($log->new_values as $k => $v)
                                                        <div class="flex gap-1"><span class="text-green-600 font-medium">{{ $k }}:</span><span>{{ is_array($v) ? json_encode($v) : $v }}</span></div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </details>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->role->slug === 'super-admin' ? 8 : 7 }}" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-list-check text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-lg font-medium">No activity logs found.</p>
                                    <p class="text-sm text-gray-400">Try adjusting the filters above.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="mt-4 px-6 pb-4">
                {{ $logs->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
