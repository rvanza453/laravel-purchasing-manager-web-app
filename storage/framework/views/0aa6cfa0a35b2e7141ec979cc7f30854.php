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
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                 <h2 class="text-2xl font-bold text-gray-800">Riwayat Pergerakan Stok</h2>
                 <p class="text-gray-500">Gudang: <?php echo e($warehouse->name); ?></p>
            </div>
            
            <a href="<?php echo e(route('inventory.show', $warehouse)); ?>" class="text-sm text-gray-500 hover:text-gray-700 md:ml-auto">
                &larr; Kembali ke Stok
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="GET" action="<?php echo e(route('inventory.history', $warehouse)); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                <!-- Filter Product -->
                <div>
                     <label class="block text-sm font-medium text-gray-700 mb-1">Barang</label>
                     <select name="product_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="">Semua Barang</option>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($product->id); ?>" <?php echo e(request('product_id') == $product->id ? 'selected' : ''); ?>>
                                <?php echo e($product->code); ?> - <?php echo e($product->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                     </select>
                </div>

                <!-- Filter Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                    <select name="type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="">Semua Tipe</option>
                        <option value="IN" <?php echo e(request('type') == 'IN' ? 'selected' : ''); ?>>Barang Masuk</option>
                        <option value="OUT" <?php echo e(request('type') == 'OUT' ? 'selected' : ''); ?>>Barang Keluar</option>
                    </select>
               </div>

               <!-- Filter Date Range -->
               <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
               </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <div class="flex gap-2">
                        <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- History Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto pr-mobile-scroll">
            <table class="min-w-[1100px] w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tujuan / Keperluan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oleh</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                <?php echo e($movement->date->format('d/m/Y')); ?>

                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if($movement->type === 'IN'): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                        MASUK
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                        KELUAR
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-bold text-gray-800"><?php echo e($movement->product->name); ?></div>
                                <div class="text-xs text-gray-500 bg-gray-100 inline-block px-1.5 py-0.5 rounded mt-0.5"><?php echo e($movement->product->code); ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-bold text-center">
                                <?php echo e($movement->quantity); ?> <?php echo e($movement->product->unit); ?>

                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php if($movement->type === 'OUT'): ?>
                                    <div class="font-bold">
                                        <?php echo e($movement->subDepartment->department->name ?? ''); ?> - <?php echo e($movement->subDepartment->name ?? '-'); ?>

                                    </div>
                                    <?php if($movement->job): ?>
                                        <div class="text-xs text-gray-500"><?php echo e($movement->job->code); ?> - <?php echo e($movement->job->name); ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium text-right whitespace-nowrap">
                                Rp <?php echo e(number_format($movement->quantity * $movement->price, 0, ',', '.')); ?>

                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?php echo e($movement->remarks ?? '-'); ?>

                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                        <?php echo e(substr($movement->user->name ?? 'S', 0, 1)); ?>

                                    </div>
                                    <?php echo e($movement->user->name ?? 'System'); ?>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    <span class="text-lg font-medium text-gray-900">Tidak ada riwayat ditemukan</span>
                                    <p class="text-sm text-gray-500 mt-1">Coba sesuaikan filter pencarian anda.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                <?php echo e($movements->links()); ?>

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
<?php /**PATH C:\Users\deniz\Downloads\plantation.oilpam.my.id (1)\plantation.oilpam.my.id\Modules/PrSystem\resources/views/inventory/history.blade.php ENDPATH**/ ?>