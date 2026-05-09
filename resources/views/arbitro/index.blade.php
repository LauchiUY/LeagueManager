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

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <span class="text-uppercase fw-bold" style="color: #ff2a5f; letter-spacing: 2px; font-size: 0.85rem;">Panel Arbitral</span>
            <h1 class="text-white fw-bold m-0 title-font display-5">
                <i class="bi bi-stopwatch me-2"></i> Mis Partidos Asignados
            </h1>
        </div>
    </div>

    <div class="row g-4">
        @foreach($partidos as $partido)
            <div class="col-md-6">
                <div class="card glass-card h-100 text-center p-4 position-relative overflow-hidden">
                    <!-- Barra superior de color según estado -->
                    <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: {{ $partido->estado === 'finalizado' ? '#198754' : 'var(--lm-primary)' }};"></div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                            Jornada {{ $partido->jornada }}
                        </span>
                        <span class="badge rounded-pill {{ $partido->estado === 'finalizado' ? 'bg-success' : 'bg-warning text-dark' }}">
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
                                @if($partido->goles_local !== null || $partido->estado === 'finalizado')
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

                    @if($partido->estado === 'finalizado')
                        <button class="btn btn-outline-success w-100 py-3 rounded-pill" disabled style="background: rgba(25, 135, 84, 0.1); border-color: rgba(25, 135, 84, 0.3);">
                            <i class="bi bi-check-all me-1"></i> Acta Validada
                        </button>
                    @else
                        <a href="{{ route('arbitro.acta', $partido->id) }}" class="btn btn-gradient w-100 py-3 text-uppercase" style="letter-spacing: 1px;">
                            <i class="bi bi-pencil-square me-2"></i> Gestionar Acta
                        </a>
                    @endif
                </div>
            </div>
        @endforeach

        @if($partidos->isEmpty())
            <div class="col-12 text-center py-5">
                <i class="bi bi-calendar-x display-1 mb-3 text-muted-custom" style="opacity: 0.5;"></i>
                <h4 class="text-white title-font">Sin Partidos Asignados</h4>
                <p class="text-muted-custom">Actualmente no tienes ningún partido asignado a tu nombre para arbitrar.</p>
            </div>
        @endif
    </div>
</div>
@endsection
