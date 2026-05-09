@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('equipos.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Volver al Directorio
        </a>
        
        <div class="card bg-dark border-secondary shadow-sm">
            <div class="card-body p-4 text-center text-md-start d-md-flex align-items-center gap-4">
                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mb-md-0" style="width: 120px; height: 120px; flex-shrink: 0;">
                    <i class="bi bi-shield-fill text-dark" style="font-size: 4rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <h1 class="text-white fw-bold mb-1">{{ $equipo->nombre }}</h1>
                    <p class="text-secondary fs-5 mb-3">Capitán: {{ $equipo->capitan->nombre ?? 'Desconocido' }}</p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                        <span class="badge bg-primary fs-6"><i class="bi bi-people"></i> {{ $equipo->plantilla->count() }} Jugadores</span>
                        @if($equipo->puntos_sancion > 0)
                            <span class="badge bg-danger fs-6"><i class="bi bi-exclamation-triangle"></i> -{{ $equipo->puntos_sancion }} Puntos (Sanción)</span>
                        @endif
                    </div>
                </div>
                @if(auth()->user()->rol === 'admin')
                    <div class="mt-3 mt-md-0 d-flex flex-column gap-2">
                        <a href="{{ route('equipos.edit', $equipo->id) }}" class="btn btn-outline-info">
                            <i class="bi bi-pencil"></i> Editar Equipo
                        </a>
                        <form action="{{ route('equipos.destroy', $equipo->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas ELIMINAR este equipo? Esta acción es irreversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0 alert-dismissible fade show mb-4">
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Plantilla -->
        <div class="col-lg-5">
            <div class="card bg-dark border-secondary h-100">
                <div class="card-header border-secondary bg-transparent py-3">
                    <h5 class="mb-0 text-white"><i class="bi bi-list-ol text-info me-2"></i> Plantilla Actual</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($equipo->plantilla->where('estado', 'activo') as $jugador)
                            <div class="list-group-item bg-transparent text-white border-secondary d-flex align-items-center py-3">
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold" style="width: 40px; height: 40px;">
                                    {{ $jugador->dorsal }}
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $jugador->usuario->nombre }}</h6>
                                    @if($jugador->usuario->id === $equipo->id_capitan)
                                        <small class="text-warning"><i class="bi bi-star-fill"></i> Capitán</small>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-secondary">
                                El equipo no tiene jugadores activos.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos Partidos -->
        <div class="col-lg-7">
            <div class="card bg-dark border-secondary h-100">
                <div class="card-header border-secondary bg-transparent py-3">
                    <h5 class="mb-0 text-white"><i class="bi bi-calendar-event text-info me-2"></i> Próximos y Últimos Partidos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($partidos as $partido)
                            @php
                                $esLocal = $partido->id_local === $equipo->id;
                                $rival = $esLocal ? $partido->equipoVisitante : $partido->equipoLocal;
                                $golesFavor = $esLocal ? $partido->goles_local : $partido->goles_visitante;
                                $golesContra = $esLocal ? $partido->goles_visitante : $partido->goles_local;
                            @endphp
                            <div class="list-group-item bg-transparent border-secondary text-white py-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-secondary">{{ $partido->fecha_hora->format('d/m/Y H:i') }} | {{ $partido->competicion->nombre }}</small>
                                    @if($partido->estado === 'jugado')
                                        @if($golesFavor > $golesContra)
                                            <span class="badge bg-success">Victoria</span>
                                        @elseif($golesFavor < $golesContra)
                                            <span class="badge bg-danger">Derrota</span>
                                        @else
                                            <span class="badge bg-secondary">Empate</span>
                                        @endif
                                    @elseif($partido->estado === 'pendiente')
                                        <span class="badge bg-info text-dark">Próximo</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ ucfirst($partido->estado) }}</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between align-items-center fs-5">
                                    <span>
                                        @if($esLocal) <span class="text-secondary">(L)</span> @else <span class="text-secondary">(V)</span> @endif
                                        vs {{ $rival->nombre ?? '---' }}
                                    </span>
                                    @if($partido->estado === 'jugado')
                                        <span class="fw-bold">{{ $golesFavor }} - {{ $golesContra }}</span>
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-secondary">
                                No hay historial de partidos para este equipo.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
