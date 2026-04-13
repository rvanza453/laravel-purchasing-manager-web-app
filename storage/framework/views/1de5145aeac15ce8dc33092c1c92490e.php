<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Login - ERP Plantation Saraswanti</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body>
    <style>
        /* Elegant Reset */
        .main-content { margin-left: 0 !important; }
        .sidebar, .top-bar { display: none !important; }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, hsla(210,100%,98%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(215,25%,90%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(210,100%,98%,1) 0, transparent 50%);
            overflow: hidden;
            position: relative;
            font-family: 'Manrope', system-ui, sans-serif;
        }

        .login-container {
            position: relative;
            margin-top: 50px; /* Space for Minion on top */
        }

        /* PROFESSIONAL CARD */
        .login-card {
            width: 440px;
            background: #ffffff;
            border-radius: 24px;
            padding: 45px 50px;
            box-shadow: 
                0 10px 25px -3px rgba(15, 23, 42, 0.05),
                0 20px 35px -5px rgba(15, 23, 42, 0.03);
            border: 1px solid rgba(226, 232, 240, 0.8);
            position: relative;
            z-index: 20;
        }

        /* BRANDING AREA */
        .brand-container {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #1f7a45, #145c32);
            color: #ffffff;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(31, 122, 69, 0.25);
            flex-shrink: 0;
        }
        .brand-text {
            font-size: 19px; /* Dikecilkan sedikit agar "Saraswanti Plantation" muat satu baris */
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }
        .brand-text span {
            color: #1f7a45;
            font-weight: 600;
            margin-left: 4px;
        }
        .brand-subtitle {
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-top: 4px;
            display: block;
        }

        /* MINION V2 (SITTING ON TOP) */
        .minion-sitting {
            position: absolute;
            top: -110px;
            right: 40px;
            z-index: 10;
            width: 110px;
            height: 120px;
            perspective: 800px;
        }

        .minion-body {
            width: 85px;
            height: 125px;
            background: #ffdb00;
            border-radius: 45px 45px 15px 15px;
            position: relative;
            box-shadow: inset -8px -8px 15px rgba(0,0,0,0.1);
            transform-style: preserve-3d;
            transition: transform 0.1s ease-out;
        }

        /* Hair Tufts */
        .hair-tuft {
            position: absolute;
            top: -12px;
            left: 50%;
            width: 2px;
            height: 15px;
            background: #333;
            transform: translateX(-50%);
        }
        .hair-tuft::before, .hair-tuft::after {
            content: '';
            position: absolute;
            width: 2px;
            height: 12px;
            background: #333;
            top: 2px;
        }
        .hair-tuft::before { transform: rotate(-25deg); left: -4px; }
        .hair-tuft::after { transform: rotate(25deg); right: -4px; }

        /* Goggles */
        .goggles {
            position: absolute;
            top: 25px;
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 2px;
        }
        .goggle-strap {
            position: absolute;
            top: 20px;
            width: 100%;
            height: 8px;
            background: #333;
            z-index: -1;
        }
        .goggle-frame {
            width: 42px;
            height: 42px;
            background: #94a3b8;
            border: 3px solid #64748b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 5px rgba(0,0,0,0.2);
        }
        .eye {
            width: 30px;
            height: 30px;
            background: #fff;
            border-radius: 50%;
            position: relative;
            overflow: hidden;
            animation: blink 4s infinite;
        }
        @keyframes blink {
            0%, 95%, 100% { height: 30px; margin-top: 0; }
            97% { height: 2px; margin-top: 14px; }
        }
        .pupil {
            width: 11px;
            height: 11px;
            background: #333;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -5.5px;
            margin-left: -5.5px;
        }

        /* Overalls V2 */
        .overalls {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 35px;
            background: #2b568e;
            border-radius: 0 0 15px 15px;
        }
        .pocket {
            position: absolute;
            top: 5px;
            left: 50%;
            width: 20px;
            height: 20px;
            background: #1e3a8a;
            border-radius: 0 0 10px 10px;
            transform: translateX(-50%);
        }
        .pocket::after {
            content: 'G';
            font-size: 8px;
            color: #fff;
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            font-weight: 900;
        }

        /* Arms & Gloves */
        .arm {
            position: absolute;
            top: 70px;
            width: 10px;
            height: 30px;
            background: #ffdb00;
            left: -8px;
            transform: rotate(15deg);
            border-radius: 5px;
        }
        .arm.right { left: auto; right: -8px; transform: rotate(-15deg); }
        .glove {
            position: absolute;
            bottom: -5px;
            width: 12px;
            height: 12px;
            background: #333;
            border-radius: 4px;
        }

        /* LEG SITTING */
        .leg-sitting {
            position: absolute;
            bottom: -15px;
            width: 15px;
            height: 20px;
            background: #2b568e;
            left: 15px;
            border-radius: 0 0 5px 5px;
        }
        .leg-sitting.right { left: auto; right: 15px; }
        .shoe {
            position: absolute;
            bottom: -5px;
            width: 20px;
            height: 10px;
            background: #333;
            border-radius: 10px 10px 0 0;
            left: -2px;
        }

        /* ANIMATIONS */
        @keyframes happy-jump {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-30px) rotate(5deg); }
        }
        .animate-jump { animation: happy-jump 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); }

        @keyframes cheering {
            0%, 100% { transform: scale(1) rotate(0); }
            25% { transform: scale(1.1) rotate(-5deg); }
            75% { transform: scale(1.1) rotate(5deg); }
        }
        .animate-cheer { animation: cheering 0.4s infinite; }

        /* FORM STYLES */
        .login-header { margin-bottom: 30px; }
        .login-title { font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
        .login-subtitle { color: #64748b; font-size: 14px; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; }
        .form-control { 
            width: 100%; 
            padding: 14px 16px; 
            background: #f8fafc; 
            border: 1.5px solid #e2e8f0; 
            border-radius: 12px; 
            font-size: 15px; 
            transition: all 0.3s ease; 
        }
        .form-control:focus { outline: none; border-color: #1f7a45; background: #fff; box-shadow: 0 0 0 4px rgba(31, 122, 69, 0.1); }

        .btn-login { 
            width: 100%; 
            padding: 14px; 
            background: linear-gradient(135deg, #1e293b, #0f172a); 
            color: #fff; 
            border: none; 
            border-radius: 12px; 
            font-weight: 700; 
            font-size: 15px;
            cursor: pointer; 
            transition: all 0.3s ease; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 10px; 
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(15, 23, 42, 0.3); }

        .alert-error { background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239; padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; }

        /* ELEGANT FOOTER */
        .login-footer {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid rgba(226, 232, 240, 0.8);
            display: flex;
            justify-content: center;
        }
        
        .credit-bottom {
            color: #94a3b8;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .credit-bottom i {
            color: #f59e0b;
            font-size: 12px;
            animation: pulse-icon 2s infinite;
        }

        .creator-link {
            position: relative;
            color: #475569;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .creator-link::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: -2px;
            left: 0;
            background: linear-gradient(90deg, #1f7a45, #10b981);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1);
            border-radius: 2px;
        }

        .creator-link:hover { color: #1f7a45; }
        .creator-link:hover::after { transform: scaleX(1); transform-origin: left; }

        @keyframes pulse-icon {
            0% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 0.8; }
        }

        @media (max-width: 500px) {
            .login-card { width: 90vw; padding: 35px 25px; }
            .minion-sitting { right: 20px; transform: scale(0.8); top: -90px; }
            .brand-text { font-size: 17px; } /* Resize on mobile */
        }
    </style>

    <div class="login-page">
        <div class="login-container">
            
            <div class="minion-sitting" id="minionContainer" style="cursor: pointer;" title="Klik aku!">
                <div class="hair-tuft"></div>
                <div class="minion-body" id="minionBody">
                    <div class="goggle-strap"></div>
                    <div class="goggles">
                        <div class="goggle-frame"><div class="eye"><div class="pupil"></div></div></div>
                        <div class="goggle-frame"><div class="eye"><div class="pupil"></div></div></div>
                    </div>
                    <div class="overalls">
                        <div class="pocket"></div>
                    </div>
                    <div class="arm">
                        <div class="glove"></div>
                    </div>
                    <div class="arm right">
                        <div class="glove"></div>
                    </div>
                    <div class="leg-sitting">
                        <div class="shoe"></div>
                    </div>
                    <div class="leg-sitting right">
                        <div class="shoe"></div>
                    </div>
                </div>
            </div>

            <div class="login-card">
                
                <div class="brand-container">
                    <div class="brand-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div>
                        <div class="brand-text">Saraswanti Plantation<span>ERP</span></div>
                        <span class="brand-subtitle">Divisi Kebun</span>
                    </div>
                </div>

                <div class="login-header">
                    <h1 class="login-title">Selamat Datang</h1>
                    <p class="login-subtitle">Akses sentralisasi data dan modul operasional kebun.</p>
                </div>

                <?php if($errors->any()): ?>
                    <div class="alert-error">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div><?php echo e($error); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('login.submit')); ?>" method="POST" id="loginForm">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label class="form-label">Email atau Username</label>
                        <input type="text" name="login" id="loginField" class="form-control" value="<?php echo e(old('login')); ?>" placeholder="user@saraswanti.test / username" required autofocus>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" id="passwordField" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: #64748b; cursor: pointer; font-weight: 500;">
                            <input type="checkbox" name="remember" style="width: 16px; height: 16px; accent-color: #1f7a45; border-radius: 4px;">
                            Ingat sesi saya
                        </label>
                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">
                        <span id="btnText">Login ke Sistem</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                
                <div class="login-footer">
                    <div class="credit-bottom">
                        Diracik dengan <i class="fas fa-bolt"></i> oleh 
                        <a href="https://github.com/rvanza453" class="creator-link" target="_blank" rel="noopener noreferrer">Muhammad Revanza</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const minionContainer = document.getElementById('minionContainer');
            const minionBody = document.getElementById('minionBody');
            const pupils = document.querySelectorAll('.pupil');
            const loginField = document.getElementById('loginField');
            const passwordField = document.getElementById('passwordField');
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');

            let isInteracting = false;
            let focusTarget = null;

            // --- 1. Minion Click (Jump) ---
            minionContainer.addEventListener('click', () => {
                minionBody.classList.add('animate-jump');
                setTimeout(() => minionBody.classList.remove('animate-jump'), 500);
            });

            // --- 2. Input Focus (Eye Lock) ---
            const handleFocus = (e) => {
                isInteracting = true;
                focusTarget = e.target;
            };

            const handleBlur = () => {
                isInteracting = false;
                focusTarget = null;
            };

            loginField.addEventListener('focus', handleFocus);
            loginField.addEventListener('blur', handleBlur);
            passwordField.addEventListener('focus', handleFocus);
            passwordField.addEventListener('blur', handleBlur);

            // --- 3. Login Click / Submit (Cheer) ---
            loginForm.addEventListener('submit', () => {
                minionBody.classList.add('animate-cheer');
                document.getElementById('btnText').innerText = 'Mengautentikasi...';
            });

            // --- Mouse Move Logic ---
            document.addEventListener('mousemove', (e) => {
                const x = e.clientX;
                const y = e.clientY;

                let targetX = x;
                let targetY = y;

                // Adjust target if focusing on input
                if (isInteracting && focusTarget) {
                    const rect = focusTarget.getBoundingClientRect();
                    targetX = rect.left + rect.width / 2;
                    targetY = rect.top + rect.height / 2;
                }

                // Pupil Eye Tracking
                pupils.forEach(pupil => {
                    const rect = pupil.parentElement.getBoundingClientRect();
                    const center = { x: rect.left + rect.width / 2, y: rect.top + rect.height / 2 };
                    const angle = Math.atan2(targetY - center.y, targetX - center.x);
                    const force = Math.min(Math.hypot(targetX - center.x, targetY - center.y) / 12, 10);
                    
                    pupil.style.transform = `translate(${Math.cos(angle) * force}px, ${Math.sin(angle) * force}px)`;
                });

                // Head Tilt Physics
                const mRect = minionBody.getBoundingClientRect();
                const mCenter = { x: mRect.left + mRect.width / 2, y: mRect.top + mRect.height / 2 };
                
                const rx = (mCenter.y - targetY) / 50; 
                const ry = (targetX - mCenter.x) / 50;
                const limit = isInteracting ? 15 : 12; // Tilt more when curious
                
                minionBody.style.transform = `rotateX(${Math.max(-limit, Math.min(limit, rx))}deg) rotateY(${Math.max(-limit, Math.min(limit, ry))}deg)`;
            });
        });
    </script>
</body>
</html><?php /**PATH C:\Users\deniz\Downloads\plantation.oilpam.my.id (1)\plantation.oilpam.my.id\resources\views/auth/login.blade.php ENDPATH**/ ?>