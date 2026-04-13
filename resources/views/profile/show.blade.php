<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Saya - Oilpam One ERP</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&family=space-grotesk:500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-color: #f4f7f6;
            --surface: rgba(255, 255, 255, 0.85);
            --surface-hover: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --border: rgba(226, 232, 240, 0.8);
            --steel: #3b82f6;
            --steel-soft: #eff6ff;
            --amber: #f59e0b;
            --amber-soft: #fffbeb;
            --green: #10b981;
            --green-soft: #ecfdf5;
            --danger: #ef4444;
            --hero-start: #0f172a;
            --hero-mid: #1e293b;
            --hero-end: #334155;
            --hero-glow: #38bdf8;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: 'Manrope', sans-serif;
            color: var(--text);
            background: 
                radial-gradient(at 0% 0%, rgba(220, 232, 255, 0.7) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(216, 243, 220, 0.7) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(255, 238, 204, 0.7) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(230, 224, 255, 0.7) 0px, transparent 50%);
            background-color: var(--bg-color);
            background-size: 150% 150%;
            animation: gradientMove 15s ease infinite alternate;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* Header Navigation */
        .profile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 40px;
            animation: fade-slide-down 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .profile-header-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 12px;
            padding: 10px 20px;
            color: var(--text);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.95);
            border-color: var(--steel);
            color: var(--steel);
            transform: translateX(-4px);
        }

        .profile-title h1 {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 36px;
            letter-spacing: -0.03em;
            background: linear-gradient(to right, var(--hero-start), var(--steel));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .profile-title p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        .profile-actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            font-family: inherit;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--steel);
            color: #fff;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(226, 232, 240, 0.8);
            color: var(--text);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.95);
            border-color: var(--steel);
        }

        /* Profile Hero Card */
        .profile-hero {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--hero-start), var(--hero-mid), var(--hero-end));
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            padding: 40px;
            color: #ffffff;
            box-shadow: 0 30px 60px -15px rgba(15, 23, 42, 0.4);
            margin-bottom: 32px;
            animation: fade-slide-down 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.1s both;
        }

        .profile-hero::before,
        .profile-hero::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(60px);
            z-index: 0;
        }

        .profile-hero::before {
            width: 300px;
            height: 300px;
            right: -100px;
            top: -100px;
            background: rgba(56, 189, 248, 0.3);
            animation: float 8s ease-in-out infinite;
        }

        .profile-hero::after {
            width: 250px;
            height: 250px;
            left: -80px;
            bottom: -80px;
            background: rgba(16, 185, 129, 0.2);
            animation: float 10s ease-in-out infinite reverse;
        }

        .profile-hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            gap: 30px;
        }

        .profile-avatar-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #38bdf8, var(--steel));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 48px;
            font-weight: 800;
            box-shadow: 0 8px 24px rgba(56, 189, 248, 0.3);
            border: 3px solid rgba(255, 255, 255, 0.2);
        }

        .profile-avatar-name {
            font-weight: 700;
            font-size: 14px;
            text-align: center;
        }

        .profile-info {
            flex: 1;
            padding-top: 8px;
        }

        .profile-info h2 {
            margin: 0 0 4px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 32px;
            letter-spacing: -0.02em;
        }

        .profile-role {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .role-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 100px;
            padding: 4px 12px;
            font-weight: 600;
            font-size: 12px;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 24px;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            padding: 14px 16px;
            text-align: center;
        }

        .stat-box strong {
            display: block;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .stat-box span {
            display: block;
            font-size: 12px;
            color: #cbd5e1;
            font-weight: 500;
        }

        /* Content Sections */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
            animation: card-rise 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.2s both;
        }

        .info-card {
            background: var(--surface);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.05);
            transition: all 0.3s ease;
        }

        .info-card:hover {
            border-color: rgba(15, 23, 42, 0.1);
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.1);
            transform: translateY(-4px);
        }

        .info-card h3 {
            margin: 0 0 16px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text);
        }

        .info-card h3 i {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: var(--steel-soft);
            color: var(--steel);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .info-item {
            margin-bottom: 16px;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .info-value {
            display: block;
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            word-break: break-word;
        }

        .info-value.email {
            color: var(--steel);
        }

        .module-access {
            background: var(--surface);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.05);
            margin-bottom: 24px;
            animation: card-rise 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.3s both;
        }

        .module-access h3 {
            margin: 0 0 16px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text);
        }

        .module-access h3 i {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: var(--green-soft);
            color: var(--green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .module-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }

        .module-item {
            background: linear-gradient(135deg, #f0f4f8 0%, #f9fafb 100%);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 16px;
            text-align: center;
            transition: all 0.2s ease;
        }

        .module-item:hover {
            background: var(--steel-soft);
            border-color: var(--steel);
            transform: translateY(-2px);
        }

        .module-item-name {
            display: block;
            font-weight: 700;
            color: var(--text);
            font-size: 13px;
            margin-bottom: 6px;
        }

        .module-item-role {
            display: block;
            font-size: 11px;
            color: var(--steel);
            background: var(--steel-soft);
            border-radius: 6px;
            padding: 2px 8px;
            width: fit-content;
            margin: 0 auto;
            font-weight: 600;
        }

        /* Footer */
        .profile-footer {
            display: flex;
            align-items: center;
            gap: 20px;
            justify-content: space-between;
            padding-top: 24px;
            border-top: 1px solid rgba(15, 23, 42, 0.05);
            margin-top: 40px;
        }

        .credit {
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 13px;
        }

        .credit-top {
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.02em;
        }

        .credit-bottom {
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .credit-bottom i {
            color: var(--amber);
            font-size: 11px;
            animation: pulse-icon 2s infinite;
        }

        .creator-link {
            position: relative;
            color: var(--text);
            font-weight: 700;
            text-decoration: none;
            transition: color 0.3s ease;
            margin-left: 2px;
        }

        .creator-link::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: -2px;
            left: 0;
            background: linear-gradient(90deg, var(--steel), var(--green));
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1);
            border-radius: 2px;
        }

        .creator-link:hover {
            color: var(--steel);
        }

        .creator-link:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }

        /* Animations */
        @keyframes gradientMove {
            0% { background-position: 0% 0%; }
            100% { background-position: 100% 100%; }
        }

        @keyframes float {
            0% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
            100% { transform: translateY(0) scale(1); }
        }

        @keyframes fade-slide-down {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes card-rise {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes pulse-icon {
            0% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 0.8; }
        }

        /* Alert Messages */
        .alert {
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            animation: fade-slide-down 0.4s ease;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #d1fae5;
            color: #065f46;
        }

        .alert-success i {
            color: var(--green);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .profile-title h1 {
                font-size: 28px;
            }

            .profile-hero {
                padding: 28px 20px;
            }

            .profile-hero-content {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .profile-stats {
                grid-template-columns: 1fr;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .module-list {
                grid-template-columns: 1fr;
            }

            .profile-footer {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-actions {
                width: 100%;
            }

            .btn {
                flex: 1;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
@include('components.impersonation-banner')

<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="profile-header">
        <div class="profile-header-content">
            <a href="{{ route('modules.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <div class="profile-title">
                <h1>Profil Saya</h1>
                <p>Kelola data dan preferensi pribadi Anda</p>
            </div>
        </div>
        <div class="profile-actions">
            <a href="{{ route('global.profile.edit') }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Profil
            </a>
        </div>
    </div>

    <!-- Hero Card with Avatar and Stats -->
    <div class="profile-hero">
        <div class="profile-hero-content">
            <div class="profile-avatar-section">
                <div class="profile-avatar">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
                <div class="profile-avatar-name">
                    {{ $user->name }}
                </div>
            </div>

            <div class="profile-info">
                <h2>{{ $user->name }}</h2>
                <div class="profile-role">
                    <i class="fas fa-badge-check"></i>
                    <span class="role-badge">
                        {{ optional($user->roles->first())->name ?? 'User Reguler' }}
                    </span>
                </div>

                <div class="profile-stats">
                    <div class="stat-box">
                        <strong>{{ $totalModulesAccess }}</strong>
                        <span>Modul Akses</span>
                    </div>
                    <div class="stat-box">
                        <strong>{{ $user->roles->count() }}</strong>
                        <span>Peran Global</span>
                    </div>
                    <div class="stat-box">
                        <strong>{{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}</strong>
                        <span>Status</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Cards -->
    <div class="content-grid">
        <!-- Personal Info Card -->
        <div class="info-card">
            <h3>
                <i class="fas fa-user"></i>
                Informasi Pribadi
            </h3>
            <div class="info-item">
                <span class="info-label">Nama Lengkap</span>
                <span class="info-value">{{ $user->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Email</span>
                <span class="info-value email">{{ $user->email }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">No. Telepon</span>
                <span class="info-value">{{ $user->phone_number ?? '–' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Posisi</span>
                <span class="info-value">{{ $user->position ?? '–' }}</span>
            </div>
        </div>

        <!-- Organization Card -->
        <div class="info-card">
            <h3>
                <i class="fas fa-building"></i>
                Informasi Organisasi
            </h3>
            <div class="info-item">
                <span class="info-label">Situs / Lokasi</span>
                <span class="info-value">{{ optional($user->site)->name ?? '–' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Departemen</span>
                <span class="info-value">{{ optional($user->department)->name ?? '–' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Status Akun</span>
                <span class="info-value">
                    @if($user->is_active)
                        <span style="color: var(--green);">✓ Aktif</span>
                    @else
                        <span style="color: var(--danger);">✗ Nonaktif</span>
                    @endif
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Bergabung Sejak</span>
                <span class="info-value">{{ $user->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Module Access Card -->
    @if($totalModulesAccess > 0)
        <div class="module-access">
            <h3>
                <i class="fas fa-th-large"></i>
                Akses Modul
            </h3>
            <div class="module-list">
                @forelse($user->moduleRoles as $moduleRole)
                    <div class="module-item">
                        <span class="module-item-name">{{ ucfirst($moduleRole->module_key) }}</span>
                        <span class="module-item-role">{{ $moduleRole->role_name }}</span>
                    </div>
                @empty
                    <p style="color: var(--muted); font-size: 14px;">Tidak ada akses modul khusus</p>
                @endforelse
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="profile-footer">
        <div class="credit" aria-label="Copyright dan kredit pembuat">
            <div class="credit-top">&copy; {{ date('Y') }} Oilpam One ERP Module Hub</div>
            <div class="credit-bottom">
                Create by  
                <a href="https://github.com/rvanza453" class="creator-link" target="_blank" rel="noopener noreferrer">Muhammad Revanza</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
