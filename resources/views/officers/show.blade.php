@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-5 bg-white border-b border-gray-200">
            <div class="mb-2 border-b border-gray-200 pb-2">
                <h1 class="text-2xl font-bold text-gray-800">Officer Details: {{ $officer->name }}</h1>
                <p class="text-sm text-gray-500 mt-1">Detailed information about the officer.</p>
            </div>

            <div class="space-y-4 text-gray-700">
                <div>
                    <p class="font-bold">Name:</p>
                    <p>{{ $officer->name }}</p>
                </div>
                <div>
                    <p class="font-bold">Designation:</p>
                    <p>{{ $officer->designation ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="font-bold">Contact:</p>
                    <p>{{ $officer->contact ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="font-bold">Department:</p>
                    <p>{{ $officer->department->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="font-bold">Center:</p>
                    <p>{{ $officer->center->name ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="mt-8 mb-4 border-b border-gray-200 pb-2">
                <h2 class="text-xl font-bold text-gray-800">User Account Details</h2>
            </div>

            <div class="space-y-4 text-gray-700">
                <div>
                    <p class="font-bold">User Email:</p>
                    <p>{{ $officer->user->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="font-bold">Employee ID:</p>
                    <p>{{ $officer->user->employee_id ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="font-bold">Role:</p>
                    <p>{{ $officer->user->role->name ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="flex items-center justify-end mt-6 pt-3 border-t border-gray-200">
                <a href="{{ route('officers.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
                    Back to Officers
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
