<x-app-layout>
    <div class="max-w-7xl mx-auto py-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Verifikasi PR CAPEX</h2>
                <p class="mt-1 text-sm text-gray-500">Daftar PR CAPEX yang menunggu verifikasi nomor.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                            <th class="px-6 py-4">No. PR</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Requester</th>
                            <th class="px-6 py-4">Unit</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4 text-center">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($prs as $pr)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <a href="{{ route('pr.show', $pr->id) }}" class="text-primary-600 hover:underline" target="_blank">
                                        {{ $pr->pr_number }}
                                    </a>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 ml-2">CAPEX</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $pr->request_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $pr->user->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $pr->department->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-700">Rp {{ number_format($pr->final_total, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $pr->description }}</td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.capex.verify', $pr->id) }}" method="POST" class="flex items-center justify-center space-x-2" onsubmit="return confirm('Verifikasi PR ini sebagai CAPEX?')">
                                        @csrf
                                        <div class="flex flex-col items-start">
                                             <div class="flex rounded-md shadow-sm w-48">
                                                <input type="text" name="capex_number" placeholder="01" class="uppercase flex-1 block w-full rounded-none rounded-l-md border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm py-1.5" required>
                                                <span class="inline-flex items-center px-2 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-xs whitespace-nowrap">
                                                    /Capex-{{ $pr->department->site->name ?? 'HO' }}...
                                                </span>
                                             </div>
                                        </div>
                                        <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-sm transition">
                                            Verifikasi
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-base font-medium">Tidak ada PR CAPEX yang menunggu verifikasi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <p class="text-xs text-gray-500 italic">
                    * PR yang diverifikasi akan melanjutkan proses approval ke atasan terkait.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
