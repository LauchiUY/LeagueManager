<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LeagueManager - Inicio</title>
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
            overflow-x: hidden;
        }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        
        .navbar { 
            background: rgba(15, 16, 21, 0.8) !important; 
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            z-index: 1000;
            top: 0;
        }
        .navbar-brand { font-weight: 800; font-size: 1.5rem; color: #fff !important; letter-spacing: 1px; display: flex; align-items: center; gap: 8px; }
        .navbar-brand i { color: var(--lm-primary); font-size: 1.8rem; }
        .nav-link { color: rgba(255,255,255,0.7) !important; font-weight: 500; padding: 0.5rem 1.2rem !important; transition: all 0.3s ease; border-radius: 8px;}
        .nav-link:hover { color: #fff !important; background: rgba(255, 42, 95, 0.1); }
        .nav-link.active { color: var(--lm-primary) !important; background: rgba(255, 42, 95, 0.1); }
        
        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            padding-top: 80px;
        }
        .hero-bg {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at 20% 30%, rgba(255, 42, 95, 0.15) 0%, transparent 40%),
                        radial-gradient(circle at 80% 70%, rgba(0, 240, 255, 0.1) 0%, transparent 40%);
            z-index: -1;
        }
        .hero-title {
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, #fff, #a0a5ba);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-title span {
            background: linear-gradient(to right, var(--lm-primary), #ff7b9f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-subtitle {
            font-size: 1.25rem;
            color: #a0a5ba;
            margin-bottom: 2.5rem;
            font-weight: 300;
            max-width: 600px;
        }
        
        .btn-custom {
            padding: 12px 32px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--lm-primary), #d01c48);
            color: white;
            box-shadow: 0 10px 25px rgba(255, 42, 95, 0.3);
        }
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(255, 42, 95, 0.4);
            color: white;
        }
        .btn-secondary-custom {
            background: rgba(255,255,255,0.05);
            color: white;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        .btn-secondary-custom:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateY(-3px);
        }

        /* Glass Cards */
        .glass-card {
            background: rgba(26, 27, 35, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 24px;
            padding: 2.5rem;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.4s;
            height: 100%;
        }
        .glass-card:hover {
            transform: translateY(-10px);
            border-color: rgba(255,255,255,0.1);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, rgba(255, 42, 95, 0.2), rgba(255, 42, 95, 0.05));
            color: var(--lm-primary);
            border: 1px solid rgba(255, 42, 95, 0.2);
        }
        
        /* Stats Section */
        .stats-section {
            padding: 6rem 0;
            position: relative;
            background: linear-gradient(to bottom, var(--lm-dark), #121319);
            border-top: 1px solid rgba(255,255,255,0.02);
            border-bottom: 1px solid rgba(255,255,255,0.02);
        }
        .stat-item h3 {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--lm-accent);
            margin-bottom: 0.5rem;
        }
        .stat-item p {
            color: #a0a5ba;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.9rem;
        }
        
        .hero-image-wrapper {
            position: relative;
            perspective: 1000px;
        }
        .hero-image {
            width: 100%;
            border-radius: 24px;
            transform: rotateY(-15deg) rotateX(5deg);
            box-shadow: 0 30px 60px rgba(0,0,0,0.5), -20px 20px 60px rgba(255, 42, 95, 0.2);
            transition: transform 0.5s ease;
            animation: float 6s ease-in-out infinite;
        }
        .hero-image-wrapper:hover .hero-image {
            transform: rotateY(0) rotateX(0);
        }
        
        @keyframes float {
            0% { transform: translateY(0px) rotateY(-15deg) rotateX(5deg); }
            50% { transform: translateY(-20px) rotateY(-10deg) rotateX(8deg); }
            100% { transform: translateY(0px) rotateY(-15deg) rotateX(5deg); }
        }

        footer { background: #0a0b0f; color: rgba(255,255,255,0.6); padding: 3rem 0; border-top: 1px solid rgba(255,255,255,0.05); }
        
        /* Responsive adjustments */
        @media (max-width: 991px) {
            .hero-title { font-size: 3.5rem; }
            .hero-section { padding-top: 120px; text-align: center; }
            .hero-subtitle { margin-left: auto; margin-right: auto; }
            .d-flex.gap-3 { justify-content: center; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><i class="bi bi-trophy-fill"></i>LeagueManager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-2">
                    <li class="nav-item">
                        <a class="nav-link active" href="/"><i class="bi bi-house-fill me-1"></i>Inicio</a>
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
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <a class="nav-link" href="{{ route('login') }}" style="background: rgba(255, 42, 95, 0.2); color: #fff !important; border: 1px solid rgba(255, 42, 95, 0.5);"><i class="bi bi-box-arrow-in-right me-1"></i>Ingresar</a>
                        </li>
                    @else
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-welcome').submit();" style="background: rgba(255, 255, 255, 0.1); color: #fff !important; border: 1px solid rgba(255, 255, 255, 0.2);">
                                <i class="bi bi-box-arrow-right me-1"></i>Salir
                            </a>
                            <form id="logout-form-welcome" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 z-1">
                    <div class="badge rounded-pill bg-dark border border-secondary text-light px-3 py-2 mb-4">
                        <i class="bi bi-stars text-warning me-2"></i> La nueva era de la gestión deportiva
                    </div>
                    <h1 class="hero-title">Domina tu <span>Liga</span> con Precisión</h1>
                    <p class="hero-subtitle">La plataforma definitiva para organizar competiciones, gestionar equipos y analizar estadísticas en tiempo real con inteligencia.</p>
                    
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="{{ route('clasificacion.competiciones') }}" class="btn-custom btn-primary-custom">
                            Ver Clasificaciones <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="{{ route('equipos.index') }}" class="btn-custom btn-secondary-custom">
                            Explorar Equipos
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <div class="hero-image-wrapper">
                        <img src="{{ asset('images/hero_trophy.png') }}" alt="League Manager Trophy" class="hero-image img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container py-5 my-5">
        <div class="text-center mb-5 pb-3">
            <h2 class="display-5 font-weight-bold mb-3">Funciones Diseñadas para Ganar</h2>
            <p class="text-secondary mx-auto" style="max-width: 600px;">Todo lo que necesitas para llevar tu torneo al siguiente nivel, sin complicaciones.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="glass-card">
                    <div class="feature-icon">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <h4 class="mb-3">Calendario Inteligente</h4>
                    <p class="text-secondary mb-0">Algoritmo Round-Robin automatizado para generar jornadas perfectas sin solapamientos en segundos.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card">
                    <div class="feature-icon" style="background: linear-gradient(135deg, rgba(0, 240, 255, 0.2), rgba(0, 240, 255, 0.05)); color: var(--lm-accent); border-color: rgba(0, 240, 255, 0.2);">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h4 class="mb-3">Estadísticas en Vivo</h4>
                    <p class="text-secondary mb-0">Actualización en tiempo real de puntos, goles y rendimiento. Analíticas detalladas para cada equipo.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card">
                    <div class="feature-icon" style="background: linear-gradient(135deg, rgba(162, 0, 255, 0.2), rgba(162, 0, 255, 0.05)); color: #a200ff; border-color: rgba(162, 0, 255, 0.2);">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4 class="mb-3">Sanciones Automáticas</h4>
                    <p class="text-secondary mb-0">Auditoría en segundo plano para detectar alineaciones indebidas y aplicar el reglamento de forma imparcial.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section text-center mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4 stat-item">
                    <h3>+10k</h3>
                    <p>Partidos Gestionados</p>
                </div>
                <div class="col-md-4 stat-item">
                    <h3>500+</h3>
                    <p>Ligas Activas</p>
                </div>
                <div class="col-md-4 stat-item">
                    <h3>99.9%</h3>
                    <p>Uptime de Plataforma</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center">
        <div class="container">
            <div class="mb-4">
                <a href="#" class="text-secondary mx-2 fs-5"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="text-secondary mx-2 fs-5"><i class="bi bi-instagram"></i></a>
                <a href="#" class="text-secondary mx-2 fs-5"><i class="bi bi-github"></i></a>
            </div>
            <p class="mb-0">&copy; 2026 LeagueManager - Lautaro, Ayman & Marcos. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
