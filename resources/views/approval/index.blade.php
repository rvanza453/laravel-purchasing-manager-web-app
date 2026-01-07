<x-app-layout>
    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">Inbox Approval Pending</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($approvals as $approval)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden border-l-4 border-yellow-400">
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-xs font-semibold text-gray-400 uppercase">PR Number</span>
                                <h3 class="text-lg font-bold text-gray-800">{{ $approval->purchaseRequest->pr_number }}</h3>
                            </div>
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-semibold">Level {{ $approval->level }}</span>
                        </div>
                        
                         <div class="space-y-2 mb-4">
                            <div>
                                <span class="text-xs text-gray-400 block">Pemohon</span>
                                <span class="text-sm font-medium">{{ $approval->purchaseRequest->user->name }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 block">Departemen</span>
                                <span class="text-sm text-gray-600">{{ $approval->purchaseRequest->department->name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 block">Total Estimasi</span>
                                <span class="text-sm font-bold text-gray-800">Rp {{ number_format($approval->purchaseRequest->total_estimated_cost, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <div class="flex gap-2 pt-4 border-t border-gray-100">
                             <a href="{{ route('pr.show', $approval->purchaseRequest) }}" class="flex-1 text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium transition">
                                Detail
                            </a>
                            <button onclick="openRejectModal({{ $approval->id }})" class="flex-1 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-sm font-medium transition">
                                Reject
                            </button>
                            <form action="{{ route('approval.approve', $approval) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium transition">
                                    Approve
                                </button>
                            </form>
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

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Konfirmasi Reject</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan</label>
                    <textarea name="remarks" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject PR</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(id) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/approvals/\${id}/reject`; // Adjust route if needed
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    </script>
</x-app-layout>
