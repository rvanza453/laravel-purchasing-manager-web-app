<?php if (isset($component)) { $__componentOriginal8ffca6aaed06613cf5643e13e74c8806 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806 = $attributes; } ?>
<?php $component = Modules\ServiceAgreementSystem\View\Components\Layouts\Master::resolve(['title' => 'Persetujuan Saya'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('serviceagreementsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Modules\ServiceAgreementSystem\View\Components\Layouts\Master::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="d-flex justify-between align-center mb-4">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: var(--text-primary);">Persetujuan Saya</h1>
            <p class="text-muted" style="font-size: 14px;">Daftar USPK yang memerlukan persetujuan dari Anda.</p>
        </div>
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
                        <th>Pengaju</th>
                        <th>Tanggal Ajuan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $pendingUspks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uspk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                        <td><?php echo e($uspk->submitter->name ?? '-'); ?></td>
                        <td><?php echo e($uspk->created_at->format('d M Y')); ?></td>
                        <td class="text-right">
                            <a href="<?php echo e(route('sas.uspk.show', $uspk)); ?>" class="btn btn-primary btn-sm" title="Tinjau & Proses">
                                <i class="fas fa-gavel"></i> Proses
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fas fa-check-circle" style="color: var(--success);"></i>
                                <p class="mb-2" style="font-weight: 600; font-size: 16px; color: var(--text-primary);">Semua Selesai!</p>
                                <p class="text-muted">Tidak ada pengajuan USPK yang menunggu persetujuan Anda saat ini.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($pendingUspks->hasPages()): ?>
        <div class="pagination-wrapper">
            <?php echo e($pendingUspks->links()); ?>

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
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/ServiceAgreementSystem\resources/views/uspk-approval/index.blade.php ENDPATH**/ ?>