@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-5 bg-white border-b border-gray-200">
            <div class="mb-2 border-b border-gray-200 pb-2">
                <h1 class="text-2xl font-bold text-gray-800">Create New Dispatch</h1>
                <p class="text-sm text-gray-500 mt-1">Enter the details to create a new outgoing dispatch.</p>
            </div>

            <form method="POST" action="{{ route('dispatches.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Left Column -->
                    <div class="space-y-3">
                        <!-- Applicant Name -->
                        <div>
                            <label for="applicant_name" class="block font-medium text-sm text-gray-700">{{ __('Applicant Name') }}</label>
                            <input id="applicant_name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('applicant_name') border-red-500 @enderror" name="applicant_name" value="{{ old('applicant_name') }}" required autofocus>
                            @error('applicant_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Father Name -->
                        <div>
                            <label for="father_name" class="block font-medium text-sm text-gray-700">{{ __('Father Name') }}</label>
                            <input id="father_name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('father_name') border-red-500 @enderror" name="father_name" value="{{ old('father_name') }}" required>
                            @error('father_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Applicant Contact Number -->
                        <div>
                            <label for="applicant_contact" class="block font-medium text-sm text-gray-700">{{ __('Applicant Contact Number') }}</label>
                            <input id="applicant_contact" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('applicant_contact') border-red-500 @enderror" name="applicant_contact" value="{{ old('applicant_contact') }}" required>
                            @error('applicant_contact')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-3">
                        <!-- Case Number -->
                        <div>
                            <label for="case_number" class="block font-medium text-sm text-gray-700">{{ __('Case Number') }}</label>
                            <input id="case_number" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('case_number') border-red-500 @enderror" name="case_number" value="{{ old('case_number') }}" required>
                            @error('case_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dispatch Courier Company -->
                        <div>
                            <label for="dispatch_courier_company" class="block font-medium text-sm text-gray-700">{{ __('Dispatch Courier Company') }}</label>
                            <input id="dispatch_courier_company" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-100 text-gray-500 cursor-not-allowed" name="dispatch_courier_company" value="TCS" readonly>
                            @error('dispatch_courier_company')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dispatched From (Officer/Department) -->
                        <div>
                            <label for="dispatched_from" class="block font-medium text-sm text-gray-700">{{ __('Dispatched From (Officer/Department)') }}</label>
                            <input id="dispatched_from" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('dispatched_from') border-red-500 @enderror" name="dispatched_from" value="{{ old('dispatched_from') }}" required>
                            @error('dispatched_from')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4 pt-3 border-t border-gray-200 space-x-4">
                    <a href="{{ route('dispatches.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                        {{ __('Create Dispatch') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection