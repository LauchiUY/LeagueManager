@extends('layouts.app')

@section('styles')
<style>
    .glass-card {
        background: rgba(26, 27, 35, 0.6);
        border: 1px solid rgba(255,255,255,0.05);
        backdrop-filter: blur(20px);
        border-radius: 20px;
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
    .player-row {
        background: rgba(255,255,255,0.02);
        border-radius: 12px;
        padding: 10px 15px;
        margin-bottom: 8px;
        border: 1px solid rgba(255,255,255,0.02);
        transition: all 0.2s ease;
    }
    .player-row:hover {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.1);
    }
    .action-btn {
        width: 35px;
        height: 35px;
        padding: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-gol {
        background: rgba(25, 135, 84, 0.2);
        color: #20c997;
        border: 1px solid rgba(25, 135, 84, 0.5);
    }
    .btn-gol:hover { background: #198754; color: white; }
    .btn-amarilla {
        background: rgba(255, 193, 7, 0.2);
        color: #ffc107;
        border: 1px solid rgba(255, 193, 7, 0.5);
    }
    .btn-amarilla:hover { background: #ffc107; color: black; }
    .btn-roja {
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.5);
    }
    .btn-roja:hover { background: #dc3545; color: white; }
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

    <!-- Botón Volver -->
    <div class="mb-4">
        <a href="{{ route('arbitro.partidos') }}" class="btn btn-outline-light rounded-pill px-4" style="border-color: rgba(255,255,255,0.1); color: #a0a5ba;">
            <i class="bi bi-arrow-left me-2"></i> Volver a mis Partidos
        </a>
    </div>

    <!-- Marcador Central -->
    <div class="card glass-card shadow-lg mb-5 border-0 text-center py-5">
        <div class="row align-items-center justify-content-center">
            <div class="col-md-4">
                <h2 class="text-white fw-bold title-font">{{ $partido->equipoLocal->nombre ?? 'Local' }}</h2>
                <span class="badge bg-secondary">LOCAL</span>
            </div>
            <div class="col-md-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-4 px-5 py-3 shadow" style="background: rgba(0,0,0,0.4); border: 2px solid rgba(255,255,255,0.05);">
                    <h1 class="display-1 fw-bolder text-white m-0 title-font" style="letter-spacing: 5px;">
                        {{ $partido->goles_local ?? 0 }} - {{ $partido->goles_visitante ?? 0 }}
                    </h1>
                </div>
                <div class="mt-3 text-muted-custom small">
                    <i class="bi bi-geo-alt-fill me-1"></i> {{ $partido->campo_pista }}
                </div>
            </div>
            <div class="col-md-4">
                <h2 class="text-white fw-bold title-font">{{ $partido->equipoVisitante->nombre ?? 'Visitante' }}</h2>
                <span class="badge bg-secondary">VISITANTE</span>
            </div>
        </div>
    </div>

    <!-- Dos Columnas de Jugadores -->
    <div class="row g-4 mb-5">
        <!-- Equipo Local -->
        <div class="col-lg-6">
            <div class="card glass-card h-100 border-0 p-4">
                <h4 class="text-white title-font mb-4 text-center border-bottom pb-3" style="border-color: rgba(255,255,255,0.1) !important;">
                    <i class="bi bi-shield-fill me-2" style="color: #ff2a5f;"></i> Plantilla Local
                </h4>
                
                @if($partido->equipoLocal && $partido->equipoLocal->plantilla)
                    @foreach($partido->equipoLocal->plantilla as $jugador)
                        <div class="player-row d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-dark me-3" style="font-size: 1rem;">#{{ $jugador->dorsal ?? '?' }}</span>
                                <div>
                                    <span class="text-white fw-bold d-block">{{ $jugador->usuario->nombre ?? 'Desconocido' }}</span>
                                </div>
                            </div>
                            
                            @if($partido->estado !== 'finalizado')
                            <div class="d-flex gap-2">
                                <form action="{{ route('arbitro.registrar_evento', $partido->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_jugador" value="{{ $jugador->usuario->id }}">
                                    <input type="hidden" name="id_equipo" value="{{ $partido->equipoLocal->id }}">
                                    <input type="hidden" name="tipo_evento" value="gol">
                                    <button type="submit" class="btn action-btn btn-gol" title="Gol"><i class="bi bi-record-circle"></i></button>
                                </form>
                                <form action="{{ route('arbitro.registrar_evento', $partido->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_jugador" value="{{ $jugador->usuario->id }}">
                                    <input type="hidden" name="id_equipo" value="{{ $partido->equipoLocal->id }}">
                                    <input type="hidden" name="tipo_evento" value="tarjeta_amarilla">
                                    <button type="submit" class="btn action-btn btn-amarilla" title="Tarjeta Amarilla"><i class="bi bi-square-fill"></i></button>
                                </form>
                                <form action="{{ route('arbitro.registrar_evento', $partido->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_jugador" value="{{ $jugador->usuario->id }}">
                                    <input type="hidden" name="id_equipo" value="{{ $partido->equipoLocal->id }}">
                                    <input type="hidden" name="tipo_evento" value="tarjeta_roja">
                                    <button type="submit" class="btn action-btn btn-roja" title="Tarjeta Roja"><i class="bi bi-square-fill"></i></button>
                                </form>
                            </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-muted-custom text-center">No hay jugadores registrados.</p>
                @endif
            </div>
        </div>

        <!-- Equipo Visitante -->
        <div class="col-lg-6">
            <div class="card glass-card h-100 border-0 p-4">
                <h4 class="text-white title-font mb-4 text-center border-bottom pb-3" style="border-color: rgba(255,255,255,0.1) !important;">
                    <i class="bi bi-shield-fill me-2 text-primary"></i> Plantilla Visitante
                </h4>
                
                @if($partido->equipoVisitante && $partido->equipoVisitante->plantilla)
                    @foreach($partido->equipoVisitante->plantilla as $jugador)
                        <div class="player-row d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-dark me-3" style="font-size: 1rem;">#{{ $jugador->dorsal ?? '?' }}</span>
                                <div>
                                    <span class="text-white fw-bold d-block">{{ $jugador->usuario->nombre ?? 'Desconocido' }}</span>
                                </div>
                            </div>
                            
                            @if($partido->estado !== 'finalizado')
                            <div class="d-flex gap-2">
                                <form action="{{ route('arbitro.registrar_evento', $partido->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_jugador" value="{{ $jugador->usuario->id }}">
                                    <input type="hidden" name="id_equipo" value="{{ $partido->equipoVisitante->id }}">
                                    <input type="hidden" name="tipo_evento" value="gol">
                                    <button type="submit" class="btn action-btn btn-gol" title="Gol"><i class="bi bi-record-circle"></i></button>
                                </form>
                                <form action="{{ route('arbitro.registrar_evento', $partido->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_jugador" value="{{ $jugador->usuario->id }}">
                                    <input type="hidden" name="id_equipo" value="{{ $partido->equipoVisitante->id }}">
                                    <input type="hidden" name="tipo_evento" value="tarjeta_amarilla">
                                    <button type="submit" class="btn action-btn btn-amarilla" title="Tarjeta Amarilla"><i class="bi bi-square-fill"></i></button>
                                </form>
                                <form action="{{ route('arbitro.registrar_evento', $partido->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_jugador" value="{{ $jugador->usuario->id }}">
                                    <input type="hidden" name="id_equipo" value="{{ $partido->equipoVisitante->id }}">
                                    <input type="hidden" name="tipo_evento" value="tarjeta_roja">
                                    <button type="submit" class="btn action-btn btn-roja" title="Tarjeta Roja"><i class="bi bi-square-fill"></i></button>
                                </form>
                            </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-muted-custom text-center">No hay jugadores registrados.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Finalizar Acta -->
    @if($partido->estado !== 'finalizado')
    <div class="text-center mb-5">
        <form action="{{ route('arbitro.finalizar_partido', $partido->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres cerrar el acta? No podrás modificarla después y las sanciones se aplicarán automáticamente.');">
            @csrf
            <button type="submit" class="btn btn-danger btn-lg px-5 py-3 rounded-pill fw-bold text-uppercase shadow-lg" style="letter-spacing: 2px;">
                <i class="bi bi-lock-fill me-2"></i> Validar y Cerrar Acta
            </button>
        </form>
        <p class="text-muted-custom mt-3">Al cerrar el acta se sumarán los puntos a la clasificación general y se ejecutarán las sanciones por tarjetas rojas o alineación indebida.</p>
    </div>
    @else
    <div class="text-center mb-5">
        <div class="alert alert-secondary bg-dark text-white border-0 py-4" style="background: rgba(255,255,255,0.05) !important;">
            <i class="bi bi-lock-fill display-4 d-block mb-3" style="color: #a0a5ba;"></i>
            <h4 class="title-font">Acta Cerrada Oficialmente</h4>
            <p class="text-muted-custom mb-0">Este partido ya ha finalizado y sus puntos y sanciones han sido computados. No se puede modificar.</p>
        </div>
    </div>
    @endif

</div>
@endsection
