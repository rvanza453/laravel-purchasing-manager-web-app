<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ?? 'QC Complaint System'); ?> - ERP</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <style>
        :root {
            --bg: #edf3f1;
            --surface: #ffffff;
            --surface-soft: #f4fbf8;
            --text: #102a2a;
            --muted: #536a68;
            --border: #cfe1db;
            --brand: #0f766e;
            --brand-2: #0b5f58;
            --brand-soft: rgba(15, 118, 110, 0.14);
            --danger: #b91c1c;
            --danger-soft: #fef2f2;
            --success: #15803d;
            --success-soft: #f0fdf4;
            --radius: 16px;
            --sidebar: 260px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Manrope', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 0% 0%, #d6e7de 0%, transparent 34%),
                radial-gradient(circle at 100% 0%, #f4fbf7 0%, transparent 32%),
                radial-gradient(circle at 80% 100%, #e5efec 0%, transparent 30%),
                var(--bg);
            display: flex;
        }

        .sidebar {
            width: var(--sidebar);
            min-height: 100vh;
            position: fixed;
            background:
                radial-gradient(circle at 15% 0%, rgba(20, 184, 166, 0.24), transparent 42%),
                linear-gradient(180deg, #0b2f2e 0%, #082322 100%);
            color: #ecfeff;
            padding: 22px 16px;
            border-right: 1px solid rgba(255, 255, 255, 0.14);
            box-shadow: 16px 0 34px rgba(8, 35, 34, 0.28);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 10px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            margin-bottom: 14px;
        }

        .logo-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(145deg, #2dd4bf, #0f766e);
            color: #fff;
            font-size: 18px;
            box-shadow: 0 10px 18px rgba(15, 118, 110, 0.36);
        }

        .logo h1 { margin: 0; font-size: 14px; letter-spacing: 0.03em; }
        .logo p { margin: 2px 0 0; font-size: 11px; opacity: 0.8; }

        .nav { display: grid; gap: 6px; }

        .nav a {
            text-decoration: none;
            color: #d1fae5;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 12px;
            font-size: 13px;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .nav a:hover,
        .nav a.active {
            background: rgba(255, 255, 255, 0.14);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.24);
            transform: translateX(2px);
        }

        .sidebar-footer {
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 13px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.15);
        }

        .sidebar-footer .name { font-size: 13px; font-weight: 600; }
        .sidebar-footer .meta { font-size: 11px; opacity: 0.8; }

        .main {
            margin-left: var(--sidebar);
            flex: 1;
            min-height: 100vh;
        }

        .top {
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid var(--border);
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .top h2 { margin: 0; font-size: 18px; }
        .actions { display: inline-flex; gap: 8px; flex-wrap: wrap; }

        .content {
            max-width: 1320px;
            margin: 0 auto;
            padding: 28px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.07);
            overflow: hidden;
        }

        .card-header {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
            background:
                linear-gradient(180deg, var(--surface-soft), #fff),
                radial-gradient(circle at 0% 0%, rgba(15, 118, 110, 0.08), transparent 50%);
            font-weight: 700;
            font-size: 14px;
        }

        .card-body { padding: 18px; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 11px;
            border: 1px solid #c8d8d1;
            background: #fbfefd;
            color: var(--text);
            padding: 9px 13px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 16px rgba(15, 23, 42, 0.08);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand), #0d9488);
            border-color: var(--brand);
            color: #fff;
            box-shadow: 0 10px 20px rgba(15, 118, 110, 0.24);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--brand-2), var(--brand));
        }

        .btn-danger { background: linear-gradient(135deg, #b91c1c, #ef4444); border-color: var(--danger); color: #fff; }
        .btn-success { background: linear-gradient(135deg, #15803d, #22c55e); border-color: var(--success); color: #fff; }
        .btn-sm { padding: 6px 10px; font-size: 12px; }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px; }
        .field label { font-size: 12px; color: var(--muted); font-weight: 700; }

        .input,
        .select,
        textarea {
            border: 1px solid #c6d7d0;
            border-radius: 11px;
            padding: 10px 12px;
            font: inherit;
            background: #fbfffd;
            color: var(--text);
            transition: all 0.2s ease;
        }

        .input:focus,
        .select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.12);
        }

        .table { width: 100%; border-collapse: collapse; }
        .table th,
        .table td {
            border-bottom: 1px solid var(--border);
            padding: 11px;
            text-align: left;
            vertical-align: top;
            font-size: 14px;
        }

        .table th {
            background: linear-gradient(180deg, #f8fcfb, #f2f8f5);
            color: #334155;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 9px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .badge-open { background: #dbeafe; color: #1d4ed8; }
        .badge-in_review { background: #fef3c7; color: #92400e; }
        .badge-closed { background: #dcfce7; color: #166534; }
        .badge-low { background: #ecfeff; color: #0f766e; }
        .badge-medium { background: #fffbeb; color: #b45309; }
        .badge-high { background: #fee2e2; color: #b91c1c; }

        .alert {
            border-radius: 12px;
            padding: 11px 13px;
            margin-bottom: 12px;
            border: 1px solid;
            font-size: 14px;
        }

        .alert-success { background: var(--success-soft); border-color: #bbf7d0; color: #166534; }
        .alert-danger { background: var(--danger-soft); border-color: #fecaca; color: #991b1b; }

        .text-muted { color: var(--muted); font-size: 13px; }

        .thumb {
            width: 100%;
            max-width: 280px;
            border-radius: 12px;
            border: 1px solid var(--border);
            object-fit: cover;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
        }

        .mobile-header,
        .mobile-bottom-nav,
        .mobile-drawer,
        .mobile-drawer-overlay {
            display: none;
        }

        @media (max-width: 980px) {
            .mobile-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: sticky;
                top: 0;
                z-index: 70;
                padding: 12px 14px;
                background: rgba(255, 255, 255, 0.96);
                border-bottom: 1px solid var(--border);
                backdrop-filter: blur(8px);
            }

            .mobile-header h2 {
                margin: 0;
                font-size: 14px;
                font-weight: 700;
            }

            .mobile-icon-btn {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                border: 1px solid #c8d8d1;
                background: #f7fcfa;
                color: var(--text);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                text-decoration: none;
            }

            .sidebar { display: none; }
            .main { margin-left: 0; }
            .top { display: none; }
            body { display: block; }
            .content { padding: 14px 14px 90px; }
            .grid { grid-template-columns: 1fr; }

            .mobile-bottom-nav {
                display: block;
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 85;
                background: #fff;
                border-top: 1px solid var(--border);
                box-shadow: 0 -8px 18px rgba(15, 23, 42, 0.08);
            }

            .mobile-bottom-nav-inner {
                height: 62px;
                display: grid;
                grid-template-columns: repeat(4, 1fr);
            }

            .mobile-bottom-nav a,
            .mobile-bottom-nav button {
                border: 0;
                background: transparent;
                text-decoration: none;
                color: #64748b;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 3px;
                font-size: 10px;
                font-weight: 700;
                cursor: pointer;
            }

            .mobile-bottom-nav .active {
                color: var(--brand);
            }

            .mobile-drawer-overlay {
                display: none;
                position: fixed;
                inset: 0;
                z-index: 90;
                background: rgba(15, 23, 42, 0.45);
            }

            .mobile-drawer {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: min(82vw, 320px);
                background: #fff;
                z-index: 95;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
                overflow-y: auto;
                padding: 16px 14px 18px;
            }

            .mobile-drawer.open { transform: translateX(0); }

            .mobile-drawer h3 {
                margin: 0 0 10px;
                font-size: 14px;
            }

            .mobile-drawer a {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 12px;
                border-radius: 10px;
                text-decoration: none;
                color: #334155;
                font-size: 13px;
                margin-bottom: 6px;
            }

            .mobile-drawer a.active,
            .mobile-drawer a:hover {
                background: #ecfdf5;
                color: var(--brand);
            }
        }
    </style>
</head>
<body>
    <?php echo $__env->make('components.impersonation-banner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('components.module-hub-button', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<aside class="sidebar">
    <div class="logo">
        <div class="logo-icon"><i class="fas fa-shield-halved"></i></div>
        <div>
            <h1>QC Complaint</h1>
            <p>Quality Control System</p>
        </div>
    </div>

    <nav class="nav">
        <?php
            $qcRole = auth()->user()?->moduleRole('qc');
            $siteName = strtolower(trim((string) auth()->user()?->site?->name));
            $position = strtolower(trim((string) auth()->user()?->position));
            $canCreateFinding = str_contains($siteName, 'head office')
                || (bool) preg_match('/\bho\b/i', $siteName)
                || str_contains($position, 'qc');
        ?>
        <a href="<?php echo e(route('modules.index')); ?>"><i class="fas fa-table-cells-large"></i> Hub Modul</a>
        <a href="<?php echo e(route('qc.dashboard')); ?>" class="<?php echo e(request()->routeIs('qc.dashboard') ? 'active' : ''); ?>"><i class="fas fa-chart-pie"></i> Dashboard Summary</a>
        <a href="<?php echo e(route('qc.findings.index')); ?>" class="<?php echo e(request()->routeIs('qc.findings.*') ? 'active' : ''); ?>"><i class="fas fa-list-check"></i> Daftar Temuan</a>
        <?php if(in_array($qcRole, ['QC Admin', 'QC Approver'])): ?>
            <a href="<?php echo e(route('qc.approvals.index')); ?>" class="<?php echo e(request()->routeIs('qc.approvals.*') ? 'active' : ''); ?>"><i class="fas fa-inbox"></i> Inbox Approval</a>
        <?php endif; ?>
        <?php if(in_array($qcRole, ['QC Admin', 'QC Officer', 'QC Approver'])): ?>
            <a href="<?php echo e(route('qc.deadlines.index')); ?>" class="<?php echo e(request()->routeIs('qc.deadlines.*') ? 'active' : ''); ?>"><i class="fas fa-calendar-day"></i> INBOX PIC</a>
        <?php endif; ?>
        <?php if($canCreateFinding): ?>
            <a href="<?php echo e(route('qc.findings.create')); ?>" class="<?php echo e(request()->routeIs('qc.findings.create') ? 'active' : ''); ?>"><i class="fas fa-square-plus"></i> Lapor Temuan</a>
        <?php endif; ?>
        <?php if($qcRole === 'QC Admin'): ?>
            <a href="<?php echo e(route('qc.approval-config.index')); ?>" class="<?php echo e(request()->routeIs('qc.approval-config.*') ? 'active' : ''); ?>"><i class="fas fa-sliders"></i> Approval Config</a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="avatar"><?php echo e(strtoupper(substr(auth()->user()->name ?? 'U', 0, 1))); ?></div>
        <div>
            <div class="name"><?php echo e(auth()->user()->name ?? '-'); ?></div>
            <div class="meta"><?php echo e(auth()->user()->moduleRole('qc') ?? 'No QC Role'); ?></div>
        </div>
    </div>
</aside>

<main class="main">
    <header class="mobile-header">
        <button type="button" class="mobile-icon-btn" onclick="toggleQcDrawer(true)" aria-label="Buka menu">
            <i class="fas fa-bars"></i>
        </button>
        <h2><?php echo e($title ?? 'QC Complaint System'); ?></h2>
        <a href="<?php echo e(route('modules.index')); ?>" class="mobile-icon-btn" aria-label="Hub modul">
            <i class="fas fa-arrow-left"></i>
        </a>
    </header>

    <div class="top">
        <h2><?php echo e($title ?? 'QC Complaint System'); ?></h2>
        <div class="actions">
            <?php echo $__env->yieldPushContent('actions'); ?>
            <form action="<?php echo e(route('logout')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn"><i class="fas fa-right-from-bracket"></i> Logout</button>
            </form>
        </div>
    </div>

    <div class="content">
        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 18px;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php echo e($slot); ?>

    </div>
</main>

<div id="qcDrawerOverlay" class="mobile-drawer-overlay" onclick="toggleQcDrawer(false)"></div>
<aside id="qcMobileDrawer" class="mobile-drawer" aria-hidden="true">
    <h3>Navigasi QC</h3>
    <?php
        $qcRole = auth()->user()?->moduleRole('qc');
        $siteName = strtolower(trim((string) auth()->user()?->site?->name));
        $position = strtolower(trim((string) auth()->user()?->position));
        $canCreateFinding = str_contains($siteName, 'head office')
            || (bool) preg_match('/\bho\b/i', $siteName)
            || str_contains($position, 'qc');
    ?>
    <a href="<?php echo e(route('modules.index')); ?>"><i class="fas fa-table-cells-large"></i> Hub Modul</a>
    <a href="<?php echo e(route('qc.dashboard')); ?>" class="<?php echo e(request()->routeIs('qc.dashboard') ? 'active' : ''); ?>"><i class="fas fa-chart-pie"></i> Dashboard Summary</a>
    <a href="<?php echo e(route('qc.findings.index')); ?>" class="<?php echo e(request()->routeIs('qc.findings.*') ? 'active' : ''); ?>"><i class="fas fa-list-check"></i> Daftar Temuan</a>
    <?php if(in_array($qcRole, ['QC Admin', 'QC Approver'])): ?>
        <a href="<?php echo e(route('qc.approvals.index')); ?>" class="<?php echo e(request()->routeIs('qc.approvals.*') ? 'active' : ''); ?>"><i class="fas fa-inbox"></i> Inbox Approval</a>
    <?php endif; ?>
    <?php if(in_array($qcRole, ['QC Admin', 'QC Officer', 'QC Approver'])): ?>
        <a href="<?php echo e(route('qc.deadlines.index')); ?>" class="<?php echo e(request()->routeIs('qc.deadlines.*') ? 'active' : ''); ?>"><i class="fas fa-calendar-day"></i> INBOX PIC</a>
    <?php endif; ?>
    <?php if($canCreateFinding): ?>
        <a href="<?php echo e(route('qc.findings.create')); ?>" class="<?php echo e(request()->routeIs('qc.findings.create') ? 'active' : ''); ?>"><i class="fas fa-square-plus"></i> Lapor Temuan</a>
    <?php endif; ?>
    <?php if($qcRole === 'QC Admin'): ?>
        <a href="<?php echo e(route('qc.approval-config.index')); ?>" class="<?php echo e(request()->routeIs('qc.approval-config.*') ? 'active' : ''); ?>"><i class="fas fa-sliders"></i> Approval Config</a>
    <?php endif; ?>
</aside>

<nav class="mobile-bottom-nav" aria-label="QC mobile navigation">
    <div class="mobile-bottom-nav-inner">
        <a href="<?php echo e(route('modules.index')); ?>"><i class="fas fa-th-large"></i><span>Modul</span></a>
        <a href="<?php echo e(route('qc.dashboard')); ?>" class="<?php echo e(request()->routeIs('qc.dashboard') ? 'active' : ''); ?>"><i class="fas fa-chart-pie"></i><span>Summary</span></a>
        <a href="<?php echo e(route('qc.deadlines.index')); ?>" class="<?php echo e(request()->routeIs('qc.deadlines.*') ? 'active' : ''); ?>"><i class="fas fa-calendar-day"></i><span>INBOX PIC</span></a>
        <button type="button" onclick="toggleQcDrawer(true)"><i class="fas fa-bars"></i><span>Menu</span></button>
    </div>
</nav>

<?php echo $__env->yieldPushContent('scripts'); ?>
<script>
    function toggleQcDrawer(show) {
        const drawer = document.getElementById('qcMobileDrawer');
        const overlay = document.getElementById('qcDrawerOverlay');

        if (!drawer || !overlay) {
            return;
        }

        drawer.classList.toggle('open', show);
        overlay.style.display = show ? 'block' : 'none';
        document.body.style.overflow = show ? 'hidden' : '';
    }
</script>
</body>
</html>
<?php /**PATH C:\Users\deniz\Downloads\plantation.oilpam.my.id (1)\plantation.oilpam.my.id\Modules/QcComplaintSystem\resources/views/components/layouts/master.blade.php ENDPATH**/ ?>