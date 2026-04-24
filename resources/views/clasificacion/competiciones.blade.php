@extends('layouts.app')

@section('title', 'Competiciones')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="fw-bold"><i class="bi bi-trophy-fill text-warning me-2"></i>Competiciones</h1>
        <p class="text-muted">Selecciona una competición para ver la clasificación en tiempo real.</p>
    </div>
</div>

<div class="row g-4">
    @foreach($competiciones as $comp)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:50px; height:50px; background: linear-gradient(135deg, #dc3545, #e94560); color:white; font-size:1.3rem;">
                        <i class="bi bi-trophy"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0 fw-bold">{{ $comp->nombre }}</h5>
                        <small class="text-muted"><i class="bi bi-dribbble me-1"></i>{{ $comp->deporte }}</small>
                    </div>
                </div>
                <div class="mb-3">
                    @if($comp->estado == 'en_curso')
                        <span class="badge bg-success"><i class="bi bi-play-circle me-1"></i>En curso</span>
                    @elseif($comp->estado == 'pendiente')
                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Pendiente</span>
                    @else
                        <span class="badge bg-secondary"><i class="bi bi-check-circle me-1"></i>Finalizada</span>
                    @endif
                    <span class="badge bg-light text-dark ms-1">
                        <i class="bi bi-star-fill text-warning me-1"></i>V: {{ $comp->puntos_victoria }}pts | E: {{ $comp->puntos_empate }}pt
                    </span>
                </div>
                <a href="{{ route('clasificacion.index', $comp->id) }}" class="btn btn-outline-danger mt-auto fw-semibold">
                    <i class="bi bi-bar-chart-fill me-1"></i>Ver Clasificación
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($competiciones->isEmpty())
<div class="alert alert-info mt-4">
    <i class="bi bi-info-circle me-2"></i>No hay competiciones registradas todavía.
</div>
@endif
@endsection
