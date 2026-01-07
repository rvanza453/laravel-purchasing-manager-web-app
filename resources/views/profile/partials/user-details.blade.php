<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Kepegawaian') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Informasi detail mengenai posisi dan penempatan kerja Anda.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update-employment') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Site -->
        <div>
            <x-input-label for="site_id" :value="__('Lokasi / Site')" />
            <select id="site_id" name="site_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">-- Pilih Site --</option>
                @foreach($sites as $site)
                    <option value="{{ $site->id }}" {{ object_get($user, 'site_id') == $site->id ? 'selected' : '' }}>
                        {{ $site->name }} ({{ $site->code }})
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('site_id')" />
        </div>

        <!-- Department -->
        <div>
            <x-input-label for="department_id" :value="__('Departemen')" />
             <select id="department_id" name="department_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">-- Pilih Departemen --</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ object_get($user, 'department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }} ({{ $dept->code }})
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('department_id')" />
        </div>

        <!-- Position -->
        <div>
            <x-input-label for="position" :value="__('Jabatan / Posisi')" />
            <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" :value="old('position', $user->position)" />
            <x-input-error class="mt-2" :messages="$errors->get('position')" />
        </div>

        <!-- Role (Read Only) -->
        <div>
            <x-input-label for="role" :value="__('Role Akses (Tidak dapat diubah)')" />
            <div class="mt-1 block w-full p-2.5 bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg">
                @foreach($user->getRoleNames() as $role)
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">{{ $role }}</span>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'employment-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
