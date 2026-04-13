<x-admin-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Edit Master Department (Unit)</h2>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.master-departments.update', $department) }}" class="bg-white rounded-lg shadow p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                <select name="site_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" @selected(old('site_id', $department->site_id) == $site->id)>{{ $site->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Unit</label>
                <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('name', $department->name) }}" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">COA (Kode)</label>
                <input type="text" name="coa" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('coa', $department->coa) }}" required>
            </div>

            <div class="flex gap-3 justify-end pt-6 border-t border-gray-200">
                <a href="{{ route('admin.master-departments.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500">Simpan</button>
            </div>
        </form>
    </div>
</x-admin-layout>
