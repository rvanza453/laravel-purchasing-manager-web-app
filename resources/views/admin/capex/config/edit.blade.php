<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Configure Capex Approval: {{ $department->name }}</h2>
            <a href="{{ route('admin.capex.config.index') }}" class="text-gray-600 hover:text-gray-900">Back to List</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="{{ route('admin.capex.config.update', $department) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div class="grid grid-cols-12 gap-4 px-2 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100 pb-2">
                        <div class="col-span-1 text-center">Step</div>
                        <div class="col-span-3">Label (Display Name)</div>
                        <div class="col-span-3">Approver (Role-based)</div>
                        <div class="col-span-1 text-center">OR</div>
                        <div class="col-span-4">Approver (Specific User)</div>
                    </div>

                    @foreach($configs as $index => $config)
                        <div class="grid grid-cols-12 gap-4 items-center bg-gray-50 p-4 rounded-lg border border-gray-200 hover:border-indigo-300 transition-colors">
                            <!-- Hidden ID -->
                            <input type="hidden" name="configs[{{$index}}][id]" value="{{ $config->id }}">
                            
                            <!-- Step Badge -->
                            <div class="col-span-1 flex justify-center">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                    {{ $config->column_index }}
                                </div>
                            </div>
                            
                            <!-- Label -->
                            <div class="col-span-3">
                                <input type="text" name="configs[{{$index}}][label]" value="{{ $config->label }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="e.g. Manager" required>
                            </div>
                            
                            <!-- Role Approver -->
                            <div class="col-span-3">
                                <select name="configs[{{$index}}][approver_role]" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">-- No Role --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ $config->approver_role == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- OR -->
                            <div class="col-span-1 text-center text-gray-400 text-xs font-bold">OR</div>
                            
                            <!-- User Approver -->
                            <div class="col-span-4">
                                <select name="configs[{{$index}}][approver_user_id]" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">-- No Specific User --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $config->approver_user_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end pt-4 border-t border-gray-100">
                    <x-primary-button>
                        {{ __('Save All Configuration') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
        
        <div class="p-4 bg-blue-50 text-blue-700 text-sm rounded-lg border border-blue-100 flex items-start gap-3">
             <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
             <div>
                <p class="font-bold mb-1">How Capex Approval Works:</p>
                <ul class="list-disc list-inside space-y-1 ml-1">
                    <li>The system will check <strong>Step 1 to 5</strong> sequentially.</li>
                    <li>If a <strong>Specific User</strong> is selected, only that user can approve.</li>
                    <li>If a <strong>Role</strong> is selected, any user with that role can approve.</li>
                    <li>If both are empty, the step might be skipped or fail (ensure at least one is set).</li>
                </ul>
             </div>
        </div>
    </div>
</x-app-layout>
