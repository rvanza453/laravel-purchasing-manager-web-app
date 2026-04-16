<?php if (isset($component)) { $__componentOriginal8ffca6aaed06613cf5643e13e74c8806 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806 = $attributes; } ?>
<?php $component = Modules\ServiceAgreementSystem\View\Components\Layouts\Master::resolve(['title' => 'Pengajuan USPK'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('serviceagreementsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Modules\ServiceAgreementSystem\View\Components\Layouts\Master::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php $__env->startPush('actions'); ?>
        <a href="<?php echo e(route('sas.uspk.create')); ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Buat USPK Baru
        </a>
    <?php $__env->stopPush(); ?>

    
    <div class="d-flex gap-2 flex-wrap mb-4">
        <a href="<?php echo e(route('sas.uspk.index')); ?>" class="btn btn-sm <?php echo e(!$status ? 'btn-primary' : 'btn-secondary'); ?>">Semua</a>
        <a href="<?php echo e(route('sas.uspk.index', ['status' => 'draft'])); ?>" class="btn btn-sm <?php echo e($status === 'draft' ? 'btn-primary' : 'btn-secondary'); ?>">Draft</a>
        <a href="<?php echo e(route('sas.uspk.index', ['status' => 'submitted'])); ?>" class="btn btn-sm <?php echo e($status === 'submitted' ? 'btn-primary' : 'btn-secondary'); ?>">Submitted</a>
        <a href="<?php echo e(route('sas.uspk.index', ['status' => 'in_review'])); ?>" class="btn btn-sm <?php echo e($status === 'in_review' ? 'btn-primary' : 'btn-secondary'); ?>">In Review</a>
        <a href="<?php echo e(route('sas.uspk.index', ['status' => 'approved'])); ?>" class="btn btn-sm <?php echo e($status === 'approved' ? 'btn-primary' : 'btn-secondary'); ?>">Approved</a>
        <a href="<?php echo e(route('sas.uspk.index', ['status' => 'rejected'])); ?>" class="btn btn-sm <?php echo e($status === 'rejected' ? 'btn-primary' : 'btn-secondary'); ?>">Rejected</a>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No. USPK</th>
                        <th>Judul Pekerjaan</th>
                        <th>Department</th>
                        <th>Blok</th>
                        <th>Estimasi Nilai</th>
                        <th>Tender</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uspk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="font-weight: 600; color: var(--text-primary);">
                            <a href="<?php echo e(route('sas.uspk.show', $uspk)); ?>" style="color: var(--accent); text-decoration: none;">
                                <?php echo e($uspk->uspk_number); ?>

                            </a>
                        </td>
                        <td><?php echo e(Str::limit($uspk->title, 40)); ?></td>
                        <td><?php echo e($uspk->department->name ?? '-'); ?></td>
                        <td><?php echo e($uspk->block->name ?? '-'); ?></td>
                        <td style="font-weight: 600;">Rp <?php echo e(number_format($uspk->estimated_value, 0, ',', '.')); ?></td>
                        <td>
                            <span style="color: var(--accent);"><?php echo e($uspk->tenders->count()); ?> kontraktor</span>
                        </td>
                        <td><span class="badge badge-<?php echo e($uspk->status); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $uspk->status))); ?></span></td>
                        <td><?php echo e($uspk->created_at->format('d M Y')); ?></td>
                        <td class="text-right">
                            <div class="d-flex gap-2" style="justify-content: flex-end;">
                                <a href="<?php echo e(route('sas.uspk.show', $uspk)); ?>" class="btn btn-secondary btn-sm" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if($uspk->isEditable()): ?>
                                <a href="<?php echo e(route('sas.uspk.edit', $uspk)); ?>" class="btn btn-primary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('sas.uspk.destroy', $uspk)); ?>" method="POST" onsubmit="return confirm('Yakin ingin menghapus USPK ini?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <i class="fas fa-file-signature"></i>
                                <p>Belum ada pengajuan USPK.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($submissions->hasPages()): ?>
        <div class="pagination-wrapper">
            <?php echo e($submissions->links()); ?>

        </div>
        <?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ffca6aaed06613cf5643e13e74c8806)): ?>
<?php $attributes = $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806; ?>
<?php unset($__attributesOriginal8ffca6aaed06613cf5643e13e74c8806); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ffca6aaed06613cf5643e13e74c8806)): ?>
<?php $component = $__componentOriginal8ffca6aaed06613cf5643e13e74c8806; ?>
<?php unset($__componentOriginal8ffca6aaed06613cf5643e13e74c8806); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/ServiceAgreementSystem\resources/views/uspk/index.blade.php ENDPATH**/ ?>