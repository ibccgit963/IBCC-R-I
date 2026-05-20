<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            IBCC Parcel Office - {{ Auth::user()->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow" role="alert">
                    <p class="font-bold">Success</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Receive New Courier</h3>
                    <form action="{{ route('courier.store') }}" method="POST"
                        class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Tracking ID</label>
                            <input type="text" name="tracking_id" placeholder="e.g. TCS-123456"
                                class="w-full border rounded p-2" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Courier Company</label>
                            <select name="courier_company" class="w-full border rounded p-2">
                                <option>TCS</option>
                                <option>Leopards</option>
                                <option>M&P</option>
                                <option>Post Office (UMS)</option>
                                <option>DCS</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Category</label>
                            <select name="category" class="w-full border rounded p-2">
                                <option>Attestation</option>
                                <option>Equivalence</option>
                                <option>Department Officer</option>
                                <option>General / Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Sender Name</label>
                            <input type="text" name="sender_name" placeholder="Name on parcel"
                                class="w-full border rounded p-2" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Sender CNIC (Optional)</label>
                            <input type="text" name="sender_cnic" placeholder="00000-0000000-0"
                                class="w-full border rounded p-2">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Sender Contact</label>
                            <input type="text" name="sender_contact" placeholder="0300-1234567"
                                class="w-full border rounded p-2" required>
                        </div>

                        <div class="md:col-span-3 text-right">
                            <button type="submit"
                                class="bg-blue-600 text-white rounded px-6 py-2 hover:bg-blue-700 font-bold shadow">
                                + Receive Courier
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-12">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Recent Incoming Couriers</h3>
                    <table class="min-w-full border-collapse border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2 text-left">Tracking ID</th>
                                <th class="border p-2 text-left">Sender</th>
                                <th class="border p-2 text-left">Company</th>
                                <th class="border p-2 text-left">Center</th>
                                <th class="border p-2 text-left">Date</th>
                                <th class="border p-2 text-left">Status</th>
                                <th class="border p-2 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($couriers as $courier)
                                <tr class="hover:bg-gray-50">
                                    <td class="border p-2 font-mono">{{ $courier->tracking_id }}</td>
                                    <td class="border p-2">{{ $courier->sender_name }}</td>
                                    <td class="border p-2">{{ $courier->courier_company }}</td>
                                    <td class="border p-2">{{ $courier->center->name ?? 'Head Office' }}</td>
                                    <td class="border p-2">{{ $courier->created_at->format('d M Y') }}</td>
                                    <td class="border p-2">
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">
                                            {{ ucfirst($courier->status) }}
                                        </span>
                                    </td>
                                    <td class="border p-2">
                                        @if ($courier->status == 'received')
                                            <a href="{{ route('courier.transfer', $courier->id) }}"
                                                class="bg-indigo-600 text-white text-xs px-3 py-1 rounded hover:bg-indigo-700">
                                                Transfer ->
                                            </a>
                                        @else
                                            <span class="text-gray-400 text-xs">Transferred</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($couriers->isEmpty())
                        <p class="text-gray-500 text-center mt-4">No couriers received yet.</p>
                    @endif
                </div>
            </div>

            <div class="border-t-4 border-gray-300 my-8"></div>

            <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-blue-200">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-blue-800">Dispatch Outgoing Document</h3>
                    <form action="{{ route('dispatch.store') }}" method="POST"
                        class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Applicant Name</label>
                            <input type="text" name="applicant_name" placeholder="Name"
                                class="w-full border rounded p-2" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Father Name</label>
                            <input type="text" name="father_name" placeholder="Father Name"
                                class="w-full border rounded p-2" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Contact Number</label>
                            <input type="text" name="contact_number" placeholder="Applicant Contact #"
                                class="w-full border rounded p-2" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Case Number</label>
                            <input type="text" name="case_number" placeholder="e.g. IBCC-2025-001"
                                class="w-full border rounded p-2 bg-yellow-50" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Dispatch Tracking ID</label>
                            <input type="text" name="tracking_id" placeholder="TCS/Leopard ID"
                                class="w-full border rounded p-2" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Courier Service</label>
                            <select name="courier_company" class="w-full border rounded p-2">
                                <option>TCS</option>
                                <option>Leopards</option>
                                <option>Post Office</option>
                            </select>
                        </div>

                        <div class="md:col-span-3 text-right">
                            <button type="submit"
                                class="bg-blue-800 text-white rounded px-6 py-2 hover:bg-blue-900 font-bold shadow">
                                Send / Dispatch ->
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Recent Outgoing Dispatches</h3>
                    <table class="min-w-full border-collapse border border-gray-200">
                        <thead class="bg-blue-50 text-blue-900">
                            <tr>
                                <th class="border p-2 text-left">Tracking ID</th>
                                <th class="border p-2 text-left">Applicant</th>
                                <th class="border p-2 text-left">Case #</th>
                                <th class="border p-2 text-left">Company</th>
                                <th class="border p-2 text-left">Dispatched Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dispatches as $dispatch)
                                <tr class="hover:bg-gray-50">
                                    <td class="border p-2 font-mono">{{ $dispatch->tracking_id }}</td>
                                    <td class="border p-2">{{ $dispatch->applicant_name }}</td>
                                    <td class="border p-2">{{ $dispatch->case_number }}</td>
                                    <td class="border p-2">{{ $dispatch->courier_company }}</td>
                                    <td class="border p-2">{{ $dispatch->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if ($dispatches->isEmpty())
                        <p class="text-gray-500 text-center mt-4">No documents dispatched yet.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
