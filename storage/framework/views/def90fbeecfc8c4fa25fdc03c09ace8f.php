<?php if (isset($component)) { $__componentOriginal5f4fc931fc804e57d1b38ad56db9270c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5f4fc931fc804e57d1b38ad56db9270c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'qccomplaintsystem::components.layouts.master','data' => ['title' => 'INBOX PIC QC']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('qccomplaintsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('INBOX PIC QC')]); ?>
    <style>
        .dl-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .dl-header h2 {
            margin: 0 0 4px;
            font-size: 22px;
            letter-spacing: -0.02em;
        }

        .dl-header p {
            margin: 0;
            font-size: 13px;
            color: #64748b;
        }

        .dl-count {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            background: linear-gradient(135deg, #0f766e, #0284c7);
        }

        .dl-section {
            margin-top: 18px;
        }

        .dl-section-title {
            margin: 0 0 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            font-weight: 800;
            color: #1f2937;
        }

        .dl-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 12px;
        }

        .dl-card {
            border: 1px solid #d7e4dd;
            border-radius: 14px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
            display: flex;

        .dl-actions > * {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }
            flex-direction: column;
        }

        .dl-card-body {
            padding: 13px;
            display: grid;
            gap: 10px;
        }
            grid-template-columns: 1fr;
        .dl-top {
            display: flex;

        .dl-inline > * {
            width: 100%;
            min-width: 0;
        }
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .dl-number {
            font-size: 10px;
            font-weight: 800;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            padding: 2px 7px;
        }

        .dl-deadline-status {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            border-radius: 6px;
            padding: 2px 7px;
        }

        .dl-deadline-status.set {
            background: #dcfce7;
            color: #166534;
        }

        .dl-deadline-status.unset {
            background: #fee2e2;
            color: #b91c1c;
        }

        .dl-title {
            font-size: 14px;
            font-weight: 800;
            color: #1f2937;
            line-height: 1.3;
        }

        .dl-title-link {
            text-decoration: none;
            color: inherit;
        }

        .dl-title-link:hover .dl-title {
            color: #0f766e;
            text-decoration: underline;
        }

        .dl-meta {
            font-size: 12px;
            color: #475569;
            display: grid;
            gap: 4px;
        }

        .dl-meta .label {
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
        }

        .dl-actions {
            border-top: 1px solid #e9f0ec;
            background: #f8fcfa;
            padding: 12px;
            display: grid;
            gap: 8px;
        }

        .dl-note {
            font-size: 12px;
            color: #64748b;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 10px;
        }

        .dl-actions form {
            display: grid;
            gap: 8px;
        }

        .dl-inline {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .dl-upload {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 7px;
            width: 100%;
            border-radius: 8px;
            background: linear-gradient(135deg, #0f766e, #0284c7);
            color: #fff;
            text-decoration: none;
            padding: 8px;
            font-size: 12px;
            font-weight: 800;
            white-space: normal;
            text-align: center;
        }

        .dl-detail {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 7px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            text-decoration: none;
            padding: 8px;
            font-size: 12px;
            font-weight: 700;
            white-space: normal;
            text-align: center;
        }

        .dl-detail:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }

        .dl-empty {
            grid-column: 1 / -1;
            text-align: center;
            color: #64748b;
            padding: 60px 20px;
        }

        .dl-empty i {
            display: block;
            font-size: 42px;
            opacity: 0.22;
            margin-bottom: 12px;
        }

        @media (max-width: 760px) {
            .dl-grid {
                grid-template-columns: 1fr;
            }

            .dl-inline {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="dl-header">
        <div>
            <h2>INBOX PIC</h2>
            <p>Pisahkan pekerjaan PIC: isi deadline dulu, lalu upload penyelesaian untuk item yang deadline-nya sudah terisi.</p>
        </div>
        <div class="dl-count">
            <i class="fas fa-layer-group"></i>
            <?php echo e($deadlines->total() + $completions->total()); ?> total item PIC
        </div>
    </div>

    <section class="dl-section">
        <h3 class="dl-section-title"><i class="fas fa-calendar-plus"></i> Butuh Input Deadline</h3>
        <div class="dl-grid">
            <?php $__empty_1 = true; $__currentLoopData = $deadlines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $finding): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $loc = collect([
                        $finding->department?->name,
                        $finding->subDepartment?->name,
                        $finding->block?->name,
                    ])->filter()->implode(' > ');

                    $resolvedPicIds = collect(array_map('intval', (array) ($finding->pic_user_ids ?? [])));
                    if (!empty($finding->pic_user_id)) {
                        $resolvedPicIds->push((int) $finding->pic_user_id);
                    }

                    $picNames = $resolvedPicIds
                        ->filter()
                        ->unique()
                        ->values()
                        ->map(fn ($id) => $picNameMap[$id] ?? null)
                        ->filter()
                        ->values();
                ?>

                <div class="dl-card">
                    <div class="dl-card-body">
                        <div class="dl-top">
                            <span class="dl-number"><?php echo e($finding->finding_number); ?></span>
                            <span class="dl-deadline-status unset">Belum Ada Deadline</span>
                        </div>

                        <a href="<?php echo e(route('qc.findings.show', $finding)); ?>" class="dl-title-link">
                            <div class="dl-title"><?php echo e($finding->title); ?></div>
                        </a>

                        <div class="dl-meta">
                            <div><span class="label">PIC:</span> <?php echo e($picNames->isNotEmpty() ? $picNames->join(', ') : '-'); ?></div>
                            <div><span class="label">Lokasi:</span> <?php echo e($loc ?: '-'); ?></div>
                            <div><span class="label">Status:</span> <?php echo e(strtoupper($finding->status)); ?></div>
                        </div>
                    </div>

                    <div class="dl-actions">
                        <?php if($canSetDeadlineMap[$finding->id] ?? false): ?>
                            <form method="POST" action="<?php echo e(route('qc.findings.set-deadline', $finding)); ?>">
                                <?php echo csrf_field(); ?>
                                <div class="dl-inline">
                                    <input type="date" name="target_resolution_date" class="input" value="<?php echo e(optional($finding->target_resolution_date)->toDateString()); ?>" min="<?php echo e(now()->toDateString()); ?>" required>
                                    <input type="text" name="follow_up_plan" class="input" value="<?php echo e(old('follow_up_plan')); ?>" placeholder="Rencana singkat (opsional)">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-calendar-check"></i> Simpan Deadline</button>
                            </form>
                        <?php else: ?>
                            <div class="dl-note">Anda bisa melihat item ini, tetapi tidak terdaftar sebagai PIC aktif untuk set deadline.</div>
                        <?php endif; ?>

                        <a href="<?php echo e(route('qc.findings.show', $finding)); ?>" class="dl-detail">
                            <i class="fas fa-arrow-up-right-from-square"></i> Lihat Detail Temuan
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="dl-empty">
                    <i class="fas fa-inbox"></i>
                    <p>Tidak ada temuan yang menunggu pengisian deadline dari Anda.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if($deadlines->hasPages()): ?>
            <div style="margin-top:12px;">
                <?php echo e($deadlines->links()); ?>

            </div>
        <?php endif; ?>
    </section>

    <section class="dl-section">
        <h3 class="dl-section-title"><i class="fas fa-cloud-arrow-up"></i> Butuh Upload Penyelesaian</h3>
        <div class="dl-grid">
            <?php $__empty_1 = true; $__currentLoopData = $completions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $finding): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $loc = collect([
                        $finding->department?->name,
                        $finding->subDepartment?->name,
                        $finding->block?->name,
                    ])->filter()->implode(' > ');

                    $resolvedPicIds = collect(array_map('intval', (array) ($finding->pic_user_ids ?? [])));
                    if (!empty($finding->pic_user_id)) {
                        $resolvedPicIds->push((int) $finding->pic_user_id);
                    }

                    $picNames = $resolvedPicIds
                        ->filter()
                        ->unique()
                        ->values()
                        ->map(fn ($id) => $picNameMap[$id] ?? null)
                        ->filter()
                        ->values();
                ?>

                <div class="dl-card">
                    <div class="dl-card-body">
                        <div class="dl-top">
                            <span class="dl-number"><?php echo e($finding->finding_number); ?></span>
                            <span class="dl-deadline-status set">Deadline Terisi</span>
                        </div>

                        <a href="<?php echo e(route('qc.findings.show', $finding)); ?>" class="dl-title-link">
                            <div class="dl-title"><?php echo e($finding->title); ?></div>
                        </a>

                        <div class="dl-meta">
                            <div><span class="label">PIC:</span> <?php echo e($picNames->isNotEmpty() ? $picNames->join(', ') : '-'); ?></div>
                            <div><span class="label">Lokasi:</span> <?php echo e($loc ?: '-'); ?></div>
                            <div><span class="label">Status:</span> <?php echo e(strtoupper($finding->status)); ?></div>
                            <div>
                                <span class="label">Deadline:</span>
                                <?php echo e(optional($finding->target_resolution_date)->format('d M Y') ?: '-'); ?>

                            </div>
                        </div>
                    </div>

                    <div class="dl-actions">
                        <?php if($canSubmitCompletionMap[$finding->id] ?? false): ?>
                            <a href="<?php echo e(route('qc.findings.show', $finding)); ?>" class="dl-upload">
                                <i class="fas fa-cloud-arrow-up"></i> Buka Detail untuk Upload Penyelesaian
                            </a>
                        <?php else: ?>
                            <div class="dl-note">Anda bisa melihat item ini, tetapi tidak terdaftar sebagai PIC aktif untuk upload penyelesaian.</div>
                        <?php endif; ?>

                        <a href="<?php echo e(route('qc.findings.show', $finding)); ?>" class="dl-detail">
                            <i class="fas fa-arrow-up-right-from-square"></i> Lihat Detail Temuan
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="dl-empty">
                    <i class="fas fa-cloud-check"></i>
                    <p>Tidak ada item yang menunggu upload penyelesaian saat ini.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if($completions->hasPages()): ?>
            <div style="margin-top:12px;">
                <?php echo e($completions->links()); ?>

            </div>
        <?php endif; ?>
    </section>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5f4fc931fc804e57d1b38ad56db9270c)): ?>
<?php $attributes = $__attributesOriginal5f4fc931fc804e57d1b38ad56db9270c; ?>
<?php unset($__attributesOriginal5f4fc931fc804e57d1b38ad56db9270c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5f4fc931fc804e57d1b38ad56db9270c)): ?>
<?php $component = $__componentOriginal5f4fc931fc804e57d1b38ad56db9270c; ?>
<?php unset($__componentOriginal5f4fc931fc804e57d1b38ad56db9270c); ?>
<?php endif; ?>
<?php /**PATH C:\Users\deniz\Downloads\plantation.oilpam.my.id (1)\plantation.oilpam.my.id\Modules/QcComplaintSystem\resources/views/deadlines/index.blade.php ENDPATH**/ ?>