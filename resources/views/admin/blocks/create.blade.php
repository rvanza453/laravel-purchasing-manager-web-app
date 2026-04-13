<x-admin-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Tambah Block</h1>
            <p class="mt-1 text-sm text-gray-600">Buat block/lokasi kerja baru di afdeling</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="mb-6 px-4 py-3 rounded-lg bg-red-100 border border-red-300">
                <h3 class="text-sm font-medium text-red-800 mb-2">Ada kesalahan:</h3>
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('admin.blocks.store') }}" class="bg-white rounded-lg shadow">
            @csrf
            <div class="p-6 space-y-6">
                <!-- Afdeling Selection -->
                <div>
                    <label for="sub_department_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Afdeling <span class="text-red-600">*</span>
                    </label>
                    <select id="sub_department_id" name="sub_department_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('sub_department_id') border-red-500 @enderror">
                        <option value="">-- Pilih Afdeling --</option>
                        @foreach ($subDepartments as $siteName => $deptGroup)
                            <optgroup label="{{ $siteName }}">
                                @foreach ($deptGroup as $afdeling)
                                    <option value="{{ $afdeling->id }}" @selected(old('sub_department_id') == $afdeling->id)>
                                        {{ $afdeling->department->name }} - {{ $afdeling->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('sub_department_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Block Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Block <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Blok A1, Blok Utama, dsb" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Block Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                        Kode Block <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" required
                           placeholder="Contoh: BLK-A1, A1, dsb" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('code') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Kode harus unik per afdeling</p>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                               @checked(old('is_active', true))
                               class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Aktif</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Tandai jika block ini sedang aktif digunakan</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-end px-6 py-4 border-t border-gray-200">
                <a href="{{ route('admin.blocks.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                    Buat Block
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
