@extends('systemsupport::components.layouts.master')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header Back Button & Status Glow -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('systemsupport.tickets.index') }}" class="w-10 h-10 rounded-full glass-panel flex items-center justify-center text-gray-400 hover:text-white transition border border-[#30363d] shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div class="flex flex-col">
                <span class="text-xs font-mono text-gray-500 mb-0.5">TICKET #{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
                <h2 class="text-2xl font-bold text-white tracking-tight">{{ $ticket->title }}</h2>
            </div>
        </div>
        
        <div class="flex gap-3">
            @if($ticket->status === 'Open')
                <div class="px-4 py-1.5 rounded-full border border-yellow-500/40 bg-yellow-500/10 text-yellow-500 font-bold text-xs uppercase tracking-wider flex items-center shadow-[0_0_10px_rgba(234,179,8,0.2)]">
                    <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2 animate-pulse"></span> Open
                </div>
            @elseif($ticket->status === 'In Progress')
                <div class="px-4 py-1.5 rounded-full border border-blue-500/40 bg-blue-500/10 text-blue-400 font-bold text-xs uppercase tracking-wider flex items-center shadow-[0_0_10px_rgba(59,130,246,0.2)]">
                    <i class="fa-solid fa-gears mr-2"></i> In Progress
                </div>
            @elseif($ticket->status === 'Resolved')
                <div class="px-4 py-1.5 rounded-full border border-green-500/40 bg-green-500/10 text-green-400 font-bold text-xs uppercase tracking-wider flex items-center shadow-[0_0_10px_rgba(34,197,94,0.2)]">
                    <i class="fa-regular fa-circle-check mr-2 text-sm"></i> Resolved
                </div>
            @else
                <div class="px-4 py-1.5 rounded-full border border-gray-600/40 bg-gray-800/80 text-gray-400 font-bold text-xs uppercase tracking-wider flex items-center">
                    <i class="fa-solid fa-lock mr-2 text-sm"></i> Closed
                </div>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-900/40 border border-green-500/50 text-green-300 px-4 py-3 rounded-xl mb-6 shadow-lg">
            <i class="fa-solid fa-circle-check text-green-400 mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Main Thread -->
        <div class="lg:col-span-2 flex flex-col gap-6">
            
            <!-- User Report Box -->
            <div class="glass-panel overflow-hidden border border-[#30363d] relative">
                <div class="absolute inset-0 bg-gradient-to-b from-[#21262d] to-transparent opacity-30 pointer-events-none"></div>
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-6 pb-6 border-b border-[#30363d]">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-gray-700 to-gray-600 border-2 border-gray-500 flex items-center justify-center text-lg text-white font-bold shadow-lg">
                            {{ substr($ticket->user->name ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-200 text-lg">{{ $ticket->user->name }}</p>
                            <p class="text-xs text-gray-500 font-mono"><i class="fa-regular fa-clock text-gray-600 mr-1"></i> {{ $ticket->created_at->format('d M Y, H:i') }} ({{ $ticket->created_at->diffForHumans() }})</p>
                        </div>
                    </div>
                    
                    <div class="prose prose-invert max-w-none text-gray-300 whitespace-pre-line leading-relaxed pb-4 text-sm md:text-base">
                        {{ $ticket->description }}
                    </div>
                </div>
            </div>

            <!-- IT Team Response Box -->
            @if($ticket->admin_response)
                @php
                    $isResolved = in_array($ticket->status, ['Resolved', 'Closed']);
                @endphp
                <div class="glass-panel overflow-hidden border {{ $isResolved ? 'border-green-500/30' : 'border-blue-500/30' }} shadow-xl relative ml-6 md:ml-12 mt-4">
                    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $isResolved ? 'bg-green-500' : 'bg-blue-500' }}"></div>
                    <div class="p-6 pl-8">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-[#0d1117] border border-[#30363d] flex items-center justify-center text-lg {{ $isResolved ? 'text-green-400' : 'text-blue-400' }}">
                                    <i class="fa-solid fa-user-astronaut"></i>
                                </div>
                                <div>
                                    <p class="font-bold {{ $isResolved ? 'text-green-400' : 'text-blue-400' }} tracking-wide flex items-center">
                                        SYSTEM IT SUPPORT <i class="fa-solid fa-circle-check ml-1.5 text-xs opacity-70"></i>
                                    </p>
                                    <p class="text-xs text-gray-500 font-mono">Official Engineer Response</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pl-0 text-sm md:text-base whitespace-pre-line leading-relaxed text-gray-300 border-l border-gray-700/50 block pl-4">
                            {{ $ticket->admin_response }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Sidebar Info & Controls -->
        <div class="flex flex-col gap-6">
            
            <!-- Metadata Card -->
            <div class="glass-panel p-6">
                <h3 class="font-bold text-gray-200 text-sm tracking-widest uppercase mb-5 flex items-center border-b border-[#30363d] pb-3">
                    <i class="fa-solid fa-circle-info text-blue-500 mr-2"></i> Ticket Details
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Module / Area</p>
                        <p class="font-medium text-blue-300 text-sm py-1.5 px-3 bg-blue-900/10 border border-blue-900/30 rounded inline-block"><i class="fa-solid fa-cube text-blue-500 opacity-70 mr-1.5"></i> {{ $ticket->module }}</p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Priority Level</p>
                        <p class="font-bold text-sm flex items-center gap-2 priority-{{ $ticket->priority }}">
                            @if($ticket->priority == 'Urgent') <i class="fa-solid fa-fire"></i> 
                            @elseif($ticket->priority == 'High') <i class="fa-solid fa-circle-arrow-up"></i>
                            @elseif($ticket->priority == 'Medium') <i class="fa-solid fa-minus"></i>
                            @else <i class="fa-solid fa-circle-arrow-down opacity-60"></i> @endif
                            {{ Str::upper($ticket->priority) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Reporter</p>
                        <p class="font-medium text-gray-300 text-sm flex items-center">
                            <i class="fa-solid fa-user text-gray-600 mr-2 text-xs"></i> {{ $ticket->user->name }}
                        </p>
                        <p class="text-xs text-gray-600 ml-5 mt-0.5">{{ $ticket->user->roles->first()->name ?? 'Staff' }}</p>
                    </div>
                </div>
            </div>

            <!-- Admin Controls (Only visible to IT/Admin) -->
            @if(Auth::user()->hasRole('Admin'))
            <div class="glass-panel p-6 border-t-2 border-t-purple-500/50 relative overflow-hidden">
                <!-- Diagonal stripes bg -->
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: repeating-linear-gradient(45deg, #fff 0, #fff 2px, transparent 2px, transparent 8px);"></div>
                
                <h3 class="font-bold text-purple-400 text-sm tracking-widest uppercase mb-4 flex items-center border-b border-[#30363d] pb-3 relative z-10">
                    <i class="fa-solid fa-terminal mr-2"></i> Engineer Controls
                </h3>
                
                <form action="{{ route('systemsupport.tickets.update', $ticket->id) }}" method="POST" class="relative z-10">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label text-xs">Update Status Ticket</label>
                        <select name="status" class="form-select text-sm py-2 px-3 border-gray-700 bg-gray-900 focus:border-purple-500">
                            <option value="Open" {{ $ticket->status == 'Open' ? 'selected' : '' }}>Open</option>
                            <option value="In Progress" {{ $ticket->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Resolved" {{ $ticket->status == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="Closed" {{ $ticket->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    
                    <div class="mb-5">
                        <label class="form-label text-xs" for="admin_response">Response / Solusi</label>
                        <textarea name="admin_response" id="admin_response" rows="4" class="form-textarea text-xs placeholder:text-gray-700 bg-gray-900 border-gray-700 focus:border-purple-500" placeholder="Ketik jawaban atau solusi teknis di sini...">{{ old('admin_response', $ticket->admin_response) }}</textarea>
                    </div>
                    
                    <button type="submit" class="w-full py-2.5 bg-purple-600 hover:bg-purple-500 text-white text-sm font-bold rounded-lg border border-purple-400/30 transition-colors flex justify-center items-center">
                        <i class="fa-solid fa-microchip mr-2"></i> Update Ticket Action
                    </button>
                </form>
            </div>
            @endif
            
        </div>
    </div>
</div>
@endsection
