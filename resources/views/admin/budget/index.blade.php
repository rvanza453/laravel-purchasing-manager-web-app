<x-app-layout>
    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Budget Departemen</h2>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departemen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Budget</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Update Budget</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($departments as $dept)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $dept->name }} ({{ $dept->code }})</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $dept->site->name }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                Rp {{ number_format($dept->budget, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 text-right">
                                <form action="{{ route('admin.budget.update', $dept) }}" method="POST" class="flex items-center justify-end gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="budget" value="{{ $dept->budget }}" class="block w-40 rounded-md border-0 py-1.5 pl-10 pr-2 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 text-right" required>
                                    </div>
                                    <button type="submit" class="bg-primary-600 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-primary-700">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
