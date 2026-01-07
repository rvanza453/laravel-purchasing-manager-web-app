<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Card 1 -->
            <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col items-center justify-center border-b-4 border-yellow-400">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Menunggu</span>
                <span class="text-sm font-bold text-yellow-600 mb-2">Pending Approval</span>
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['pending_approval'] }}</span>
                </div>
            </div>

            <!-- Card 2 -->
             <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col items-center justify-center border-b-4 border-blue-400">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Menunggu</span>
                <span class="text-sm font-bold text-blue-600 mb-2">PO Generation</span>
                 <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['approved'] }}</span>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col items-center justify-center border-b-4 border-red-400">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</span>
                <span class="text-sm font-bold text-red-600 mb-2">Rejected/Revision</span>
                 <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['rejected'] }}</span>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col items-center justify-center border-b-4 border-pink-400">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Menunggu</span>
                <span class="text-sm font-bold text-pink-600 mb-2">Barang Datang</span>
                 <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['po_created'] }}</span>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-6">Sisa Budget (Realisasi PR)</h3>
            
            <div class="relative h-64 w-full flex items-end justify-around gap-2 px-4 pb-8 border-b border-gray-200">
                <!-- Graph Bars Loop -->
                @php
                    $max = $budgetChart->max('total') ?: 1;
                    $colors = ['bg-primary-500', 'bg-blue-500', 'bg-purple-500', 'bg-yellow-500', 'bg-red-500'];
                @endphp

                @foreach($budgetChart as $index => $data)
                    <div class="flex flex-col items-center group relative w-full max-w-[80px]">
                        <!-- Tooltip -->
                        <div class="absolute bottom-full mb-2 hidden group-hover:block bg-black text-white text-xs rounded py-1 px-2">
                             Rp {{ number_format($data->total, 0, ',', '.') }}
                        </div>
                        
                        <div class="w-full rounded-t-lg {{ $colors[$index % 5] }} transition-all duration-500 hover:opacity-90" 
                             style="height: {{ ($data->total / $max) * 100 }}%"></div>
                        <span class="absolute top-full mt-2 text-xs font-semibold text-gray-600 text-center w-24">{{ $data->name }}</span>
                    </div>
                @endforeach
                
                @if($budgetChart->isEmpty())
                    <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                        Belum ada data PR
                    </div>
                @endif
            </div>
             <!-- X Axis Line already in border-b above -->
        </div>
    </div>
</x-app-layout>
