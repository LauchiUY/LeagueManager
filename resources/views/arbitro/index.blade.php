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
    .btn-gradient {
        background: linear-gradient(135deg, #ff2a5f, #d01c48);
        border-radius: 50px;
        border: none;
        color: white;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255, 42, 95, 0.3);
        color: white;
    }
    .section-divider {
        border-top: 1px solid rgba(255,255,255,0.08);
        margin: 3rem 0 2rem;
    }
</style>
@endsection

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0 shadow-lg" style="background-color: rgba(25, 135, 84, 0.8) !important; backdrop-filter: blur(10px);">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger bg-danger text-white border-0 shadow-lg" style="background-color: rgba(220, 53, 69, 0.8) !important; backdrop-filter: blur(10px);">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Sección: Partidos Pendientes --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <span class="text-uppercase fw-bold" style="color: #ff2a5f; letter-spacing: 2px; font-size: 0.85rem;">Panel Arbitral</span>
            <h1 class="text-white fw-bold m-0 title-font display-5">
                <i class="bi bi-stopwatch me-2"></i> Mis Partidos Pendientes
            </h1>
        </div>
    </div>

    <div class="row g-4">
        @foreach($partidosPendientes as $partido)
            <div class="col-md-6">
                <div class="card glass-card h-100 text-center p-4 position-relative overflow-hidden">
                    <!-- Barra superior de color según estado -->
                    <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: var(--lm-primary);"></div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                            Jornada {{ $partido->jornada }}
                        </span>
                        <span class="badge rounded-pill 
                            @if($partido->estado === 'en_curso') bg-primary
                            @elseif($partido->estado === 'aplazado') bg-warning text-dark
                            @else bg-secondary
                            @endif">
                            {{ strtoupper($partido->estado) }}
                        </span>
                    </div>
                    
                    <div class="row align-items-center mb-4">
                        <div class="col-5">
                            <h4 class="text-white fw-bold mb-1 title-font">{{ $partido->equipoLocal->nombre ?? 'N/A' }}</h4>
                            <p class="text-muted-custom small mb-0">Local</p>
                        </div>
                        <div class="col-2">
                            <h2 class="text-white title-font fw-bolder mb-0">
                                @if($partido->goles_local !== null)
                                    {{ $partido->goles_local ?? 0 }} - {{ $partido->goles_visitante ?? 0 }}
                                @else
                                    VS
                                @endif
                            </h2>
                        </div>
                        <div class="col-5">
                            <h4 class="text-white fw-bold mb-1 title-font">{{ $partido->equipoVisitante->nombre ?? 'N/A' }}</h4>
                            <p class="text-muted-custom small mb-0">Visitante</p>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center text-muted-custom small mb-4 mt-2 px-3 py-2 rounded-3" style="background: rgba(0,0,0,0.2);">
                        <div><i class="bi bi-calendar-event me-1"></i> {{ $partido->fecha_hora ? $partido->fecha_hora->format('d/m/Y H:i') : 'Fecha por definir' }}</div>
                        <div><i class="bi bi-geo-alt me-1"></i> {{ $partido->campo_pista ?? 'Sin pista' }}</div>
                    </div>

                    <a href="{{ route('arbitro.acta', $partido->id) }}" class="btn btn-gradient w-100 py-3 text-uppercase" style="letter-spacing: 1px;">
                        <i class="bi bi-pencil-square me-2"></i> Gestionar Acta
                    </a>
                </div>
            </div>
        @endforeach

        @if($partidosPendientes->isEmpty())
            <div class="col-12 text-center py-5">
                <i class="bi bi-calendar-check display-1 mb-3 text-muted-custom" style="opacity: 0.5;"></i>
                <h4 class="text-white title-font">Sin Partidos Pendientes</h4>
                <p class="text-muted-custom">¡Estás al día! No tienes partidos pendientes de arbitrar.</p>
            </div>
        @endif
    </div>

    {{-- Sección: Historial de Partidos Arbitrados --}}
    <div class="section-divider"></div>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="text-uppercase fw-bold" style="color: #198754; letter-spacing: 2px; font-size: 0.85rem;">Historial</span>
            <h2 class="text-white fw-bold m-0 title-font">
                <i class="bi bi-clock-history me-2"></i> Partidos Arbitrados
            </h2>
        </div>
        <span class="badge bg-secondary fs-6 px-3 py-2">{{ $partidosHistorial->count() }} partidos</span>
    </div>

    <div class="row g-4">
        @foreach($partidosHistorial as $partido)
            <div class="col-md-6 col-lg-4">
                <div class="card glass-card h-100 text-center p-4 position-relative overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: #198754;"></div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                            Jornada {{ $partido->jornada }}
                        </span>
                        <span class="badge rounded-pill bg-success">
                            <i class="bi bi-check-all me-1"></i> FINALIZADO
                        </span>
                    </div>
                    
                    <div class="row align-items-center mb-3">
                        <div class="col-5">
                            <h5 class="text-white fw-bold mb-0 title-font">{{ $partido->equipoLocal->nombre ?? 'N/A' }}</h5>
                        </div>
                        <div class="col-2">
                            <h3 class="text-white title-font fw-bolder mb-0">
                                {{ $partido->goles_local ?? 0 }} - {{ $partido->goles_visitante ?? 0 }}
                            </h3>
                        </div>
                        <div class="col-5">
                            <h5 class="text-white fw-bold mb-0 title-font">{{ $partido->equipoVisitante->nombre ?? 'N/A' }}</h5>
                        </div>
                    </div>
                    
                    <div class="text-muted-custom small px-3 py-2 rounded-3" style="background: rgba(0,0,0,0.2);">
                        <i class="bi bi-calendar-event me-1"></i> {{ $partido->fecha_hora ? $partido->fecha_hora->format('d/m/Y H:i') : '---' }}
                        <span class="mx-2">|</span>
                        <i class="bi bi-geo-alt me-1"></i> {{ $partido->campo_pista ?? '---' }}
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('arbitro.acta', $partido->id) }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                            <i class="bi bi-eye me-1"></i> Ver Acta
                        </a>
                    </div>
                </div>
            </div>
        @endforeach

        @if($partidosHistorial->isEmpty())
            <div class="col-12 text-center py-4">
                <p class="text-muted-custom mb-0"><i class="bi bi-inbox me-2"></i>Aún no has arbitrado ningún partido.</p>
            </div>
        @endif
    </div>
</div>
@endsection
