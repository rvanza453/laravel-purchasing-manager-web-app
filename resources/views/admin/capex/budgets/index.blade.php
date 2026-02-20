<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Capex Budgets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Create New Budget -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <h3 class="text-lg font-bold mb-4">Set Budget</h3>
                <form action="{{ route('admin.capex.budgets.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department</label>
                        <select name="department_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Asset</label>
                        <select name="capex_asset_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->code }} - {{ $asset->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Budget Code Auto Generated --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Quantity</label>
                        <input type="number" name="original_quantity" min="1" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                        <input type="number" name="amount" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fiscal Year</label>
                        <input type="number" name="fiscal_year" value="{{ date('Y') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    </div>
                    <div class="flex items-center pt-6">
                        <input type="checkbox" name="is_budgeted" value="1" checked id="is_budgeted" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <label for="is_budgeted" class="ml-2 block text-sm text-gray-900">Is Budgeted (Dianggarkan)</label>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Set Budget</button>
                    </div>
                </form>
            </div>

            <!-- List Budgets -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dept & Asset</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty (Rem)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total (Rem)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($budgets as $budget)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $budget->budget_code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $budget->department->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $budget->capexAsset->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $budget->fiscal_year }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $budget->original_quantity }} 
                                        <span class="text-xs text-gray-400">({{ $budget->remaining_quantity }})</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="font-bold">Rp {{ number_format($budget->amount, 0) }}</div>
                                        <div class="text-xs text-green-600">Rem: Rp {{ number_format($budget->remaining_amount, 0) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($budget->is_budgeted)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Budgeted</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Unbudgeted</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <form action="{{ route('admin.capex.budgets.destroy', $budget) }}" method="POST" onsubmit="return confirm('Delete this budget?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
