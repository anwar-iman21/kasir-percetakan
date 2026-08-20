<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kasir Percetakan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }
        .login-header .brand-icon {
            width: 64px; height: 64px;
            background: rgba(255,255,255,0.2);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1rem;
        }
        .login-body { padding: 2rem; }
        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1.5px solid #e9ecef;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
        }
        .input-group-text {
            border-radius: 10px 0 0 10px;
            background: #f8f9fa;
            border: 1.5px solid #e9ecef;
        }
        .input-group .form-control {
            border-radius: 0 10px 10px 0;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            width: 100%;
            transition: opacity 0.2s;
        }
        .btn-login:hover { opacity: 0.9; color: white; }
        .demo-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="brand-icon"><i class="bi bi-printer"></i></div>
            <h4 class="fw-bold mb-1">Kasir Percetakan</h4>
            <p class="mb-0 opacity-75" style="font-size:0.85rem;">Sistem Kasir Modern untuk Percetakan</p>
        </div>
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger rounded-3 py-2">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" placeholder="admin@percetakan.com" required autofocus>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" id="passwordInput"
                            class="form-control" placeholder="••••••••" required>
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePass()" style="border-radius:0 10px 10px 0;">
                            <i class="bi bi-eye" id="passIcon"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small" for="remember">Ingat saya</label>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>

            <div class="demo-info mt-3">
                <div class="fw-semibold mb-1">🔐 Akun Demo:</div>
                <div><b>Admin:</b> admin@percetakan.com</div>
                <div><b>Kasir:</b> kasir@percetakan.com</div>
                <div class="mt-1"><b>Password:</b> password123</div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function togglePass() {
        const inp = document.getElementById('passwordInput');
        const icon = document.getElementById('passIcon');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            inp.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }
    </script>
</body>
</html>
