<x-app-layout>
    @php
        // Find the next approval step (lowest level pending)
        $nextApproval = $pr->approvals->where('status', 'Pending')->sortBy('level')->first();
        
        $canApprove = false;
        $currentApproval = null;
        
        if ($nextApproval) {
            // Check permissions: Owner OR Admin
            if (auth()->id() === $nextApproval->approver_id || auth()->user()->hasRole('admin')) {
                $canApprove = true;
                $currentApproval = $nextApproval;
            }
        }
        
        $isHO = auth()->user()->hasRole('admin'); 
    @endphp

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Pengajuan PR</h2>
                <div class="text-sm text-gray-500">Nomor: {{ $pr->pr_number }}</div>
            </div>
            <div class="flex items-center gap-3">
                @php
                    $statusColor = match($pr->status) {
                        'Pending' => 'bg-yellow-100 text-yellow-800',
                        'Approved' => 'bg-green-100 text-green-800',
                        'Rejected' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800',
                    };
                @endphp
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColor }}">
                    {{ $pr->status }}
                </span>
                
                @if($pr->status === 'Approved')
                    <a href="{{ route('pr.export.pdf', $pr) }}" 
                       class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export PDF
                    </a>
                @endif
            </div>
        </div>

        <!-- Details Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
            @if(isset($budgetWarnings) && count($budgetWarnings) > 0)
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <div class="font-bold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Peringatan Budget:
                    </div>
                    <ul class="list-disc list-inside text-sm mt-1">
                        @foreach($budgetWarnings as $warning)
                            <li>{!! $warning !!}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="block text-xs text-gray-400 uppercase">Pemohon</span>
                    <span class="block text-sm font-medium text-gray-800">{{ $pr->user->name }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 uppercase">Tanggal</span>
                    <span class="block text-sm font-medium text-gray-800">{{ $pr->request_date->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 uppercase">Departemen</span>
                    <span class="block text-sm font-medium text-gray-800">{{ $pr->department->name ?? '-' }} ({{ $pr->department->code ?? '-' }})</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 uppercase">Total Estimasi</span>
                    <span class="block text-sm font-medium text-gray-800">Rp {{ number_format($pr->total_estimated_cost, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <div class="pt-4 border-t border-gray-100">
                <span class="block text-xs text-gray-400 uppercase mb-1">Keterangan</span>
                <p class="text-sm text-gray-600">{{ $pr->description }}</p>
            </div>
        </div>

        @if($canApprove)
        <form method="POST" id="approval-form">
            @csrf
        @endif

        <!-- Items Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-bold text-gray-700 uppercase">Item Barang</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Original</th>
                        @if($canApprove && $isHO)
                           <th class="px-6 py-3 text-left text-xs font-medium text-blue-600 uppercase tracking-wider bg-blue-50">Adjust Qty</th>
                        @else
                           <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Final</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pr->items as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $item->item_name }}</div>
                                @if($item->specification)
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $item->specification }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $item->quantity }}
                            </td>
                            
                            @if($canApprove && $isHO)
                                <td class="px-6 py-4 text-sm text-gray-900 bg-blue-50">
                                    <input type="number" 
                                           name="adjusted_quantities[{{ $item->id }}]" 
                                           class="w-24 border-blue-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                           min="0" 
                                           step="1"
                                           value="{{ $item->getFinalQuantity() }}">
                                </td>
                            @else
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @php
                                        $finalQty = $item->getFinalQuantity();
                                        $hasAdjustment = $finalQty != $item->quantity;
                                    @endphp
                                    
                                    @if($hasAdjustment)
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            <span class="font-bold text-blue-600">{{ $finalQty }}</span>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif

                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->unit }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">Rp {{ number_format($item->price_estimation, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($canApprove)
            <!-- Approval Actions -->
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-800">Tindakan Approval</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan / Alasan (Opsional untuk Approve, Wajib untuk Reject)</label>
                    <textarea name="remarks" id="remarks-input" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Tulis catatan disini..."></textarea>
                </div>
                
                <div class="flex justify-end gap-3 pt-2">
                     <button type="submit" 
                             formaction="{{ route('approval.reject', $currentApproval->id) }}"
                             onclick="return validateReject()"
                             class="px-6 py-2.5 bg-red-50 text-red-600 font-medium rounded-lg hover:bg-red-100 transition focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                         Reject PR
                     </button>
                     <button type="submit" 
                             formaction="{{ route('approval.approve', $currentApproval->id) }}"
                             class="px-6 py-2.5 bg-primary-600 text-white font-bold rounded-lg hover:bg-primary-700 transition shadow-md hover:shadow-lg focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 flex items-center gap-2">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                         Approve PR
                     </button>
                </div>
            </div>
        </form>
        
        <script>
            function validateReject() {
                const remarks = document.getElementById('remarks-input').value;
                if (!remarks.trim()) {
                    alert('Mohon isi catatan/alasan untuk melakukan Reject.');
                    document.getElementById('remarks-input').focus();
                    return false;
                }
                return confirm('Apakah Anda yakin ingin MENOLAK pengajuan ini?');
            }
        </script>
        @endif

        <!-- Approval Timeline -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Riwayat Approval</h3>
            <div class="relative pl-6 border-l-2 border-gray-200 space-y-8">
                @foreach($pr->approvals as $approval)
                    <div class="relative">
                        <!-- Dot -->
                        <div class="absolute -left-[31px] bg-white border-2 {{ $approval->status === 'Approved' ? 'border-green-500' : ($approval->status === 'Rejected' ? 'border-red-500' : 'border-gray-300') }} w-4 h-4 rounded-full"></div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <span class="text-sm font-bold text-gray-900">{{ $approval->role_name }} ({{ $approval->approver->name }})</span>
                                <span class="block text-xs text-gray-400">Level {{ $approval->level }}</span>
                            </div>
                            <div class="mt-1 sm:mt-0">
                                @if($approval->status === 'Approved')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                                     <span class="block text-xs text-gray-400 text-right">{{ $approval->approved_at ? $approval->approved_at->format('d M H:i') : '' }}</span>
                                @elseif($approval->status === 'Rejected')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                     <span class="block text-xs text-gray-400 text-right">{{ $approval->approved_at ? $approval->approved_at->format('d M H:i') : '' }}</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @endif
                            </div>
                        </div>
                        @if($approval->remarks)
                            <div class="mt-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                "<i>{{ $approval->remarks }}</i>"
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
