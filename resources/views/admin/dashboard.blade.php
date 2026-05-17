@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-white mb-0">Panel de Administración</h1>
            <p class="text-secondary">Visión general y métricas del sistema</p>
        </div>
        <div>
            <a href="{{ route('admin.competiciones') }}" class="btn btn-primary"><i class="bi bi-trophy"></i> Gestionar Competiciones</a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-dark border-primary h-100">
                <div class="card-body text-center">
                    <h5 class="text-secondary">Equipos</h5>
                    <div class="display-4 text-white fw-bold">{{ $stats['equipos'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-warning h-100">
                <div class="card-body text-center">
                    <h5 class="text-secondary">Partidos Pendientes</h5>
                    <div class="display-4 text-white fw-bold">{{ $stats['partidos_pendientes'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-success h-100">
                <div class="card-body text-center">
                    <h5 class="text-secondary">Partidos Jugados</h5>
                    <div class="display-4 text-white fw-bold">{{ $stats['partidos_jugados'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-danger h-100">
                <div class="card-body text-center">
                    <h5 class="text-secondary">Sanciones Activas</h5>
                    <div class="display-4 text-white fw-bold">{{ $stats['sanciones_activas'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <a href="{{ route('admin.partidos') }}" class="btn btn-outline-light w-100 py-3 border-secondary">
                <i class="bi bi-calendar-event fs-4 d-block mb-2 text-info"></i>
                Supervisar Partidos
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.sanciones') }}" class="btn btn-outline-light w-100 py-3 border-secondary">
                <i class="bi bi-hammer fs-4 d-block mb-2 text-danger"></i>
                Comité Disciplinario (Sanciones)
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.aplazamientos') }}" class="btn btn-outline-light w-100 py-3 border-secondary">
                <i class="bi bi-clock-history fs-4 d-block mb-2 text-warning"></i>
                Solicitudes de Aplazamiento
            </a>
        </div>
    </div>
</div>
@endsection
