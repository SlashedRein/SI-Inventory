<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dewi Cookies</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #E07A5F;       /* Terracotta */
            --primary-hover: #D06348;
            --secondary: #264653;     /* Deep Slate */
            --bg-light: #FDFBF7;      /* Cream */
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            height: 100vh;
            overflow: hidden;
        }

        .split-screen {
            display: flex;
            height: 100%;
            width: 100%;
        }

        /* === BAGIAN KIRI (BRANDING) === */
        .left-pane {
            width: 50%;
            background-color: var(--secondary);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        /* Dekorasi Lingkaran Background */
        .circle-deco {
            position: absolute;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        .c1 { width: 400px; height: 400px; top: -100px; left: -100px; }
        .c2 { width: 300px; height: 300px; bottom: -50px; right: -50px; }

        /* Animasi Logo Melayang */
        .floating-icon {
            font-size: 8rem;
            color: var(--primary);
            animation: float 6s ease-in-out infinite;
            text-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        /* === BAGIAN KANAN (FORM) === */
        .right-pane {
            width: 50%;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Styling Input Form */
        .form-floating > .form-control {
            border: 2px solid #eee;
            border-radius: 12px;
        }
        .form-floating > .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(224, 122, 95, 0.1);
        }
        .form-floating > label { color: #999; }

        /* Tombol Login */
        .btn-login {
            background-color: var(--primary);
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(224, 122, 95, 0.3);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .left-pane { display: none; }
            .right-pane { width: 100%; background-color: var(--bg-light); }
            .login-card { background: #fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        }
    </style>
</head>
<body>

    <div class="split-screen">
        
        <div class="left-pane">
            <div class="circle-deco c1"></div>
            <div class="circle-deco c2"></div>
            
            <i class="bi bi-cookie floating-icon mb-4"></i>
            <h1 class="fw-bold mb-2" style="font-size: 2.5rem;">Dewi Cookies</h1>
            <p class="text-white-50 fs-5">Inventory & Point of Sales System</p>
        </div>

        <div class="right-pane">
            <div class="login-card">
                <div class="text-center mb-5">
                    <h3 class="fw-bold text-dark">Selamat Datang!</h3>
                    <p class="text-muted">Silakan login untuk mengelola toko.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                        <i class="bi bi-exclamation-circle-fill me-2"></i> Username atau Password salah.
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="{{ old('username') }}" required autofocus>
                        <label for="username">Username</label>
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                            <label class="form-check-label text-muted small" for="remember_me">
                                Ingat Saya
                            </label>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-login text-white">
                            Masuk Dashboard <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>

                </form>

                <div class="text-center mt-5 text-muted small">
                    &copy; {{ date('Y') }} Dewi Cookies System v1.0
                </div>
            </div>
        </div>

    </div>

</body>
</html>