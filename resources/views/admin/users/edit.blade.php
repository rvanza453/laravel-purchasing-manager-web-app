<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Edit Pengguna</h2>
            <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-900">Kembali</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <!-- Role -->
                <div>
                    <x-input-label for="role" :value="__('Role')" />
                    <select id="role" name="role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', $user->roles->first()->name ?? '') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('role')" />
                </div>

                <!-- Site -->
                <div>
                    <x-input-label for="site_id" :value="__('Site')" />
                    <select id="site_id" name="site_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Pilih Site (Opsional)</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ old('site_id', $user->site_id) == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('site_id')" />
                </div>

                <!-- Department -->
                <div>
                    <x-input-label for="department_id" :value="__('Departemen')" />
                    <select id="department_id" name="department_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Pilih Departemen (Opsional)</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('department_id')" />
                </div>

                <!-- Position -->
                <div>
                    <x-input-label for="position" :value="__('Posisi / Jabatan')" />
                    <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" :value="old('position', $user->position)" />
                    <x-input-error class="mt-2" :messages="$errors->get('position')" />
                </div>

                <div class="border-t border-gray-100 pt-6 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ubah Password (Opsional)</h3>
                    
                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password Baru')" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah password.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('password')" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-4">
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                        <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Batal
                    </a>
                    <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
