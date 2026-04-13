@csrf

<div class="space-y-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
        <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('name', $user->name) }}" required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
        <input type="text" name="username" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('username', $user->username) }}" placeholder="contoh: muhammad_revanza" required>
        <small class="text-gray-500">Gunakan huruf, angka, garis bawah, atau tanda minus.</small>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('email', $user->email) }}" required>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp / HP</label>
        <input type="text" name="phone_number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('phone_number', $user->phone_number) }}">
        <small class="text-gray-500">Format: 08123xxx atau 628123xxx</small>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password {{ $isEdit ? '(Kosongkan jika tidak diubah)' : '' }}</label>
        <input type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" {{ $isEdit ? '' : 'required' }}>
    </div>

    <div class="pt-4 border-t border-gray-200">
        <h3 class="text-md font-semibold text-gray-800 mb-4">Informasi Organisasi & Lokasi</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                <select name="site_id" id="site_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Pilih Site (Opsional)</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" @selected(old('site_id', $user->site_id) == $site->id)>{{ $site->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unit / Department</label>
                <select name="department_id" id="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Pilih Unit (Opsional)</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" class="dept-option" data-site="{{ $dept->site_id }}" @selected(old('department_id', $user->department_id) == $dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Posisi / Jabatan</label>
                <input type="text" name="position" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" value="{{ old('position', $user->position) }}">
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Global Role (Opsional)</label>
        <select name="global_role" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">Tanpa Global Role</option>
            @foreach($spatieRoles as $role)
                <option value="{{ $role }}" @selected(old('global_role', $selectedGlobalRole) === $role)>{{ $role }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-3">Role Per Module</label>
        <div class="space-y-3">
            @foreach($moduleRoleConfig as $moduleKey => $moduleConfig)
                <div class="border border-gray-200 rounded-md p-3">
                    <strong class="text-sm text-gray-800">{{ $moduleConfig['label'] ?? strtoupper($moduleKey) }}</strong>
                    <select name="module_roles[{{ $moduleKey }}]" class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tidak di-set</option>
                        @foreach(($moduleConfig['roles'] ?? []) as $moduleRole)
                            <option value="{{ $moduleRole }}" @selected(old('module_roles.' . $moduleKey, $selectedModuleRoles[$moduleKey] ?? null) === $moduleRole)>
                                {{ $moduleRole }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endforeach
        </div>
        <small class="text-gray-500 mt-2 block">Role ini memungkinkan 1 user memiliki role berbeda di setiap modul.</small>
    </div>
</div>

<div class="flex gap-3 justify-end pt-6 border-t border-gray-200">
    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500">{{ $isEdit ? 'Simpan Perubahan' : 'Buat Pengguna' }}</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const siteSelect = document.getElementById('site_id');
    const deptSelect = document.getElementById('department_id');
    
    // Fallback original departments for local filtering approach (if all rendered)
    const originalDepts = Array.from(deptSelect.querySelectorAll('.dept-option')).map(opt => ({
        id: opt.value,
        name: opt.textContent,
        site_id: opt.getAttribute('data-site'),
        selected: opt.selected
    }));
    
    // We already have API route too, but caching all and filtering is also fine if they are passed.
    // However, if we didn't pass site_id to departments in view natively easily, let's use the API route.
    siteSelect.addEventListener('change', function() {
        const siteId = this.value;
        
        deptSelect.innerHTML = '<option value="">Memuat...</option>';
        
        if (!siteId) {
            deptSelect.innerHTML = '<option value="">Pilih Unit (Opsional)</option>';
            return;
        }
        
        // Fetch via API route
        fetch(`/admin/api/sites/${siteId}/departments`)
            .then(res => res.json())
            .then(data => {
                deptSelect.innerHTML = '<option value="">Pilih Unit (Opsional)</option>';
                data.forEach(dept => {
                    const selected = "{{ old('department_id', $user->department_id) }}" == dept.id ? 'selected' : '';
                    deptSelect.innerHTML += `<option value="${dept.id}" ${selected}>${dept.name}</option>`;
                });
            })
            .catch(err => {
                console.error('Fetch error:', err);
                deptSelect.innerHTML = '<option value="">Pilih Unit (Opsional)</option>';
            });
    });
    
    // Trigger on initial load if site is already selected and it's create mode
    // (for edit mode, options are already there but might need filtering if site changes)
});
</script>
