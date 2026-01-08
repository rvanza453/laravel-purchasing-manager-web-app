<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex justify-between items-center">
             <h2 class="text-2xl font-bold text-gray-800">Konfigurasi Approval: {{ $department->name }}</h2>
             <a href="{{ route('departments.index') }}" class="text-gray-600 hover:text-gray-900">Kembali</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('departments.update', $department) }}" method="POST">
                @csrf
                @method('PUT')
                
                {{-- Read Only Info --}}
                <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-lg">
                    <div>
                        <span class="block text-xs font-medium text-gray-500 uppercase">Site</span>
                        <span class="font-semibold text-gray-800">{{ $department->site->name }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-medium text-gray-500 uppercase">Kode Departemen</span>
                        <span class="font-semibold text-gray-800">{{ $department->code }}</span>
                    </div>
                </div>

                <div class="my-4">
                    <label for="use_global_approval" class="inline-flex items-center">
                        <input id="use_global_approval" type="checkbox" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" name="use_global_approval" value="1" {{ old('use_global_approval', $department->use_global_approval) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-600">{{ __('Wajib Approval HO (Head Office)') }}</span>
                    </label>
                    <p class="text-xs text-gray-500 ml-6">Jika dicentang, akan otomatis meminta approval ke tim HO setelah approval lokal selesai.</p>
                </div>

                {{-- Approver Config Section --}}
                <div class="mb-6 border-t border-gray-100 pt-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Konfigurasi Approver</h3>
                        <button type="button" onclick="addApprover()" class="text-sm text-primary-600 hover:text-primary-700 font-medium">+ Tambah Level</button>
                    </div>
                    
                    <div id="approver-container" class="space-y-3">
                        @foreach($department->approverConfigs as $index => $config)
                            <div class="grid grid-cols-12 gap-2 approver-row bg-gray-50 p-3 rounded-lg">
                                <div class="col-span-2 flex items-center">
                                    <span class="text-sm font-bold text-gray-500">Level <input type="number" name="approvers[{{$index}}][level]" value="{{ $config->level }}" class="w-16 h-8 text-sm p-1 border-gray-300 rounded focus:border-primary-500 focus:ring-primary-500"></span>
                                </div>
                                <div class="col-span-4">
                                     <input type="text" name="approvers[{{$index}}][role_name]" value="{{ $config->role_name }}" placeholder="Nama Jabatan (misal: Manager)" class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5" required>
                                </div>
                                <div class="col-span-5">
                                    <select name="approvers[{{$index}}][user_id]" class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5" required>
                                        <option value="">Pilih User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $config->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-1 flex items-center justify-center">
                                    <button type="button" onclick="this.closest('.approver-row').remove()" class="text-red-500 hover:text-red-700">x</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mt-2">* Urutan level menentukan alur approval (1 -> 2 -> 3)</p>
                </div>

                <div class="flex justify-end pt-4 border-t">
                    <x-primary-button>
                        {{ __('Simpan Perubahan') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
