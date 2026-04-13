<x-prsystem::app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Stock Awal') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-sm text-slate-700">
                        <p class="font-semibold mb-2">Format minimum kolom CSV:</p>
                        <p class="font-mono text-xs">Warehouse, Item ID, Item Name, Unit, Qty, Price</p>
                        <p class="mt-2 text-xs text-slate-600">Nama file bebas. Sistem akan membaca isi kolom, tidak wajib nama file tertentu.</p>
                    </div>

                    <form action="{{ route('inventory.import.kde.process') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sumber File</label>
                            <div class="space-y-2">
                                <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-slate-50">
                                    <input type="radio" name="source" value="default" class="mt-1" checked>
                                    <span>
                                        <span class="block font-medium text-gray-900">Gunakan file default di server</span>
                                        <span class="block text-xs text-gray-500">Mencari file <span class="font-mono">inventory_kde_final.csv</span> di folder project/public.</span>
                                    </span>
                                </label>

                                <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-slate-50">
                                    <input type="radio" name="source" value="upload" class="mt-1">
                                    <span>
                                        <span class="block font-medium text-gray-900">Upload file custom</span>
                                        <span class="block text-xs text-gray-500">Pakai file CSV/TXT apa saja sesuai kebutuhan.</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div id="upload-wrapper" class="hidden">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Pilih File</label>
                            <input type="file" name="file" id="file" accept=".csv,.txt"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-md">
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 font-semibold">
                                Jalankan Import Stock Awal
                            </button>
                            <a href="{{ route('inventory.index') }}" class="text-gray-600 hover:text-gray-900">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sourceInputs = document.querySelectorAll('input[name="source"]');
            const uploadWrapper = document.getElementById('upload-wrapper');
            const fileInput = document.getElementById('file');

            function syncUploadVisibility() {
                const selected = document.querySelector('input[name="source"]:checked')?.value;
                const isUpload = selected === 'upload';
                uploadWrapper.classList.toggle('hidden', !isUpload);
                fileInput.required = isUpload;
            }

            sourceInputs.forEach((input) => input.addEventListener('change', syncUploadVisibility));
            syncUploadVisibility();
        });
    </script>
</x-prsystem::app-layout>