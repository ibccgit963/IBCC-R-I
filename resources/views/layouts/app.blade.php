<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'IBCC Portal') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .sidebar-bg { background-color: #00583A; }
        .sidebar-active { background-color: #004028 !important; color: #fbbf24 !important; }
        .sidebar-active i { color: #fbbf24 !important; }
        .sidebar-hover:hover { background-color: rgba(255, 255, 255, 0.05); }
        /* Dark mode */
        .dark body { background-color: #111827; color: #f9fafb; }
        .dark .bg-white { background-color: #1f2937 !important; }
        .dark .bg-gray-50 { background-color: #111827 !important; }
        .dark .bg-gray-100 { background-color: #374151 !important; }
        .dark .text-gray-900 { color: #f9fafb !important; }
        .dark .text-gray-700 { color: #d1d5db !important; }
        .dark .text-gray-600 { color: #9ca3af !important; }
        .dark .text-gray-500 { color: #6b7280 !important; }
        .dark .border-gray-200 { border-color: #374151 !important; }
        .dark .border-gray-300 { border-color: #4b5563 !important; }
        .dark .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0,0,0,0.4) !important; }
        .dark .divide-gray-200 > * + * { border-color: #374151 !important; }
        .dark table thead { background-color: #1e40af !important; }
        .dark input, .dark select, .dark textarea { background-color: #374151 !important; color: #f9fafb !important; border-color: #4b5563 !important; }
        .dark .hover\:bg-gray-100:hover { background-color: #374151 !important; }
        .dark .hover\:bg-gray-50:hover { background-color: #1f2937 !important; }
    </style>
    <script>
        (function() {
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="h-full">
    <div x-data="{ sidebarOpen: false }" class="min-h-full">
        
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden" @click="sidebarOpen = false" x-cloak></div>

        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 z-50 w-72 sidebar-bg px-6 pb-4 overflow-y-auto lg:hidden" x-cloak>
            <div class="flex shrink-0 items-center justify-between mb-4 mt-2">
                <div class="flex items-center justify-center w-full py-4">
                    <img src="{{ asset('images/ibcc-logo.png') }}" alt="IBCC Logo" class="h-24 w-auto drop-shadow-lg">
                </div>
                <button type="button" class="-m-2.5 p-2.5 text-white absolute right-4 top-4" @click="sidebarOpen = false">
                    <span class="sr-only">Close sidebar</span>
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <nav class="flex flex-1 flex-col mt-4" 
                @auth
                    @php
                        $activeDropdown = 'none';
                        if (request()->routeIs('centers.*') || request()->routeIs('departments.*') || request()->routeIs('users.*') || request()->routeIs('officers.*') || request()->routeIs('activity-logs.*')) { $activeDropdown = 'admin'; }
                        elseif (request()->routeIs('incoming*') || request()->routeIs('couriers.*')) { $activeDropdown = 'receive'; }
                        elseif (request()->routeIs('outgoing1') || request()->routeIs('outgoing2') || request()->routeIs('outgoing3') || request()->routeIs('outgoing4') || request()->routeIs('dispatches.*') || request()->routeIs('dispatch-logs.*') || request()->routeIs('dispatch-others.*') || request()->routeIs('receive-logs.*')) { $activeDropdown = 'dispatch'; }
                    @endphp
                    x-data="{ openDropdown: '{{ $activeDropdown }}' }"
                @endauth
            >
                <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                        <ul role="list" class="-mx-2 space-y-1">
                            @auth
                                <li>
                                    <a href="{{ route('home') }}" 
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('home') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}"
                                       :class="{ 'sidebar-active': openDropdown === 'none' && {{ request()->routeIs('home') ? 'true' : 'false' }}, 'text-gray-100 hover:text-white': openDropdown !== 'none' || !{{ request()->routeIs('home') ? 'true' : 'false' }} }">
                                        <i class="fa-solid fa-house w-6 text-center text-gray-200 group-hover:text-white"></i>
                                        Dashboard
                                    </a>
                                </li>
                                @if (in_array(Auth::user()->role->slug, ['super-admin', 'center-admin']))
                                    <li x-data="{}">
                                        <button @click="openDropdown = (openDropdown === 'admin' ? 'none' : 'admin')" type="button" 
                                            class="group flex w-full items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover {{ (request()->routeIs('centers.*') || request()->routeIs('departments.*') || request()->routeIs('users.*') || request()->routeIs('officers.*') || request()->routeIs('activity-logs.*')) ? 'text-white' : 'text-gray-100 hover:text-white' }}"
                                            :class="{ 'sidebar-active': openDropdown === 'admin' }">
                                            <i class="fa-solid fa-cogs w-6 text-center text-gray-200 group-hover:text-white"></i>
                                            <span>Administration</span>
                                            <i class="fa-solid fa-chevron-right ml-auto h-3 w-3 shrink-0 text-gray-200 transition-transform duration-200" :class="{ 'rotate-90': openDropdown === 'admin' }"></i>
                                        </button>
                                        <ul x-show="openDropdown === 'admin'" class="mt-1 px-2" x-cloak>
                                            @if (Auth::user()->role->slug === 'super-admin')
                                                <li>
                                                    <a href="{{ route('centers.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('centers.index') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                        <i class="fa-solid fa-building-columns w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                        Centers
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('departments.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('departments.index') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                        <i class="fa-solid fa-building w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                        Departments
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a href="{{ route('users.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('users.index') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                    <i class="fa-solid fa-users w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                    Users
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('activity-logs.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('activity-logs.*') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                    <i class="fa-solid fa-list-check w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                    Activity Logs
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                                <li x-data="{}">
                                    <button @click="openDropdown = (openDropdown === 'receive' ? 'none' : 'receive')" type="button" 
                                        class="group flex w-full items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover {{ (request()->routeIs('incoming*') || request()->routeIs('couriers.*')) ? 'text-white' : 'text-gray-100 hover:text-white' }}"
                                        :class="{ 'sidebar-active': openDropdown === 'receive' }">
                                        <i class="fa-solid fa-box-open w-6 text-center text-gray-200 group-hover:text-white"></i>
                                        <span>Receive</span>
                                        <i class="fa-solid fa-chevron-right ml-auto h-3 w-3 shrink-0 text-gray-200 transition-transform duration-200" :class="{ 'rotate-90': openDropdown === 'receive' }"></i>
                                    </button>
                                    <ul x-show="openDropdown === 'receive'" class="mt-1 px-2" x-cloak>
                                        <li>
                                            <a href="{{ route('incoming1') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('incoming1') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                <i class="fa-solid fa-inbox w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                Applicant Receive
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('incoming2') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('incoming2') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                <i class="fa-solid fa-box w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                Internal Receive
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('incoming3') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('incoming3') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                <i class="fa-solid fa-folder-open w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                Sub-office Receive
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('incoming4') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('incoming4') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                <i class="fa-solid fa-file-import w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                Ministry/Department Receive
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li x-data="{}">
                                    <button @click="openDropdown = (openDropdown === 'dispatch' ? 'none' : 'dispatch')" type="button"
                                        class="group flex w-full items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover {{ (request()->routeIs('outgoing1') || request()->routeIs('outgoing2') || request()->routeIs('outgoing3') || request()->routeIs('outgoing4') || request()->routeIs('dispatches.*') || request()->routeIs('dispatch-logs.*') || request()->routeIs('dispatch-others.*') || request()->routeIs('receive-logs.*')) ? 'text-white' : 'text-gray-100 hover:text-white' }}"
                                        :class="{ 'sidebar-active': openDropdown === 'dispatch' }">
                                        <i class="fa-solid fa-paper-plane w-6 text-center text-gray-200 group-hover:text-white"></i>
                                        <span>Dispatch</span>
                                        <i class="fa-solid fa-chevron-right ml-auto h-3 w-3 shrink-0 text-gray-200 transition-transform duration-200" :class="{ 'rotate-90': openDropdown === 'dispatch' }"></i>
                                    </button>
                                    <ul x-show="openDropdown === 'dispatch'" class="mt-1 px-2" x-cloak>
                                        <li>
                                            <a href="{{ route('outgoing1') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('outgoing1') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                <i class="fa-solid fa-truck-fast w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                Applicant Dispatch
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('outgoing2') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('outgoing2') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                <i class="fa-solid fa-file-lines w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                Internal Dispatch
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('outgoing3') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('outgoing3') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                <i class="fa-solid fa-file-circle-plus w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                Sub-offices Dispatch
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('outgoing4') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('outgoing4') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                <i class="fa-solid fa-file-import w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                Ministry/Department Dispatch
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endauth
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Desktop sidebar -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto sidebar-bg px-6 pb-4">
                <div class="flex shrink-0 items-center mb-4">
                    <div class="flex items-center justify-center w-full py-4">
                        <img src="{{ asset('images/ibcc-logo.png') }}" alt="IBCC Logo" class="h-28 w-auto drop-shadow-xl">
                    </div>
                </div>
                <nav class="flex flex-1 flex-col"
                    @auth
                        @php
                            $activeDropdown = 'none';
                            if (request()->routeIs('centers.*') || request()->routeIs('departments.*') || request()->routeIs('users.*') || request()->routeIs('officers.*') || request()->routeIs('activity-logs.*')) { $activeDropdown = 'admin'; }
                            elseif (request()->routeIs('incoming*') || request()->routeIs('couriers.*')) { $activeDropdown = 'receive'; }
                            elseif (request()->routeIs('outgoing1') || request()->routeIs('outgoing2') || request()->routeIs('outgoing3') || request()->routeIs('outgoing4') || request()->routeIs('dispatches.*') || request()->routeIs('dispatch-logs.*') || request()->routeIs('dispatch-others.*') || request()->routeIs('receive-logs.*')) { $activeDropdown = 'dispatch'; }
                        @endphp
                        x-data="{ openDropdown: '{{ $activeDropdown }}' }"
                    @endauth
                >
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                @auth
                                    <li>
                                        <a href="{{ route('home') }}" 
                                           class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('home') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}"
                                           :class="{ 'sidebar-active': openDropdown === 'none' && {{ request()->routeIs('home') ? 'true' : 'false' }}, 'text-gray-100 hover:text-white': openDropdown !== 'none' || !{{ request()->routeIs('home') ? 'true' : 'false' }} }">
                                            <i class="fa-solid fa-house w-6 text-center text-gray-200 group-hover:text-white"></i>
                                            Dashboard
                                        </a>
                                    </li>
                                @if (in_array(Auth::user()->role->slug, ['super-admin', 'center-admin']))
                                    <li x-data="{}">
                                        <button @click="openDropdown = (openDropdown === 'admin' ? 'none' : 'admin')" type="button" 
                                            class="group flex w-full items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover {{ (request()->routeIs('centers.*') || request()->routeIs('departments.*') || request()->routeIs('users.*') || request()->routeIs('officers.*') || request()->routeIs('activity-logs.*')) ? 'text-white' : 'text-gray-100 hover:text-white' }}"
                                            :class="{ 'sidebar-active': openDropdown === 'admin' }">
                                            <i class="fa-solid fa-cogs w-6 text-center text-gray-200 group-hover:text-white"></i>
                                            <span>Administration</span>
                                            <i class="fa-solid fa-chevron-right ml-auto h-3 w-3 shrink-0 text-gray-200 transition-transform duration-200" :class="{ 'rotate-90': openDropdown === 'admin' }"></i>
                                        </button>
                                        <ul x-show="openDropdown === 'admin'" class="mt-1 px-2" x-cloak>
                                            @if (Auth::user()->role->slug === 'super-admin')
                                                <li>
                                                    <a href="{{ route('centers.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('centers.index') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                        <i class="fa-solid fa-building-columns w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                        Centers
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('departments.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('departments.index') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                        <i class="fa-solid fa-building w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                        Departments
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a href="{{ route('users.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('users.index') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                    <i class="fa-solid fa-users w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                    Users
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('activity-logs.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('activity-logs.*') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                    <i class="fa-solid fa-list-check w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                    Activity Logs
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                                    <li x-data="{}">
                                        <button @click="openDropdown = (openDropdown === 'receive' ? 'none' : 'receive')" type="button" 
                                            class="group flex w-full items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover {{ (request()->routeIs('incoming*') || request()->routeIs('couriers.*')) ? 'text-white' : 'text-gray-100 hover:text-white' }}"
                                            :class="{ 'sidebar-active': openDropdown === 'receive' }">
                                            <i class="fa-solid fa-box-open w-6 text-center text-gray-200 group-hover:text-white"></i>
                                            <span>Receive</span>
                                            <i class="fa-solid fa-chevron-right ml-auto h-3 w-3 shrink-0 text-gray-200 transition-transform duration-200" :class="{ 'rotate-90': openDropdown === 'receive' }"></i>
                                        </button>
                                        <ul x-show="openDropdown === 'receive'" class="mt-1 px-2" x-cloak>
                                            <li>
                                                <a href="{{ route('incoming1') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('incoming1') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                    <i class="fa-solid fa-inbox w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                    Applicant Receive
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('incoming2') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('incoming2') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                    <i class="fa-solid fa-box w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                    Internal Receive
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('incoming3') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('incoming3') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                    <i class="fa-solid fa-folder-open w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                    Sub-office Receive
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('incoming4') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('incoming4') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                    <i class="fa-solid fa-file-import w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                    Ministry/Department Receive
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li x-data="{}">
                                        <button @click="openDropdown = (openDropdown === 'dispatch' ? 'none' : 'dispatch')" type="button"
                                            class="group flex w-full items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover {{ (request()->routeIs('outgoing1') || request()->routeIs('outgoing2') || request()->routeIs('outgoing3') || request()->routeIs('outgoing4') || request()->routeIs('dispatches.*') || request()->routeIs('dispatch-logs.*') || request()->routeIs('dispatch-others.*') || request()->routeIs('receive-logs.*')) ? 'text-white' : 'text-gray-100 hover:text-white' }}"
                                            :class="{ 'sidebar-active': openDropdown === 'dispatch' }">
                                            <i class="fa-solid fa-paper-plane w-6 text-center text-gray-200 group-hover:text-white"></i>
                                            <span>Dispatch</span>
                                            <i class="fa-solid fa-chevron-right ml-auto h-3 w-3 shrink-0 text-gray-200 transition-transform duration-200" :class="{ 'rotate-90': openDropdown === 'dispatch' }"></i>
                                        </button>
                                        <ul x-show="openDropdown === 'dispatch'" class="mt-1 px-2" x-cloak>
                                            <li>
                                                <a href="{{ route('outgoing1') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('outgoing1') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                    <i class="fa-solid fa-truck-fast w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                    Applicant Dispatch
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('outgoing2') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('outgoing2') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                    <i class="fa-solid fa-file-lines w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                    Internal Dispatch
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('outgoing3') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('outgoing3') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                    <i class="fa-solid fa-file-circle-plus w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                    Sub-offices Dispatch
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('outgoing4') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold sidebar-hover items-center {{ request()->routeIs('outgoing4') ? 'sidebar-active' : 'text-gray-100 hover:text-white' }}">
                                                    <i class="fa-solid fa-file-import w-6 text-center text-gray-200 group-hover:text-white"></i>
                                                    Ministry/Department Dispatch
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endauth
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="lg:pl-72">
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="sidebarOpen = true">
                    <span class="sr-only">Open sidebar</span>
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>

                <!-- Separator -->
                <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="relative flex flex-1 items-center">
                        <form method="GET" action="{{ route('search') }}" class="w-full max-w-sm">
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs"></i>
                                </div>
                                <input type="text" name="q" value="{{ request('q') }}"
                                    placeholder="   Search couriers"
                                    class="block w-full rounded-lg border border-gray-200 bg-gray-50 py-2 pl-9 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 focus:outline-none transition-colors">
                            </div>
                        </form>
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">

                        <!-- Dark mode toggle -->
                        <button id="dark-mode-toggle" type="button"
                            class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-all shadow-sm"
                            title="Toggle dark mode">
                            <i id="dark-icon" class="fa-solid fa-moon text-sm"></i>
                        </button>

                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="-m-1.5 flex items-center p-1.5 focus:outline-none group" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                <div class="flex items-center gap-x-3 rounded-full bg-white border border-gray-200 px-3 py-1.5 shadow-sm group-hover:bg-gray-50 group-hover:border-gray-300 transition-all duration-200">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-emerald-600 to-emerald-800 flex items-center justify-center text-white font-bold text-sm shadow-sm ring-2 ring-white transition-transform duration-200 group-hover:scale-105">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <span class="hidden lg:flex lg:items-center">
                                        <span class="text-sm font-semibold leading-6 text-gray-700 group-hover:text-emerald-800 transition-colors duration-200" aria-hidden="true">{{ Auth::user()->name }}</span>
                                        <i class="fa-solid fa-chevron-down ml-2 text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                                    </span>
                                </div>
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200" 
                                 x-transition:enter-start="transform opacity-0 scale-95 translate-y-2" 
                                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0" 
                                 x-transition:leave="transition ease-in duration-150" 
                                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0" 
                                 x-transition:leave-end="transform opacity-0 scale-95 translate-y-2" 
                                 class="absolute right-0 z-20 mt-3 w-64 origin-top-right rounded-xl bg-white py-1 shadow-2xl ring-1 ring-black/5 focus:outline-none overflow-hidden" 
                                 role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" style="display: none;" x-cloak>
                                
                                <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Signed in as</p>
                                    <p class="mt-0.5 text-sm font-bold text-gray-900 truncate" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</p>
                                </div>

                                <div class="py-1">
                                    <a href="{{ route('users.show', Auth::id()) }}" class="flex items-center px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-emerald-700 transition-colors duration-150 group" role="menuitem">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-500 group-hover:bg-emerald-50 group-hover:text-emerald-600 mr-3 transition-colors duration-150">
                                            <i class="fa-solid fa-user"></i>
                                        </span>
                                        My Profile
                                    </a>
                                    <a href="{{ route('users.edit', Auth::id()) }}" class="flex items-center px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-700 transition-colors duration-150 group" role="menuitem">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-500 group-hover:bg-indigo-50 group-hover:text-indigo-600 mr-3 transition-colors duration-150">
                                            <i class="fa-solid fa-key"></i>
                                        </span>
                                        Change Password
                                    </a>
                                </div>
                                <div class="py-1 border-t border-gray-100">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center px-5 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-red-600 transition-colors duration-150 group" role="menuitem">
                                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-500 group-hover:bg-red-50 group-hover:text-red-600 mr-3 transition-colors duration-150">
                                                <i class="fa-solid fa-right-from-bracket"></i>
                                            </span>
                                            {{ __('Log Out') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <!-- Global Modal for Dispatch Tracking ID -->
    <div x-data="{ 
        open: false, 
        dispatchId: null, 
        trackingId: '', 
        actionUrl: '',
        openModal(id, url) {
            this.dispatchId = id;
            this.actionUrl = url;
            this.trackingId = '';
            this.open = true;
        }
    }" @open-dispatch-modal.window="openModal($event.detail.id, $event.detail.url)">
        <div x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm" x-cloak>
            <div @click.away="open = false" x-transition class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md mx-4">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-xl font-bold text-gray-800">Mark as Dispatched</h3>
                    <button @click="open = false" type="button" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>
                <form :action="actionUrl" method="POST">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Courier Tracking ID (Optional)</label>
                        <input type="text" name="tracking_id" x-model="trackingId" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm px-4 py-2.5" placeholder="e.g. TCS-123456789">
                        <p class="mt-2 text-xs text-gray-500">You can also add or edit this later by editing the dispatch record.</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="open = false" class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-medium rounded-lg hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all shadow-md">
                            <i class="fa-solid fa-paper-plane mr-2"></i>Confirm Dispatch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts') {{-- This line will render pushed scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('dark-mode-toggle');
            const icon = document.getElementById('dark-icon');
            function applyDark(on) {
                document.documentElement.classList.toggle('dark', on);
                if (icon) {
                    icon.className = on ? 'fa-solid fa-sun text-sm text-yellow-400' : 'fa-solid fa-moon text-sm';
                }
            }
            applyDark(localStorage.getItem('darkMode') === 'true');
            if (btn) {
                btn.addEventListener('click', function() {
                    const isDark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('darkMode', isDark);
                    applyDark(isDark);
                });
            }
        });
    </script>
</body>
</html>
