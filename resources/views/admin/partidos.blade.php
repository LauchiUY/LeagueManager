@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver al Dashboard
            </a>
            <h1 class="text-white mb-0">Gestión de Partidos</h1>
            <p class="text-secondary">Supervisión y asignación de árbitros</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0 alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card bg-dark border-secondary shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Competición</th>
                            <th>Encuentro</th>
                            <th>Estado</th>
                            <th>Árbitro Asignado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($partidos as $partido)
                            <tr class="align-middle">
                                <td>
                                    <div class="fw-bold">{{ $partido->fecha_hora->format('d/m/Y') }}</div>
                                    <small class="text-secondary">{{ $partido->fecha_hora->format('H:i') }}</small>
                                </td>
                                <td>{{ $partido->competicion->nombre }} <br><small class="text-secondary">Jornada {{ $partido->jornada }}</small></td>
                                <td>
                                    <span class="fw-bold">{{ $partido->equipoLocal->nombre ?? '---' }}</span>
                                    <span class="text-secondary mx-1">vs</span>
                                    <span class="fw-bold">{{ $partido->equipoVisitante->nombre ?? '---' }}</span>
                                </td>
                                <td>
                                    @if($partido->estado === 'pendiente')
                                        <span class="badge bg-secondary">Pendiente</span>
                                    @elseif($partido->estado === 'en_curso')
                                        <span class="badge bg-primary">En Curso</span>
                                    @elseif($partido->estado === 'jugado')
                                        <span class="badge bg-success">Jugado</span>
                                    @elseif($partido->estado === 'aplazado')
                                        <span class="badge bg-warning text-dark">Aplazado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($partido->arbitro)
                                        <span class="text-white"><i class="bi bi-person-badge text-info"></i> {{ $partido->arbitro->nombre }}</span>
                                    @else
                                        <span class="text-danger"><i class="bi bi-exclamation-circle"></i> Sin Árbitro</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#arbitroModal{{ $partido->id }}">
                                        Cambiar Árbitro
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Cambiar Árbitro -->
                            <div class="modal fade" id="arbitroModal{{ $partido->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content bg-dark text-white border-secondary">
                                        <form action="{{ route('admin.partidos.arbitro', $partido->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header border-secondary">
                                                <h5 class="modal-title">Asignar Árbitro</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Selecciona el Árbitro:</label>
                                                    <select name="id_arbitro" class="form-select bg-dark text-white border-secondary" required>
                                                        <option value="">Selecciona uno...</option>
                                                        @foreach($arbitros as $arbitro)
                                                            <option value="{{ $arbitro->id }}" {{ $partido->id_arbitro == $arbitro->id ? 'selected' : '' }}>
                                                                {{ $arbitro->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-secondary">
                                    No hay partidos en el sistema.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($partidos->hasPages())
            <div class="card-footer border-secondary">
                {{ $partidos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
