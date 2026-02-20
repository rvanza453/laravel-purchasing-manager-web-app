<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Capex Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if($budgets->isEmpty())
                    <div class="bg-yellow-50 text-yellow-800 p-4 rounded-lg mb-4">
                        Warning: No Active Budgets found for this year. Please ask Admin to set up Capex Budgets.
                    </div>
                @else
                
                <form action="{{ route('capex.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Basic Info -->
                    <!-- Request Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <select name="department_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Request Type</label>
                            <div class="mt-2 space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="type" value="New" class="form-radio text-indigo-600" checked>
                                    <span class="ml-2">New</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="type" value="Modification" class="form-radio text-indigo-600">
                                    <span class="ml-2">Modification</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="type" value="Replacement" class="form-radio text-indigo-600">
                                    <span class="ml-2">Replacement</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" name="quantity" id="quantity" min="1" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Price Per Unit (Rp)</label>
                            <input type="number" name="price" id="price" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                            <input type="text" id="total_amount" class="mt-1 block w-full rounded-md border-gray-200 bg-gray-100 text-gray-500" readonly>
                        </div>
                        
                        <div class="flex items-center pt-6">
                            <input type="checkbox" name="code_budget_ditanam" value="1" checked id="code_budget_ditanam" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <label for="code_budget_ditanam" class="ml-2 block text-sm text-gray-900">Is Budgeted (Dianggarkan)</label>
                        </div>
                    </div>

                    <script>
                        const qtyInput = document.getElementById('quantity');
                        const priceInput = document.getElementById('price');
                        const totalInput = document.getElementById('total_amount');

                        function calculateTotal() {
                            const qty = parseFloat(qtyInput.value) || 0;
                            const price = parseFloat(priceInput.value) || 0;
                            const total = qty * price;
                            totalInput.value = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(total);
                        }

                        qtyInput.addEventListener('input', calculateTotal);
                        priceInput.addEventListener('input', calculateTotal);
                    </script>

                    <!-- Budget Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Select Budget / Asset</label>
                        <select name="capex_budget_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">-- Choose Asset Budget --</option>
                            @foreach($budgets as $budget)
                                <option value="{{ $budget->id }}">
                                    [{{ $budget->budget_code }}] {{ $budget->capexAsset->name }} (Remaining: Rp {{ number_format($budget->remaining_amount, 0) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Select the locked budget item for this request.</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-bold mb-4 text-gray-800">Justifikasi & Kuesioner</h3>
                        <div class="space-y-4">
                            @php
                                $questions = [
                                    1 => 'Apa yang biasa dipakai selama ini?',
                                    2 => 'Mengapa pengeluaran diperlukan?',
                                    3 => 'Dapatkah pengeluaran ditunda pada tahun depan? jika tidak, mengapa?',
                                    4 => 'Apa konsekuensi jika pengeluaran di tolak?',
                                    5 => 'Mungkinkah ada dampak buruk pada operasi yang ada? (contoh: Kekacauan, waktu, lingkungan)',
                                    6 => 'Berapa lama proyek berlangsung? Kapan proyek tersebut selesai?'
                                ];
                            @endphp

                            @foreach($questions as $index => $q)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $index }}. {{ $q }}</label>
                                    <textarea name="questionnaire[{{ $index }}]" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-indigo-700 transition">
                            Submit Capex Request
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
