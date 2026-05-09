@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Cabecera del perfil --}}
    <div class="card bg-dark border-secondary shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-4">
                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                     style="width:90px;height:90px;flex-shrink:0;">
                    <span class="fw-bold text-white" style="font-size:2.5rem;">
                        {{ strtoupper(substr($user->nombre, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <h2 class="text-white fw-bold mb-1">{{ $user->nombre }}</h2>
                    <p class="text-secondary mb-2">{{ $user->email }}</p>
                    <span class="badge fs-6 px-3 py-2
                        @if($user->rol === 'admin') bg-danger
                        @elseif($user->rol === 'capitan') bg-warning text-dark
                        @elseif($user->rol === 'arbitro') bg-info text-dark
                        @else bg-secondary
                        @endif">
                        {{ ucfirst($user->rol) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas (solo jugadores y capitanes) --}}
    @if(in_array($user->rol, ['jugador', 'capitan']))
    <h5 class="text-secondary mb-3">Mis Estadísticas</h5>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card bg-dark border-primary text-center py-3">
                <div class="display-5 fw-bold text-white">{{ $stats['goles'] }}</div>
                <div class="text-secondary mt-1">⚽ Goles</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-dark border-warning text-center py-3">
                <div class="display-5 fw-bold text-white">{{ $stats['amarillas'] }}</div>
                <div class="text-secondary mt-1">🟨 Amarillas</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-dark border-danger text-center py-3">
                <div class="display-5 fw-bold text-white">{{ $stats['rojas'] }}</div>
                <div class="text-secondary mt-1">🟥 Rojas</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-dark border-success text-center py-3">
                <div class="display-5 fw-bold text-white">{{ $stats['partidos_jugados'] }}</div>
                <div class="text-secondary mt-1">🏟️ Partidos</div>
            </div>
        </div>
    </div>

    {{-- Alerta de sanción activa --}}
    @if($stats['sanciones_activas'] > 0)
    <div class="alert border-danger text-white mb-4" style="background:rgba(220,53,69,0.15);">
        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
        Tienes <strong>{{ $stats['sanciones_activas'] }} sanción(es) activa(s)</strong>.
        No podrás ser convocado mientras estén vigentes.
    </div>
    @endif

    {{-- Últimos eventos --}}
    <h5 class="text-secondary mb-3">Últimos Eventos</h5>
    <div class="card bg-dark border-secondary shadow-sm">
        <div class="card-body p-0">
            @if($ultimosEventos->count() > 0)
            <ul class="list-group list-group-flush">
                @foreach($ultimosEventos as $evento)
                <li class="list-group-item bg-dark border-secondary text-white py-3 d-flex align-items-center gap-3">
                    <span class="fs-4">
                        @if($evento->tipo_evento === 'Gol') ⚽
                        @elseif($evento->tipo_evento === 'Amarilla') 🟨
                        @elseif($evento->tipo_evento === 'Roja') 🟥
                        @endif
                    </span>
                    <div class="flex-grow-1">
                        <div class="fw-bold">{{ $evento->tipo_evento }}</div>
                        <small class="text-secondary">
                            Min. {{ $evento->minuto }}' —
                            {{ $evento->partido->equipoLocal->nombre ?? '---' }}
                            vs
                            {{ $evento->partido->equipoVisitante->nombre ?? '---' }}
                        </small>
                    </div>
                    <small class="text-secondary">{{ $evento->created_at->format('d/m/Y') }}</small>
                </li>
                @endforeach
            </ul>
            @else
            <div class="text-center py-5 text-secondary">
                <i class="bi bi-clipboard-x display-1 mb-3 opacity-25"></i>
                <p>No tienes eventos registrados todavía.</p>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection
