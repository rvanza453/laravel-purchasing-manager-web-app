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
            <?php echo e(__('Import Stock Awal')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <?php if(session('success')): ?>
                        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                            <ul class="list-disc list-inside">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-sm text-slate-700">
                        <p class="font-semibold mb-2">Format minimum kolom CSV:</p>
                        <p class="font-mono text-xs">Warehouse, Item ID, Item Name, Unit, Qty, Price</p>
                        <p class="mt-2 text-xs text-slate-600">Nama file bebas. Sistem akan membaca isi kolom, tidak wajib nama file tertentu.</p>
                    </div>

                    <form action="<?php echo e(route('inventory.import.kde.process')); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
                        <?php echo csrf_field(); ?>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sumber File</label>
                            <div class="space-y-2">
                                <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-slate-50">
                                    <input type="radio" name="source" value="default" class="mt-1" checked>
                                    <span>
                                        <span class="block font-medium text-gray-900">Gunakan file default di server</span>
                                        <span class="block text-xs text-gray-500">Mencari file <span class="font-mono">inventory_kde_final.csv</span> di folder project/public.</span>
                                    </span>
                                </label>

                                <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-slate-50">
                                    <input type="radio" name="source" value="upload" class="mt-1">
                                    <span>
                                        <span class="block font-medium text-gray-900">Upload file custom</span>
                                        <span class="block text-xs text-gray-500">Pakai file CSV/TXT apa saja sesuai kebutuhan.</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div id="upload-wrapper" class="hidden">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Pilih File</label>
                            <input type="file" name="file" id="file" accept=".csv,.txt"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-md">
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 font-semibold">
                                Jalankan Import Stock Awal
                            </button>
                            <a href="<?php echo e(route('inventory.index')); ?>" class="text-gray-600 hover:text-gray-900">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sourceInputs = document.querySelectorAll('input[name="source"]');
            const uploadWrapper = document.getElementById('upload-wrapper');
            const fileInput = document.getElementById('file');

            function syncUploadVisibility() {
                const selected = document.querySelector('input[name="source"]:checked')?.value;
                const isUpload = selected === 'upload';
                uploadWrapper.classList.toggle('hidden', !isUpload);
                fileInput.required = isUpload;
            }

            sourceInputs.forEach((input) => input.addEventListener('change', syncUploadVisibility));
            syncUploadVisibility();
        });
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
<?php endif; ?><?php /**PATH C:\Users\deniz\Downloads\plantation.oilpam.my.id (1)\plantation.oilpam.my.id\Modules/PrSystem\resources/views/inventory/import_initial.blade.php ENDPATH**/ ?>