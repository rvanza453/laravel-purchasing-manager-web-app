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
    <div class="space-y-6">
        <!-- Header -->
        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Card 1 -->
            <a href="<?php echo e(route('pr.index', ['status' => \Modules\PrSystem\Enums\PrStatus::PENDING->value])); ?>" 
                class="bg-white rounded-xl shadow-sm p-6 flex flex-col items-center justify-center border-b-4 border-yellow-400 hover:scale-[1.02] transition-all cursor-pointer hover:shadow-md group">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Menunggu</span>
                <span class="text-sm font-bold text-yellow-600 mb-2 group-hover:text-yellow-700">Pending Approval</span>
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-3xl font-bold text-gray-800"><?php echo e($stats['pending_approval']); ?></span>
                </div>
            </a>

            <!-- Card 2 -->
             <a href="<?php echo e(route('pr.index', ['status' => \Modules\PrSystem\Enums\PrStatus::APPROVED->value])); ?>" 
                class="bg-white rounded-xl shadow-sm p-6 flex flex-col items-center justify-center border-b-4 border-blue-400 hover:scale-[1.02] transition-all cursor-pointer hover:shadow-md group">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Menunggu</span>
                <span class="text-sm font-bold text-blue-600 mb-2 group-hover:text-blue-700">Waiting PO</span>
                 <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="text-3xl font-bold text-gray-800"><?php echo e($stats['waiting_po']); ?></span>
                </div>
            </a>

            <!-- Card 3 -->
            <a href="<?php echo e(route('pr.index', ['status' => \Modules\PrSystem\Enums\PrStatus::REJECTED->value])); ?>" 
                class="bg-white rounded-xl shadow-sm p-6 flex flex-col items-center justify-center border-b-4 border-red-400 hover:scale-[1.02] transition-all cursor-pointer hover:shadow-md group">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</span>
                <span class="text-sm font-bold text-red-600 mb-2 group-hover:text-red-700">Rejected</span>
                 <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="text-3xl font-bold text-gray-800"><?php echo e($stats['rejected']); ?></span>
                </div>
            </a>

            <!-- Card 4 -->
            <a href="<?php echo e(route('po.index', ['status' => 'Completed'])); ?>" 
                class="bg-white rounded-xl shadow-sm p-6 flex flex-col items-center justify-center border-b-4 border-green-400 hover:scale-[1.02] transition-all cursor-pointer hover:shadow-md group">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</span>
                <span class="text-sm font-bold text-green-600 mb-2 group-hover:text-green-700">PO Completed</span>
                 <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-3xl font-bold text-gray-800"><?php echo e($stats['po_completed']); ?></span>
                </div>
            </a>
        </div>

        <!-- Budget Summary Grid -->
        <h3 class="text-lg font-bold text-gray-800">Department Budget Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $departmentBudgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('pr.index', ['department_id' => $dept->id])); ?>" 
                    class="bg-white rounded-xl shadow-sm p-4 border-l-4 <?php echo e($dept->remaining_budget < 0 ? 'border-red-500' : 'border-green-500'); ?> hover:scale-[1.01] transition-all cursor-pointer hover:shadow-md group">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase group-hover:text-gray-700 transition-colors"><?php echo e($dept->name); ?></h4>
                            <span class="text-xs text-gray-400"><?php echo e($dept->site->name); ?></span>
                        </div>
                        <span class="text-xs font-bold px-2 py-1 rounded <?php echo e($dept->remaining_budget < 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'); ?>">
                            <?php echo e($dept->remaining_budget < 0 ? 'Over Budget' : 'Safe'); ?>

                        </span>
                    </div>
                    <div class="mt-3">
                        <span class="block text-2xl font-bold <?php echo e($dept->remaining_budget < 0 ? 'text-red-600' : 'text-gray-800'); ?>">
                            Rp <?php echo e(number_format($dept->remaining_budget, 0, ',', '.')); ?>

                        </span>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Remaining Balance</span>
                            <span title="Allocated: <?php echo e(number_format($dept->calculated_budget)); ?> | Used: <?php echo e(number_format($dept->used_budget)); ?>">
                                (Alloc: <?php echo e(number_format($dept->calculated_budget, 0, ',', '.')); ?>)
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH C:\Users\deniz\Downloads\plantation.oilpam.my.id (1)\plantation.oilpam.my.id\Modules/PrSystem\resources/views/dashboard.blade.php ENDPATH**/ ?>