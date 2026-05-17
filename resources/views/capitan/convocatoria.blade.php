@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('capitan.equipo') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver a mi equipo
            </a>
            <h2 class="text-white mb-0">Gestión de Convocatoria</h2>
            <p class="text-secondary">Partido: {{ $partido->equipoLocal->nombre ?? '---' }} vs {{ $partido->equipoVisitante->nombre ?? '---' }} | {{ $partido->fecha_hora->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger bg-danger text-white border-0 alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card bg-dark border-secondary shadow-sm">
        <div class="card-header border-secondary text-white bg-transparent fw-bold py-3">
            <i class="bi bi-card-checklist text-primary me-2"></i> Selecciona los jugadores convocados
        </div>
        <div class="card-body">
            <form action="{{ route('capitan.convocatoria.guardar', $partido->id) }}" method="POST">
                @csrf
                <div class="row g-3">
                    @forelse($plantilla as $jugador)
                        @php
                            $sancionado = $jugador->usuario->sanciones->count() > 0;
                            $convocado = in_array($jugador->usuario->id, $convocados);
                        @endphp
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 bg-black bg-opacity-25 border-{{ $sancionado ? 'danger' : 'secondary' }}">
                                <div class="card-body d-flex align-items-center">
                                    <div class="form-check form-switch fs-4 me-3">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               name="jugadores[]" value="{{ $jugador->usuario->id }}"
                                               id="jugador_{{ $jugador->usuario->id }}"
                                               {{ $convocado ? 'checked' : '' }}
                                               {{ $sancionado ? 'disabled' : '' }}>
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="form-check-label w-100 text-white" for="jugador_{{ $jugador->usuario->id }}" style="cursor: pointer;">
                                            <div class="fw-bold fs-5">#{{ $jugador->dorsal }} {{ $jugador->usuario->nombre }}</div>
                                            @if($sancionado)
                                                <span class="badge bg-danger mt-1">
                                                    <i class="bi bi-exclamation-triangle"></i> Sanción Activa
                                                </span>
                                            @else
                                                <span class="text-secondary small">Disponible</span>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <p class="text-secondary">No tienes jugadores activos en tu plantilla.</p>
                            <a href="{{ route('capitan.equipo') }}" class="btn btn-outline-info">Ir a añadir jugadores</a>
                        </div>
                    @endforelse
                </div>
                
                <div class="mt-4 pt-3 border-top border-secondary d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2" {{ $plantilla->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-save me-2"></i> Guardar Convocatoria
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
