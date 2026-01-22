<x-app-layout>
    <div class="max-w-xl mx-auto space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">Tambah Job Baru</h2>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('jobs.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <x-input-label for="code" value="Kode Pekerjaan (COA)" />
                    <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" required placeholder="Contoh: 600-01" />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="name" value="Nama Pekerjaan (Job)" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required placeholder="Contoh: Potong Buah Blok A" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="flex justify-end pt-4 border-t">
                    <a href="{{ route('jobs.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition mr-2">Batal</a>
                    <x-primary-button>
                        {{ __('Simpan Job') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
