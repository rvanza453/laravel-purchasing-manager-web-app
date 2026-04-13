<x-qccomplaintsystem::layouts.master :title="'Pelaporan Temuan QC Baru'">
    <style>
        .form-hero {
            border:1px solid #d5e4df;
            border-radius:16px;
            padding:16px;
            background:
                radial-gradient(circle at 100% 0%, rgba(13, 148, 136, .16), transparent 45%),
                linear-gradient(180deg, #ffffff, #f7fcfa);
            margin-bottom:14px;
            box-shadow:0 14px 24px rgba(15,23,42,0.06);
        }
        .form-hero h2 { margin:0 0 4px; font-size:24px; letter-spacing:0.01em; }
        .section-note { font-size:12px; color:#5b6d6a; }
    </style>

    <div class="form-hero">
        <h2>Lapor Temuan QC</h2>
        <div class="section-note">Isi informasi temuan sedetail mungkin agar proses tindak lanjut dan approval lebih cepat dan tepat.</div>
    </div>

    <div class="card">
        <div class="card-header">Form Pelaporan QC</div>
        <div class="card-body">
            <form action="{{ route('qc.findings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid">
                    <div class="field">
                        <label>Pelapor (User Login)</label>
                        <input type="text" class="input" value="{{ $authUser?->name ?? '-' }}" readonly>
                    </div>
                    <div class="field">
                        <label>Email Pelapor</label>
                        <input type="text" class="input" value="{{ $authUser?->email ?? '-' }}" readonly>
                    </div>
                </div>

                <div class="field">
                    <label>Judul Temuan</label>
                    <input type="text" name="title" class="input" value="{{ old('title') }}" placeholder="Contoh: Jalan panen rusak di area blok A1" required>
                </div>

                <div class="grid">
                    <div class="field">
                        <label>Tanggal Temuan</label>
                        <input type="date" name="finding_date" class="input" value="{{ old('finding_date', now()->toDateString()) }}" required>
                    </div>
                    <div class="field">
                        <label>Urgensi</label>
                        <select name="urgency" class="select" required>
                            @foreach($urgencyOptions as $urgency)
                                <option value="{{ $urgency }}" @selected(old('urgency', 'medium') === $urgency)>{{ \Modules\QcComplaintSystem\Models\QcFinding::urgencyLabel($urgency) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Deskripsi Temuan</label>
                    <textarea name="description" rows="4" class="input" placeholder="Jelaskan kondisi, dampak, dan kronologi singkat">{{ old('description') }}</textarea>
                </div>

                <div class="grid">
                    <div class="field">
                        <label>Sumber Temuan</label>
                        <select name="source_type" id="source_type" class="select" required>
                            @foreach($sourceOptions as $source)
                                <option value="{{ $source }}" @selected(old('source_type', $sourceOptions[0]) === $source)>
                                    @if($source === 'self')
                                        Temuan Sendiri
                                    @elseif($source === 'worker_direct')
                                        Pekerja Lain (Direct)
                                    @else
                                        {{ $source }}
                                    @endif
                                </option>
                            @endforeach
                            <option value="other" @selected(old('source_type') === 'other')>Lainnya (Input Manual)</option>
                        </select>
                        <input
                            type="text"
                            name="source_type_custom"
                            id="source_type_custom"
                            class="input"
                            value="{{ old('source_type_custom') }}"
                            placeholder="Tulis sumber temuan lainnya"
                            style="margin-top:8px; display:none;"
                        >
                    </div>
                </div>

                <div class="grid">
                    <div class="field">
                        <label>Kategori</label>
                        <select name="kategori" id="kategori" class="select" required>
                            <option value="">Pilih Kategori</option>
                            @foreach(\Modules\QcComplaintSystem\Models\QcFinding::categoryHierarchy() as $key => $cat)
                                <option value="{{ $key }}" @selected(old('kategori') === $key)>{{ $cat['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field" id="sub_kategori_wrap" style="display:none;">
                        <label>Sub Kategori</label>
                        <select name="sub_kategori" id="sub_kategori" class="select">
                            <option value="">Pilih Sub Kategori</option>
                        </select>
                    </div>
                </div>

                <div class="grid">
                    <div class="field">
                        <label>Department</label>
                        <select name="department_id" id="department_id" class="select" required>
                            <option value="">Pilih Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" @selected((string) old('department_id') === (string) $department->id)>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Afdeling</label>
                        <select name="sub_department_id" id="sub_department_id" class="select" required>
                            <option value="">Pilih Afdeling</option>
                            @if(old('sub_department_id'))
                                @php
                                    $subDeptId = old('sub_department_id');
                                    $subDept = \Modules\ServiceAgreementSystem\Models\SubDepartment::find($subDeptId);
                                @endphp
                                @if($subDept)
                                    <option value="{{ $subDept->id }}" selected>{{ $subDept->name }}</option>
                                @endif
                            @endif
                        </select>
                    </div>
                </div>

                <div class="grid">
                    <div class="field">
                        <label>Blok</label>
                        <input type="text" name="block_name" class="input" value="{{ old('block_name') }}" placeholder="Contoh: Blok A12" required>
                    </div>
                    <div class="field">
                        <label>Detail Lokasi Tambahan</label>
                        <input type="text" name="location" class="input" value="{{ old('location') }}" placeholder="Contoh: dekat TPH 3, sisi jalan utama">
                    </div>
                </div>

                <div id="panen-inspection-section" class="border-t border-gray-200 pt-6" style="display:none;">
                    <h4 class="text-md font-bold text-gray-800 mb-4">Informasi Kebun & Pemeriksaan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Ha Blok</label>
                            <input type="number" step="any" min="0" name="inspection_context[total_ha_block]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" value="{{ old('inspection_context.total_ha_block') }}" placeholder="34.91">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SPH (Pokok/Ha)</label>
                            <input type="number" step="any" min="0" name="inspection_context[sph]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" value="{{ old('inspection_context.sph') }}" placeholder="108">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ABW (Kg)</label>
                            <input type="number" step="any" min="0" name="inspection_context[abw]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" value="{{ old('inspection_context.abw') }}" placeholder="16">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ALW (Kg)</label>
                            <input type="number" step="any" min="0" name="inspection_context[alw]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" value="{{ old('inspection_context.alw') }}" placeholder="0.08">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemeriksa (QC)</label>
                            <input type="text" name="inspection_context[inspector_name]" class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100 focus:border-primary-500 focus:ring-primary-500" value="{{ old('inspection_context.inspector_name', $authUser?->name ?? '-') }}" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Saksi Assistant</label>
                            <input type="text" name="inspection_context[assistant_witness]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" value="{{ old('inspection_context.assistant_witness') }}" placeholder="Nama saksi assistant">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Saksi Mandor</label>
                            <input type="text" name="inspection_context[mandor_witness]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" value="{{ old('inspection_context.mandor_witness') }}" placeholder="Nama saksi mandor">
                        </div>
                        <div class="flex items-end">
                            <div class="text-xs text-gray-500 leading-relaxed">
                                Tanggal pemeriksaan mengikuti Tgl Temuan di bagian atas.
                            </div>
                        </div>
                    </div>
                </div>

                <div id="panen-finding-items-section" class="border-t border-gray-200 pt-6" style="display:none;">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                        <div>
                            <h4 class="text-md font-bold text-gray-800">Daftar Temuan</h4>
                            <div class="text-sm text-gray-500">Tambahkan beberapa temuan dalam satu laporan. Baris temuan bisa mengikuti template kategori Panen atau diisi manual.</div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" id="btn-load-panen-template" class="px-3 py-2 text-sm font-semibold rounded-lg border border-teal-200 bg-teal-50 text-teal-700 hover:bg-teal-100 transition">Gunakan Template Panen</button>
                            <button type="button" id="btn-add-finding-item" class="px-3 py-2 text-sm font-semibold rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition">Tambah Baris Temuan</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-700 w-14">No</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-700">Format Temuan</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-700">Nama Temuan</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-700 w-32">Jumlah</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-700">Catatan</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-700 w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="finding-items-container" class="divide-y divide-gray-100"></tbody>
                        </table>
                    </div>
                    @error('finding_items')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid">
                    <div class="field">
                        <label>PIC Tanggung Jawab (bisa lebih dari satu)</label>
                        @php $selectedPicIds = array_map('intval', old('pic_user_ids', [])); @endphp
                        <input type="text" id="pic_search" class="input" placeholder="Cari nama PIC..." style="margin-bottom:6px;">
                        <select name="pic_user_ids[]" id="pic_user_ids" class="select" multiple size="6">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(in_array((int) $user->id, $selectedPicIds, true))>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <div class="text-muted">Tekan Ctrl (atau Cmd di Mac) untuk memilih lebih dari satu PIC.</div>
                    </div>
                    <div class="field">
                        <label>Lampiran / Bukti Temuan</label>
                        <div class="file-picker-wrap">
                            <button type="button" class="btn" id="btn-add-files"><i class="fas fa-plus"></i> Pilih Lampiran</button>
                            <input type="file" id="file-picker-input" accept="image/*,application/pdf,.kml,.kmz,.zip" style="display:none;" multiple>
                            <input type="file" id="file-final-input" name="finding_attachments[]" multiple style="display:none;">
                        </div>
                        <div id="file-list" style="display:none; margin-top:8px; display:grid; gap:6px;"></div>
                        <div class="text-muted" style="margin-top:6px;">Bisa pilih lebih dari satu file (bisa klik tombol berkali-kali). Format: Gambar, PDF, KML, ZIP. Maksimal 20 MB/file.</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk"></i> Simpan Laporan Temuan</button>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                /* ── Multi-file picker ── */
                const btnAdd      = document.getElementById('btn-add-files');
                const pickerInput = document.getElementById('file-picker-input');
                const finalInput  = document.getElementById('file-final-input');
                const fileList    = document.getElementById('file-list');

                if (btnAdd && pickerInput && finalInput && fileList) {
                    const MAX_FILES = 10;
                    const MAX_BYTES = 20 * 1024 * 1024;
                    let dt = new DataTransfer();

                    function formatSize(bytes) {
                        if (bytes < 1024) return bytes + ' B';
                        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(0) + ' KB';
                        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
                    }

                    function renderList() {
                        fileList.innerHTML = '';
                        if (dt.files.length === 0) {
                            fileList.style.display = 'none';
                            return;
                        }
                        fileList.style.display = 'grid';
                        Array.from(dt.files).forEach((file, idx) => {
                            const row = document.createElement('div');
                            row.style.cssText = 'display:flex;align-items:center;gap:8px;padding:7px 10px;border:1px solid #d7e4dd;border-radius:8px;background:#f8fcfa;font-size:13px;';
                            row.innerHTML = `<i class="fas fa-paperclip" style="color:#0f766e;"></i>
                                <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${file.name}</span>
                                <span style="color:#64748b;font-size:11px;white-space:nowrap;">${formatSize(file.size)}</span>
                                <button type="button" data-idx="${idx}" style="background:none;border:none;cursor:pointer;color:#b91c1c;padding:0 4px;font-size:14px;" title="Hapus">&times;</button>`;
                            row.querySelector('button').addEventListener('click', function () {
                                const newDt = new DataTransfer();
                                Array.from(dt.files).forEach((f, i) => { if (i !== idx) newDt.items.add(f); });
                                dt = newDt;
                                syncFinal();
                                renderList();
                            });
                            fileList.appendChild(row);
                        });
                        const countMsg = document.createElement('div');
                        countMsg.style.cssText = 'font-size:12px;color:#475569;';
                        countMsg.textContent = dt.files.length + ' file dipilih';
                        fileList.appendChild(countMsg);
                    }

                    function syncFinal() {
                        const newDt = new DataTransfer();
                        Array.from(dt.files).forEach(f => newDt.items.add(f));
                        finalInput.files = newDt.files;
                    }

                    btnAdd.addEventListener('click', () => pickerInput.click());

                    pickerInput.addEventListener('change', function () {
                        let rejected = [];
                        Array.from(this.files).forEach(file => {
                            if (dt.files.length >= MAX_FILES) {
                                rejected.push(file.name + ' (batas 10 file)');
                                return;
                            }
                            if (file.size > MAX_BYTES) {
                                rejected.push(file.name + ' (lebih dari 20 MB)');
                                return;
                            }
                            dt.items.add(file);
                        });
                        this.value = '';
                        syncFinal();
                        renderList();
                        if (rejected.length) {
                            alert('File berikut tidak ditambahkan:\n' + rejected.join('\n'));
                        }
                    });
                }

                const departmentSelect = document.getElementById('department_id');
                const sourceTypeSelect = document.getElementById('source_type');
                const sourceTypeCustomInput = document.getElementById('source_type_custom');
                const picSearchInput = document.getElementById('pic_search');
                const picSelect = document.getElementById('pic_user_ids');

                function initPicSearchableSelect() {
                    if (!picSearchInput || !picSelect) return;

                    const allOptions = Array.from(picSelect.options).map((option) => ({
                        value: option.value,
                        text: option.text,
                        selected: option.selected,
                    }));

                    function syncSelectedStateFromDOM() {
                        const selectedValues = new Set(Array.from(picSelect.selectedOptions).map((opt) => opt.value));
                        allOptions.forEach((item) => {
                            if (selectedValues.has(item.value)) {
                                item.selected = true;
                            } else if (Array.from(picSelect.options).some((opt) => opt.value === item.value)) {
                                item.selected = false;
                            }
                        });
                    }

                    function renderFilteredOptions() {
                        syncSelectedStateFromDOM();

                        const query = picSearchInput.value.trim().toLowerCase();
                        const filtered = allOptions.filter((item) => {
                            if (item.selected) return true;
                            return item.text.toLowerCase().includes(query);
                        });

                        picSelect.innerHTML = '';
                        filtered.forEach((item) => {
                            const option = document.createElement('option');
                            option.value = item.value;
                            option.textContent = item.text;
                            option.selected = item.selected;
                            picSelect.appendChild(option);
                        });
                    }

                    picSearchInput.addEventListener('input', renderFilteredOptions);
                    picSelect.addEventListener('change', syncSelectedStateFromDOM);

                    renderFilteredOptions();
                }

                /* ── Kategori & Sub Kategori Logic ── */
                const categoryHierarchy = @json(\Modules\QcComplaintSystem\Models\QcFinding::categoryHierarchy());
                const kategoriSelect = document.getElementById('kategori');
                const subKategoriWrap = document.getElementById('sub_kategori_wrap');
                const subKategoriSelect = document.getElementById('sub_kategori');
                const oldSubKategori = '{{ old('sub_kategori') }}';
                const panenInspectionSection = document.getElementById('panen-inspection-section');
                const panenFindingItemsSection = document.getElementById('panen-finding-items-section');
                const findingItemTemplates = @json(
                    \Modules\QcComplaintSystem\Models\QcFinding::findingItemTemplates()
                );
                const oldFindingItems = @json(old('finding_items', []));
                const findingItemsContainer = document.getElementById('finding-items-container');
                const addFindingItemButton = document.getElementById('btn-add-finding-item');
                const loadPanenTemplateButton = document.getElementById('btn-load-panen-template');

                function updateSubKategori() {
                    if (!kategoriSelect || !subKategoriWrap || !subKategoriSelect) return;

                    const catKey = kategoriSelect.value;
                    const catData = categoryHierarchy[catKey];

                    subKategoriSelect.innerHTML = '<option value="">Pilih Sub Kategori</option>';

                    if (catData && catData.subs && Object.keys(catData.subs).length > 0) {
                        subKategoriWrap.style.display = 'flex';
                        subKategoriSelect.required = true;
                        
                        for (const [subKey, subData] of Object.entries(catData.subs)) {
                            const option = document.createElement('option');
                            option.value = subKey;
                            option.textContent = subData.label;
                            if (oldSubKategori === subKey) {
                                option.selected = true;
                            }
                            subKategoriSelect.appendChild(option);
                        }
                    } else {
                        subKategoriWrap.style.display = 'none';
                        subKategoriSelect.required = false;
                    }
                }

                if (kategoriSelect) {
                    kategoriSelect.addEventListener('change', updateSubKategori);
                    updateSubKategori();
                }

                function escapeHtml(value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function getFindingItemOptions(categoryKey) {
                    if (findingItemTemplates[categoryKey]) {
                        return findingItemTemplates[categoryKey];
                    }

                    const categoryData = categoryHierarchy[categoryKey];
                    if (categoryData && categoryData.subs) {
                        return Object.entries(categoryData.subs).map(([key, data]) => ({
                            key,
                            label: data.label,
                        }));
                    }

                    return [];
                }

                function buildFindingItemOptions(categoryKey, selectedValue = '') {
                    const options = getFindingItemOptions(categoryKey);
                    let html = '<option value="">Pilih format temuan</option>';

                    options.forEach((option) => {
                        const selected = selectedValue === option.key ? ' selected' : '';
                        html += `<option value="${escapeHtml(option.key)}"${selected}>${escapeHtml(option.label)}</option>`;
                    });

                    return html;
                }

                function createFindingItemRow(item = {}) {
                    const row = document.createElement('tr');
                    row.className = 'finding-item-row align-top';

                    const categoryKey = kategoriSelect ? kategoriSelect.value : '';
                    const templateKey = item.template_key || '';
                    const labelValue = item.label || '';
                    const quantityValue = item.quantity ?? '';
                    const noteValue = item.note || '';

                    row.innerHTML = `
                        <td class="px-3 py-2 text-gray-600 align-middle finding-row-number"></td>
                        <td class="px-3 py-2">
                            <select data-field="template_key" class="finding-template-select w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                ${buildFindingItemOptions(categoryKey, templateKey)}
                            </select>
                        </td>
                        <td class="px-3 py-2">
                            <input type="text" data-field="label" class="finding-label-input w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" value="${escapeHtml(labelValue)}" placeholder="Contoh: Janjang tinggal di pokok segar" required>
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" data-field="quantity" min="0" step="any" class="finding-quantity-input w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" value="${escapeHtml(quantityValue)}" placeholder="0">
                        </td>
                        <td class="px-3 py-2">
                            <input type="text" data-field="note" class="finding-note-input w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" value="${escapeHtml(noteValue)}" placeholder="Opsional">
                        </td>
                        <td class="px-3 py-2 align-middle">
                            <button type="button" class="remove-finding-item px-3 py-2 text-sm font-semibold rounded-lg border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 transition">Hapus</button>
                        </td>
                    `;

                    const templateSelect = row.querySelector('.finding-template-select');
                    const labelInput = row.querySelector('.finding-label-input');

                    if (templateSelect && labelInput) {
                        templateSelect.addEventListener('change', function () {
                            const selectedText = this.options[this.selectedIndex]?.text || '';
                            if (selectedText && this.value) {
                                labelInput.value = selectedText;
                            }
                        });

                        if (templateKey && !labelValue) {
                            const selectedText = templateSelect.options[templateSelect.selectedIndex]?.text || '';
                            if (selectedText) {
                                labelInput.value = selectedText;
                            }
                        }
                    }

                    row.querySelector('.remove-finding-item')?.addEventListener('click', function () {
                        row.remove();
                        if (findingItemsContainer && findingItemsContainer.children.length === 0) {
                            renderFindingItems([]);
                            return;
                        }

                        updateFindingItemRowNumbers();
                    });

                    return row;
                }

                function updateFindingItemRowNumbers() {
                    if (!findingItemsContainer) {
                        return;
                    }

                    Array.from(findingItemsContainer.querySelectorAll('.finding-item-row')).forEach((row, index) => {
                        const numberCell = row.querySelector('.finding-row-number');
                        if (numberCell) {
                            numberCell.textContent = String(index + 1);
                        }

                        row.querySelectorAll('[data-field]').forEach((input) => {
                            const field = input.getAttribute('data-field');
                            input.setAttribute('name', `finding_items[${index}][${field}]`);
                        });
                    });
                }

                function renderFindingItems(items = []) {
                    if (!findingItemsContainer) {
                        return;
                    }

                    findingItemsContainer.innerHTML = '';

                    const rows = Array.isArray(items) && items.length > 0 ? items : [{}];
                    rows.forEach((item) => {
                        findingItemsContainer.appendChild(createFindingItemRow(item));
                    });

                    updateFindingItemRowNumbers();
                }

                function getPanenTemplateRows() {
                    return (findingItemTemplates.panen || []).map((item) => ({
                        template_key: item.key,
                        label: item.label,
                        quantity: '',
                        note: '',
                    }));
                }

                function refreshFindingItemOptions() {
                    if (!findingItemsContainer || !kategoriSelect) {
                        return;
                    }

                    findingItemsContainer.querySelectorAll('.finding-item-row').forEach((row) => {
                        const templateSelect = row.querySelector('.finding-template-select');
                        const labelInput = row.querySelector('.finding-label-input');
                        if (!templateSelect || !labelInput) {
                            return;
                        }

                        const currentValue = templateSelect.value;
                        templateSelect.innerHTML = buildFindingItemOptions(kategoriSelect.value, currentValue);

                        if (currentValue) {
                            const selectedText = templateSelect.options[templateSelect.selectedIndex]?.text || '';
                            if (selectedText) {
                                labelInput.value = selectedText;
                            }
                        }
                    });
                }

                function initializeFindingItems() {
                    if (!findingItemsContainer) {
                        return;
                    }

                    if (Array.isArray(oldFindingItems) && oldFindingItems.length > 0) {
                        renderFindingItems(oldFindingItems);
                        refreshFindingItemOptions();
                        return;
                    }

                    if (kategoriSelect && kategoriSelect.value === 'panen') {
                        renderFindingItems(getPanenTemplateRows());
                        return;
                    }

                    renderFindingItems([]);
                }

                function togglePanenSections() {
                    const isPanen = kategoriSelect && kategoriSelect.value === 'panen';

                    if (panenInspectionSection) {
                        panenInspectionSection.style.display = isPanen ? 'block' : 'none';
                        panenInspectionSection.querySelectorAll('input, select, textarea').forEach((el) => {
                            el.disabled = !isPanen;
                        });
                    }

                    if (panenFindingItemsSection) {
                        panenFindingItemsSection.style.display = isPanen ? 'block' : 'none';
                    }

                    if (!isPanen && findingItemsContainer) {
                        findingItemsContainer.innerHTML = '';
                    }

                    if (addFindingItemButton) {
                        addFindingItemButton.disabled = !isPanen;
                    }

                    if (loadPanenTemplateButton) {
                        loadPanenTemplateButton.disabled = !isPanen;
                    }

                    if (isPanen && findingItemsContainer && findingItemsContainer.children.length === 0) {
                        if (Array.isArray(oldFindingItems) && oldFindingItems.length > 0) {
                            renderFindingItems(oldFindingItems);
                        } else {
                            renderFindingItems(getPanenTemplateRows());
                        }
                    }
                }

                addFindingItemButton?.addEventListener('click', function () {
                    if (!findingItemsContainer) {
                        return;
                    }

                    findingItemsContainer.appendChild(createFindingItemRow());
                });

                loadPanenTemplateButton?.addEventListener('click', function () {
                    if (kategoriSelect) {
                        kategoriSelect.value = 'panen';
                        kategoriSelect.dispatchEvent(new Event('change'));
                    }

                    renderFindingItems(getPanenTemplateRows());
                });

                if (kategoriSelect) {
                    kategoriSelect.addEventListener('change', function () {
                        refreshFindingItemOptions();
                        togglePanenSections();
                    });
                }

                initializeFindingItems();
                togglePanenSections();

                function toggleCustomSourceInput() {
                    const isOther = sourceTypeSelect && sourceTypeSelect.value === 'other';
                    if (!sourceTypeCustomInput) {
                        return;
                    }

                    sourceTypeCustomInput.style.display = isOther ? 'block' : 'none';
                    sourceTypeCustomInput.required = isOther;

                    if (!isOther) {
                        sourceTypeCustomInput.value = '';
                    }
                }

                if (sourceTypeSelect) {
                    sourceTypeSelect.addEventListener('change', toggleCustomSourceInput);
                    toggleCustomSourceInput();
                }

                /* ── Handle Department → Afdeling ── */
                const deptSelect = document.getElementById('department_id');
                const afdSelect = document.getElementById('sub_department_id');

                async function loadAfdeling(departmentId) {
                    if (!departmentId) {
                        afdSelect.innerHTML = '<option value="">Pilih Afdeling</option>';
                        return;
                    }

                    try {
                        const response = await fetch(`/qc/api/sub-departments/${departmentId}`);
                        const subDepts = await response.json();
                        
                        afdSelect.innerHTML = '<option value="">Pilih Afdeling</option>';
                        subDepts.forEach(sd => {
                            const option = document.createElement('option');
                            option.value = sd.id;
                            option.textContent = sd.name;
                            afdSelect.appendChild(option);
                        });
                    } catch (error) {
                        console.error('Error loading afdelings:', error);
                        afdSelect.innerHTML = '<option value="">Error loading data</option>';
                    }
                }

                if (deptSelect && afdSelect) {
                    deptSelect.addEventListener('change', function () {
                        loadAfdeling(this.value);
                    });
                }

                initPicSearchableSelect();
            });
        </script>
    @endpush
</x-qccomplaintsystem::layouts.master>
