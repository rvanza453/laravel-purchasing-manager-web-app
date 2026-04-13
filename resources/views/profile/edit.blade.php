<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Profil - Oilpam One ERP</title>

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
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* Header */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 40px;
            animation: fade-slide-down 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
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
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.95);
            border-color: var(--steel);
            color: var(--steel);
            transform: translateX(-4px);
        }

        .page-title h1 {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 32px;
            letter-spacing: -0.03em;
            background: linear-gradient(to right, var(--hero-start), var(--steel));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-title p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        /* Form Container */
        .form-container {
            background: var(--surface);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 36px;
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.05);
            animation: fade-slide-down 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.1s both;
        }

        /* Form Sections */
        .form-section {
            margin-bottom: 32px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .form-section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid rgba(15, 23, 42, 0.05);
        }

        .form-section-title i {
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

        .form-section-title h2 {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px;
            letter-spacing: -0.02em;
            color: var(--text);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            display: block;
            font-weight: 700;
            font-size: 14px;
            color: var(--text);
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }

        .form-label .required {
            color: var(--danger);
        }

        .form-input,
        .form-textarea {
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            color: var(--text);
            background: rgba(255, 255, 255, 0.5);
            transition: all 0.2s ease;
        }

        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--steel);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-help-text {
            display: block;
            font-size: 12px;
            color: var(--muted);
            margin-top: 6px;
        }

        /* Error Messages */
        .error-message {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--danger);
            margin-top: 6px;
        }

        .error-message i {
            font-size: 14px;
        }

        /* Alert */
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

        .alert-danger {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-danger i {
            color: var(--danger);
        }

        /* Buttons */
        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid rgba(15, 23, 42, 0.05);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
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
            flex: 1;
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

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Password Strength */
        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            background: #ef4444;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .password-strength-bar.medium {
            background: #f59e0b;
            width: 50%;
        }

        .password-strength-bar.strong {
            background: var(--green);
            width: 100%;
        }

        /* Animations */
        @keyframes gradientMove {
            0% { background-position: 0% 0%; }
            100% { background-position: 100% 100%; }
        }

        @keyframes fade-slide-down {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-container {
                padding: 24px;
            }

            .page-title h1 {
                font-size: 24px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
@include('components.impersonation-banner')

<div class="container">
    <div class="page-header">
        <a href="{{ route('global.profile.show') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <div class="page-title">
            <h1>Edit Profil</h1>
            <p>Perbarui informasi pribadi dan keamanan akun Anda</p>
        </div>
    </div>

    <div class="form-container">
        <form action="{{ route('global.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Terdapat kesalahan pada formulir. Silakan periksa kembali.</span>
                </div>
            @endif

            <!-- Personal Information Section -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-user"></i>
                    <h2>Informasi Pribadi</h2>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="name" class="form-input @error('name') is-invalid @enderror" 
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-input @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="tel" name="phone_number" class="form-input @error('phone_number') is-invalid @enderror" 
                               value="{{ old('phone_number', $user->phone_number) }}" placeholder="Opsional">
                        @error('phone_number')
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jabatan / Position</label>
                        <input type="text" name="position" class="form-input @error('position') is-invalid @enderror"
                               value="{{ old('position', $user->position) }}" placeholder="Opsional">
                        @error('position')
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Security Section -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-lock"></i>
                    <h2>Keamanan Akun</h2>
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-input @error('current_password') is-invalid @enderror" 
                               placeholder="Masukkan password saat ini jika ingin mengubah password">
                        <span class="form-help-text">Diperlukan hanya jika Anda ingin mengubah password</span>
                        @error('current_password')
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" id="password" class="form-input @error('password') is-invalid @enderror" 
                               placeholder="Biarkan kosong jika tidak ingin mengubah">
                        <div class="password-strength" id="passwordStrength">
                            <div class="password-strength-bar" id="passwordBar"></div>
                        </div>
                        <span class="form-help-text">Minimal 6 karakter</span>
                        @error('password')
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-input @error('password_confirmation') is-invalid @enderror" 
                               placeholder="Konfirmasi password baru Anda">
                        @error('password_confirmation')
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Signature Section -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-signature"></i>
                    <h2>Tanda Tangan Digital</h2>
                </div>

                @if($user->signature_path)
                    <div class="form-row full">
                        <div class="form-group">
                            <label class="form-label">Tanda Tangan Saat Ini</label>
                            <div style="border: 1px solid var(--border); border-radius: 12px; padding: 14px; background: #fff; width: fit-content;">
                                <img src="{{ asset('storage/' . $user->signature_path) }}" alt="Signature" style="max-height: 90px; max-width: 320px; display: block;">
                            </div>
                        </div>
                    </div>
                @endif

                <div class="form-row full">
                    <div class="form-group">
                        <label class="form-label">Upload Tanda Tangan Baru</label>
                        <input type="file" form="signature-upload-form" name="signature" accept="image/png,image/jpg,image/jpeg" class="form-input @error('signature') is-invalid @enderror">
                        <span class="form-help-text">Format PNG/JPG/JPEG, maksimal 2MB. Rekomendasi background transparan.</span>
                        @error('signature')
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 12px; padding-top: 12px;">
                    <button type="submit" form="signature-upload-form" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Tanda Tangan
                    </button>
                    @if($user->signature_path)
                        <button type="submit" form="signature-delete-form" class="btn btn-secondary" style="border-color: #fecaca; color: #b91c1c;" onclick="return confirm('Hapus tanda tangan saat ini?')">
                            <i class="fas fa-trash"></i> Hapus Tanda Tangan
                        </button>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('global.profile.show') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<form id="signature-upload-form" action="{{ route('global.profile.signature.upload') }}" method="POST" enctype="multipart/form-data" style="display: none;">
    @csrf
</form>

<form id="signature-delete-form" action="{{ route('global.profile.signature.delete') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const passwordBar = document.getElementById('passwordBar');

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const strength = this.value.length;
            
            if (strength === 0) {
                passwordBar.className = 'password-strength-bar';
                passwordBar.style.width = '0%';
            } else if (strength < 8) {
                passwordBar.className = 'password-strength-bar';
                passwordBar.style.width = '33%';
            } else if (strength < 12) {
                passwordBar.className = 'password-strength-bar medium';
                passwordBar.style.width = '50%';
            } else {
                passwordBar.className = 'password-strength-bar strong';
                passwordBar.style.width = '100%';
            }
        });
    }
</script>

<!-- Animations -->
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .page-header, .form-container {
        animation: fadeIn 0.5s ease forwards;
    }
</style>
</body>
</html>
