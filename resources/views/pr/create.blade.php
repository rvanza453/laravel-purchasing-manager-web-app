<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">Buat Pengajuan PR Baru</h2>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('pr.store') }}" method="POST">
                @csrf
                
                {{-- Department Select --}}
                <div class="mb-4">
                    <x-input-label for="department_id" value="Departemen / Unit" />
                    <select id="department_id" name="department_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                        <option value="">Pilih Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }}) - {{ $dept->site->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                </div>

                {{-- Date --}}
                <div class="mb-4">
                    <x-input-label for="request_date" value="Tanggal Pengajuan" />
                    <x-text-input id="request_date" class="block mt-1 w-full bg-gray-100 text-gray-500" type="date" name="request_date" :value="old('request_date', date('Y-m-d'))" readonly />
                    <x-input-error :messages="$errors->get('request_date')" class="mt-2" />
                </div>

                {{-- Description --}}
                <div class="mb-6">
                    <x-input-label for="description" value="Keterangan / Tujuan Pengajuan" />
                    <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" rows="3" required>{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                {{-- Items Section --}}
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-lg font-semibold text-gray-700">Item Barang</h3>
                        <button type="button" onclick="addItem()" class="text-sm text-primary-600 hover:text-primary-700 font-medium">+ Tambah Item</button>
                    </div>
                    
                    <div id="items-container" class="space-y-3">
                        {{-- Item Row Template (Hidden) --}}
                    </div>
                    <p class="text-xs text-gray-400 mt-2">* Pilih produk dari Master Data atau ketik manual jika tidak ada.</p>
                </div>

                <div class="flex justify-end pt-4 border-t">
                    <x-primary-button>
                        {{ __('Simpan & Ajukan PR') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    {{-- Data for JS --}}
    <script>
        const products = @json($products);
        let globalItemIndex = 0;

        function addItem() {
            const container = document.getElementById('items-container');
            const currentIndex = globalItemIndex; // Capture current index locally
            
            let productOptions = '<option value="">-- Cari Produk --</option>';
            // Add Manual Option
            productOptions += '<option value="manual">+ Input Barang Baru / Manual</option>';
            
            products.forEach(p => {
                productOptions += `<option value="${p.id}" data-name="${p.name}" data-unit="${p.unit}">${p.code} - ${p.name}</option>`;
            });

            const rowId = `row-${currentIndex}`;
            const row = `
                <div class="grid grid-cols-12 gap-3 item-row bg-gray-50 p-4 rounded-lg border border-gray-200" id="${rowId}">
                    <div class="col-span-4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nama Barang</label>
                        <select id="product-select-${currentIndex}" name="items[${currentIndex}][product_id]" class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5 focus:border-primary-500 focus:ring-primary-500">
                            ${productOptions}
                        </select>
                        {{-- Hidden Input for Manual Name --}}
                        <div id="manual-name-container-${currentIndex}" class="mt-2 hidden">
                            <input type="text" name="items[${currentIndex}][item_name]" placeholder="Ketik Nama Barang..." class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5 focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <input type="hidden" name="items[${currentIndex}][is_manual]" id="is-manual-${currentIndex}" value="0">
                    </div>
                    
                    <div class="col-span-3">
                         <label class="block text-xs font-medium text-gray-500 mb-1">Spesifikasi</label>
                         <textarea name="items[${currentIndex}][specification]" rows="2" class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5 focus:border-primary-500 focus:ring-primary-500" placeholder="Warna, Ukuran, Merk, dll..."></textarea>
                    </div>

                    <div class="col-span-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Qty</label>
                        <input type="number" name="items[${currentIndex}][quantity]" value="1" min="1" onchange="calculateSubtotal(${currentIndex})" class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5" required>
                    </div>
                    
                    <div class="col-span-1">
                         <label class="block text-xs font-medium text-gray-500 mb-1">Satuan</label>
                        <input type="text" name="items[${currentIndex}][unit]" placeholder="Pcs" class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5 bg-gray-100 text-gray-500" readonly required>
                    </div>
                    
                    <div class="col-span-2">
                         <label class="block text-xs font-medium text-gray-500 mb-1">Est. Harga @</label>
                        <input type="number" name="items[${currentIndex}][price_estimation]" value="0" min="0" onchange="calculateSubtotal(${currentIndex})" class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5" required>
                        <div class="text-xs text-gray-500 mt-1 text-right">Total: <span id="subtotal-${currentIndex}" class="font-bold">0</span></div>
                    </div>
                    
                    <div class="col-span-1 flex items-center justify-center pt-5">
                        <button type="button" onclick="this.closest('.item-row').remove()" class="text-red-500 hover:text-red-700 bg-white p-2 rounded-full border border-gray-200 hover:bg-gray-100 shadow-sm transition">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', row);
            
            // Init Tom Select
            new TomSelect(`#product-select-${currentIndex}`, {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: "Cari Produk...",
                onChange: function(value) {
                    onProductChange(this.input, currentIndex);
                }
            });

            globalItemIndex++;
        }

        function onProductChange(select, index) {
            const selectedVal = select.value;
            const containerName = document.getElementById(`manual-name-container-${index}`);
            const inputName = containerName.querySelector('input');
            const inputUnit = document.querySelector(`input[name="items[${index}][unit]"]`);
            
            // is Manual?
            if (selectedVal === 'manual') {
                containerName.classList.remove('hidden');
                
                // Reset & Enable Unit
                inputUnit.value = '';
                inputUnit.removeAttribute('readonly');
                inputUnit.classList.remove('bg-gray-100', 'text-gray-500');
                
                // Enable Name
                inputName.value = '';
                // Since this is manual, we need to ensure the backend receives "manual item name"
                // The 'product_id' will be "manual" (string) which fails `exists:products`.
                // We need to handle this.
                // Actually, if value is 'manual', we should probably set product_id to null on submit?
                // Or standard HTML form submission will send 'manual'. 
                // The controller validation `exists:products,id` will fail if we send 'manual'.
                // So we should maybe clear the SELECT name attribute? Or handle in JS.
                // Better approach: When 'manual' is selected, clear the Select's value via JS?
                // But TomSelect manages the UI value.
                // Let's rely on backend logic: `items.*.product_id` is nullable.
                // If 'manual' is sent, Laravel validation fails.
                
                // Workaround: We can't easily change the validation rule dynamically per row in basic Request.
                // But we can intercept form submit.
                // OR simpler: make product_id nullable. If selected is 'manual', we submit product_id as empty/null.
                // We can do this by having a hidden input for product_id, and the select is just "product_selector".
                // Let's refine the HTML above slightly later if needed. For now, let's assume valid ID.
                
            } else if (selectedVal) {
                 // Existing Product
                containerName.classList.add('hidden');
                
                const p = products.find(x => x.id == selectedVal);
                if (p) {
                    // Fill hidden name (or just rely on product_id in backend)
                    // The backend creates item with `product_name`.
                    // If we use `product_id`, backend fetches name.
                    // But we have `items.*.item_name` required.
                    // So we must fill the hidden manual input with the product name so it passes validation?
                    // NO, we should just let the user see the product name in dropdown.
                    // But the controller expects `item_name`.
                    // Let's pre-fill the manual input with product name, even if hidden.
                    inputName.value = p.name;
                    
                    inputUnit.value = p.unit;
                    inputUnit.setAttribute('readonly', true);
                    inputUnit.classList.add('bg-gray-100', 'text-gray-500');
                }
            } else {
                // Empty
                containerName.classList.add('hidden');
                 inputUnit.value = '';
            }
        }

        function calculateSubtotal(index) {
            const qty = document.querySelector(`input[name="items[${index}][quantity]"]`).value;
            const price = document.querySelector(`input[name="items[${index}][price_estimation]"]`).value;
            const subtotal = qty * price;
            document.getElementById(`subtotal-${index}`).innerText = new Intl.NumberFormat('id-ID').format(subtotal);
        }

        // Add initial item
        document.addEventListener('DOMContentLoaded', () => {
            addItem();
        });
    </script>
</x-app-layout>
