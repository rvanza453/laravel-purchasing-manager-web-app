<x-admin-layout>
    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">Log Aktivitas</h2>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $activity->user->name ?? 'System' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $activity->action }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $activity->description }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $activity->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada log aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $activities->links() }}</div>
    </div>
</x-admin-layout>
