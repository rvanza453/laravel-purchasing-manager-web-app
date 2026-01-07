<x-app-layout>
    <div class="max-w-xl mx-auto space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">Tambah Departemen Baru</h2>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <x-input-label for="site_id" value="Site / Lokasi" />
                    <select id="site_id" name="site_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                        <option value="">Pilih Site</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}">{{ $site->name }} ({{ $site->code }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('site_id')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="name" value="Nama Departemen (contoh: HRD, Teknik)" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="code" value="Kode Departemen (Unik per Site)" />
                    <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" required />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>

                <div class="mb-6">
                    <x-input-label for="description" value="Deskripsi" />
                    <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="flex justify-end pt-4 border-t">
                    <x-primary-button>
                        {{ __('Simpan Departemen') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
