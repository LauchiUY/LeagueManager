<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LeagueManager - Iniciar Sesión</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --lm-primary: #ff2a5f;
            --lm-dark: #0f1015;
            --lm-accent: #00f0ff;
            --lm-surface: #1a1b23;
        }
        body { 
            background-color: var(--lm-dark); 
            font-family: 'Inter', sans-serif; 
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        
        .bg-shapes {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at 20% 30%, rgba(255, 42, 95, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 70%, rgba(0, 240, 255, 0.15) 0%, transparent 50%);
            z-index: -1;
        }

        .login-card {
            background: rgba(26, 27, 35, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 24px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 2rem;
        }
        .login-card .brand i { color: var(--lm-primary); font-size: 2.2rem; }

        .form-control {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #fff;
            padding: 0.8rem 1.2rem;
            border-radius: 12px;
            transition: all 0.3s;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.08);
            border-color: var(--lm-primary);
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(255, 42, 95, 0.25);
        }
        .form-control::placeholder {
            color: rgba(255,255,255,0.3);
        }

        .form-label {
            color: #a0a5ba;
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--lm-primary), #d01c48);
            color: white;
            border: none;
            padding: 0.8rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 1.5rem;
            transition: all 0.3s;
            box-shadow: 0 10px 25px rgba(255, 42, 95, 0.3);
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(255, 42, 95, 0.4);
            color: white;
        }

        .back-link {
            display: inline-block;
            margin-top: 1.5rem;
            color: #a0a5ba;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        .back-link:hover { color: #fff; }
        
        .form-check-input:checked {
            background-color: var(--lm-primary);
            border-color: var(--lm-primary);
        }
    </style>
</head>
<body>

    <div class="bg-shapes"></div>

    <div class="login-card text-center">
        <div class="brand">
            <i class="bi bi-trophy-fill"></i> LeagueManager
        </div>
        
        <h4 class="mb-4 font-weight-bold">Iniciar Sesión</h4>

        @if ($errors->any())
            <div class="alert alert-danger" style="background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.3); color: #ff8a96; border-radius: 12px; font-size: 0.9rem;">
                <ul class="mb-0 text-start">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="text-start">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text border-0" style="background: rgba(255,255,255,0.05); color: #a0a5ba;"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" value="{{ old('email') }}" placeholder="tu@email.com" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text border-0" style="background: rgba(255,255,255,0.05); color: #a0a5ba;"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label text-secondary" for="remember" style="font-size: 0.9rem;">
                        Recordarme
                    </label>
                </div>
                <!-- <a href="#" class="text-decoration-none" style="color: var(--lm-accent); font-size: 0.9rem;">¿Olvidaste tu contraseña?</a> -->
            </div>

            <button type="submit" class="btn-primary-custom">
                Entrar <i class="bi bi-box-arrow-in-right ms-2"></i>
            </button>
        </form>
        <div class="mt-4">
            <a href="/" class="back-link me-3"><i class="bi bi-arrow-left me-1"></i> Inicio</a>
            <a href="{{ route('register') }}" class="back-link" style="color: var(--lm-primary);">Crear cuenta nueva</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
