@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver al Dashboard
            </a>
            <h1 class="text-white mb-0">Solicitudes de Aplazamiento</h1>
            <p class="text-secondary">Gestión de cambios de fecha solicitados por los equipos</p>
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
                            <th>Fecha Solicitud</th>
                            <th>Partido Original</th>
                            <th>Solicitado por</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th class="text-end">Resolución</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aplazamientos as $aplazamiento)
                            <tr class="align-middle">
                                <td>{{ $aplazamiento->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="fw-bold">{{ $aplazamiento->partido->equipoLocal->nombre ?? '---' }} vs {{ $aplazamiento->partido->equipoVisitante->nombre ?? '---' }}</div>
                                    <small class="text-secondary">Fecha prev: {{ $aplazamiento->partido->fecha_hora->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <i class="bi bi-person-badge text-info"></i> {{ $aplazamiento->solicitante->nombre ?? 'Desconocido' }}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="popover" title="Motivo del Capitán" data-bs-content="{{ $aplazamiento->motivo }}">
                                        Ver Motivo
                                    </button>
                                </td>
                                <td>
                                    @if($aplazamiento->estado === 'pendiente')
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @elseif($aplazamiento->estado === 'aprobado')
                                        <span class="badge bg-success">Aprobado</span>
                                    @elseif($aplazamiento->estado === 'rechazado')
                                        <span class="badge bg-danger">Rechazado</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($aplazamiento->estado === 'pendiente')
                                        <form action="{{ route('admin.aplazamientos.gestionar', $aplazamiento->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="accion" value="aprobado">
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Aprobar y suspender el partido oficialmente?');">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.aplazamientos.gestionar', $aplazamiento->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="accion" value="rechazado">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Rechazar el aplazamiento? El partido seguirá en pie.');">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @else
                                        @if($aplazamiento->estado === 'aprobado' && $aplazamiento->partido->estado === 'aplazado')
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#reprogramarModal{{ $aplazamiento->partido->id }}">
                                                <i class="bi bi-calendar-event me-1"></i> Reprogramar
                                            </button>

                                            <!-- Modal Reprogramar Partido -->
                                            <div class="modal fade text-start" id="reprogramarModal{{ $aplazamiento->partido->id }}" tabindex="-1" aria-labelledby="reprogramarModalLabel{{ $aplazamiento->partido->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content bg-dark border-secondary text-white">
                                                        <div class="modal-header border-secondary">
                                                            <h5 class="modal-title" id="reprogramarModalLabel{{ $aplazamiento->partido->id }}">
                                                                <i class="bi bi-calendar-event text-primary me-2"></i> Reprogramar Partido
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('admin.partidos.reprogramar', $aplazamiento->partido->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <p class="text-secondary small mb-3">
                                                                    Estás reprogramando el encuentro de la <strong>Jornada {{ $aplazamiento->partido->jornada }}</strong> entre:
                                                                    <br><span class="text-white">{{ $aplazamiento->partido->equipoLocal->nombre ?? '---' }}</span> y <span class="text-white">{{ $aplazamiento->partido->equipoVisitante->nombre ?? '---' }}</span>.
                                                                </p>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="fecha_hora_{{ $aplazamiento->partido->id }}" class="form-label text-secondary small">Nueva Fecha y Hora:</label>
                                                                    <input type="datetime-local" class="form-control bg-secondary text-white border-0" id="fecha_hora_{{ $aplazamiento->partido->id }}" name="fecha_hora" value="{{ $aplazamiento->partido->fecha_hora ? $aplazamiento->partido->fecha_hora->format('Y-m-d\TH:i') : '' }}" required>
                                                                </div>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="campo_pista_{{ $aplazamiento->partido->id }}" class="form-label text-secondary small">Pista/Campo:</label>
                                                                    <input type="text" class="form-control bg-secondary text-white border-0" id="campo_pista_{{ $aplazamiento->partido->id }}" name="campo_pista" value="{{ $aplazamiento->partido->campo_pista }}" required>
                                                                </div>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="id_arbitro_{{ $aplazamiento->partido->id }}" class="form-label text-secondary small">Árbitro:</label>
                                                                    <select class="form-select bg-secondary text-white border-0" id="id_arbitro_{{ $aplazamiento->partido->id }}" name="id_arbitro" required>
                                                                        @foreach($arbitros as $arbitro)
                                                                            <option value="{{ $arbitro->id }}" {{ $aplazamiento->partido->id_arbitro == $arbitro->id ? 'selected' : '' }}>
                                                                                {{ $arbitro->nombre }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-secondary">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-primary btn-sm">Confirmar Cambios</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-secondary fst-italic">Resuelto</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-secondary">
                                    No hay solicitudes de aplazamiento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($aplazamientos->hasPages())
            <div class="card-footer border-secondary">
                {{ $aplazamientos->links() }}
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    })
})
</script>
@endsection
