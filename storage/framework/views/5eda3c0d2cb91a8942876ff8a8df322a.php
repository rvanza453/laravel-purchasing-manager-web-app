<?php if (isset($component)) { $__componentOriginal5f4fc931fc804e57d1b38ad56db9270c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5f4fc931fc804e57d1b38ad56db9270c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'qccomplaintsystem::components.layouts.master','data' => ['title' => 'Inbox Approval QC']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('qccomplaintsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Inbox Approval QC')]); ?>
    <style>
        .inbox-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .inbox-header h2 {
            margin: 0 0 4px;
            font-size: 22px;
            letter-spacing: -0.02em;
        }

        .inbox-header p {
            margin: 0;
            font-size: 13px;
            color: #64748b;
        }

        .inbox-count {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #0f766e, #0284c7);
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 999px;
        }

        .ap-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 12px;
        }

        .ap-card {
            background: #fff;
            border: 1px solid #d7e4dd;
            border-radius: 14px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: box-shadow 0.18s ease, transform 0.18s ease;
            box-shadow: 0 2px 8px rgba(15,23,42,0.05);
        }

        .ap-card:hover {
            box-shadow: 0 8px 24px rgba(15,23,42,0.11);
            transform: translateY(-2px);
        }

        .ap-card-accent { height: 4px; }
        .ap-card-accent.high   { background: linear-gradient(90deg, #ef4444, #b91c1c); }
        .ap-card-accent.medium { background: linear-gradient(90deg, #f59e0b, #f97316); }
        .ap-card-accent.low    { background: linear-gradient(90deg, #0f766e, #0284c7); }

        .ap-card-body {
            padding: 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .ap-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
        }

        .ap-finding-number {
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

        .ap-urgency {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 2px 7px;
            border-radius: 6px;
        }
        .ap-urgency-high   { background: #fee2e2; color: #b91c1c; }
        .ap-urgency-medium { background: #fef3c7; color: #92400e; }
        .ap-urgency-low    { background: #ecfeff; color: #0f766e; }

        .ap-deadline-badge {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 2px 7px;
            border-radius: 6px;
        }

        .ap-deadline-badge.set { background: #dcfce7; color: #166534; }
        .ap-deadline-badge.unset { background: #fee2e2; color: #b91c1c; }

        .ap-title {
            font-size: 14px;
            font-weight: 800;
            line-height: 1.3;
            color: #1f2937;
        }

        .ap-location {
            font-size: 11px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .ap-meta-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 7px;
        }

        .ap-meta-item {
            background: #f8fcfa;
            border: 1px solid #e4ede8;
            border-radius: 8px;
            padding: 7px 9px;
        }

        .ap-meta-item .k {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 2px;
        }

        .ap-meta-item .v {
            font-size: 12px;
            font-weight: 700;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ap-progress-wrap { display: grid; gap: 4px; }

        .ap-progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #64748b;
            font-weight: 700;
        }

        .ap-progress-bar {
            height: 5px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .ap-progress-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f59e0b, #0f766e);
        }

        .ap-card-footer {
            padding: 10px 14px;
            border-top: 1px solid #e9f0ec;
            background: #f8fcfa;
        }

        .ap-card-footer a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            background: linear-gradient(135deg, #0f766e, #0284c7);
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            transition: opacity 0.15s ease;
        }

        .ap-card-footer a:hover { opacity: 0.86; }

        .ap-empty {
            grid-column: 1/-1;
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }

        .ap-empty i {
            font-size: 40px;
            opacity: 0.22;
            margin-bottom: 12px;
            display: block;
        }

        .ap-empty p { margin: 0; font-size: 14px; }
    </style>

    <div class="inbox-header">
        <div>
            <h2>Inbox Approval</h2>
            <p>Approval berjenjang — hanya level aktif yang dapat Anda proses.</p>
        </div>
        <?php if($approvals->isNotEmpty()): ?>
            <div class="inbox-count">
                <i class="fas fa-clock-rotate-left"></i>
                <?php echo e($approvals->count()); ?> menunggu tindakan
            </div>
        <?php endif; ?>
    </div>

    <div class="ap-grid">
        <?php $__empty_1 = true; $__currentLoopData = $approvals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $finding     = $step->finding;
                $totalSteps  = $finding->approvalSteps->count();
                $doneSteps   = $finding->approvalSteps->where('status', 'approved')->count();
                $pct         = $totalSteps > 0 ? (int) round(($doneSteps / $totalSteps) * 100) : 0;
                $urgency     = $finding->urgency ?? 'low';
                 $accentClass = match(strtolower((string) $urgency)) { 'high', 'hight' => 'high', 'medium', 'normal' => 'medium', default => 'low' };
                $loc         = collect([
                                    $finding->department?->name,
                                    $finding->subDepartment?->name,
                                    $finding->block?->name,
                               ])->filter()->implode(' › ');
            ?>

            <div class="ap-card">
                <div class="ap-card-accent <?php echo e($accentClass); ?>"></div>
                <div class="ap-card-body">

                    <div class="ap-card-top">
                        <span class="ap-finding-number"><?php echo e($finding->finding_number); ?></span>
                        <div style="display:flex; gap:6px; flex-wrap:wrap; justify-content:flex-end;">
                            <span class="ap-deadline-badge <?php echo e($finding->target_resolution_date ? 'set' : 'unset'); ?>">
                                <?php echo e($finding->target_resolution_date ? 'Deadline Ada' : 'Deadline Belum Ada'); ?>

                            </span>
                            <span class="ap-urgency ap-urgency-<?php echo e($accentClass); ?>"><?php echo e(\Modules\QcComplaintSystem\Models\QcFinding::urgencyLabel($urgency)); ?></span>
                        </div>
                    </div>

                    <div class="ap-title"><?php echo e($finding->title); ?></div>

                    <?php if($loc): ?>
                        <div class="ap-location">
                            <i class="fas fa-location-dot" style="color:#0f766e; font-size:10px;"></i>
                            <?php echo e($loc); ?>

                        </div>
                    <?php endif; ?>

                    <div class="ap-meta-row">
                        <div class="ap-meta-item">
                            <div class="k">Disubmit</div>
                            <div class="v" title="<?php echo e($finding->completionSubmitter?->name ?? $finding->reporter?->name ?? '-'); ?>">
                                <?php echo e($finding->completionSubmitter?->name ?? $finding->reporter?->name ?? '-'); ?>

                            </div>
                        </div>
                        <div class="ap-meta-item">
                            <div class="k">Level Aktif</div>
                            <div class="v"><?php echo e($step->level); ?> / <?php echo e($totalSteps); ?></div>
                        </div>
                        <div class="ap-meta-item" style="grid-column: 1 / -1;">
                            <div class="k">Deadline Penyelesaian PIC</div>
                            <div class="v"><?php echo e(optional($finding->target_resolution_date)->format('d M Y') ?? 'Belum ditentukan PIC'); ?></div>
                        </div>
                    </div>

                    <div class="ap-progress-wrap">
                        <div class="ap-progress-label">
                            <span>Progress Approval</span>
                            <span><?php echo e($doneSteps); ?>/<?php echo e($totalSteps); ?></span>
                        </div>
                        <div class="ap-progress-bar">
                            <div class="ap-progress-fill" style="width:<?php echo e($pct); ?>%;"></div>
                        </div>
                    </div>

                </div>
                <div class="ap-card-footer">
                    <a href="<?php echo e(route('qc.findings.show', $finding)); ?>">
                        <i class="fas fa-circle-check"></i> Proses Approval
                    </a>
                </div>
            </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="ap-empty">
                <i class="fas fa-inbox"></i>
                <p>Tidak ada approval pending untuk Anda saat ini.</p>
            </div>
        <?php endif; ?>
    </div>
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
<?php /**PATH C:\Users\deniz\Downloads\plantation.oilpam.my.id (1)\plantation.oilpam.my.id\Modules/QcComplaintSystem\resources/views/approvals/index.blade.php ENDPATH**/ ?>