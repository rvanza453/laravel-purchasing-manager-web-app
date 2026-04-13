<x-admin-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Master Department (Unit)</h2>
            <a href="{{ route('admin.master-departments.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 transition">
                + Tambah Unit
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(isset($sites))
            <p class="text-gray-600">Pilih site untuk melihat unit di dalamnya.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($sites as $site)
                    <a href="{{ route('admin.master-departments.index', ['site_id' => $site->id]) }}" class="block p-4 bg-white rounded-lg shadow hover:shadow-md transition border hover:border-indigo-400">
                        <h3 class="font-bold text-gray-800">{{ $site->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $site->departments_count }} unit</p>
                    </a>
                @endforeach
            </div>
        @elseif(isset($site))
            <div class="flex items-center gap-2 mb-4">
                <a href="{{ route('admin.master-departments.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr; Kembali</a>
                <span class="text-gray-600">Site: <strong>{{ $site->name }}</strong></span>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">COA</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sub Dept</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($departments as $dept)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $dept->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $dept->coa }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $dept->subDepartments->count() }}</td>
                                    <td class="px-6 py-4 text-sm space-x-2">
                                        <a href="{{ route('admin.master-departments.edit', $dept) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form method="POST" action="{{ route('admin.master-departments.destroy', $dept) }}" style="display:inline;" onsubmit="return confirm('Yakin dihapus?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada unit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>{{ $departments->links() }}</div>
        @endif
    </div>
</x-admin-layout>
