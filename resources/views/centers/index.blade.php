@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="px-6 py-4 bg-[#00583A] border-b border-[#004028]">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">Centers Management</h1>
                    <p class="text-sm text-white mt-1">Manage all distribution centers from here.</p>
                </div>
                <a href="{{ route('centers.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-plus mr-2"></i> Add New Center
                </a>
            </div>
        </div>
        
        <div class="p-6 bg-white border-b border-gray-200">

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded-r shadow-sm" role="alert">
                    <div class="flex">
                        <div class="py-1"><i class="fa-solid fa-circle-check mr-2"></i></div>
                        <div>
                            <p class="font-bold">Success</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#007bff] text-white">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">
                                Center Name
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">
                                City
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">
                                Code
                            </th>
                            <th scope="col" class="px-6 py-4 text-right pr-20 text-xs font-bold uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($centers as $center)
                            <tr class="bg-gray-50 hover:bg-gray-100 transition-colors duration-200 border-b border-gray-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $center->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-800">
                                    {{ $center->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                    {{ $center->city }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                    {{ $center->code }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                    <a href="{{ route('centers.show', $center->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('centers.edit', $center->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-amber-100 text-amber-600 hover:bg-amber-500 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" title="Edit">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <form action="{{ route('centers.destroy', $center->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition-all duration-200 shadow-sm hover:shadow-md" onclick="return confirm('Are you sure you want to delete this center?')" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-building-circle-check text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-lg font-medium">No centers found.</p>
                                        <p class="text-sm text-gray-400">Get started by adding a new center.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                 {{-- Pagination links if needed --}}
                 {{-- {{ $centers->links() }} --}}
            </div>
        </div>
    </div>
</div>
@endsection