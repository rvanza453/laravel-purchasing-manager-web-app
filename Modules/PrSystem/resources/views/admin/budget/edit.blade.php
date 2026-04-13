<x-prsystem::app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(isset($department))
                Manage Budget: {{ $department->name }}
                <span class="text-sm font-normal text-gray-500 ml-2">({{ $department->site->name }})</span>
            @else
                Manage Budget: {{ $subDepartment->name }}
                <span class="text-sm font-normal text-gray-500 ml-2">({{ $subDepartment->department->name }})</span>
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ isset($department) ? route('admin.budgets.update-department', $department) : route('admin.budgets.update', $subDepartment) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            @if(isset($isJobCoa) && $isJobCoa)
                                <div class="flex items-center justify-between gap-4 mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Budget Limits per Job (Year {{ date('Y') }})</h3>
                                        <p class="text-sm text-gray-500 mt-1">Isi budget dasar dan PTA untuk tiap job.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                    @foreach ($jobs as $job)
                                        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md">
                                            <div class="border-b border-gray-100 px-4 py-3 bg-gradient-to-r from-slate-50 to-white rounded-t-2xl">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <x-prsystem::input-label :for="'budget_'.$job->id" :value="$job->code . ' - ' . $job->name" />
                                                        <p class="mt-1 text-xs text-gray-500">Masukkan budget dasar dan PTA tambahan untuk job ini.</p>
                                                    </div>
                                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 whitespace-nowrap">
                                                        {{ $job->code }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="p-4 space-y-4">
                                                <div>
                                                    <label for="budget_{{ $job->id }}" class="block text-xs font-medium text-gray-600">Budget Dasar (Rp)</label>
                                                    <input type="number"
                                                           name="budgets[{{ $job->id }}]"
                                                           id="budget_{{ $job->id }}"
                                                           class="mt-1 block w-full rounded-xl border-gray-300 bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                           placeholder="0"
                                                           value="{{ old('budgets.'.$job->id, $existingBudgets[$job->id] ?? 0) }}"
                                                           min="0"
                                                           step="any">
                                                </div>
                                                <div>
                                                    <label for="pta_budget_{{ $job->id }}" class="block text-xs font-medium text-gray-600">PTA Tambahan (Rp)</label>
                                                    <input type="number"
                                                           name="pta_budgets[{{ $job->id }}]"
                                                           id="pta_budget_{{ $job->id }}"
                                                           class="mt-1 block w-full rounded-xl border-gray-300 bg-blue-50/40 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                           placeholder="0"
                                                           value="{{ old('pta_budgets.'.$job->id, $existingPtaBudgets[$job->id] ?? 0) }}"
                                                           min="0"
                                                           step="any">
                                                </div>
                                                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm">
                                                    <span class="text-gray-600">Total limit</span>
                                                    <span class="font-bold text-indigo-700">Rp {{ number_format(($existingBudgets[$job->id] ?? 0) + ($existingPtaBudgets[$job->id] ?? 0), 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex items-center justify-between gap-4 mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Budget Limits per Category (Year {{ date('Y') }})</h3>
                                        <p class="text-sm text-gray-500 mt-1">Isi budget dasar dan PTA untuk tiap kategori.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                    @foreach ($categories as $category)
                                        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md">
                                            <div class="border-b border-gray-100 px-4 py-3 bg-gradient-to-r from-slate-50 to-white rounded-t-2xl">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <x-prsystem::input-label :for="'budget_'.$category" :value="$category" />
                                                        <p class="mt-1 text-xs text-gray-500">Masukkan budget dasar dan PTA tambahan untuk kategori ini.</p>
                                                    </div>
                                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 whitespace-nowrap">
                                                        Budget
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="p-4 space-y-4">
                                                <div>
                                                    <label for="budget_{{ Str::slug($category) }}" class="block text-xs font-medium text-gray-600">Budget Dasar (Rp)</label>
                                                    <input type="number"
                                                           name="budgets[{{ $category }}]"
                                                           id="budget_{{ Str::slug($category) }}"
                                                           class="mt-1 block w-full rounded-xl border-gray-300 bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                           placeholder="0"
                                                           value="{{ old('budgets.'.$category, $existingBudgets[$category] ?? 0) }}"
                                                           min="0"
                                                           step="any">
                                                </div>
                                                <div>
                                                    <label for="pta_budget_{{ Str::slug($category) }}" class="block text-xs font-medium text-gray-600">PTA Tambahan (Rp)</label>
                                                    <input type="number"
                                                           name="pta_budgets[{{ $category }}]"
                                                           id="pta_budget_{{ Str::slug($category) }}"
                                                           class="mt-1 block w-full rounded-xl border-gray-300 bg-blue-50/40 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                           placeholder="0"
                                                           value="{{ old('pta_budgets.'.$category, $existingPtaBudgets[$category] ?? 0) }}"
                                                           min="0"
                                                           step="any">
                                                </div>
                                                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm">
                                                    <span class="text-gray-600">Total limit</span>
                                                    <span class="font-bold text-indigo-700">Rp {{ number_format(($existingBudgets[$category] ?? 0) + ($existingPtaBudgets[$category] ?? 0), 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-4 border-t pt-4">
                            <x-prsystem::primary-button>{{ __('Save Budget Configuration') }}</x-prsystem::primary-button>
                            <a href="{{ route('admin.budgets.index') }}" class="text-gray-600 hover:text-gray-900">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-prsystem::app-layout>
