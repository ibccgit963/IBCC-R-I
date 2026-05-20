@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028]">
        <h1 class="text-2xl font-bold text-white">Edit Center</h1>
        <p class="text-sm text-white mt-1">Update the distribution center details.</p>
    </div>

    <div class="p-6 bg-white">

        <form method="POST" action="{{ route('centers.update', $center->id) }}">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <div class="mt-1">
                        <input type="text" name="name" id="name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror" value="{{ old('name', $center->name) }}" required autofocus>
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                    <div class="mt-1">
                        <input type="text" name="code" id="code" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('code') border-red-500 @enderror" value="{{ old('code', $center->code) }}" required>
                    </div>
                    @error('code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="default_courier_company" class="block text-sm font-medium text-gray-700">Default Courier Company</label>
                    <p class="text-xs text-gray-500 mb-1">This company name is pre-filled in all outgoing dispatch forms for this center. Staff can still change it per record.</p>
                    <div class="mt-1">
                        <input type="text" name="default_courier_company" id="default_courier_company"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            value="{{ old('default_courier_company', $center->default_courier_company) }}"
                            placeholder="e.g. TCS, DHL, Leopards">
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('centers.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Update Center
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection