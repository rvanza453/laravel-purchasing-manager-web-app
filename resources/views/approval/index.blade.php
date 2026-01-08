<x-app-layout>
    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">Inbox Approval Pending</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($approvals as $approval)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden border-l-4 border-yellow-400">
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-5">
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase">PR Number</span>
                                <h3 class="text-lg font-bold text-gray-800">{{ $approval->purchaseRequest->pr_number }}</h3>
                                <span class="text-[10px] text-gray-400 font-medium">Diajukan: {{ $approval->purchaseRequest->created_at->format('d M Y') }}</span>
                            </div>
                            <span class="bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs px-3 py-1.5 rounded-full font-bold shadow-sm">
                                {{ $approval->approver->name }}
                            </span>
                        </div>
                        
                         <div class="space-y-3 mb-5">
                            <div>
                                <span class="text-xs text-gray-400 block mb-1">Items Requested</span>
                                <div class="text-sm font-medium text-gray-800 bg-gray-50 p-2 rounded-md">
                                    @php
                                        $itemNames = $approval->purchaseRequest->items->pluck('item_name');
                                        $displayItems = $itemNames->take(2)->implode(', ');
                                        $remainingCount = $itemNames->count() - 2;
                                    @endphp
                                    {{ $displayItems }}
                                    @if($remainingCount > 0)
                                        <span class="text-gray-500 text-xs italic">+ {{ $remainingCount }} more</span>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-xs text-gray-400 block">Pemohon</span>
                                    <span class="text-sm font-medium">{{ $approval->purchaseRequest->user->name }}</span>
                                    <div class="text-xs text-gray-500">{{ $approval->purchaseRequest->department->name ?? '-' }}</div>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-400 block">Total Estimasi</span>
                                    <span class="text-lg font-bold text-gray-900">Rp {{ number_format($approval->purchaseRequest->total_estimated_cost, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex gap-2 pt-4 border-t border-gray-100">
                            <a href="{{ route('pr.show', $approval->purchaseRequest) }}" class="flex-1 text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium transition">
                                Detail & Approval
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-gray-500 text-lg">Tidak ada approval pending saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
