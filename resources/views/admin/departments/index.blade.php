<x-app-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Konfigurasi Approval Department</h2>
            
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site / Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Departemen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approver Config</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($departments as $department)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $department->site->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $department->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $department->code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @foreach($department->approverConfigs as $config)
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded">Lvl {{ $config->level }}</span>
                                        <span>{{ $config->role_name }} ({{ $config->user->name }})</span>
                                    </div>
                                @endforeach

                                @if($department->use_global_approval && $globalApprovers->isNotEmpty())
                                    <div class="mt-2 pt-2 border-t border-dashed border-gray-200">
                                        <div class="text-xs font-semibold text-indigo-600 mb-1">Approver HO:</div>
                                        @php $currentMaxLevel = $department->approverConfigs->max('level') ?? 0; @endphp
                                        @foreach($globalApprovers as $global)
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="bg-indigo-50 text-indigo-700 text-xs px-2 py-0.5 rounded">Lvl {{ $currentMaxLevel + $global->level }}</span>
                                                <span class="text-gray-600">{{ $global->role_name }} ({{ $global->user->name }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('departments.edit', $department) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Config Approval</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada departemen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
