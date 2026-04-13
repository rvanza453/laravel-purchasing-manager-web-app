<x-qccomplaintsystem::layouts.master :title="'Pengaturan Approval QC'">
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Pengaturan Approval QC</h2>
        </div>

        <div class="space-y-4">
            @forelse($departments as $department)
                <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <button @click="open = !open" class="w-full flex justify-between items-center px-6 py-4 bg-gray-50 hover:bg-gray-100 transition-colors border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-gray-800 text-lg">{{ $department->name }}</span>
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium">Department</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': open, 'rotate-0': !open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    
                    <div x-show="open" x-transition class="overflow-hidden">
                        <div class="px-6 py-4 bg-white">
                            @php
                                $config = $department->qcApprovalConfigs->first();
                                $approvers = $config?->approver_user_ids ?? [];
                            @endphp
                            
                            <div class="space-y-3">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Approver Levels</h4>
                                    @if(empty($approvers))
                                        <div class="text-sm text-gray-400 italic py-2">Belum dikonfigurasi</div>
                                    @else
                                        <div class="space-y-1">
                                            @foreach($approvers as $index => $userId)
                                                @php
                                                    $user = \App\Models\User::find($userId);
                                                @endphp
                                                <div class="flex items-center gap-2 text-sm">
                                                    <span class="font-bold text-gray-500 w-12">Level {{ $index + 1 }}:</span>
                                                    <span class="text-indigo-600 font-medium">{{ $user->name ?? 'User tidak ditemukan' }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex gap-2 pt-2 border-t border-gray-200">
                                    <a href="{{ route('qc.approval-config.edit', $department) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                        <i class="fas fa-edit mr-2"></i> Edit Konfigurasi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm p-10 text-center text-gray-500">
                    Tidak ada department ditemukan.
                </div>
            @endforelse
        </div>
    </div>
</x-qccomplaintsystem::layouts.master>
