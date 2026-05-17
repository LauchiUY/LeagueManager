@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver al Dashboard
            </a>
            <h1 class="text-white mb-0">Comité Disciplinario</h1>
            <p class="text-secondary">Registro de sanciones automáticas y manuales</p>
        </div>
        <div>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#crearSancionModal">
                <i class="bi bi-hammer"></i> Nueva Sanción Manual
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0 alert-dismissible fade show">
            {{ session('success') }}
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

    <div class="card bg-dark border-secondary shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Fecha Registro</th>
                            <th>Infractor</th>
                            <th>Motivo</th>
                            <th>Progreso (Partidos)</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sanciones as $sancion)
                            <tr class="align-middle">
                                <td>{{ $sancion->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="fw-bold text-white">{{ $sancion->usuario->nombre ?? 'Desconocido' }}</div>
                                    @if($sancion->partidoOrigen)
                                        <small class="text-secondary">Origen: Jornada {{ $sancion->partidoOrigen->jornada }}</small>
                                    @else
                                        <small class="text-danger">Sanción Manual (Comité)</small>
                                    @endif
                                </td>
                                <td>{{ $sancion->motivo }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1 bg-secondary" style="height: 10px;">
                                            @php
                                                $porcentaje = $sancion->partidos_suspension > 0 ? ($sancion->partidos_cumplidos / $sancion->partidos_suspension) * 100 : 100;
                                            @endphp
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                        <span class="small">{{ $sancion->partidos_cumplidos }} / {{ $sancion->partidos_suspension }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($sancion->estado === 'activa')
                                        <span class="badge bg-danger">Activa</span>
                                    @else
                                        <span class="badge bg-success">Cumplida</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editarSancionModal{{ $sancion->id }}" title="Editar sanción">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Editar Sanción -->
                            <div class="modal fade" id="editarSancionModal{{ $sancion->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content bg-dark text-white border-secondary">
                                        <form action="{{ route('admin.sanciones.editar', $sancion->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-warning">
                                                <h5 class="modal-title"><i class="bi bi-pencil-square text-warning"></i> Editar Sanción</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="small text-secondary">Editando sanción de: <strong class="text-white">{{ $sancion->usuario->nombre ?? 'Desconocido' }}</strong></p>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Motivo:</label>
                                                    <textarea name="motivo" class="form-control bg-dark text-white border-secondary" rows="2" required>{{ $sancion->motivo }}</textarea>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label">Partidos de Suspensión:</label>
                                                        <input type="number" name="partidos_suspension" class="form-control bg-dark text-white border-secondary" min="1" value="{{ $sancion->partidos_suspension }}" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label">Partidos Cumplidos:</label>
                                                        <input type="number" name="partidos_cumplidos" class="form-control bg-dark text-white border-secondary" min="0" value="{{ $sancion->partidos_cumplidos }}" required>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Estado:</label>
                                                    <select name="estado" class="form-select bg-dark text-white border-secondary" required>
                                                        <option value="activa" {{ $sancion->estado === 'activa' ? 'selected' : '' }}>Activa</option>
                                                        <option value="cumplida" {{ $sancion->estado === 'cumplida' ? 'selected' : '' }}>Cumplida</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-warning">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-secondary">
                                    No hay sanciones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sanciones->hasPages())
            <div class="card-footer border-secondary">
                {{ $sanciones->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Nueva Sanción -->
<div class="modal fade" id="crearSancionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <form action="{{ route('admin.sanciones.crear') }}" method="POST">
                @csrf
                <div class="modal-header border-danger">
                    <h5 class="modal-title"><i class="bi bi-hammer text-danger"></i> Imponer Sanción Manual</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-secondary">Aplica una sanción disciplinaria directa a un jugador o capitán.</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Usuario Infractor:</label>
                        <select name="id_usuario" class="form-select bg-dark text-white border-secondary" required>
                            <option value="">Selecciona al usuario...</option>
                            @foreach($usuarios as $user)
                                <option value="{{ $user->id }}">{{ $user->nombre }} ({{ ucfirst($user->rol) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Partidos de Suspensión:</label>
                        <input type="number" name="partidos_suspension" class="form-control bg-dark text-white border-secondary" min="1" value="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Motivo (Acta del Comité):</label>
                        <textarea name="motivo" class="form-control bg-dark text-white border-secondary" rows="3" required placeholder="Describe la falta grave..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Sanción</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
