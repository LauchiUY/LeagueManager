@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-2 text-center text-md-start">
            <img src="{{ asset('images/' . $equipo->logo_url) }}" alt="{{ $equipo->nombre }}" class="img-fluid rounded-circle border border-2 border-secondary" style="width: 100px; height: 100px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($equipo->nombre) }}&background=1a1b23&color=fff&size=100'">
        </div>
        <div class="col-md-10 mt-3 mt-md-0">
            <h1 class="text-white mb-0">{{ $equipo->nombre }}</h1>
            <p class="text-secondary mb-0">Panel de Gestión del Capitán</p>
            @if($equipo->puntos_sancion > 0)
                <span class="badge bg-danger mt-2">Sanciones Activas: -{{ $equipo->puntos_sancion }} pts</span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0 alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger bg-danger text-white border-0 alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger bg-danger text-white border-0 alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Columna Izquierda: Plantilla -->
        <div class="col-lg-7">
            <div class="card bg-dark border-secondary shadow-sm mb-4">
                <div class="card-header border-secondary text-white bg-transparent fw-bold py-3 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-people-fill text-info me-2"></i> Plantilla del Equipo</span>
                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#addJugadorModal">
                        <i class="bi bi-person-plus"></i> Añadir
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Dorsal</th>
                                    <th>Jugador</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($equipo->plantilla->where('estado', 'activo') as $plantillaJugador)
                                    <tr class="align-middle">
                                        <td class="fw-bold fs-5">#{{ $plantillaJugador->dorsal }}</td>
                                        <td>
                                            {{ $plantillaJugador->usuario->nombre }}
                                            @if($plantillaJugador->usuario->sanciones->count() > 0)
                                                <br><span class="badge bg-danger mt-1">Sancionado</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-success">Activo</span></td>
                                        <td class="text-end">
                                            <form action="{{ route('capitan.jugador.remove', $plantillaJugador->usuario->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas dar de baja a este jugador?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-person-dash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-secondary">
                                            No hay jugadores en la plantilla activa.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Próximos Partidos -->
        <div class="col-lg-5">
            <div class="card bg-dark border-secondary shadow-sm">
                <div class="card-header border-secondary text-white bg-transparent fw-bold py-3">
                    <i class="bi bi-calendar-event text-warning me-2"></i> Próximos Partidos
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush rounded-bottom">
                        @forelse($partidos as $partido)
                            <li class="list-group-item bg-dark border-secondary text-white py-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fw-bold">
                                        {{ $partido->fecha_hora->format('d/m/Y H:i') }}
                                    </div>
                                    <span class="badge bg-secondary">{{ $partido->competicion->nombre }}</span>
                                </div>
                                <div class="mb-3 text-center bg-black bg-opacity-25 py-2 rounded">
                                    <span class="{{ $partido->id_local == $equipo->id ? 'fw-bold text-info' : '' }}">{{ $partido->equipoLocal ? $partido->equipoLocal->nombre : '---' }}</span>
                                    <span class="text-secondary mx-2">vs</span>
                                    <span class="{{ $partido->id_visitante == $equipo->id ? 'fw-bold text-info' : '' }}">{{ $partido->equipoVisitante ? $partido->equipoVisitante->nombre : '---' }}</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('capitan.convocatoria', $partido->id) }}" class="btn btn-sm btn-primary flex-grow-1">
                                        <i class="bi bi-card-checklist"></i> Convocatoria
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#aplazamientoModal{{ $partido->id }}">
                                        Aplazar
                                    </button>
                                </div>
                            </li>

                            <!-- Modal Aplazamiento -->
                            <div class="modal fade" id="aplazamientoModal{{ $partido->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content bg-dark text-white border-secondary">
                                        <form action="{{ route('capitan.aplazar', $partido->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header border-secondary">
                                                <h5 class="modal-title">Solicitar Aplazamiento</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="small text-secondary">Estás solicitando aplazar el partido del {{ $partido->fecha_hora->format('d/m/Y') }}. La administración deberá aprobarlo.</p>
                                                <div class="mb-3">
                                                    <label class="form-label">Motivo del aplazamiento:</label>
                                                    <textarea name="motivo" class="form-control bg-dark text-white border-secondary" rows="3" required minlength="10"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-warning">Enviar Solicitud</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-secondary">
                                <i class="bi bi-calendar-x display-1 mb-3 opacity-25"></i>
                                <p class="fs-5">No hay partidos programados.</p>
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Añadir Jugador -->
<div class="modal fade" id="addJugadorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <form action="{{ route('capitan.jugador.add') }}" method="POST">
                @csrf
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Añadir Jugador a Plantilla</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-secondary">El usuario debe estar registrado en el sistema con el rol "jugador".</p>
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico del jugador:</label>
                        <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required placeholder="ejemplo@correo.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dorsal a asignar:</label>
                        <input type="number" name="dorsal" class="form-control bg-dark text-white border-secondary" required min="1" max="99" placeholder="Ej: 10">
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Añadir Jugador</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
