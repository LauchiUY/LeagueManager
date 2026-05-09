@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="text-white mb-0">Mis Partidos Asignados</h2>
            <p class="text-secondary">Panel de gestión de actas digitales</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger bg-danger text-white border-0">
            {{ session('error') }}
        </div>
    @endif

    <div class="card bg-dark border-secondary">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Competición</th>
                            <th>Encuentro</th>
                            <th>Campo</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($partidos as $partido)
                            <tr class="align-middle">
                                <td>
                                    <div class="fw-bold">{{ $partido->fecha_hora->format('d/m/Y') }}</div>
                                    <small class="text-secondary">{{ $partido->fecha_hora->format('H:i') }}</small>
                                </td>
                                <td>{{ $partido->competicion->nombre }} <br> <small class="text-secondary">Jornada {{ $partido->jornada }}</small></td>
                                <td>
                                    <span class="fw-bold">{{ $partido->equipoLocal ? $partido->equipoLocal->nombre : '---' }}</span>
                                    <span class="text-secondary mx-2">vs</span>
                                    <span class="fw-bold">{{ $partido->equipoVisitante ? $partido->equipoVisitante->nombre : '---' }}</span>
                                </td>
                                <td>{{ $partido->campo_pista }}</td>
                                <td>
                                    @if($partido->estado === 'pendiente')
                                        <span class="badge bg-secondary">Pendiente</span>
                                    @elseif($partido->estado === 'en_curso')
                                        <span class="badge bg-primary">En Curso</span>
                                    @elseif($partido->estado === 'jugado')
                                        <span class="badge bg-success">Jugado</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ ucfirst($partido->estado) }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('partidos.show', $partido->id) }}" class="btn btn-sm btn-outline-light">
                                        @if($partido->estado === 'jugado')
                                            <i class="bi bi-eye"></i> Ver Acta
                                        @else
                                            <i class="bi bi-pencil-square"></i> Registrar Acta
                                        @endif
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-secondary">
                                    No tienes partidos asignados en este momento.
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
