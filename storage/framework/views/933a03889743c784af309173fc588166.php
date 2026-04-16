

<?php $__env->startSection('content'); ?>
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-tight">System Announcements</h2>
        <p class="text-sm text-gray-500">Kelola papan pengumuman global untuk seluruh aplikasi ERP.</p>
    </div>
</div>

<?php if(session('success')): ?>
<div class="bg-green-900/40 border border-green-500/50 text-green-300 px-4 py-3 rounded-xl mb-6 shadow-lg shadow-green-900/10">
    <i class="fa-solid fa-circle-check text-green-400"></i> <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left: Form Create -->
    <div class="lg:col-span-1">
        <div class="glass-panel p-6 sticky top-24">
            <h3 class="text-lg font-bold text-white mb-4"><i class="fa-solid fa-bullhorn text-blue-400 mr-2"></i> Buat Pengumuman</h3>
            <form action="<?php echo e(route('systemsupport.announcements.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Judul Singkat</label>
                        <input type="text" name="title" class="form-input" required placeholder="Contoh: Server Maintenance">
                    </div>
                    <div>
                        <label class="form-label">Tipe Banner</label>
                        <select name="type" class="form-select">
                            <option value="info">Info Umum (Biru)</option>
                            <option value="warning">Peringatan (Kuning)</option>
                            <option value="maintenance">Maintenance (Merah)</option>
                            <option value="update">Update Fitur (Hijau)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Isi Pesan (Opsional)</label>
                        <textarea name="content" class="form-textarea" rows="4" required placeholder="Sistem akan offline jam..."></textarea>
                    </div>
                    <div class="flex items-center gap-2 mt-4">
                        <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-blue-500 focus:ring-blue-500">
                        <label class="text-sm text-gray-300 font-medium">Langsung Aktifkan Sekarang?</label>
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="w-full btn-neon py-2"><i class="fa-solid fa-paper-plane mr-2"></i> Broadcast Pesan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Right: List / History -->
    <div class="lg:col-span-2 space-y-4">
        <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ann): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="glass-panel p-5 relative overflow-hidden flex flex-col sm:flex-row justify-between items-start gap-4">
                <!-- Color border accent -->
                <?php
                    $colors = [
                        'info' => 'bg-blue-500', 
                        'warning' => 'bg-yellow-500', 
                        'maintenance' => 'bg-red-500', 
                        'update' => 'bg-green-500'
                    ];
                    $bgColors = [
                        'info' => 'bg-blue-500/10 text-blue-400 border-blue-500/30', 
                        'warning' => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/30', 
                        'maintenance' => 'bg-red-500/10 text-red-500 border-red-500/30', 
                        'update' => 'bg-green-500/10 text-green-400 border-green-500/30'
                    ];
                ?>
                <div class="absolute left-0 top-0 bottom-0 w-1 <?php echo e($colors[$ann->type] ?? 'bg-gray-500'); ?>"></div>
                
                <div class="flex-1 w-full pl-2">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded border <?php echo e($bgColors[$ann->type] ?? 'bg-gray-800 text-gray-400 border-gray-600'); ?>">
                            <?php echo e($ann->type); ?>

                        </span>
                        
                        <?php if($ann->is_active): ?>
                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 text-green-400 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> ACTIVE LIVE</span>
                        <?php else: ?>
                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 text-gray-500 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> INACTIVE</span>
                        <?php endif; ?>
                    </div>
                    
                    <h4 class="text-lg font-bold text-white mb-1"><?php echo e($ann->title); ?></h4>
                    <p class="text-sm text-gray-400 mb-3 leading-relaxed"><?php echo e(Str::limit($ann->content, 120)); ?></p>
                    
                    <div class="flex flex-wrap items-center gap-4 text-xs font-mono text-gray-500">
                        <span><i class="fa-regular fa-clock mr-1"></i> Created <?php echo e($ann->created_at->diffForHumans()); ?></span>
                    </div>
                </div>
                
                <div class="flex sm:flex-col gap-2 w-full sm:w-auto mt-4 sm:mt-0 pt-4 sm:pt-0 border-t sm:border-none border-[#30363d]">
                    <form action="<?php echo e(route('systemsupport.announcements.toggle', $ann->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="w-full sm:w-28 text-center text-xs font-bold py-2 rounded-lg border <?php echo e($ann->is_active ? 'border-yellow-500/50 text-yellow-500 hover:bg-yellow-500/10' : 'border-green-500/50 text-green-500 hover:bg-green-500/10'); ?> transition">
                            <i class="fa-solid fa-power-off mr-1"></i> <?php echo e($ann->is_active ? 'Turn Off' : 'Turn On'); ?>

                        </button>
                    </form>
                    
                    <form action="<?php echo e(route('systemsupport.announcements.destroy', $ann->id)); ?>" method="POST" onsubmit="return confirm('Hapus permanen pengumuman ini?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="w-full sm:w-28 text-center text-xs font-bold py-2 rounded-lg border border-red-500/30 text-red-400 hover:bg-red-500/10 transition">
                            <i class="fa-solid fa-trash mr-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="glass-panel p-10 text-center">
                <i class="fa-regular fa-comment-dots text-4xl text-gray-600 mb-3"></i>
                <p class="text-gray-400 font-medium">Belum ada pengumuman sistem dibuat.</p>
            </div>
        <?php endif; ?>
        
        <div class="pt-4">
            <?php echo e($announcements->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('systemsupport::components.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/SystemSupport\resources/views/announcements/index.blade.php ENDPATH**/ ?>