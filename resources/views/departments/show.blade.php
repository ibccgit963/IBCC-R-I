@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028]">
            <h1 class="text-2xl font-bold text-white">Department Details: {{ $department->name }}</h1>
            <p class="text-sm text-white mt-1">Detailed information about the department.</p>
        </div>

        <div class="p-5 bg-white">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!-- Left Column -->
                <div class="space-y-3">
                    <!-- Name -->
                    <div>
                        <label class="block font-medium text-sm text-gray-700 font-bold">Name</label>
                        <div class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 sm:text-sm text-gray-700">
                            {{ $department->name }}
                        </div>
                    </div>

                    <!-- Center -->
                    <div>
                        <label class="block font-medium text-sm text-gray-700 font-bold">Center</label>
                        <div class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 sm:text-sm text-gray-700">
                            {{ $department->center->name ?? 'N/A' }}
                        </div>
                    </div>

                    <!-- ID -->
                    <div>
                        <label class="block font-medium text-sm text-gray-700 font-bold">Department ID</label>
                        <div class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 sm:text-sm text-gray-700">
                            {{ $department->id }}
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-3">
                    <!-- Created At -->
                    <div>
                        <label class="block font-medium text-sm text-gray-700 font-bold">Created At</label>
                        <div class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 sm:text-sm text-gray-700">
                            {{ $department->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>

                    <!-- Updated At -->
                    <div>
                        <label class="block font-medium text-sm text-gray-700 font-bold">Updated At</label>
                        <div class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 sm:text-sm text-gray-700">
                            {{ $department->updated_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-6 pt-3 border-t border-gray-200">
                <a href="{{ route('departments.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
                    Back to Departments
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
