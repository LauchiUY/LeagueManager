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
    .player-row.has-yellow {
        border-left: 3px solid #ffc107;
    }
    .action-btn {
        width: 35px;
        height: 35px;
        padding: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }
    .btn-gol {
        background: rgba(25, 135, 84, 0.2);
        color: #20c997;
        border: 1px solid rgba(25, 135, 84, 0.5);
    }
    .btn-gol:hover { background: #198754; color: white; }
    .btn-autogol {
        background: rgba(108, 117, 125, 0.2);
        color: #adb5bd;
        border: 1px solid rgba(108, 117, 125, 0.5);
    }
    .btn-autogol:hover { background: #6c757d; color: white; }
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
    .minuto-input {
        width: 55px;
        background: rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.1);
        color: white;
        border-radius: 8px;
        text-align: center;
        font-size: 0.85rem;
        padding: 4px;
    }
    .minuto-input::placeholder { color: rgba(255,255,255,0.3); }
    .timeline-event {
        border-left: 3px solid rgba(255,255,255,0.1);
        padding-left: 20px;
        position: relative;
        margin-bottom: 15px;
    }
    .timeline-event::before {
        content: '';
        position: absolute;
        left: -7px;
        top: 8px;
        width: 11px;
        height: 11px;
        border-radius: 50%;
        background: var(--lm-primary);
        border: 2px solid rgba(26, 27, 35, 0.6);
    }
    .timeline-event.event-gol::before { background: #198754; }
    .timeline-event.event-autogol::before { background: #6c757d; }
    .timeline-event.event-amarilla::before { background: #ffc107; }
    .timeline-event.event-roja::before { background: #dc3545; }
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
                    <span class="mx-2">|</span>
                    <span class="badge {{ $partido->estado === 'finalizado' || $partido->estado === 'jugado' ? 'bg-success' : 'bg-primary' }}">
                        {{ strtoupper($partido->estado) }}
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <h2 class="text-white fw-bold title-font">{{ $partido->equipoVisitante->nombre ?? 'Visitante' }}</h2>
                <span class="badge bg-secondary">VISITANTE</span>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Equipo Local -->
        <div class="col-lg-6">
            <div class="card glass-card h-100 border-0 p-4">
                <h4 class="text-white title-font mb-4 text-center border-bottom pb-3" style="border-color: rgba(255,255,255,0.1) !important;">
                    <i class="bi bi-shield-fill me-2" style="color: #ff2a5f;"></i> Convocados Local
                    <span class="badge bg-secondary ms-2">{{ $jugadoresLocal->count() }}</span>
                </h4>
                
                @if($jugadoresLocal->isNotEmpty())
                    @foreach($jugadoresLocal as $jugador)
                        @php
                            $amarillas = $amarillasPorJugador[$jugador->usuario->id] ?? 0;
                        @endphp
                        <div class="player-row d-flex justify-content-between align-items-center {{ $amarillas > 0 ? 'has-yellow' : '' }}">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-dark me-3" style="font-size: 1rem;">#{{ $jugador->dorsal ?? '?' }}</span>
                                <div>
                                    <span class="text-white fw-bold d-block">{{ $jugador->usuario->nombre ?? 'Desconocido' }}</span>
                                    @if($amarillas > 0)
                                        <small class="text-warning"><i class="bi bi-square-fill me-1" style="font-size: 0.5rem;"></i>{{ $amarillas }} amarilla(s)</small>
                                    @endif
                                </div>
                            </div>
                            
                            @if(!in_array($partido->estado, ['finalizado', 'jugado']))
                            <div class="d-flex gap-1 align-items-center">
                                <input type="number" class="minuto-input" id="minuto_local_{{ $jugador->usuario->id }}" placeholder="Min" min="1" max="120">
                                
                                <form action="{{ route('arbitro.registrar_evento', $partido->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id_jugador" value="{{ $jugador->usuario->id }}">
                                    <input type="hidden" name="id_equipo" value="{{ $partido->equipoLocal->id }}">
                                    <input type="hidden" name="tipo_evento" value="Gol">
                                    <input type="hidden" name="minuto" class="minuto-hidden-local-{{ $jugador->usuario->id }}">
                                    <button type="submit" class="btn action-btn btn-gol" title="Gol" onclick="this.form.querySelector('.minuto-hidden-local-{{ $jugador->usuario->id }}').value = document.getElementById('minuto_local_{{ $jugador->usuario->id }}').value"><i class="bi bi-record-circle"></i></button>
                                </form>
                                <form action="{{ route('arbitro.registrar_evento', $partido->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id_jugador" value="{{ $jugador->usuario->id }}">
                                    <input type="hidden" name="id_equipo" value="{{ $partido->equipoLocal->id }}">
                                    <input type="hidden" name="tipo_evento" value="Autogol">
                                    <input type="hidden" name="minuto" class="minuto-hidden-local-{{ $jugador->usuario->id }}">
                                    <button type="submit" class="btn action-btn btn-autogol" title="Autogol (en propia)" onclick="this.form.querySelector('.minuto-hidden-local-{{ $jugador->usuario->id }}').value = document.getElementById('minuto_local_{{ $jugador->usuario->id }}').value"><i class="bi bi-arrow-down-circle"></i></button>
                                </form>
                                <form action="{{ route('arbitro.registrar_evento', $partido->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id_jugador" value="{{ $jugador->usuario->id }}">
                                    <input type="hidden" name="id_equipo" value="{{ $partido->equipoLocal->id }}">
                                    <input type="hidden" name="tipo_evento" value="Amarilla">
                                    <input type="hidden" name="minuto" class="minuto-hidden-local-{{ $jugador->usuario->id }}">
                                    <button type="submit" class="btn action-btn btn-amarilla" title="Tarjeta Amarilla" onclick="this.form.querySelector('.minuto-hidden-local-{{ $jugador->usuario->id }}').value = document.getElementById('minuto_local_{{ $jugador->usuario->id }}').value"><i class="bi bi-square-fill"></i></button>
                                </form>
                                <form action="{{ route('arbitro.registrar_evento', $partido->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id_jugador" value="{{ $jugador->usuario->id }}">
                                    <input type="hidden" name="id_equipo" value="{{ $partido->equipoLocal->id }}">
                                    <input type="hidden" name="tipo_evento" value="Roja">
                                    <input type="hidden" name="minuto" class="minuto-hidden-local-{{ $jugador->usuario->id }}">
                                    <button type="submit" class="btn action-btn btn-roja" title="Tarjeta Roja" onclick="this.form.querySelector('.minuto-hidden-local-{{ $jugador->usuario->id }}').value = document.getElementById('minuto_local_{{ $jugador->usuario->id }}').value"><i class="bi bi-square-fill"></i></button>
                                </form>
                            </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-muted-custom text-center">
                        <i class="bi bi-exclamation-circle me-1"></i> No hay jugadores convocados para este equipo.
                    </p>
                @endif
            </div>
        </div>

        <!-- Equipo Visitante -->
        <div class="col-lg-6">
            <div class="card glass-card h-100 border-0 p-4">
                <h4 class="text-white title-font mb-4 text-center border-bottom pb-3" style="border-color: rgba(255,255,255,0.1) !important;">
                    <i class="bi bi-shield-fill me-2 text-primary"></i> Convocados Visitante
                    <span class="badge bg-secondary ms-2">{{ $jugadoresVisitante->count() }}</span>
                </h4>
                
                @if($jugadoresVisitante->isNotEmpty())
                    @foreach($jugadoresVisitante as $jugador)
                        @php
                            $amarillas = $amarillasPorJugador[$jugador->usuario->id] ?? 0;
                        @endphp
                        <div class="player-row d-flex justify-content-between align-items-center {{ $amarillas > 0 ? 'has-yellow' : '' }}">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-dark me-3" style="font-size: 1rem;">#{{ $jugador->dorsal ?? '?' }}</span>
                                <div>
                                    <span class="text-white fw-bold d-block">{{ $jugador->usuario->nombre ?? 'Desconocido' }}</span>
                                    @if($amarillas > 0)
                                        <small class="text-warning"><i class="bi bi-square-fill me-1" style="font-size: 0.5rem;"></i>{{ $amarillas }} amarilla(s)</small>
                                    @endif
                                </div>
                            </div>
                            
                            @if(!in_array($partido->estado, ['finalizado', 'jugado']))
                            <div class="d-flex gap-1 align-items-center">
                                <input type="number" class="minuto-input" id="minuto_visit_{{ $jugador->usuario->id }}" placeholder="Min" min="1" max="120">
                                
                                <form action="{{ route('arbitro.registrar_evento', $partido->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id_jugador" value="{{ $jugador->usuario->id }}">
                                    <input type="hidden" name="id_equipo" value="{{ $partido->equipoVisitante->id }}">
                                    <input type="hidden" name="tipo_evento" value="Gol">
                                    <input type="hidden" name="minuto" class="minuto-hidden-visit-{{ $jugador->usuario->id }}">
                                    <button type="submit" class="btn action-btn btn-gol" title="Gol" onclick="this.form.querySelector('.minuto-hidden-visit-{{ $jugador->usuario->id }}').value = document.getElementById('minuto_visit_{{ $jugador->usuario->id }}').value"><i class="bi bi-record-circle"></i></button>
                                </form>
                                <form action="{{ route('arbitro.registrar_evento', $partido->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id_jugador" value="{{ $jugador->usuario->id }}">
                                    <input type="hidden" name="id_equipo" value="{{ $partido->equipoVisitante->id }}">
                                    <input type="hidden" name="tipo_evento" value="Autogol">
                                    <input type="hidden" name="minuto" class="minuto-hidden-visit-{{ $jugador->usuario->id }}">
                                    <button type="submit" class="btn action-btn btn-autogol" title="Autogol (en propia)" onclick="this.form.querySelector('.minuto-hidden-visit-{{ $jugador->usuario->id }}').value = document.getElementById('minuto_visit_{{ $jugador->usuario->id }}').value"><i class="bi bi-arrow-down-circle"></i></button>
                                </form>
                                <form action="{{ route('arbitro.registrar_evento', $partido->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id_jugador" value="{{ $jugador->usuario->id }}">
                                    <input type="hidden" name="id_equipo" value="{{ $partido->equipoVisitante->id }}">
                                    <input type="hidden" name="tipo_evento" value="Amarilla">
                                    <input type="hidden" name="minuto" class="minuto-hidden-visit-{{ $jugador->usuario->id }}">
                                    <button type="submit" class="btn action-btn btn-amarilla" title="Tarjeta Amarilla" onclick="this.form.querySelector('.minuto-hidden-visit-{{ $jugador->usuario->id }}').value = document.getElementById('minuto_visit_{{ $jugador->usuario->id }}').value"><i class="bi bi-square-fill"></i></button>
                                </form>
                                <form action="{{ route('arbitro.registrar_evento', $partido->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id_jugador" value="{{ $jugador->usuario->id }}">
                                    <input type="hidden" name="id_equipo" value="{{ $partido->equipoVisitante->id }}">
                                    <input type="hidden" name="tipo_evento" value="Roja">
                                    <input type="hidden" name="minuto" class="minuto-hidden-visit-{{ $jugador->usuario->id }}">
                                    <button type="submit" class="btn action-btn btn-roja" title="Tarjeta Roja" onclick="this.form.querySelector('.minuto-hidden-visit-{{ $jugador->usuario->id }}').value = document.getElementById('minuto_visit_{{ $jugador->usuario->id }}').value"><i class="bi bi-square-fill"></i></button>
                                </form>
                            </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-muted-custom text-center">
                        <i class="bi bi-exclamation-circle me-1"></i> No hay jugadores convocados para este equipo.
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Timeline de Eventos -->
    @if($partido->eventoPartido->count() > 0)
    <div class="card glass-card border-0 p-4 mb-5">
        <h4 class="text-white title-font mb-4 text-center border-bottom pb-3" style="border-color: rgba(255,255,255,0.1) !important;">
            <i class="bi bi-clock-history me-2 text-warning"></i> Timeline de Eventos
        </h4>
        
        @foreach($partido->eventoPartido->sortBy('minuto') as $evento)
            @php
                $esLocal = $jugadoresLocal->pluck('id_usuario')->contains($evento->id_jugador);
                $eventClass = match($evento->tipo_evento) {
                    'Gol' => 'event-gol',
                    'Autogol' => 'event-autogol',
                    'Amarilla' => 'event-amarilla',
                    'Roja' => 'event-roja',
                    default => ''
                };
                $eventIcon = match($evento->tipo_evento) {
                    'Gol' => '⚽',
                    'Autogol' => '⚽🔄',
                    'Amarilla' => '🟨',
                    'Roja' => '🟥',
                    default => '📋'
                };
            @endphp
            <div class="timeline-event {{ $eventClass }} d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted-custom fw-bold" style="width: 40px;">{{ $evento->minuto }}'</span>
                    <span class="fs-5">{{ $eventIcon }}</span>
                    <div>
                        <span class="text-white fw-bold">{{ $evento->jugador->nombre ?? 'Desconocido' }}</span>
                        <span class="badge {{ $esLocal ? 'bg-primary' : 'bg-info text-dark' }} opacity-75 ms-2">
                            {{ $esLocal ? 'Local' : 'Visitante' }}
                        </span>
                        @if($evento->observaciones)
                            <br><small class="text-muted-custom">{{ $evento->observaciones }}</small>
                        @endif
                    </div>
                </div>
                @if(!in_array($partido->estado, ['finalizado', 'jugado']))
                    <form action="{{ route('arbitro.eliminar_evento', [$partido->id, $evento->id]) }}" method="POST" onsubmit="return confirm('¿Eliminar este evento? El marcador se ajustará automáticamente.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar evento">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    <!-- Finalizar Acta -->
    @if(!in_array($partido->estado, ['finalizado', 'jugado']))
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
