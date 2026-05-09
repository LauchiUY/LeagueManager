<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LeagueManager - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --lm-primary: #dc3545;
            --lm-dark: #1a1a2e;
            --lm-accent: #e94560;
        }
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background: linear-gradient(135deg, var(--lm-dark) 0%, #16213e 100%) !important; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        .navbar-brand { font-weight: 800; font-size: 1.4rem; color: var(--lm-primary) !important; letter-spacing: 1px; }
        .navbar-brand i { margin-right: 6px; }
        .nav-link { color: rgba(255,255,255,0.85) !important; font-weight: 500; transition: color 0.2s; }
        .nav-link:hover { color: var(--lm-primary) !important; }
        .nav-link.active { color: var(--lm-primary) !important; border-bottom: 2px solid var(--lm-primary); }
        footer { background: var(--lm-dark); color: rgba(255,255,255,0.6); }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/"><i class="bi bi-trophy-fill"></i>LeagueManager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/"><i class="bi bi-house-fill me-1"></i>Inicio</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('clasificacion.competiciones') }}"><i class="bi bi-bar-chart-fill me-1"></i>Clasificación</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('equipos.index') }}"><i class="bi bi-people-fill me-1"></i>Equipos</a>
                        </li>
                    @endauth
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i>Ingresar</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('perfil.index') }}">
                                <i class="bi bi-person-circle me-1"></i>Mi Perfil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-1"></i>Salir ({{ Auth::user()->nombre }})
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    <footer class="text-center mt-5 py-3">
        &copy; 2026 LeagueManager - Lautaro, Ayman & Marcos
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>