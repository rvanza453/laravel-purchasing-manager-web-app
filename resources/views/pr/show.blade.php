<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Pengajuan PR</h2>
                <div class="text-sm text-gray-500">Nomor: {{ $pr->pr_number }}</div>
            </div>
            <div>
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
            </div>
        </div>

        <!-- Details Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
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

        <!-- Items Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-bold text-gray-700 uppercase">Item Barang</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($pr->items as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item->item_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->unit }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">Rp {{ number_format($item->price_estimation, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

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
