<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
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
    <body class="font-sans antialiased bg-[#f3f4f6]">
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
                    
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span class="text-sm font-medium">Dashboard</span>
                    </x-nav-link>

                    <x-nav-link :href="route('pr.index')" :active="request()->routeIs('pr.*')" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span class="text-sm font-medium">Daftar PR</span>
                    </x-nav-link>
                    
                    <x-nav-link :href="route('approval.index')" :active="request()->routeIs('approval.*')" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">Inbox Approval</span>
                    </x-nav-link>

                    @role('admin')
                    <!-- Section: Master Data -->
                    <div class="px-4 py-2 mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        Data Master
                    </div>

                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span class="text-sm font-medium">Pengguna</span>
                    </x-nav-link>

                    <x-nav-link :href="route('master-departments.index')" :active="request()->routeIs('master-departments.*')" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="text-sm font-medium">Departemen</span>
                    </x-nav-link>

                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span class="text-sm font-medium">Produk</span>
                    </x-nav-link>

                    <!-- Section: Pengaturan -->
                    <div class="px-4 py-2 mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        Pengaturan & Budget
                    </div>

                    <x-nav-link :href="route('admin.budgets.index')" :active="request()->routeIs('admin.budgets.*')" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">Manajemen Budget</span>
                    </x-nav-link>

                    <x-nav-link :href="route('departments.index')" :active="request()->routeIs('departments.*')" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-sm font-medium">Config Approval Dept</span>
                    </x-nav-link>

                    <x-nav-link :href="route('global-approvers.index')" :active="request()->routeIs('global-approvers.*')" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-white hover:shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        <span class="text-sm font-medium">Config Approval HO</span>
                    </x-nav-link>
                    @endrole
                    
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
            </aside>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                {{-- Header mobile trigger could go here --}}
                
                {{-- Top Bar if needed --}}

                <div class="py-6 px-8">
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
    </body>
</html>
