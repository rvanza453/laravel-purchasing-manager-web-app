<?php if (isset($component)) { $__componentOriginal06cf778fd0d059309aebf5aee808823e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal06cf778fd0d059309aebf5aee808823e = $attributes; } ?>
<?php $component = Modules\PrSystem\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('prsystem::app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Modules\PrSystem\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Daftar Capex Request')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <a href="<?php echo e(route('capex.create')); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    + Create New Capex
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capex Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Step</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $capexRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $capex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                            <a href="<?php echo e(route('capex.show', $capex)); ?>"><?php echo e($capex->capex_number); ?></a>
                                            <div class="text-xs text-gray-400 mt-1"><?php echo e($capex->created_at->format('d M Y')); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo e($capex->department->name ?? '-'); ?>

                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs" title="<?php echo e($capex->description); ?>">
                                            <div class="font-bold text-gray-900"><?php echo e($capex->capexBudget->capexAsset->name ?? '-'); ?></div>
                                            <div class="mt-1 text-xs truncate" title="<?php echo e($capex->description); ?>"><?php echo e($capex->description); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                            Rp <?php echo e(number_format($capex->amount, 0)); ?>

                                            <div class="mt-1 font-normal">
                                                <?php if($capex->code_budget_ditanam): ?>
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-800 border border-green-200">
                                                        ✓ Dianggarkan<?php echo e($capex->capexBudget->pta_amount > 0 ? ' + PTA' : ''); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                        ⚠ Tidak Dianggarkan<?php echo e($capex->capexBudget->pta_amount > 0 ? ' + PTA' : ''); ?>

                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo e($capex->status === 'Approved' ? 'bg-green-100 text-green-800' : ''); ?>

                                                <?php echo e($capex->status === 'Rejected' ? 'bg-red-100 text-red-800' : ''); ?>

                                                <?php echo e($capex->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : ''); ?>">
                                                <?php echo e($capex->status); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            Step <?php echo e($capex->current_step); ?> / 5
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="<?php echo e(route('capex.show', $capex)); ?>" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded-md">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                            No Capex Requests found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <?php echo e($capexRequests->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal06cf778fd0d059309aebf5aee808823e)): ?>
<?php $attributes = $__attributesOriginal06cf778fd0d059309aebf5aee808823e; ?>
<?php unset($__attributesOriginal06cf778fd0d059309aebf5aee808823e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal06cf778fd0d059309aebf5aee808823e)): ?>
<?php $component = $__componentOriginal06cf778fd0d059309aebf5aee808823e; ?>
<?php unset($__componentOriginal06cf778fd0d059309aebf5aee808823e); ?>
<?php endif; ?>
<?php /**PATH C:\Users\deniz\Downloads\plantation.oilpam.my.id (1)\plantation.oilpam.my.id\Modules/PrSystem\resources/views/capex/index.blade.php ENDPATH**/ ?>