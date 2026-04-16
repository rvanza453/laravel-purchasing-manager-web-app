<x-serviceagreementsystem::layouts.master :title="'Edit USPK'">
    @push('actions')
        <a href="{{ route('sas.uspk.show', $uspk) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    @endpush

    <form action="{{ route('sas.uspk.update', $uspk) }}" method="POST" enctype="multipart/form-data" id="uspkForm">
        @csrf @method('PUT')

        {{-- Informasi Pekerjaan --}}
        <div class="card mb-4">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-info-circle" style="color: var(--accent); margin-right: 8px;"></i> Informasi Pekerjaan</div>
                <span class="text-muted" style="font-size: 12px;">{{ $uspk->uspk_number }}</span>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label required">Nama Kegiatan</label>
                    <select id="namaKegiatanSelect" class="form-control" onchange="handleNamaKegiatanChange(this)">
                        <option value="">-- Pilih Kegiatan --</option>
                        <option value="Pemeliharaan Jalan">Pemeliharaan Jalan</option>
                        <option value="Pembersihan Parit / Saluran Air">Pembersihan Parit / Saluran Air</option>
                        <option value="Pemupukan Tanaman">Pemupukan Tanaman</option>
                        <option value="Penyemprotan Herbisida">Penyemprotan Herbisida</option>
                        <option value="Panen Buah Sawit">Panen Buah Sawit</option>
                        <option value="Pengendalian Hama Terpadu">Pengendalian Hama Terpadu</option>
                        <option value="Pembangunan / Perbaikan Infrastruktur">Pembangunan / Perbaikan Infrastruktur</option>
                        <option value="Perawatan Alat Berat">Perawatan Alat Berat</option>
                        <option value="Lainnya">Isi Sendiri / Lainnya...</option>
                    </select>
                    <input type="hidden" name="title" id="titleInput" value="{{ old('title', $uspk->title) }}">
                    <div id="customTitleWrapper" style="margin-top: 10px; display: none;">
                        <input type="text" id="customTitleInput" class="form-control" placeholder="Tuliskan nama kegiatan..." value="{{ old('title', $uspk->title) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $uspk->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Lokasi & Aktivitas --}}
        <div class="card mb-4">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-map-marker-alt" style="color: var(--success); margin-right: 8px;"></i> Lokasi & Aktivitas</div>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Site / Department</label>
                        <select name="department_id" id="department_id" class="form-control" required>
                            <option value="">-- Pilih Site --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $uspk->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Afdeling</label>
                        <select name="sub_department_id" id="sub_department_id" class="form-control" required>
                            <option value="">-- Pilih Afdeling --</option>
                            @foreach($subDepartments as $sd)
                                <option value="{{ $sd->id }}" {{ old('sub_department_id', $uspk->sub_department_id) == $sd->id ? 'selected' : '' }}>
                                    {{ $sd->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <label class="form-label required">Blok <span style="font-size: 11px; color: var(--text-muted);">(bisa pilih lebih dari satu)</span></label>
                        <select name="block_ids[]" id="block_ids" class="form-control" multiple required style="min-height: 110px;">
                            @foreach($blocks as $b)
                                <option value="{{ $b->id }}" {{ in_array($b->id, old('block_ids', $uspk->block_ids ?? [])) ? 'selected' : '' }}>
                                    {{ $b->name }} {{ $b->code ? '(' . $b->code . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;"><i class="fas fa-info-circle"></i> Tekan Ctrl (Windows) atau Cmd (Mac) untuk memilih lebih dari satu blok.</div>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label required">Aktivitas (Job)</label>
                        <select name="job_id" id="job_id" class="form-control" required>
                            <option value="">-- Pilih Aktivitas --</option>
                            @foreach($jobs as $job)
                                <option value="{{ $job->id }}" {{ old('job_id', $uspk->job_id) == $job->id ? 'selected' : '' }}>
                                    {{ $job->code ? $job->code . ' - ' : '' }}{{ $job->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Budget Aktivitas</label>
                    <select name="uspk_budget_activity_id" id="uspk_budget_activity_id" class="form-control">
                        <option value="">-- Pilih Budget (opsional) --</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Tender Pembanding --}}
        <div class="card mb-4">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-balance-scale" style="color: var(--warning); margin-right: 8px;"></i> Tender Pembanding</div>
                <button type="button" id="addTender" class="btn btn-primary btn-sm" onclick="addTenderRow()">
                    <i class="fas fa-plus"></i> Tambah Tender
                </button>
            </div>
            <div class="card-body">
                <p style="font-size: 12.5px; color: var(--text-muted); margin-bottom: 16px;">
                    <i class="fas fa-info-circle"></i> Minimal 1, maksimal 3 tender pembanding.
                </p>

                <div id="tendersContainer">
                    @foreach($uspk->tenders as $index => $tender)
                    <div class="tender-row" data-index="{{ $index }}" style="background: var(--bg-input); border: 1px solid var(--border-color); border-radius: 10px; padding: 20px; margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                            <div style="font-weight: 600; font-size: 14px; color: var(--accent);">
                                <i class="fas fa-user-tie"></i> Tender #{{ $index + 1 }}
                            </div>
                            <div class="d-flex align-center gap-2">
                                <label style="font-size: 12px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                    <input type="radio" name="selected_tender" value="{{ $index }}" {{ $tender->is_selected ? 'checked' : '' }} style="accent-color: var(--success);">
                                    <span style="color: var(--success); font-weight: 600;">Tender Diajukan</span>
                                </label>
                                @if($index > 0)
                                <button type="button" onclick="removeTenderRow({{ $index }})" class="btn btn-danger btn-sm" style="padding: 4px 10px;">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                            </div>
                        </div>

                        <input type="hidden" name="tenders[{{ $index }}][is_selected]" value="{{ $tender->is_selected ? '1' : '0' }}" id="tender_selected_{{ $index }}">

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Kontraktor</label>
                                <select name="tenders[{{ $index }}][contractor_id]" class="form-control" required>
                                    <option value="">-- Pilih Kontraktor --</option>
                                    @foreach($contractors as $c)
                                        <option value="{{ $c->id }}" {{ $tender->contractor_id == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }} {{ $c->company_name ? '(' . $c->company_name . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Nilai Tender (Rp)</label>
                                <input type="number" name="tenders[{{ $index }}][tender_value]" class="form-control" value="{{ $tender->tender_value }}" step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Durasi (Hari)</label>
                                <input type="number" name="tenders[{{ $index }}][tender_duration]" class="form-control" value="{{ $tender->tender_duration }}" min="1">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Lampiran</label>
                                <input type="file" name="tenders[{{ $index }}][attachment]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                @if($tender->attachment_path)
                                    <div style="margin-top: 4px; font-size: 11px; color: var(--text-muted);"><i class="fas fa-paperclip"></i> File sudah ada</div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Keterangan</label>
                            <textarea name="tenders[{{ $index }}][description]" class="form-control" rows="2">{{ $tender->description }}</textarea>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('sas.uspk.show', $uspk) }}" class="btn btn-secondary btn-lg">Batal</a>
        </div>
    </form>

    @push('scripts')
    <script>
        let tenderCount = {{ $uspk->tenders->count() }};
        const maxTenders = 3;
        const contractorsData = @json($contractors);
        const departmentsData = @json($departments);
        const jobsData = @json($jobs);
        const existingBlockIds = @json($uspk->block_ids ?? []);
        const selectedBudgetId = @json(old('uspk_budget_activity_id', $uspk->uspk_budget_activity_id));
        const initialDepartmentId = @json(old('department_id', $uspk->department_id));
        const initialJobId = @json(old('job_id', $uspk->job_id));
        const departmentSelect = document.getElementById('department_id');
        const jobSelect = document.getElementById('job_id');

        // --- Nama Kegiatan ---
        function handleNamaKegiatanChange(select) {
            const val = select.value;
            const customWrapper = document.getElementById('customTitleWrapper');
            const titleInput = document.getElementById('titleInput');
            const customInput = document.getElementById('customTitleInput');
            if (val === 'Lainnya') {
                customWrapper.style.display = 'block';
                titleInput.value = customInput.value;
                customInput.oninput = () => titleInput.value = customInput.value;
            } else {
                customWrapper.style.display = 'none';
                titleInput.value = val;
            }
        }

        const currentTitle = @json($uspk->title ?? '');
        if (currentTitle) {
            const select = document.getElementById('namaKegiatanSelect');
            let matched = false;
            for (let opt of select.options) {
                if (opt.value === currentTitle) { opt.selected = true; matched = true; break; }
            }
            if (!matched) {
                select.value = 'Lainnya';
                document.getElementById('customTitleWrapper').style.display = 'block';
                document.getElementById('customTitleInput').value = currentTitle;
            }
        }

        function getSiteIdByDepartment(departmentId) {
            const department = departmentsData.find(item => String(item.id) === String(departmentId));
            return department ? String(department.site_id ?? '') : '';
        }

        function renderJobOptions(selectedJobId = '') {
            const siteId = getSiteIdByDepartment(departmentSelect.value);
            let options = '<option value="">-- Pilih Aktivitas --</option>';

            if (!siteId) {
                jobSelect.innerHTML = options;
                return;
            }

            jobsData
                .filter(job => String(job.site_id ?? '') === String(siteId))
                .forEach(job => {
                    const selected = String(selectedJobId) === String(job.id) ? 'selected' : '';
                    const label = `${job.code ? `${job.code} - ` : ''}${job.name}`;
                    options += `<option value="${job.id}" ${selected}>${label}</option>`;
                });

            jobSelect.innerHTML = options;
        }

        // --- Tender ---
        function addTenderRow() {
            if (tenderCount >= maxTenders) { alert('Maksimal 3 tender pembanding.'); return; }
            const index = tenderCount;
            let contractorOptions = '<option value="">-- Pilih Kontraktor --</option>';
            contractorsData.forEach(c => {
                const company = c.company_name ? ` (${c.company_name})` : '';
                contractorOptions += `<option value="${c.id}">${c.name}${company}</option>`;
            });
            const html = `
                <div class="tender-row" data-index="${index}" style="background: var(--bg-input); border: 1px solid var(--border-color); border-radius: 10px; padding: 20px; margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                        <div style="font-weight: 600; font-size: 14px; color: var(--accent);"><i class="fas fa-user-tie"></i> Tender #${index + 1}</div>
                        <div class="d-flex align-center gap-2">
                            <label style="font-size: 12px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                <input type="radio" name="selected_tender" value="${index}" style="accent-color: var(--success);">
                                <span style="color: var(--success); font-weight: 600;">Tender Diajukan</span>
                            </label>
                            <button type="button" onclick="removeTenderRow(${index})" class="btn btn-danger btn-sm" style="padding: 4px 10px;"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <input type="hidden" name="tenders[${index}][is_selected]" value="0" id="tender_selected_${index}">
                    <div class="form-row">
                        <div class="form-group"><label class="form-label required">Kontraktor</label><select name="tenders[${index}][contractor_id]" class="form-control" required>${contractorOptions}</select></div>
                        <div class="form-group"><label class="form-label required">Nilai Tender (Rp)</label><input type="number" name="tenders[${index}][tender_value]" class="form-control" step="0.01" min="0" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">Durasi (Hari)</label><input type="number" name="tenders[${index}][tender_duration]" class="form-control" min="1"></div>
                        <div class="form-group"><label class="form-label">Lampiran</label><input type="file" name="tenders[${index}][attachment]" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
                    </div>
                    <div class="form-group mb-0"><label class="form-label">Keterangan</label><textarea name="tenders[${index}][description]" class="form-control" rows="2"></textarea></div>
                </div>`;
            document.getElementById('tendersContainer').insertAdjacentHTML('beforeend', html);
            tenderCount++;
            updateAddButton();
        }

        function removeTenderRow(index) {
            const row = document.querySelector(`.tender-row[data-index="${index}"]`);
            if (row) { row.remove(); tenderCount--; updateAddButton(); }
        }

        function updateAddButton() {
            document.getElementById('addTender').style.display = tenderCount >= maxTenders ? 'none' : '';
        }

        document.getElementById('uspkForm').addEventListener('submit', function() {
            document.querySelectorAll('[id^="tender_selected_"]').forEach(el => el.value = '0');
            const sel = document.querySelector('input[name="selected_tender"]:checked');
            if (sel) { const f = document.getElementById(`tender_selected_${sel.value}`); if (f) f.value = '1'; }
        });

        // --- Cascade Dropdowns ---
        document.getElementById('department_id').addEventListener('change', function() {
            const deptId = this.value;
            const subDeptSelect = document.getElementById('sub_department_id');
            const blockSelect = document.getElementById('block_ids');
            subDeptSelect.innerHTML = '<option value="">-- Memuat... --</option>';
            blockSelect.innerHTML = '';
            if (!deptId) { subDeptSelect.innerHTML = '<option value="">-- Pilih Afdeling --</option>'; return; }
            fetch(`/sas/api/sub-departments/${deptId}`).then(r => r.json()).then(data => {
                let o = '<option value="">-- Pilih Afdeling --</option>';
                data.forEach(sd => o += `<option value="${sd.id}">${sd.name}</option>`);
                subDeptSelect.innerHTML = o;
            });
        });

        document.getElementById('sub_department_id').addEventListener('change', function() {
            const subDeptId = this.value;
            const blockSelect = document.getElementById('block_ids');
            blockSelect.innerHTML = '<option disabled>-- Memuat... --</option>';
            if (!subDeptId) {
                blockSelect.innerHTML = '';
                loadBudgetOptions();
                return;
            }
            fetch(`/sas/api/blocks/${subDeptId}`).then(r => r.json()).then(data => {
                blockSelect.innerHTML = '';
                if (!Array.isArray(data) || data.length === 0) {
                    blockSelect.innerHTML = '<option disabled>-- Tidak ada blok aktif --</option>';
                    return;
                }

                data.forEach(b => {
                    const name = (b.name ?? b.block_name ?? b.nama ?? '').toString().trim();
                    const codeRaw = (b.code ?? b.block_code ?? '').toString().trim();
                    const code = codeRaw ? ` (${codeRaw})` : '';
                    const label = name ? `${name}${code}` : (codeRaw ? `Blok${code}` : `Blok #${b.id}`);

                    const opt = document.createElement('option');
                    opt.value = b.id;
                    opt.textContent = label;
                    if (existingBlockIds.includes(b.id)) opt.selected = true;
                    blockSelect.appendChild(opt);
                });
            }).catch(() => {
                blockSelect.innerHTML = '<option disabled>-- Gagal memuat blok --</option>';
            });

            loadBudgetOptions();
        });

        jobSelect.addEventListener('change', loadBudgetOptions);

        function loadBudgetOptions() {
            const subDepartmentId = document.getElementById('sub_department_id').value;
            const jobId = document.getElementById('job_id').value;
            const budgetSelect = document.getElementById('uspk_budget_activity_id');

            budgetSelect.innerHTML = '<option value="">-- Memuat budget... --</option>';

            if (!subDepartmentId) {
                budgetSelect.innerHTML = '<option value="">-- Pilih Budget (opsional) --</option>';
                return;
            }

            const params = new URLSearchParams({
                sub_department_id: subDepartmentId,
                year: new Date().getFullYear().toString(),
            });

            if (jobId) {
                params.append('job_id', jobId);
            }

            fetch(`/sas/api/budget-activities?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    budgetSelect.innerHTML = '<option value="">-- Pilih Budget (opsional) --</option>';

                    if (!Array.isArray(data) || data.length === 0) {
                        return;
                    }

                    data.forEach(item => {
                        const remaining = Number(item.budget_amount) - Number(item.used_amount);
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = `${item.job?.name ?? 'Job'} - Budget: ${formatCurrency(item.budget_amount)} | Sisa: ${formatCurrency(remaining)}`;

                        if (selectedBudgetId && Number(selectedBudgetId) === Number(item.id)) {
                            option.selected = true;
                        }

                        budgetSelect.appendChild(option);
                    });
                })
                .catch(() => {
                    budgetSelect.innerHTML = '<option value="">-- Gagal memuat budget --</option>';
                });
        }

        updateAddButton();

        if (departmentSelect.value) {
            renderJobOptions(initialJobId);
        } else {
            renderJobOptions('');
        }

        if (document.getElementById('sub_department_id').value) {
            loadBudgetOptions();
        }
    </script>
    @endpush
</x-serviceagreementsystem::layouts.master>
