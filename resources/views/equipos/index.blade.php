@extends('layouts.app')

@section('styles')
<style>
    .glass-card {
        background: rgba(26, 27, 35, 0.6);
        border: 1px solid rgba(255,255,255,0.05);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .glass-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.4);
        border-color: rgba(255,255,255,0.1);
    }
    .title-font {
        font-family: 'Outfit', sans-serif;
    }
    .text-muted-custom {
        color: #a0a5ba !important;
        font-family: 'Inter', sans-serif;
    }
    .btn-gradient {
        background: linear-gradient(135deg, #ff2a5f, #d01c48);
        border-radius: 50px;
        border: none;
        color: white;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255, 42, 95, 0.3);
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="text-white fw-bold m-0 title-font">
            <i class="bi bi-shield-shaded me-2" style="color: #ff2a5f;"></i> Directorio de Equipos
        </h2>
        @if(auth()->user() && auth()->user()->rol === 'admin')
            <a href="{{ route('equipos.create') }}" class="btn btn-gradient px-4 py-2">
                <i class="bi bi-plus-lg me-1"></i> Registrar Equipo
            </a>
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
                <div class="card glass-card h-100 text-center p-4">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px; background: rgba(255,42,95,0.1); border: 1px solid rgba(255,42,95,0.2);">
                        <i class="bi bi-shield-fill fs-1" style="color: #ff2a5f;"></i>
                    </div>
                    
                    <h4 class="text-white fw-bold mb-1 title-font">{{ $equipo->nombre }}</h4>
                    <p class="text-muted-custom small mb-3">
                        <i class="bi bi-person-badge"></i> Capitán: {{ $equipo->capitan->nombre ?? 'Sin asignar' }}
                    </p>
                    
                    <div class="d-flex justify-content-center gap-4 text-muted-custom mb-4 mt-2">
                        <div>
                            <div class="fw-bold fs-4 text-white">{{ $equipo->plantilla_count }}</div>
                            <small style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Jugadores</small>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 {{ $equipo->puntos_sancion > 0 ? 'text-danger' : 'text-white' }}">{{ $equipo->puntos_sancion ?? 0 }}</div>
                            <small style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Sanciones</small>
                        </div>
                    </div>

                    <a href="{{ route('equipos.show', $equipo->id) }}" class="btn btn-outline-light rounded-pill w-100" style="border-color: rgba(255,255,255,0.1); color: #a0a5ba; transition: all 0.3s;">
                        Ver Ficha del Equipo
                    </a>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox display-1 mb-3 text-muted-custom" style="opacity: 0.5;"></i>
                <h4 class="text-white title-font">Liga Vacía</h4>
                <p class="text-muted-custom">Aún no se ha inscrito ningún equipo en la competición.</p>
                @if(auth()->user()->rol === 'admin')
                    <a href="{{ route('equipos.create') }}" class="btn btn-gradient mt-2">Registrar el primer equipo</a>
                @endif
            </div>
        @endforelse
    </div>
</div>
@endsection