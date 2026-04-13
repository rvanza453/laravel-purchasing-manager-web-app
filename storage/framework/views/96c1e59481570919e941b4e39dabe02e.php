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
    <div class="py-12 bg-gray-50"> 
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        <?php echo e(__('Master Products')); ?>

                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Kelola data produk, harga, dan ketersediaan site.</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('products.export')); ?>" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-200 transition ease-in-out duration-150 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export CSV
                    </a>
                    
                    <a href="<?php echo e(route('products.create')); ?>" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition ease-in-out duration-150 shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Tambah Produk
                    </a>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
                <form action="<?php echo e(route('products.index')); ?>" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-1/4">
                        <label for="site_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Filter Lokasi</label>
                        <div class="relative">
                            <select name="site_id" id="site_id" onchange="this.form.submit()" class="block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg">
                                <option value="">-- Semua Site --</option>
                                <option value="non_active" <?php echo e(request('site_id') == 'non_active' ? 'selected' : ''); ?> class="text-red-600 font-semibold">
                                    Non-Aktif (Tanpa Site)
                                </option>
                                <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($site->id); ?>" <?php echo e(request('site_id') == $site->id ? 'selected' : ''); ?>>
                                        <?php echo e($site->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="w-full md:w-3/4">
                        <label for="search" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pencarian</label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>" placeholder="Cari Kode atau Nama Produk..." class="block w-full pl-4 py-2.5 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" style="padding-right: 4.5rem;">
                            <button type="submit" class="bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-700 transition" style="position: absolute; right: 4px; top: 4px; bottom: 4px; padding-left: 1rem; padding-right: 1rem;">
                                Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Code</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Product Name</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Category</th>
                                <th class="px-6 py-4 font-bold tracking-wider" width="25%">Availability</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Price (Est)</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-center">Minimal Stock</th>
                                
                                <th class="px-6 py-4 font-bold tracking-wider text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out group">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-mono">
                                            <?php echo e($product->code); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800 text-base"><?php echo e($product->name); ?></div>
                                        <div class="text-xs text-gray-400 mt-0.5"><?php echo e($product->unit); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <?php echo e($product->category); ?>

                                        </span>
                                    </td>
                                    
                                    
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1.5">
                                            <?php $__empty_2 = true; $__currentLoopData = $product->sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                    <?php echo e($site->name); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                <span class="inline-flex items-center text-xs text-red-500 bg-red-50 px-2 py-1 rounded border border-red-100">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Non-Aktif
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-right font-mono font-medium text-gray-700">
                                        Rp <?php echo e(number_format($product->price_estimation, 0, ',', '.')); ?>

                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="<?php echo e($product->min_stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?> px-3 py-1 rounded-full text-xs font-bold">
                                            <?php echo e($product->min_stock); ?>

                                        </span>
                                    </td>
                                    
                                    
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex justify-center items-center gap-2 opacity-100 group-hover:opacity-100 transition-opacity">
                                            
                                            <a href="<?php echo e(route('products.edit', $product)); ?>" class="inline-flex items-center px-3 py-1.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-md text-xs font-semibold transition border border-amber-200">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                Edit
                                            </a>
                                            
                                            <form action="<?php echo e(route('products.destroy', $product)); ?>" method="POST" class="inline" onsubmit="return confirmDelete(this, 'produk', '<?php echo e($product->name); ?>')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <input type="hidden" name="admin_password" class="delete-password">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md text-xs font-semibold transition border border-red-200">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-16 text-center text-gray-500 bg-white">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-100 rounded-full p-4 mb-3">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900">Belum ada produk</h3>
                                            <p class="text-sm text-gray-500 mt-1">Coba sesuaikan filter atau tambah produk baru.</p>
                                            <a href="<?php echo e(route('products.create')); ?>" class="mt-4 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                                + Tambah Produk Sekarang
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <?php echo e($products->links()); ?>

                </div>
            </div>
        </div>
    </div>

    <script>
    function confirmDelete(form, type, name) {
        const password = prompt(`Masukkan password verifikasi untuk menghapus ${type} "${name}":`);
        if (password === null) {
            return false; // User cancelled
        }
        form.querySelector('.delete-password').value = password;
        return true;
    }
    </script>
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
<?php /**PATH C:\Users\deniz\Downloads\plantation.oilpam.my.id (1)\plantation.oilpam.my.id\Modules/PrSystem\resources/views/admin/products/index.blade.php ENDPATH**/ ?>