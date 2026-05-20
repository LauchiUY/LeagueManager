@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver al Dashboard
            </a>
            <h1 class="text-white mb-0">Gestión de Competiciones</h1>
            <p class="text-secondary">Crea ligas, asigna equipos y genera el calendario</p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearCompeticionModal">
                <i class="bi bi-plus-lg"></i> Nueva Competición
            </button>
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
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($competiciones as $competicion)
            <div class="col-12">
                <div class="card bg-dark border-secondary shadow-sm">
                    <div class="card-header border-secondary text-white bg-transparent py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $competicion->nombre }}</h4>
                            <small class="text-secondary">{{ $competicion->deporte }} | Pts Victoria: {{ $competicion->puntos_victoria }} | Pts Empate: {{ $competicion->puntos_empate }}</small>
                            <br>
                            <span class="badge bg-{{ $competicion->estado === 'en_curso' ? 'primary' : ($competicion->estado === 'finalizada' ? 'success' : 'secondary') }} mt-1">
                                {{ ucfirst(str_replace('_', ' ', $competicion->estado)) }}
                            </span>
                        </div>
                        <div class="d-flex gap-2 flex-wrap justify-content-end">
                            <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#asignarEquiposModal{{ $competicion->id }}">
                                <i class="bi bi-people"></i> Equipos ({{ $competicion->equipos->count() }})
                            </button>
                            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarCompeticionModal{{ $competicion->id }}">
                                <i class="bi bi-pencil"></i> Editar
                            </button>

                            @if($competicion->partidos_count > 0)
                                <button class="btn btn-success btn-sm" disabled>
                                    <i class="bi bi-calendar-check"></i> Calendario Generado
                                </button>
                            @else
                                <form action="{{ route('admin.competiciones.calendario', $competicion->id) }}" method="POST" onsubmit="return confirm('¿Generar calendario ida y vuelta?');">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm" {{ $competicion->equipos->count() < 2 ? 'disabled' : '' }}>
                                        <i class="bi bi-magic"></i> Generar Calendario
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('admin.competiciones.eliminar', $competicion->id) }}" method="POST" onsubmit="return confirm('¿ELIMINAR esta competición y TODOS sus partidos? Esta acción es irreversible.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="text-secondary">Equipos asignados:</h6>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @forelse($competicion->equipos as $equipoAsignado)
                                <span class="badge bg-secondary fs-6">{{ $equipoAsignado->nombre }}</span>
                            @empty
                                <span class="text-secondary fst-italic">Ningún equipo asignado.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Editar Competición -->
            <div class="modal fade" id="editarCompeticionModal{{ $competicion->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content bg-dark text-white border-secondary">
                        <form action="{{ route('admin.competiciones.actualizar', $competicion->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header border-secondary">
                                <h5 class="modal-title">Editar - {{ $competicion->nombre }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombre" class="form-control bg-dark text-white border-secondary" value="{{ $competicion->nombre }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Deporte</label>
                                    <input type="text" name="deporte" class="form-control bg-dark text-white border-secondary" value="{{ $competicion->deporte }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-select bg-dark text-white border-secondary" required>
                                        <option value="pendiente" {{ $competicion->estado === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="en_curso" {{ $competicion->estado === 'en_curso' ? 'selected' : '' }}>En Curso</option>
                                        <option value="finalizada" {{ $competicion->estado === 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                                        <option value="suspendida" {{ $competicion->estado === 'suspendida' ? 'selected' : '' }}>Suspendida</option>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Pts Victoria</label>
                                        <input type="number" name="puntos_victoria" class="form-control bg-dark text-white border-secondary" value="{{ $competicion->puntos_victoria }}" min="0" required>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Pts Empate</label>
                                        <input type="number" name="puntos_empate" class="form-control bg-dark text-white border-secondary" value="{{ $competicion->puntos_empate }}" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-secondary">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Asignar Equipos -->
            <div class="modal fade" id="asignarEquiposModal{{ $competicion->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content bg-dark text-white border-secondary">
                        <form action="{{ route('admin.competiciones.equipos', $competicion->id) }}" method="POST">
                            @csrf
                            <div class="modal-header border-secondary">
                                <h5 class="modal-title">Equipos Participantes - {{ $competicion->nombre }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-secondary">Selecciona los equipos que jugarán este torneo.</p>
                                <div class="row g-3 mt-2">
                                    @foreach($equipos as $equipo)
                                        <div class="col-md-4">
                                            <div class="form-check form-switch fs-5">
                                                <input class="form-check-input" type="checkbox" name="equipos[]" value="{{ $equipo->id }}" id="equipo_{{ $competicion->id }}_{{ $equipo->id }}"
                                                    {{ $competicion->equipos->contains($equipo->id) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="equipo_{{ $competicion->id }}_{{ $equipo->id }}">
                                                    {{ $equipo->nombre }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer border-secondary">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar Asignaciones</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-trophy display-1 text-secondary"></i>
                <h3 class="text-white mt-3">No hay competiciones</h3>
                <p class="text-secondary">Crea tu primera liga para empezar a organizar partidos.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Crear Competición -->
<div class="modal fade" id="crearCompeticionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <form action="{{ route('admin.competiciones.crear') }}" method="POST">
                @csrf
                <div class="modal-header border-primary">
                    <h5 class="modal-title"><i class="bi bi-trophy text-primary"></i> Nueva Competición</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Competición</label>
                        <input type="text" name="nombre" class="form-control bg-dark text-white border-secondary" required placeholder="Ej: Liga Verano 2026">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deporte</label>
                        <input type="text" name="deporte" class="form-control bg-dark text-white border-secondary" required placeholder="Ej: Fútbol Sala" value="Fútbol Sala">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Puntos por Victoria</label>
                            <input type="number" name="puntos_victoria" class="form-control bg-dark text-white border-secondary" value="3" min="0" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Puntos por Empate</label>
                            <input type="number" name="puntos_empate" class="form-control bg-dark text-white border-secondary" value="1" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Competición</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
