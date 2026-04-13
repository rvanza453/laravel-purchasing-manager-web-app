<?php if (isset($component)) { $__componentOriginal5f4fc931fc804e57d1b38ad56db9270c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5f4fc931fc804e57d1b38ad56db9270c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'qccomplaintsystem::components.layouts.master','data' => ['title' => 'Dashboard Summary QC']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('qccomplaintsystem::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Dashboard Summary QC')]); ?>
    <?php
        $siteName = strtolower(trim((string) auth()->user()?->site?->name));
        $position = strtolower(trim((string) auth()->user()?->position));
        $canCreateFinding = str_contains($siteName, 'head office')
            || (bool) preg_match('/\bho\b/i', $siteName)
            || str_contains($position, 'qc');
    ?>

    <style>
        .summary-shell { display:grid; gap:14px; font-family:'Manrope', sans-serif; }
        .summary-hero {
            border:1px solid #d5e4df;
            border-radius:16px;
            padding:16px;
            background:
                radial-gradient(circle at 8% 0%, rgba(14,165,233,0.18), transparent 42%),
                radial-gradient(circle at 90% 0%, rgba(16,185,129,0.18), transparent 35%),
                linear-gradient(180deg, #ffffff, #f7fcfa);
            box-shadow:0 14px 24px rgba(15,23,42,0.06);
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:12px;
            flex-wrap:wrap;
        }
        .stats-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; }
        .stat-card {
            border:1px solid #dbe7e1;
            border-radius:13px;
            padding:12px;
            background:linear-gradient(180deg,#ffffff,#f9fdfb);
            box-shadow:0 10px 18px rgba(15,23,42,0.05);
        }
        .stat-card .k { font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700; }
        .stat-card .v { font-size:26px; font-weight:800; margin-top:2px; }

        .panel {
            border:1px solid #d5e4df;
            border-radius:16px;
            background:#fff;
            padding:14px;
            box-shadow:0 14px 26px rgba(15,23,42,0.06);
        }

        .site-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; }
        .site-card {
            display:block;
            text-decoration:none;
            color:inherit;
            border:1px solid #dbe7e1;
            border-radius:13px;
            padding:12px;
            background:linear-gradient(180deg,#ffffff,#f4fbf8);
            transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .site-card:hover {
            transform:translateY(-2px);
            box-shadow:0 14px 20px rgba(15,23,42,0.08);
            border-color:#99d6c8;
        }
        .site-title { font-weight:800; font-size:15px; margin-bottom:6px; }
        .chip-row { display:flex; flex-wrap:wrap; gap:6px; }
        .chip {
            font-size:11px;
            font-weight:700;
            border-radius:999px;
            padding:3px 8px;
            border:1px solid #dbe7e1;
            background:#f8fafc;
            color:#334155;
        }

        .table-wrap { overflow-x:auto; }
        .dept-table { width:100%; border-collapse:collapse; min-width:700px; }
        .dept-table th, .dept-table td { padding:10px; border-bottom:1px solid #e2e8f0; font-size:13px; }
        .dept-table th {
            font-size:11px;
            text-transform:uppercase;
            letter-spacing:.04em;
            color:#475569;
            background:linear-gradient(180deg,#f8fcfb,#eef7f3);
            text-align:left;
        }

        @media (max-width: 1100px) {
            .site-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        }

        @media (max-width: 760px) {
            .stats-grid,
            .site-grid { grid-template-columns:1fr; }
        }
    </style>

    <div class="summary-shell">
        <div class="summary-hero">
            <div>
                <div style="font-size:12px; color:#64748b; text-transform:uppercase; font-weight:700;">QC Complaint System</div>
                <h2 style="margin:4px 0 0; font-size:22px;">Dashboard Summary Temuan Aktif</h2>
                <div class="text-muted">Ringkasan ini hanya menampilkan temuan dengan status selain <strong>Closed</strong>.</div>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="<?php echo e(route('qc.findings.index')); ?>" class="btn btn-primary"><i class="fas fa-list-check"></i> Buka Daftar Temuan</a>
                <?php if($canCreateFinding): ?>
                    <a href="<?php echo e(route('qc.findings.create')); ?>" class="btn"><i class="fas fa-plus"></i> Lapor Temuan</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="k">Total Temuan Aktif</div>
                <div class="v"><?php echo e($totalActiveFindings); ?></div>
            </div>
            <div class="stat-card">
                <div class="k">Open</div>
                <div class="v" style="color:#b45309;"><?php echo e($totalOpenFindings); ?></div>
            </div>
            <div class="stat-card">
                <div class="k">In Review</div>
                <div class="v" style="color:#1d4ed8;"><?php echo e($totalInReviewFindings); ?></div>
            </div>
        </div>

        <div class="panel">
            <div style="display:flex; justify-content:space-between; align-items:flex-end; gap:10px; flex-wrap:wrap; margin-bottom:10px;">
                <div>
                    <h3 style="margin:0; font-size:16px;">Summary Per Site</h3>
                    <div class="text-muted">Klik kartu site untuk langsung buka daftar temuan site tersebut.</div>
                </div>
            </div>

            <?php if($siteSummary->isEmpty()): ?>
                <div class="text-muted">Belum ada temuan aktif untuk ditampilkan.</div>
            <?php else: ?>
                <div class="site-grid">
                    <?php $__currentLoopData = $siteSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a class="site-card" href="<?php echo e(route('qc.findings.index', ['site_id' => $item->site_id])); ?>">
                            <div class="site-title"><?php echo e($item->site_name); ?></div>
                            <div class="chip-row">
                                <span class="chip">Total: <?php echo e((int) $item->total_findings); ?></span>
                                <span class="chip">Open: <?php echo e((int) $item->open_total); ?></span>
                                <span class="chip">In Review: <?php echo e((int) $item->in_review_total); ?></span>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="panel">
            <div style="display:flex; justify-content:space-between; align-items:flex-end; gap:10px; flex-wrap:wrap; margin-bottom:10px;">
                <div>
                    <h3 style="margin:0; font-size:16px;">Summary Per Department</h3>
                    <div class="text-muted">Klik tombol "Lihat Temuan" untuk membuka daftar temuan department terkait (status Closed dikecualikan).</div>
                </div>

                <form method="GET" action="<?php echo e(route('qc.dashboard')); ?>" style="display:flex; align-items:flex-end; gap:8px; flex-wrap:wrap;">
                    <div class="field" style="margin:0; min-width:220px;">
                        <label>Filter Site</label>
                        <select name="site_id" class="select" <?php if(!$isHoUser): echo 'disabled'; endif; ?>>
                            <option value="">Semua Site</option>
                            <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($site->id); ?>" <?php if((string) $selectedSiteId === (string) $site->id): echo 'selected'; endif; ?>><?php echo e($site->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php if(!$isHoUser): ?>
                            <input type="hidden" name="site_id" value="<?php echo e($selectedSiteId); ?>">
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn"><i class="fas fa-filter"></i> Terapkan</button>
                    <a href="<?php echo e(route('qc.dashboard')); ?>" class="btn">Reset</a>
                </form>
            </div>

            <?php if($departmentSummary->isEmpty()): ?>
                <div class="text-muted">Belum ada data department pada filter yang dipilih.</div>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="dept-table">
                        <thead>
                            <tr>
                                <th>Site</th>
                                <th>Department</th>
                                <th>Total Aktif</th>
                                <th>Open</th>
                                <th>In Review</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $departmentSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($item->site_name); ?></td>
                                    <td><?php echo e($item->department_name); ?></td>
                                    <td><?php echo e((int) $item->total_findings); ?></td>
                                    <td><?php echo e((int) $item->open_total); ?></td>
                                    <td><?php echo e((int) $item->in_review_total); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('qc.findings.index', ['site_id' => $item->site_id, 'department_id' => $item->department_id])); ?>" class="btn btn-primary btn-sm">
                                            Lihat Temuan
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
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
<?php /**PATH C:\Users\deniz\Downloads\plantation.oilpam.my.id (1)\plantation.oilpam.my.id\Modules/QcComplaintSystem\resources/views/dashboard/summary.blade.php ENDPATH**/ ?>