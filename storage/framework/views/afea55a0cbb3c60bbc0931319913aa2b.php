<?php if (isset($component)) { $__componentOriginal8ffca6aaed06613cf5643e13e74c8806 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ffca6aaed06613cf5643e13e74c8806 = $attributes; } ?>
<?php $component = Modules\ServiceAgreementSystem\View\Components\Layouts\Master::resolve(['title' => 'Dashboard'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('serviceagreementsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Modules\ServiceAgreementSystem\View\Components\Layouts\Master::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-value"><?php echo e($stats['total_uspk']); ?></div>
            <div class="stat-label">Total USPK</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                <i class="fas fa-pencil-alt"></i>
            </div>
            <div class="stat-value"><?php echo e($stats['draft']); ?></div>
            <div class="stat-label">Draft</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="stat-value"><?php echo e($stats['submitted']); ?></div>
            <div class="stat-label">Submitted</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                <i class="fas fa-search"></i>
            </div>
            <div class="stat-value"><?php echo e($stats['in_review']); ?></div>
            <div class="stat-label">In Review</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value"><?php echo e($stats['approved']); ?></div>
            <div class="stat-label">Approved</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-value"><?php echo e($stats['rejected']); ?></div>
            <div class="stat-label">Rejected</div>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header">
            <div class="card-title">USPK Terbaru</div>
            <a href="<?php echo e(route('sas.uspk.index')); ?>" class="btn btn-secondary btn-sm">
                Lihat Semua <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No. USPK</th>
                        <th>Judul</th>
                        <th>Department</th>
                        <th>Pengaju</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentUspk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uspk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="font-weight: 600; color: var(--text-primary);"><?php echo e($uspk->uspk_number); ?></td>
                        <td><?php echo e($uspk->title); ?></td>
                        <td><?php echo e($uspk->department->name ?? '-'); ?></td>
                        <td><?php echo e($uspk->submitter->name ?? '-'); ?></td>
                        <td><span class="badge badge-<?php echo e($uspk->status); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $uspk->status))); ?></span></td>
                        <td><?php echo e($uspk->created_at->format('d M Y')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Belum ada pengajuan USPK.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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
<?php /**PATH C:\laragon\www\plantation.oilpam.my.id\Modules/ServiceAgreementSystem\resources/views/dashboard.blade.php ENDPATH**/ ?>