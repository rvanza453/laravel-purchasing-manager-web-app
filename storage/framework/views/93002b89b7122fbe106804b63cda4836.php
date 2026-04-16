

<?php $__env->startSection('content'); ?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="glass-panel p-5 neon-accent">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-400 font-medium tracking-wide">TOTAL TICKETS</p>
                <h3 class="text-3xl font-bold text-white mt-1"><?php echo e($stats['total']); ?></h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400">
                <i class="fa-solid fa-layer-group text-lg"></i>
            </div>
        </div>
    </div>
    <div class="glass-panel p-5 border-l-4 border-l-yellow-600">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-400 font-medium tracking-wide">OPEN</p>
                <h3 class="text-3xl font-bold text-white mt-1"><?php echo e($stats['open']); ?></h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-yellow-900/30 flex items-center justify-center text-yellow-500">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
            </div>
        </div>
    </div>
    <div class="glass-panel p-5 border-l-4 border-l-blue-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-400 font-medium tracking-wide">IN PROGRESS</p>
                <h3 class="text-3xl font-bold text-white mt-1"><?php echo e($stats['in_progress']); ?></h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-900/30 flex items-center justify-center text-blue-400">
                <i class="fa-solid fa-spinner fa-spin-pulse text-lg"></i>
            </div>
        </div>
    </div>
    <div class="glass-panel p-5 border-l-4 border-l-green-600">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-400 font-medium tracking-wide">RESOLVED</p>
                <h3 class="text-3xl font-bold text-white mt-1"><?php echo e($stats['resolved']); ?></h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-green-900/30 flex items-center justify-center text-green-500">
                <i class="fa-solid fa-check-double text-lg"></i>
            </div>
        </div>
    </div>
</div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <h2 class="text-xl font-bold text-white">Tickets & Issue Tracker</h2>
    
    <div class="flex gap-3 w-full sm:w-auto">
        <form action="<?php echo e(route('systemsupport.tickets.index')); ?>" method="GET" class="flex items-center">
            <select name="status" onchange="this.form.submit()" class="form-select text-sm py-2">
                <option value="">All Status</option>
                <option value="Open" <?php echo e(request('status') == 'Open' ? 'selected' : ''); ?>>Open</option>
                <option value="In Progress" <?php echo e(request('status') == 'In Progress' ? 'selected' : ''); ?>>In Progress</option>
                <option value="Resolved" <?php echo e(request('status') == 'Resolved' ? 'selected' : ''); ?>>Resolved</option>
                <option value="Closed" <?php echo e(request('status') == 'Closed' ? 'selected' : ''); ?>>Closed</option>
            </select>
        </form>
        <a href="<?php echo e(route('systemsupport.tickets.create')); ?>" class="btn-neon px-5 py-2 shadow-lg shadow-green-900/20 w-full sm:w-auto text-sm">
            <i class="fa-solid fa-plus mr-2"></i> New Ticket
        </a>
    </div>
</div>

<?php if(session('success')): ?>
<div class="bg-green-900/40 border border-green-500/50 text-green-300 px-4 py-3 rounded-xl mb-6 flex items-center gap-3 shadow-lg shadow-green-900/10">
    <i class="fa-solid fa-circle-check text-green-400"></i> <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<div class="glass-panel overflow-hidden shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-400">
            <thead class="bg-[#161b22] text-gray-300 uppercase font-semibold text-xs border-b border-[#30363d]">
                <tr>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4">Ticket details</th>
                    <th scope="col" class="px-6 py-4">Module / Priority</th>
                    <th scope="col" class="px-6 py-4">Reporter</th>
                    <th scope="col" class="px-6 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#30363d]">
                <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-[#1c2128] transition-colors">
                    <td class="px-6 py-5 whitespace-nowrap">
                        <?php if($t->status === 'Open'): ?>
                            <span class="status-badge badge-open"><i class="fa-regular fa-envelope mr-1"></i> Open</span>
                        <?php elseif($t->status === 'In Progress'): ?>
                            <span class="status-badge badge-progress"><i class="fa-solid fa-gears mr-1"></i> In Progress</span>
                        <?php elseif($t->status === 'Resolved'): ?>
                            <span class="status-badge badge-resolved"><i class="fa-solid fa-check mr-1"></i> Resolved</span>
                        <?php else: ?>
                            <span class="status-badge badge-closed"><i class="fa-solid fa-lock mr-1"></i> Closed</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-5">
                        <div class="font-bold text-gray-100 text-base mb-1"><?php echo e(Str::limit($t->title, 50)); ?></div>
                        <div class="text-xs text-gray-500">#T<?php echo e(str_pad($t->id, 5, '0', STR_PAD_LEFT)); ?> opened <?php echo e($t->created_at->diffForHumans()); ?></div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex flex-col gap-2">
                            <span class="inline-flex items-center text-xs bg-gray-800 text-gray-300 rounded px-2 py-0.5 w-max border border-gray-700 shadow-sm">
                                <i class="fa-solid fa-cube mr-1.5 opacity-60"></i> <?php echo e($t->module); ?>

                            </span>
                            <div class="text-xs font-semibold tracking-wide priority-<?php echo e($t->priority); ?>">
                                <?php if($t->priority == 'Urgent'): ?> <i class="fa-solid fa-fire mr-1"></i> <?php else: ?> <i class="fa-regular fa-flag mr-1 opacity-75"></i> <?php endif; ?> 
                                <?php echo e($t->priority); ?>

                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-800 border-2 border-gray-700 flex items-center justify-center text-xs text-white uppercase font-bold shadow-sm">
                                <?php echo e(substr($t->user->name ?? '?', 0, 1)); ?>

                            </div>
                            <div>
                                <span class="font-medium text-gray-300 block leading-tight"><?php echo e($t->user->name ?? 'Unknown'); ?></span>
                                <span class="text-xs text-gray-600"><?php echo e($t->user->roles->first()->name ?? 'Staff'); ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <a href="<?php echo e(route('systemsupport.tickets.show', $t->id)); ?>" class="btn-ghost px-4 py-1.5 text-xs inline-block">
                            View <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                        <div class="bg-gray-800/50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-700">
                            <i class="fa-solid fa-meteor text-3xl opacity-50 text-blue-400"></i>
                        </div>
                        <p class="text-lg font-medium text-gray-400">All clear!</p>
                        <p class="text-sm">Tidak ada ticket dalam sistem ini.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($tickets->hasPages()): ?>
    <div class="p-4 border-t border-[#30363d] bg-[#0d1117]/50">
        <?php echo e($tickets->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('systemsupport::components.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/SystemSupport\resources/views/tickets/index.blade.php ENDPATH**/ ?>