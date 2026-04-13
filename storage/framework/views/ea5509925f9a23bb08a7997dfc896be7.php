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
    <div class="max-w-4xl mx-auto py-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Reset Data Warehouse & Budget</h2>
            <p class="text-sm text-gray-500">
                Fitur ini akan <strong class="text-red-600">MENGHAPUS SEMUA</strong> data stock dan riwayat pergerakan di Warehouse, 
                serta mereset seluruh `used_amount` di tabel Budget menjadi 0.
            </p>
        </div>

        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">PERINGATAN!</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>Tindakan ini tidak dapat dibatalkan. Pastikan Anda benar-benar ingin mereset data sebelum melanjutkan.</p>
                        <ul class="list-disc pl-5 mt-1">
                            <li>Menghapus semua records di tabel <code class="bg-red-100 px-1 rounded">warehouse_stocks</code></li>
                            <li>Menghapus semua records di tabel <code class="bg-red-100 px-1 rounded">stock_movements</code></li>
                            <li>Mereset nilai di tabel <code class="bg-red-100 px-1 rounded">budgets</code> kolom `used_amount` menjadi 0</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
            <form action="<?php echo e(route('system.reset-warehouse.post')); ?>" method="POST" onsubmit="return confirm('APAKAH ANDA YAKIN INGIN MERESET SEMUA DATA WAREHOUSE DAN BUDGET? TINDAKAN INI TIDAK BISA DIBATALKAN!');">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="reset_warehouse">
                <div class="mb-4">
                    <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Masukkan Password Verifikasi Admin
                    </label>
                    <input type="password" name="admin_password" id="admin_password" required
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                           placeholder="Password Verifikasi" autocomplete="off">
                    <?php $__errorArgs = ['admin_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <a href="<?php echo e(route('pr.dashboard')); ?>" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Eksekusi Reset Data
                    </button>
                </div>
            </form>

            <div class="border-t pt-6">
                <div class="mb-3">
                    <h3 class="text-lg font-semibold text-gray-800">Clear Cache Laravel</h3>
                    <p class="text-sm text-gray-500">Gunakan ini jika halaman masih 404 karena route/config/view cache belum bersih. Ini tidak menghapus data aplikasi.</p>
                </div>
                <form action="<?php echo e(route('system.reset-warehouse.post')); ?>" method="POST" onsubmit="return confirm('Bersihkan cache Laravel sekarang? Tindakan ini aman untuk data, hanya menghapus cache, route, config, dan view cache.');">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="clear_cache">
                    <div class="mb-4">
                        <label for="admin_password_cache" class="block text-sm font-medium text-gray-700 mb-1">
                            Masukkan Password Verifikasi Admin
                        </label>
                        <input type="password" name="admin_password" id="admin_password_cache" required
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               placeholder="Password Verifikasi" autocomplete="off">
                        <?php $__errorArgs = ['admin_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Clear Cache
                        </button>
                    </div>
                </form>
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
<?php /**PATH C:\Users\deniz\Downloads\plantation.oilpam.my.id (1)\plantation.oilpam.my.id\Modules/PrSystem\resources/views/admin/system/reset-warehouse.blade.php ENDPATH**/ ?>