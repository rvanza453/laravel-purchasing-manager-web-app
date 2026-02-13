<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>
    
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        {{-- Tom Select for Searchable Dropdowns --}}
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <style>
            .ts-control { border-radius: 0.375rem; border-color: #d1d5db; padding: 0.5rem; }
            .ts-wrapper.single .ts-control { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#f3f4f6] pb-16 md:pb-0">
        @if(session()->has('impersonate_admin_id'))
            <div class="bg-yellow-500 text-white px-4 py-2 sticky top-0 z-50">
                <div class="max-w-7xl mx-auto flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="font-semibold">You are currently logged in as: {{ Auth::user()->name }}</span>
                    </div>
                    <form action="{{ route('users.leave-impersonate') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-white text-yellow-600 px-4 py-1 rounded font-medium hover:bg-yellow-50 transition">
                            Leave Impersonation
                        </button>
                    </form>
                </div>
            </div>
        @endif
        
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside class="w-64 bg-[#fff8e1] border-r border-gray-200 flex-shrink-0 hidden md:block" style="background-color: #fdfbf7;">
                <div class="h-16 flex items-center px-6 border-b border-gray-100">
                    <!-- Logo -->
                    <div class="flex items-center gap-2 text-primary-700 font-bold text-lg">
                       <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                       Purchasing System
                    </div>
                </div>

                <div class="p-4 border-b border-gray-100 mb-4">
                     <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition-colors cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-gray-200 group-hover:bg-primary-100 flex items-center justify-center text-gray-500 group-hover:text-primary-600 transition-colors">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="overflow-hidden">
                            <div class="text-sm font-medium text-gray-900 truncate group-hover:text-primary-700">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
                        </div>
                     </a>
                </div>

                <nav class="px-4 space-y-1">
                    <!-- Section: Operasional -->
                    <div class="px-4 py-2 mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        Operasional
                    </div>
                    
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span class="text-sm font-medium">Dashboard</span>
                    </x-nav-link>

                    <x-nav-link :href="route('pr.index')" :active="request()->routeIs('pr.*')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span class="text-sm font-medium">Daftar PR</span>
                    </x-nav-link>

                    @role('Admin')
                    <x-nav-link :href="route('admin.capex.index')" :active="request()->routeIs('admin.capex.*')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">Verifikasi CAPEX</span>
                    </x-nav-link>
                    @endrole

                    <x-nav-link :href="route('po.index')" :active="request()->routeIs('po.*')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span class="text-sm font-medium">Daftar PO</span>
                    </x-nav-link>

                    @hasanyrole('Purchasing|Admin|Warehouse')
                    <x-nav-link :href="route('inventory.index')" :active="request()->routeIs('inventory.*')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                       <span class="text-sm font-medium">Inventory</span>
                   </x-nav-link>
                   @endhasanyrole
                    
                   @hasanyrole('Approver|Admin')
                    <x-nav-link :href="route('approval.index')" :active="request()->routeIs('approval.*')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">Inbox Approval</span>
                    </x-nav-link>
                    @endhasanyrole

                    @role('Admin')
                    <!-- Section: Master Data -->
                    <div class="px-4 py-2 mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        Data Master
                    </div>

                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span class="text-sm font-medium">Pengguna</span>
                    </x-nav-link>

                    <x-nav-link :href="route('master-departments.index')" :active="request()->routeIs('master-departments.*')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="text-sm font-medium">Unit</span>
                    </x-nav-link>
                    @endrole

                    @hasanyrole('Purchasing|Admin|Warehouse')
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span class="text-sm font-medium">Produk</span>
                    </x-nav-link>
                    <x-nav-link :href="route('vendors.index')" :active="request()->routeIs('vendors.*')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="text-sm font-medium">Suppliers</span>
                    </x-nav-link>
                    @endhasanyrole

                    @role('Admin')

                    <x-nav-link :href="route('jobs.index')" :active="request()->routeIs('jobs.*')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span class="text-sm font-medium">Pekerjaan (Jobs)</span>
                    </x-nav-link>

                    <!-- Section: Pengaturan -->
                    <div class="px-4 py-2 mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        Pengaturan & Budget
                    </div>

                    <x-nav-link :href="route('admin.budgets.index')" :active="request()->routeIs('admin.budgets.index')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">Manajemen Budget</span>
                    </x-nav-link>

                    <x-nav-link :href="route('activity-logs.index')" :active="request()->routeIs('activity-logs.*')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">Log Aktivitas</span>
                    </x-nav-link>

                    <x-nav-link :href="route('departments.index')" :active="request()->routeIs('departments.*')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-sm font-medium">Config Unit</span>
                    </x-nav-link>

                    <x-nav-link :href="route('global-approvers.index')" :active="request()->routeIs('global-approvers.*')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        <span class="text-sm font-medium">Config Approval HO</span>
                    </x-nav-link>
                    @endrole

                    @hasanyrole('Approver|Admin|Finance')
                    <x-nav-link :href="route('admin.budgets.monitoring')" :active="request()->routeIs('admin.budgets.monitoring')" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span class="text-sm font-medium">Monitoring Budget</span>
                    </x-nav-link>
                    @endhasanyrole
                    
                     <div class="pt-4 mt-4 border-t border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-gray-600 hover:bg-red-50 hover:text-red-600 transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </nav>
                <div class="mt-auto px-6 py-4 border-t border-gray-100">
                    <div class="text-xs text-gray-400">
                        &copy; {{ date('Y') }} revanza
                    </div>
                </div>
            </aside>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="py-4 px-4 md:py-6 md:px-8">
                     @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Mobile Bottom Navigation (Vanilla JS) -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-300 z-30 shadow-lg" id="mobileNav">
            <div class="flex justify-around items-center h-16 px-2">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span class="text-xs mt-1 font-medium">Dashboard</span>
                </a>
                <a href="{{ route('pr.index') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('pr.*') ? 'text-indigo-600' : 'text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span class="text-xs mt-1 font-medium">PR</span>
                </a>
                <a href="{{ route('po.index') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('po.*') ? 'text-indigo-600' : 'text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span class="text-xs mt-1 font-medium">PO</span>
                </a>
                @hasanyrole('Approver|Admin')
                <a href="{{ route('approval.index') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('approval.*') ? 'text-indigo-600' : 'text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-xs mt-1 font-medium">Approval</span>
                </a>
                @endhasanyrole
                <button onclick="toggleMobileDrawer()" class="flex flex-col items-center justify-center flex-1 py-2 text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <span class="text-xs mt-1 font-medium">Menu</span>
                </button>
            </div>
        </nav>

        <!-- Mobile Drawer -->
        <div id="mobileDrawer" class="md:hidden fixed inset-0 z-40 hidden">
            <div onclick="toggleMobileDrawer()" class="absolute inset-0 bg-black bg-opacity-50"></div>
            <div id="drawerContent" class="absolute left-0 top-0 bottom-0 w-80 max-w-[85vw] bg-white shadow-2xl overflow-y-auto transform -translate-x-full transition-transform duration-300">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Menu</h3>
                        <button onclick="toggleMobileDrawer()" class="p-2 rounded-lg hover:bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="space-y-2">
                        @hasanyrole('Purchasing|Admin|Warehouse')
                        <a href="{{ route('inventory.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Inventory</span>
                        </a>
                        @endhasanyrole
                        @role('Admin')
                        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span>Users</span>
                        </a>
                        <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Products</span>
                        </a>
                        @endrole
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Profile</span>
                        </a>
                        <div class="pt-4 mt-4 border-t">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-red-50 text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function toggleMobileDrawer() {
            const drawer = document.getElementById('mobileDrawer');
            const drawerContent = document.getElementById('drawerContent');
            
            if (drawer.classList.contains('hidden')) {
                drawer.classList.remove('hidden');
                setTimeout(() => drawerContent.classList.remove('-translate-x-full'), 10);
            } else {
                drawerContent.classList.add('-translate-x-full');
                setTimeout(() => drawer.classList.add('hidden'), 300);
            }
        }
        </script>
        
        <!-- PO Cart Floating Button (Embedded directly due to component loading issue) -->
        <div x-data="poCart()" x-init="init()" class="relative z-[9999]">
            <!-- Floating Action Button -->
            <button @click="toggle()" 
                    x-show="count > 0"
                    class="fixed bottom-6 right-6 md:bottom-10 md:right-10 bg-primary-600 text-white p-4 rounded-full shadow-2xl hover:bg-primary-700 transition hover:scale-105 active:scale-95 flex items-center justify-center group z-[9999]"
                    title="Keranjang PO">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span x-text="count" 
                      x-show="count > 0"
                      class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full border-2 border-white shadow-sm min-w-[24px] text-center">
                </span>
            </button>

            <!-- Modal Backdrop -->
            <div x-show="isOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm z-[10000]"
                 @click="isOpen = false"
                 style="display: none;"></div>

            <!-- Modal Content -->
            <div x-show="isOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                 class="fixed bottom-24 right-4 md:right-10 w-full max-w-md bg-white rounded-xl shadow-2xl overflow-hidden max-h-[80vh] flex flex-col z-[10001]"
                 style="display: none;">
                
                <!-- Header -->
                <div class="px-6 py-4 bg-primary-600 text-white flex justify-between items-center shrink-0">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Keranjang PO
                    </h3>
                    <button @click="isOpen = false" class="text-white hover:text-gray-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="flex-1 overflow-y-auto p-0 bg-gray-50">
                    <template x-if="count === 0">
                        <div class="p-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            <p>Keranjang masih kosong.</p>
                        </div>
                    </template>

                    <template x-if="count > 0">
                        <ul class="divide-y divide-gray-100">
                            <template x-for="item in items" :key="item.id">
                                <li class="p-4 bg-white hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start gap-3">
                                        <div class="flex-1">
                                            <div class="text-xs font-bold text-primary-600 mb-0.5" x-text="item.pr_number"></div>
                                            <div class="text-sm font-medium text-gray-900" x-text="item.item_name"></div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Qty: <span class="font-semibold text-gray-700" x-text="item.quantity"></span> <span x-text="item.unit"></span>
                                                <span x-show="item.specification" class="mx-1">•</span>
                                                <span x-show="item.specification" x-text="item.specification"></span>
                                            </div>
                                        </div>
                                        <button @click="removeItem(item.id)" class="text-gray-400 hover:text-red-600 transition p-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </template>
                </div>

                <!-- Footer -->
                <div class="p-4 bg-white border-t border-gray-100 shrink-0" x-show="count > 0">
                    <div class="flex gap-3">
                        <button @click="clearCart()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                            Kosongkan
                        </button>
                        <form action="{{ route('po.create') }}" method="POST" class="flex-1">
                            @csrf
                            <!-- Hidden inputs for selected items -->
                            <template x-for="item in items" :key="item.id">
                                <input type="hidden" name="items[]" :value="item.id">
                            </template>
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-bold hover:bg-green-700 shadow-md transition flex justify-center items-center gap-2">
                                <span>Buat PO</span>
                                <span class="bg-green-700 px-1.5 py-0.5 rounded text-xs" x-text="count"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        @include('layouts.mobile-nav')

        <script>
        function poCart() {
            return {
                isOpen: false,
                count: 0,
                items: [],
                init() {
                    console.log('PO Cart Component Initialized');
                    this.refresh();
                    window.addEventListener('cart-updated', () => {
                        console.log('Cart updated event received');
                        this.refresh();
                    });
                },
                async refresh() {
                    try {
                        const res = await fetch('{{ route('po.cart.data') }}');
                        const data = await res.json();
                        console.log('Cart data:', data);
                        this.count = data.count;
                        this.items = data.items;
                    } catch (error) {
                        console.error('Failed to fetch cart data', error);
                    }
                },
                toggle() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen) {
                        this.refresh();
                    }
                },
                async removeItem(id) {
                    if (!confirm('Hapus item ini?')) return;
                    
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                        const res = await fetch('{{ route('po.cart.remove') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ pr_item_id: id })
                        });
                        
                        if (res.ok) {
                            this.refresh();
                            window.dispatchEvent(new CustomEvent('item-removed-from-cart', { detail: { id: id } }));
                        }
                    } catch (error) {
                        console.error('Failed to remove item', error);
                    }
                },
                async clearCart() {
                    if (!confirm('Kosongkan semua item di keranjang?')) return;

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                        const res = await fetch('{{ route('po.cart.clear') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (res.ok) {
                            this.refresh();
                            this.isOpen = false;
                            window.dispatchEvent(new CustomEvent('cart-cleared'));
                        }
                    } catch (error) {
                        console.error('Failed to clear cart', error);
                    }
                }
            }
        }
        </script>
    </body>
</html>
