<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage Budget: {{ $subDepartment->name }}
            <span class="text-sm font-normal text-gray-500 ml-2">({{ $subDepartment->department->name }})</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.budgets.update', $subDepartment) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Budget Limits per Category (Year {{ date('Y') }})</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($categories as $category)
                                    <div>
                                        <x-input-label :for="'budget_'.$category" :value="$category" />
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input type="number" 
                                                   name="budgets[{{ $category }}]" 
                                                   id="budget_{{Str::slug($category)}}"
                                                   class="block w-full rounded-md border-gray-300 pl-12 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                                   placeholder="0"
                                                   value="{{ old('budgets.'.$category, $existingBudgets[$category] ?? 0) }}"
                                                   min="0"
                                                   step="1000">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center gap-4 border-t pt-4">
                            <x-primary-button>{{ __('Save Budget Configuration') }}</x-primary-button>
                            <a href="{{ route('admin.budgets.index') }}" class="text-gray-600 hover:text-gray-900">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
