<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'System Support') }} | IT Helpdesk</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0d1117; color: #c9d1d9; }
        .glass-panel { background: rgba(22, 27, 34, 0.75); backdrop-filter: blur(16px); border: 1px solid #30363d; border-radius: 16px; }
        .neon-accent { border-left: 3px solid #58a6ff; }
        .neon-text { color: #58a6ff; text-shadow: 0 0 10px rgba(88,166,255,0.4); }
        .form-input, .form-textarea, .form-select { background-color: #010409 !important; border: 1px solid #30363d !important; color: #c9d1d9 !important; border-radius: 8px !important; width: 100% !important; padding: 0.5rem 0.75rem !important; transition: all 0.2s; }
        .form-input:focus, .form-textarea:focus, .form-select:focus { border-color: #58a6ff !important; box-shadow: 0 0 0 1px #58a6ff !important; outline: none !important; }
        .form-label { display: block; font-size: 0.875rem; font-weight: 500; color: #8b949e; margin-bottom: 0.5rem; }
        .btn-neon { background: #238636; color: white; border-radius: 8px; font-weight: 600; text-align: center; border: 1px solid rgba(240,246,252,0.1); cursor: pointer; transition: all 0.2s;}
        .btn-neon:hover { background: #2ea043; border-color: #2ea043; }
        .btn-ghost { background: transparent; color: #58a6ff; border: 1px solid #58a6ff; border-radius: 8px; font-weight: 600; text-align: center; cursor: pointer; transition: all 0.2s;}
        .btn-ghost:hover { background: rgba(88,166,255,0.1); }
        .status-badge { padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .badge-open { border: 1px solid rgba(210,153,34,0.4); background: rgba(210,153,34,0.1); color: #d29922; }
        .badge-progress { border: 1px solid rgba(88,166,255,0.4); background: rgba(88,166,255,0.1); color: #58a6ff; }
        .badge-resolved { border: 1px solid rgba(46,160,67,0.4); background: rgba(46,160,67,0.1); color: #3fb950; }
        .badge-closed { border: 1px solid #30363d; background: rgba(48,54,61,0.5); color: #8b949e; }
        
        .priority-Urgent { color: #f85149; }
        .priority-High { color: #d29922; }
        .priority-Medium { color: #58a6ff; }
        .priority-Low { color: #8b949e; }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col relative overflow-x-hidden pt-6">
    @include('components.impersonation-banner')

    <!-- Glow effects -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-900/10 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[30%] h-[50%] bg-purple-900/10 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <!-- Minimal Header -->
    <header class="glass-panel mx-6 p-4 flex justify-between items-center z-10 sticky top-6 shadow-xl shadow-black/50">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center">
                <i class="fa-solid fa-satellite-dish neon-text text-xl"></i>
            </div>
            <div>
                <a href="{{ route('systemsupport.dashboard') }}">
                    <h1 class="text-xl font-bold text-white tracking-tight">System<span class="text-blue-400 font-light">Support</span></h1>
                    <p class="text-xs text-gray-400">IT Helpdesk Portal</p>
                </a>
            </div>
        </div>
        <nav class="flex gap-2 items-center">
            @if(Auth::user()->hasRole('Admin'))
                <span class="bg-[#1f6f5f]/20 border border-[#1f6f5f]/50 text-[#1f6f5f] px-3 py-1 rounded-full text-xs font-bold mr-3 neon-text" style="color:#58a6ff;">Admin Access</span>
            @endif
            <div class="hidden md:flex gap-4 mr-4 text-sm font-medium text-gray-400">
                <a href="{{ route('systemsupport.tickets.index') }}" class="hover:text-white transition {{ request()->routeIs('systemsupport.tickets.*') ? 'text-white border-b-2 border-blue-500' : '' }}">Tickets</a>
                @if(Auth::user()->hasAnyRole(['Admin', 'Admin IT', 'Helpdesk']))
                <a href="{{ route('systemsupport.announcements.index') }}" class="hover:text-white transition {{ request()->routeIs('systemsupport.announcements.*') ? 'text-white border-b-2 border-blue-500' : '' }}">Announcements</a>
                @endif
                <!-- Future module links -->
                <a href="#" class="hover:text-white transition opacity-50 cursor-not-allowed">Changelog</a>
            </div>
            <a href="{{ route('modules.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i> Module Hub
            </a>
        </nav>
    </header>

    <main class="flex-grow p-6 z-10 w-full max-w-7xl mx-auto mt-6">
        @yield('content')
    </main>
</body>
</html>