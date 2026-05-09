@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-white mb-0">Directorio de Equipos</h1>
            <p class="text-secondary">Conoce a los clubes registrados en League Manager</p>
        </div>
        @if(auth()->user()->rol === 'admin')
            <div>
                <a href="{{ route('equipos.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Registrar Equipo</a>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0 alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($equipos as $equipo)
            <div class="col-md-4">
                <div class="card bg-dark border-secondary h-100 hover-shadow transition-all">
                    <div class="card-body text-center">
                        <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-shield-fill fs-1 text-dark"></i>
                        </div>
                        <h4 class="text-white fw-bold">{{ $equipo->nombre }}</h4>
                        <p class="text-secondary mb-3">
                            <i class="bi bi-person-badge"></i> Capitán: {{ $equipo->capitan->nombre ?? 'Sin asignar' }}
                        </p>
                        
                        <div class="d-flex justify-content-center gap-3 mb-3">
                            <span class="badge bg-primary text-white p-2">
                                <i class="bi bi-people"></i> {{ $equipo->plantilla_count }} Jugadores
                            </span>
                            @if($equipo->puntos_sancion > 0)
                                <span class="badge bg-danger text-white p-2" title="Puntos de Sanción">
                                    <i class="bi bi-exclamation-triangle"></i> -{{ $equipo->puntos_sancion }} Pts
                                </span>
                            @endif
                        </div>
                        
                        <a href="{{ route('equipos.show', $equipo->id) }}" class="btn btn-outline-info w-100">Ver Ficha del Equipo</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-shield-x display-1 text-secondary mb-3"></i>
                <h3 class="text-white">No hay equipos registrados</h3>
                @if(auth()->user()->rol === 'admin')
                    <p class="text-secondary">Empieza creando el primer equipo del sistema.</p>
                @endif
            </div>
        @endforelse
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.4) !important;
        border-color: #0dcaf0 !important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
</style>
@endsection