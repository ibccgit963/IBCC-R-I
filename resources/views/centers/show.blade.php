@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028]">
            <h1 class="text-2xl font-bold text-white">Center Details</h1>
            <p class="text-sm text-white mt-1">View the details of the distribution center.</p>
        </div>

        <div class="p-5 bg-white">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!-- Left Column -->
                <div class="space-y-3">
                    <!-- Name -->
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Center Name</label>
                        <div class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 sm:text-sm text-gray-700">
                            {{ $center->name }}
                        </div>
                    </div>

                    <!-- ID -->
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Center ID</label>
                        <div class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 sm:text-sm text-gray-700">
                            {{ $center->id }}
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-3">
                    <!-- Created At -->
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Created At</label>
                        <div class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 sm:text-sm text-gray-700">
                            {{ $center->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>

                    <!-- Updated At -->
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Updated At</label>
                        <div class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 sm:text-sm text-gray-700">
                            {{ $center->updated_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-4 pt-3 border-t border-gray-200">
                <a href="{{ route('centers.index') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                    Back to Centers
                </a>
            </div>
        </div>
    </div>
</div>
@endsection