@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028]">
            <h1 class="text-2xl font-bold text-white">Add New Department</h1>
            <p class="text-sm text-white mt-1">Enter the details for the new department.</p>
        </div>

        <div class="p-5 bg-white">

            <form method="POST" action="{{ route('departments.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">{{ __('Name') }}</label>
                            <input id="name" type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-500 @enderror"
                                name="name" value="{{ old('name') }}" required autofocus placeholder="Enter department name">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <!-- Center -->
                        <div>
                            <label for="center_id" class="block font-medium text-sm text-gray-700">{{ __('Center') }}</label>
                            <select id="center_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('center_id') border-red-500 @enderror"
                                name="center_id" required>
                                <option value="">Select Center</option>
                                @foreach ($centers as $center)
                                    <option value="{{ $center->id }}"
                                        {{ old('center_id') == $center->id ? 'selected' : '' }}>
                                        {{ $center->name }}</option>
                                @endforeach
                            </select>
                            @error('center_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4 pt-3 border-t border-gray-200 space-x-4">
                    <a href="{{ route('departments.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                        {{ __('Save Department') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
