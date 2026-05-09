@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver al Dashboard
            </a>
            <h1 class="text-white mb-0">Gestión de Competiciones</h1>
            <p class="text-secondary">Asigna equipos y genera el calendario de la liga</p>
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

    <div class="row g-4">
        @forelse($competiciones as $competicion)
            <div class="col-12">
                <div class="card bg-dark border-secondary shadow-sm">
                    <div class="card-header border-secondary text-white bg-transparent py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $competicion->nombre }}</h4>
                            <span class="badge bg-{{ $competicion->estado === 'en_curso' ? 'primary' : 'secondary' }} mt-1">
                                {{ ucfirst($competicion->estado) }}
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#asignarEquiposModal{{ $competicion->id }}">
                                <i class="bi bi-people"></i> Equipos Inscritos ({{ $competicion->equipos->count() }})
                            </button>
                            
                            @if($competicion->partidos_count > 0)
                                <button class="btn btn-success" disabled>
                                    <i class="bi bi-calendar-check"></i> Calendario Generado
                                </button>
                            @else
                                <form action="{{ route('admin.competiciones.calendario', $competicion->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro? Se generará el calendario ida y vuelta de forma automática. No podrás deshacerlo fácilmente.');">
                                    @csrf
                                    <button type="submit" class="btn btn-warning" {{ $competicion->equipos->count() < 2 ? 'disabled' : '' }}>
                                        <i class="bi bi-magic"></i> Generar Calendario
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="text-secondary">Equipos asignados actualmente:</h6>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            @forelse($competicion->equipos as $equipoAsignado)
                                <span class="badge bg-secondary fs-6">{{ $equipoAsignado->nombre }}</span>
                            @empty
                                <span class="text-secondary fst-italic">Ningún equipo asignado.</span>
                            @endforelse
                        </div>
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
                                <p class="text-secondary">Selecciona los equipos que jugarán este torneo. Cuidado: Si ya generaste el calendario, agregar equipos nuevos podría no incluirlos hasta la próxima temporada.</p>
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
                <p class="text-secondary">No hay competiciones registradas.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
