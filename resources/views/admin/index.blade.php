@extends('layouts.app')

@section('styles')
<style>
    .glass-card {
        background: rgba(26, 27, 35, 0.6);
        border: 1px solid rgba(255,255,255,0.05);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.4);
        border-color: rgba(255,255,255,0.1);
    }
    .title-font {
        font-family: 'Outfit', sans-serif;
    }
    .text-muted-custom {
        color: #a0a5ba !important;
        font-family: 'Inter', sans-serif;
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }
</style>
@endsection

@section('content')
<div class="container py-4">

    <!-- Cabecera de Administración -->
    <div class="card glass-card shadow-lg mb-5 border-0 position-relative overflow-hidden">
        <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background: linear-gradient(to right, #ff2a5f, #d01c48);"></div>
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center text-center text-md-start">
                <div class="col-md-auto mb-4 mb-md-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow" 
                         style="width: 100px; height: 100px; background: rgba(255,42,95,0.1); border: 2px solid rgba(255,42,95,0.3); margin: 0 auto;">
                        <i class="bi bi-shield-lock-fill" style="font-size: 3.5rem; color: #ff2a5f;"></i>
                    </div>
                </div>
                <div class="col-md">
                    <span class="text-uppercase fw-bold" style="color: #ff2a5f; letter-spacing: 2px; font-size: 0.85rem;">Centro de Control</span>
                    <h1 class="text-white fw-bold display-5 mb-1 title-font">Panel de Administración</h1>
                    <p class="text-muted-custom fs-5 mb-0">Bienvenido, {{ Auth::user()->nombre }}. Tienes el control total sobre la liga.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row g-4 mb-5">
        <div class="col-6 col-lg-3">
            <div class="card glass-card h-100 p-4 border-0">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon" style="background: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <h2 class="text-white fw-bold title-font mb-0">{{ $stats['usuarios'] }}</h2>
                <p class="text-muted-custom mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Usuarios Registrados</p>
            </div>
        </div>
        
        <div class="col-6 col-lg-3">
            <div class="card glass-card h-100 p-4 border-0">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon" style="background: rgba(255, 42, 95, 0.1); color: #ff2a5f;">
                        <i class="bi bi-shield-shaded"></i>
                    </div>
                </div>
                <h2 class="text-white fw-bold title-font mb-0">{{ $stats['equipos'] }}</h2>
                <p class="text-muted-custom mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Equipos Inscritos</p>
            </div>
        </div>
        
        <div class="col-6 col-lg-3">
            <div class="card glass-card h-100 p-4 border-0">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon" style="background: rgba(25, 135, 84, 0.1); color: #198754;">
                        <i class="bi bi-stopwatch-fill"></i>
                    </div>
                </div>
                <h2 class="text-white fw-bold title-font mb-0">{{ $stats['partidos_pendientes'] }}</h2>
                <p class="text-muted-custom mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Partidos Pendientes</p>
            </div>
        </div>
        
        <div class="col-6 col-lg-3">
            <div class="card glass-card h-100 p-4 border-0">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                </div>
                <h2 class="text-white fw-bold title-font mb-0">{{ $stats['competiciones'] }}</h2>
                <p class="text-muted-custom mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Competiciones</p>
            </div>
        </div>
    </div>

    <!-- Accesos Directos -->
    <h4 class="text-white title-font mb-4"><i class="bi bi-lightning-charge-fill me-2" style="color: #ff2a5f;"></i> Acciones Rápidas</h4>
    <div class="row g-4">
        <div class="col-md-4">
            <a href="{{ route('equipos.create') }}" class="text-decoration-none">
                <div class="card glass-card h-100 p-4 border-0 text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; background: rgba(255,255,255,0.05); color: white;">
                        <i class="bi bi-shield-plus fs-2"></i>
                    </div>
                    <h5 class="text-white title-font">Registrar Nuevo Equipo</h5>
                    <p class="text-muted-custom small mb-0">Crea un equipo en la liga y asígnale un capitán.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="/admin/test-sanciones" class="text-decoration-none">
                <div class="card glass-card h-100 p-4 border-0 text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; background: rgba(255,42,95,0.1); color: #ff2a5f;">
                        <i class="bi bi-exclamation-triangle fs-2"></i>
                    </div>
                    <h5 class="text-white title-font">Forzar Sanciones</h5>
                    <p class="text-muted-custom small mb-0">Ejecuta el robot de sanciones manualmente para toda la liga.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="/ver-resultados" target="_blank" class="text-decoration-none">
                <div class="card glass-card h-100 p-4 border-0 text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; background: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                        <i class="bi bi-database fs-2"></i>
                    </div>
                    <h5 class="text-white title-font">Supervisar Base de Datos</h5>
                    <p class="text-muted-custom small mb-0">Abre el panel de desarrollo para ver actas y sanciones en crudo.</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
