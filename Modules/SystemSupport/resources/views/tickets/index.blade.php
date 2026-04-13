@extends('systemsupport::components.layouts.master')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="glass-panel p-5 neon-accent">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-400 font-medium tracking-wide">TOTAL TICKETS</p>
                <h3 class="text-3xl font-bold text-white mt-1">{{ $stats['total'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400">
                <i class="fa-solid fa-layer-group text-lg"></i>
            </div>
        </div>
    </div>
    <div class="glass-panel p-5 border-l-4 border-l-yellow-600">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-400 font-medium tracking-wide">OPEN</p>
                <h3 class="text-3xl font-bold text-white mt-1">{{ $stats['open'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-yellow-900/30 flex items-center justify-center text-yellow-500">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
            </div>
        </div>
    </div>
    <div class="glass-panel p-5 border-l-4 border-l-blue-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-400 font-medium tracking-wide">IN PROGRESS</p>
                <h3 class="text-3xl font-bold text-white mt-1">{{ $stats['in_progress'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-900/30 flex items-center justify-center text-blue-400">
                <i class="fa-solid fa-spinner fa-spin-pulse text-lg"></i>
            </div>
        </div>
    </div>
    <div class="glass-panel p-5 border-l-4 border-l-green-600">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-400 font-medium tracking-wide">RESOLVED</p>
                <h3 class="text-3xl font-bold text-white mt-1">{{ $stats['resolved'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-green-900/30 flex items-center justify-center text-green-500">
                <i class="fa-solid fa-check-double text-lg"></i>
            </div>
        </div>
    </div>
</div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <h2 class="text-xl font-bold text-white">Tickets & Issue Tracker</h2>
    
    <div class="flex gap-3 w-full sm:w-auto">
        <form action="{{ route('systemsupport.tickets.index') }}" method="GET" class="flex items-center">
            <select name="status" onchange="this.form.submit()" class="form-select text-sm py-2">
                <option value="">All Status</option>
                <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </form>
        <a href="{{ route('systemsupport.tickets.create') }}" class="btn-neon px-5 py-2 shadow-lg shadow-green-900/20 w-full sm:w-auto text-sm">
            <i class="fa-solid fa-plus mr-2"></i> New Ticket
        </a>
    </div>
</div>

@if(session('success'))
<div class="bg-green-900/40 border border-green-500/50 text-green-300 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 shadow-lg shadow-green-900/10">
    <i class="fa-solid fa-circle-check text-green-400"></i> {{ session('success') }}
</div>
@endif

<div class="glass-panel overflow-hidden shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-400">
            <thead class="bg-[#161b22] text-gray-300 uppercase font-semibold text-xs border-b border-[#30363d]">
                <tr>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4">Ticket details</th>
                    <th scope="col" class="px-6 py-4">Module / Priority</th>
                    <th scope="col" class="px-6 py-4">Reporter</th>
                    <th scope="col" class="px-6 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#30363d]">
                @forelse($tickets as $t)
                <tr class="hover:bg-[#1c2128] transition-colors">
                    <td class="px-6 py-5 whitespace-nowrap">
                        @if($t->status === 'Open')
                            <span class="status-badge badge-open"><i class="fa-regular fa-envelope mr-1"></i> Open</span>
                        @elseif($t->status === 'In Progress')
                            <span class="status-badge badge-progress"><i class="fa-solid fa-gears mr-1"></i> In Progress</span>
                        @elseif($t->status === 'Resolved')
                            <span class="status-badge badge-resolved"><i class="fa-solid fa-check mr-1"></i> Resolved</span>
                        @else
                            <span class="status-badge badge-closed"><i class="fa-solid fa-lock mr-1"></i> Closed</span>
                        @endif
                    </td>
                    <td class="px-6 py-5">
                        <div class="font-bold text-gray-100 text-base mb-1">{{ Str::limit($t->title, 50) }}</div>
                        <div class="text-xs text-gray-500">#T{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }} opened {{ $t->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex flex-col gap-2">
                            <span class="inline-flex items-center text-xs bg-gray-800 text-gray-300 rounded px-2 py-0.5 w-max border border-gray-700 shadow-sm">
                                <i class="fa-solid fa-cube mr-1.5 opacity-60"></i> {{ $t->module }}
                            </span>
                            <div class="text-xs font-semibold tracking-wide priority-{{ $t->priority }}">
                                @if($t->priority == 'Urgent') <i class="fa-solid fa-fire mr-1"></i> @else <i class="fa-regular fa-flag mr-1 opacity-75"></i> @endif 
                                {{ $t->priority }}
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-800 border-2 border-gray-700 flex items-center justify-center text-xs text-white uppercase font-bold shadow-sm">
                                {{ substr($t->user->name ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-300 block leading-tight">{{ $t->user->name ?? 'Unknown' }}</span>
                                <span class="text-xs text-gray-600">{{ $t->user->roles->first()->name ?? 'Staff' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <a href="{{ route('systemsupport.tickets.show', $t->id) }}" class="btn-ghost px-4 py-1.5 text-xs inline-block">
                            View <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                        <div class="bg-gray-800/50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-700">
                            <i class="fa-solid fa-meteor text-3xl opacity-50 text-blue-400"></i>
                        </div>
                        <p class="text-lg font-medium text-gray-400">All clear!</p>
                        <p class="text-sm">Tidak ada ticket dalam sistem ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tickets->hasPages())
    <div class="p-4 border-t border-[#30363d] bg-[#0d1117]/50">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
@endsection
