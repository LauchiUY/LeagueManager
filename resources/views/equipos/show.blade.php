@extends('layouts.app')

@section('styles')
<style>
    .glass-card { background: rgba(26, 27, 35, 0.6); border: 1px solid rgba(255,255,255,0.05); backdrop-filter: blur(20px); border-radius: 20px; }
    .title-font { font-family: 'Outfit', sans-serif; }
    .text-muted-custom { color: #a0a5ba !important; font-family: 'Inter', sans-serif; }
    .table-custom { background: transparent !important; color: white !important; }
    .table-custom th { border-bottom: 1px solid rgba(255,255,255,0.1) !important; color: #a0a5ba; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; }
    .table-custom td { border-color: rgba(255,255,255,0.05) !important; padding: 1rem 0.5rem; vertical-align: middle; }
    .table-custom tbody tr:hover td { background: rgba(255,255,255,0.05); }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('equipos.index') }}" class="btn btn-outline-light rounded-pill px-4" style="border-color: rgba(255,255,255,0.1); color: #a0a5ba;">
            <i class="bi bi-arrow-left me-2"></i> Volver al Directorio
        </a>
    </div>

    <div class="card glass-card shadow-lg mb-5 border-0 position-relative overflow-hidden">
        <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background: linear-gradient(to right, #ff2a5f, #d01c48);"></div>
        <div class="card-body p-5">
            <div class="row align-items-center text-center text-md-start">
                <div class="col-md-auto mb-4 mb-md-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 120px; height: 120px; background: rgba(255,42,95,0.1); border: 2px solid rgba(255,42,95,0.3); margin: 0 auto;">
                        <i class="bi bi-shield-fill" style="font-size: 4rem; color: #ff2a5f;"></i>
                    </div>
                </div>
                <div class="col-md">
                    <h1 class="text-white fw-bold display-4 mb-2 title-font">{{ $equipo->nombre }}</h1>
                    <p class="text-muted-custom fs-5 mb-0">Capitán: <span class="text-white fw-bold">{{ $equipo->capitan->nombre ?? 'Sin asignar' }}</span></p>
                </div>
                <div class="col-md-auto mt-4 mt-md-0 d-flex flex-column gap-2">
                    <div class="p-3 rounded-4 text-center border" style="background: rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.05) !important;">
                        <div class="text-muted-custom small text-uppercase mb-1">Sanciones</div>
                        <div class="display-5 fw-bold {{ $equipo->puntos_sancion > 0 ? 'text-danger' : 'text-success' }}">{{ $equipo->puntos_sancion ?? 0 }}</div>
                    </div>
                    @if(auth()->user()->rol === 'admin')
                        <a href="{{ route('equipos.edit', $equipo->id) }}" class="btn btn-outline-info btn-sm rounded-pill"><i class="bi bi-pencil"></i> Editar</a>
                        <form action="{{ route('equipos.destroy', $equipo->id) }}" method="POST" onsubmit="return confirm('¿Eliminar equipo?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill w-100"><i class="bi bi-trash"></i> Eliminar</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0 alert-dismissible fade show mb-4">{{ session('success') }}<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card glass-card shadow-lg">
                <div class="card-header bg-transparent p-4 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                    <h3 class="text-white m-0 title-font"><i class="bi bi-people-fill me-2" style="color: #ff2a5f;"></i> Plantilla</h3>
                </div>
                <div class="card-body p-0">
                    @if($equipo->plantilla && $equipo->plantilla->count() > 0)
                        <div class="table-responsive px-4 pb-3">
                            <table class="table table-dark table-hover table-borderless align-middle table-custom mb-0 mt-3">
                                <thead><tr><th class="ps-3">Jugador</th><th>Rol</th><th>Dorsal</th></tr></thead>
                                <tbody>
                                    @foreach($equipo->plantilla->where('estado', 'activo') as $jugador)
                                        <tr>
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1) !important;"><i class="bi bi-person-fill text-white"></i></div>
                                                    <div>
                                                        <div class="text-white fw-bold">{{ $jugador->usuario->nombre ?? 'Desconocido' }}</div>
                                                        <div class="text-muted-custom small">{{ $jugador->usuario->email ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($jugador->es_capitan)
                                                    <span class="badge rounded-pill bg-warning text-dark"><i class="bi bi-star-fill me-1"></i> Capitán</span>
                                                @else
                                                    <span class="badge rounded-pill" style="background: rgba(255,255,255,0.1);">Jugador</span>
                                                @endif
                                            </td>
                                            <td><span class="text-white fw-bold">#{{ $jugador->dorsal ?? '-' }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-person-x display-1 mb-3" style="color: rgba(255,255,255,0.1);"></i>
                            <h4 class="text-white title-font">Plantilla Vacía</h4>
                            <p class="text-muted-custom">No hay jugadores inscritos.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card glass-card shadow-lg h-100">
                <div class="card-header bg-transparent p-4 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                    <h3 class="text-white m-0 title-font"><i class="bi bi-calendar-event me-2" style="color: #ff2a5f;"></i> Partidos</h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($partidos as $partido)
                            @php
                                $esLocal = $partido->id_local === $equipo->id;
                                $rival = $esLocal ? $partido->equipoVisitante : $partido->equipoLocal;
                                $gF = $esLocal ? $partido->goles_local : $partido->goles_visitante;
                                $gC = $esLocal ? $partido->goles_visitante : $partido->goles_local;
                            @endphp
                            <div class="list-group-item bg-transparent text-white py-3" style="border-color: rgba(255,255,255,0.05) !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted-custom">{{ $partido->fecha_hora->format('d/m/Y H:i') }} | {{ $partido->competicion->nombre }}</small>
                                    @if($partido->estado === 'jugado')
                                        @if($gF > $gC) <span class="badge bg-success">Victoria</span>
                                        @elseif($gF < $gC) <span class="badge bg-danger">Derrota</span>
                                        @else <span class="badge bg-secondary">Empate</span> @endif
                                    @elseif($partido->estado === 'pendiente')
                                        <span class="badge bg-info text-dark">Próximo</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ ucfirst($partido->estado) }}</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between align-items-center fs-5">
                                    <span>@if($esLocal) <span class="text-muted-custom">(L)</span> @else <span class="text-muted-custom">(V)</span> @endif vs {{ $rival->nombre ?? '---' }}</span>
                                    @if($partido->estado === 'jugado') <span class="fw-bold">{{ $gF }} - {{ $gC }}</span> @else <span class="text-muted-custom">-</span> @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted-custom">No hay partidos registrados.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
