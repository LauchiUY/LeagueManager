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
            --lm-darker: #121225;
            --lm-accent: #e94560;
        }
        body { background-color: var(--lm-darker); color: #e0e0e0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; }
        .navbar { background: linear-gradient(135deg, var(--lm-dark) 0%, #16213e 100%) !important; box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        .navbar-brand { font-weight: 800; font-size: 1.4rem; color: var(--lm-primary) !important; letter-spacing: 1px; }
        .navbar-brand i { margin-right: 6px; }
        .nav-link { color: rgba(255,255,255,0.85) !important; font-weight: 500; transition: color 0.2s; }
        .nav-link:hover { color: var(--lm-primary) !important; }
        .nav-link.active { color: var(--lm-primary) !important; border-bottom: 2px solid var(--lm-primary); }
        .nav-role-badge { font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; vertical-align: middle; margin-left: 4px; }
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
                <ul class="navbar-nav me-auto">
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

                        {{-- Enlaces específicos por rol --}}
                        @if(auth()->user()->rol === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-lock-fill me-1"></i>Panel Admin</a>
                            </li>
                        @endif

                        @if(auth()->user()->rol === 'capitan')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('capitan.equipo') }}"><i class="bi bi-flag-fill me-1"></i>Mi Equipo</a>
                            </li>
                        @endif

                        @if(auth()->user()->rol === 'arbitro')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('partidos.index') }}"><i class="bi bi-clipboard-check-fill me-1"></i>Mis Partidos</a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i>Ingresar</a>
                        </li>
                    @else
                        <!-- Dropdown de Notificaciones -->
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link dropdown-toggle position-relative" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell-fill"></i>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notifDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                                <li><h6 class="dropdown-header">Notificaciones</h6></li>
                                @forelse(auth()->user()->notifications->take(5) as $notificacion)
                                    <li class="dropdown-item py-2 {{ $notificacion->unread() ? 'bg-light' : '' }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-wrap" style="max-width: 250px;">
                                                <strong class="d-block text-dark" style="font-size: 0.9rem;">
                                                    @if(isset($notificacion->data['estado']) && $notificacion->data['estado'] === 'aprobado')
                                                        <i class="bi bi-check-circle-fill text-success"></i>
                                                    @elseif(isset($notificacion->data['estado']) && $notificacion->data['estado'] === 'rechazado')
                                                        <i class="bi bi-x-circle-fill text-danger"></i>
                                                    @else
                                                        <i class="bi bi-info-circle-fill text-info"></i>
                                                    @endif
                                                    {{ $notificacion->data['titulo'] ?? 'Aviso' }}
                                                </strong>
                                                <small class="text-secondary" style="font-size: 0.8rem;">
                                                    {{ $notificacion->data['mensaje'] ?? 'Tienes un nuevo aviso en el sistema.' }}
                                                </small>
                                            </div>
                                            @if($notificacion->unread())
                                                <form action="{{ route('notificaciones.leer', $notificacion->id) }}" method="POST" class="m-0 p-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-link text-primary p-0 m-0" title="Marcar como leída">
                                                        <i class="bi bi-check2-all fs-5"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </li>
                                @empty
                                    <li><span class="dropdown-item text-secondary text-center">No tienes notificaciones.</span></li>
                                @endforelse
                            </ul>
                        </li>
                        
                        <!-- Usuario y Rol -->
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-1"></i>{{ Auth::user()->nombre }}
                                <span class="nav-role-badge 
                                    @if(auth()->user()->rol === 'admin') bg-danger
                                    @elseif(auth()->user()->rol === 'capitan') bg-warning text-dark
                                    @elseif(auth()->user()->rol === 'arbitro') bg-info text-dark
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst(Auth::user()->rol) }}
                                </span>
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