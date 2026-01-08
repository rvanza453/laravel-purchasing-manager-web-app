<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">Buat Pengajuan PR Baru</h2>

        <div class="bg-white rounded-xl shadow-sm p-6">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <div class="font-bold mb-2">Terjadi kesalahan:</div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Budget Warning Container --}}
            <div id="budget-warning" class="hidden mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                <div class="font-bold mb-2">Peringatan Budget:</div>
                <div id="budget-warning-list" class="text-sm"></div>
            </div>
            
            <form action="{{ route('pr.store') }}" method="POST">
                @csrf
                
                {{-- Department Select --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-input-label for="department_id" value="Departemen" />
                        <select id="department_id" name="department_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                            <option value="">Pilih Departemen</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }}) - {{ $dept->site->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="sub_department_id" value="Afdeling / Sub Departemen" />
                        <select id="sub_department_id" name="sub_department_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" required disabled>
                            <option value="">Pilih Departemen Terlebih Dahulu</option>
                        </select>
                        <x-input-error :messages="$errors->get('sub_department_id')" class="mt-2" />
                    </div>
                </div>

                <script>
                    const deptSelect = document.getElementById('department_id');
                    const subDeptSelect = document.getElementById('sub_department_id');
                    const departmentsData = @json($departments);

                    deptSelect.addEventListener('change', function() {
                        const deptId = this.value;
                        subDeptSelect.innerHTML = '<option value="">Pilih Sub Departemen</option>';
                        
                        if (deptId) {
                            const selectedDept = departmentsData.find(d => d.id == deptId);
                            if (selectedDept && selectedDept.sub_departments && selectedDept.sub_departments.length > 0) {
                                subDeptSelect.disabled = false;
                                selectedDept.sub_departments.forEach(sub => {
                                    subDeptSelect.innerHTML += `<option value="${sub.id}">${sub.name} (${sub.code || '-'})</option>`;
                                });
                            } else {
                                subDeptSelect.disabled = true;
                                subDeptSelect.innerHTML = '<option value="">Tidak ada Sub Departemen</option>';
                            }
                        } else {
                            subDeptSelect.disabled = true;
                            subDeptSelect.innerHTML = '<option value="">Pilih Departemen Terlebih Dahulu</option>';
                        }
                    });
                </script>

                {{-- Date --}}
                <div class="mb-4">
                    <x-input-label for="request_date" value="Tanggal Pengajuan" />
                    <x-text-input id="request_date" class="block mt-1 w-full bg-gray-100 text-gray-500" type="date" name="request_date" :value="date('Y-m-d')" readonly />
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
        const categories = @json($categories);
        let globalItemIndex = 0;

        function addItem() {
            const container = document.getElementById('items-container');
            const currentIndex = globalItemIndex; // Capture current index locally
            
            let productOptions = '<option value="">-- Cari Produk --</option>';
            // Add Manual Option
            productOptions += '<option value="manual">+ Input Barang Baru / Manual</option>';
            
            products.forEach(p => {
                productOptions += `<option value="${p.id}" data-name="${p.name}" data-unit="${p.unit}" data-price="${p.price_estimation || 0}">${p.code} - ${p.name}</option>`;
            });

            let categoryOptions = '<option value="">-- Pilih Kategori --</option>';
            categories.forEach(cat => {
                categoryOptions += `<option value="${cat}">${cat}</option>`;
            });

            const rowId = `row-${currentIndex}`;
            const row = `
                <div class="grid grid-cols-12 gap-3 item-row bg-gray-50 p-4 rounded-lg border border-gray-200" id="${rowId}">
                    <div class="col-span-4">
                        <label for="product-select-${currentIndex}" class="block text-xs font-medium text-gray-500 mb-1">Nama Barang</label>
                        <select id="product-select-${currentIndex}" name="items[${currentIndex}][product_id]" class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5 focus:border-primary-500 focus:ring-primary-500">
                            ${productOptions}
                        </select>
                        {{-- Hidden Input for Manual Name --}}
                        <div id="manual-name-container-${currentIndex}" class="mt-2 hidden space-y-2">
                            <input type="text" id="item-name-${currentIndex}" name="items[${currentIndex}][item_name]" placeholder="Ketik Nama Barang..." class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5 focus:border-primary-500 focus:ring-primary-500">
                            
                            {{-- Manual Category Select --}}
                            <select name="items[${currentIndex}][manual_category]" class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5 focus:border-primary-500 focus:ring-primary-500">
                                ${categoryOptions}
                            </select>
                        </div>
                        <input type="hidden" name="items[${currentIndex}][is_manual]" id="is-manual-${currentIndex}" value="0">
                    </div>
                    
                    <div class="col-span-3">
                         <label for="specification-${currentIndex}" class="block text-xs font-medium text-gray-500 mb-1">Spesifikasi</label>
                         <textarea id="specification-${currentIndex}" name="items[${currentIndex}][specification]" rows="2" class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5 focus:border-primary-500 focus:ring-primary-500" placeholder="Warna, Ukuran, Merk, dll..."></textarea>
                    </div>

                    <div class="col-span-1">
                        <label for="quantity-${currentIndex}" class="block text-xs font-medium text-gray-500 mb-1">Qty</label>
                        <input type="number" id="quantity-${currentIndex}" name="items[${currentIndex}][quantity]" value="1" min="1" onchange="calculateSubtotal(${currentIndex})" class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5" required>
                    </div>
                    
                    <div class="col-span-1">
                         <label for="unit-${currentIndex}" class="block text-xs font-medium text-gray-500 mb-1">Satuan</label>
                        <input type="text" id="unit-${currentIndex}" name="items[${currentIndex}][unit]" placeholder="Pcs" class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5 bg-gray-100 text-gray-500" readonly required>
                    </div>
                    
                    <div class="col-span-2">
                         <label for="price-${currentIndex}" class="block text-xs font-medium text-gray-500 mb-1">Est. Harga @</label>
                        <input type="number" id="price-${currentIndex}" name="items[${currentIndex}][price_estimation]" value="0" min="0" onchange="calculateSubtotal(${currentIndex})" class="block w-full border-gray-300 rounded-md shadow-sm text-sm p-1.5" required>
                        <div class="text-xs text-gray-500 mt-1 text-right">Total: <span id="subtotal-${currentIndex}" class="font-bold">0</span></div>
                    </div>
                    
                    <div class="col-span-1 flex items-center justify-center pt-5">
                        <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700 bg-white p-2 rounded-full border border-gray-200 hover:bg-gray-100 shadow-sm transition">
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
            const inputCategory = containerName.querySelector('select');
            const inputUnit = document.querySelector(`input[name="items[${index}][unit]"]`);
            const inputPrice = document.querySelector(`input[name="items[${index}][price_estimation]"]`);
            
            // is Manual?
            if (selectedVal === 'manual') {
                containerName.classList.remove('hidden');
                
                // Add required
                inputName.required = true;
                if(inputCategory) inputCategory.required = true;

                // Reset & Enable Unit
                inputUnit.value = '';
                inputUnit.removeAttribute('readonly');
                inputUnit.classList.remove('bg-gray-100', 'text-gray-500');
                
                // Reset price (user must enter manually)
                inputPrice.value = '0';
                inputPrice.removeAttribute('readonly');
                inputPrice.classList.remove('bg-gray-100', 'text-gray-500');
                
                // Enable Name
                inputName.value = '';

            } else if (selectedVal) {
                 // Existing Product
                containerName.classList.add('hidden');
                
                // Remove required
                inputName.required = false;
                if(inputCategory) inputCategory.required = false;

                const p = products.find(x => x.id == selectedVal);
                if (p) {
                    inputName.value = p.name;
                    
                    inputUnit.value = p.unit;
                    inputUnit.setAttribute('readonly', true);
                    inputUnit.classList.add('bg-gray-100', 'text-gray-500');
                    
                    // Auto-fill price from product
                    inputPrice.value = p.price_estimation || 0;
                    inputPrice.removeAttribute('readonly');
                    inputPrice.classList.remove('bg-gray-100', 'text-gray-500');
                    
                    // Recalculate subtotal
                    calculateSubtotal(index);
                }
            } else {
                // Empty
                containerName.classList.add('hidden');
                inputName.required = false;
                if(inputCategory) inputCategory.required = false;

                 inputUnit.value = '';
                 inputPrice.value = '0';
            }
            
            checkBudget(); // Check budget after product change
        }
        
        function removeItem(btn) {
            btn.closest('.item-row').remove();
            checkBudget();
        }

        function calculateSubtotal(index) {
            const qty = document.querySelector(`input[name="items[${index}][quantity]"]`).value;
            const price = document.querySelector(`input[name="items[${index}][price_estimation]"]`).value;
            const subtotal = qty * price;
            document.getElementById(`subtotal-${index}`).innerText = new Intl.NumberFormat('id-ID').format(subtotal);
            
            checkBudget();
        }

        // Budget Data
        let budgetData = {};

        // Fetch budget when sub dept changes
        subDeptSelect.addEventListener('change', function() {
            const subId = this.value;
            if(subId) {
                fetch(`/api/budget/${subId}`)
                    .then(response => response.json())
                    .then(data => {
                        budgetData = data;
                        checkBudget(); // Check immediately if items exist
                    });
            } else {
                budgetData = {};
                document.getElementById('budget-warning').classList.add('hidden');
            }
        });

        // Function to check budget
        function checkBudget() {
            const warningContainer = document.getElementById('budget-warning');
            const listContainer = document.getElementById('budget-warning-list');
            let warnings = [];
            
            // Calculate current PR total by category
            let currentRequest = {};
            
            // Iterate all visible items
            const rows = document.querySelectorAll('.item-row');
            rows.forEach(row => {
                 // Get inputs directly by name attribute pattern since ID might be complex or relying on index
                 // We can traverse the DOM from row
                 const select = row.querySelector('select[name^="items"][name$="[product_id]"]');
                 const manualCatSelect = row.querySelector('select[name^="items"][name$="[manual_category]"]');
                 const qtyInput = row.querySelector('input[name^="items"][name$="[quantity]"]');
                 const priceInput = row.querySelector('input[name^="items"][name$="[price_estimation]"]');
                 
                 if(select && qtyInput && priceInput) {
                     const qty = parseFloat(qtyInput.value) || 0;
                     const price = parseFloat(priceInput.value) || 0;
                     const total = qty * price;
                     
                     let category = 'Uncategorized';
                     
                     if (select.value === 'manual') {
                         if (manualCatSelect) {
                             category = manualCatSelect.value || 'Uncategorized';
                         }
                     } else if (select.value) {
                         // Find product category from products data
                         const p = products.find(x => x.id == select.value);
                         if (p && p.category) category = p.category;
                     }
                     
                     if (!currentRequest[category]) currentRequest[category] = 0;
                     currentRequest[category] += total;
                 }
            });

            // Compare with budgetData
            for (const [cat, amount] of Object.entries(currentRequest)) {
                if (budgetData[cat]) {
                    const remaining = budgetData[cat].remaining;
                    if (amount > remaining) {
                        const fmtAmount = new Intl.NumberFormat('id-ID').format(amount);
                        const fmtRemaining = new Intl.NumberFormat('id-ID').format(remaining);
                        warnings.push(`Kategori <strong>${cat}</strong>: Estimasi (${fmtAmount}) melebihi sisa budget (${fmtRemaining}). Sisa akan menjadi negatif.`);
                    }
                }
            }

            if (warnings.length > 0) {
                listContainer.innerHTML = warnings.join('<br>');
                warningContainer.classList.remove('hidden');
            } else {
                warningContainer.classList.add('hidden');
            }
        }


        
        // Also trigger on manual category change
        document.addEventListener('change', function(e) {
            if(e.target.name && e.target.name.includes('[manual_category]')) {
                checkBudget();
            }
        });
        
    </script>
</x-app-layout>
