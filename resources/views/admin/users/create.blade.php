<x-admin-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Tambah Pengguna</h2>
            <p class="text-gray-600">Buat akun baru dan atur role per modul.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded-lg shadow p-6 space-y-6">
            @include('admin.users._form')
        </form>
    </div>
</x-admin-layout>
