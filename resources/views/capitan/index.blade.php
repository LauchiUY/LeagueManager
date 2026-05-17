@extends('layouts.app')

@section('styles')
<style>
    .glass-card {
        background: rgba(26, 27, 35, 0.6);
        border: 1px solid rgba(255,255,255,0.05);
        backdrop-filter: blur(20px);
        border-radius: 20px;
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
    .table-custom {
        background: transparent !important;
        color: white !important;
    }
    .table-custom th {
        border-bottom: 1px solid rgba(255,255,255,0.1) !important;
        color: #a0a5ba;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
        padding-bottom: 1rem;
    }
    .table-custom td {
        border-color: rgba(255,255,255,0.05) !important;
        padding: 1rem 0.5rem;
        vertical-align: middle;
    }
    .table-custom tbody tr:hover td {
        background: rgba(255,255,255,0.05);
    }
    .form-control-custom {
        background: rgba(255,255,255,0.05) !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        color: white !important;
        border-radius: 12px;
    }
    .form-control-custom:focus {
        border-color: #ff2a5f !important;
        box-shadow: 0 0 0 0.25rem rgba(255, 42, 95, 0.25) !important;
    }
</style>
@endsection

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0 shadow-lg" style="background-color: rgba(25, 135, 84, 0.8) !important; backdrop-filter: blur(10px);">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger bg-danger text-white border-0 shadow-lg" style="background-color: rgba(220, 53, 69, 0.8) !important; backdrop-filter: blur(10px);">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger bg-danger text-white border-0 shadow-lg" style="background-color: rgba(220, 53, 69, 0.8) !important; backdrop-filter: blur(10px);">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Cabecera -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <span class="text-uppercase fw-bold" style="color: #ff2a5f; letter-spacing: 2px; font-size: 0.85rem;">Panel de Gestión</span>
            <h1 class="text-white fw-bold m-0 title-font display-5">
                <i class="bi bi-shield-shaded me-2"></i> {{ $equipo->nombre }}
            </h1>
        </div>
    </div>

    <div class="row g-4">
        <!-- Fichar Jugador Lateral -->
        <div class="col-lg-4">
            <div class="card glass-card shadow-lg h-100 border-0 position-relative overflow-hidden">
                <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(to right, #ff2a5f, #d01c48);"></div>
                <div class="card-body p-4">
                    <h4 class="text-white title-font mb-4"><i class="bi bi-person-plus-fill me-2 text-danger"></i> Fichar Jugador</h4>
                    <p class="text-muted-custom small mb-4">Introduce el correo electrónico del jugador que quieres inscribir en tu equipo. (Solo puedes añadir a usuarios con el rol de "jugador" que no estén en otro equipo).</p>
                    
                    <form action="{{ route('capitan.fichar') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label text-muted-custom fw-bold" style="letter-spacing: 1px; font-size: 0.85rem; text-transform: uppercase;">Correo Electrónico</label>
                            <input type="email" class="form-control form-control-custom py-2" name="email" placeholder="ejemplo@jugador.com" required>
                        </div>
                        <button type="submit" class="btn btn-gradient w-100 py-3">
                            Fichar Ahora
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de Plantilla -->
        <div class="col-lg-8">
            <div class="card glass-card shadow-lg h-100 border-0">
                <div class="card-header bg-transparent p-4 border-bottom d-flex justify-content-between align-items-center" style="border-color: rgba(255,255,255,0.05) !important;">
                    <h4 class="text-white m-0 title-font"><i class="bi bi-people-fill me-2" style="color: #ff2a5f;"></i> Tu Plantilla Actual</h4>
                    <span class="badge" style="background: rgba(255,42,95,0.2); color: #ff2a5f; border: 1px solid rgba(255,42,95,0.3);">
                        {{ $equipo->plantilla->count() }} Jugadores
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($equipo->plantilla && $equipo->plantilla->count() > 0)
                        <div class="table-responsive px-4 pb-3">
                            <table class="table table-dark table-hover table-borderless align-middle table-custom mb-0 mt-3">
                                <thead>
                                    <tr>
                                        <th class="ps-3">Jugador</th>
                                        <th>Dorsal</th>
                                        <th>Rol</th>
                                        <th class="text-end pe-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equipo->plantilla as $plantilla)
                                        <tr>
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: rgba(255,255,255,0.1) !important;">
                                                        <i class="bi bi-person-fill text-white"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-white fw-bold">{{ $plantilla->usuario->nombre ?? 'Desconocido' }}</div>
                                                        <div class="text-muted-custom small">{{ $plantilla->usuario->email ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-white fw-bold">#{{ $plantilla->dorsal ?? '?' }}</span>
                                            </td>
                                            <td>
                                                @if($plantilla->es_capitan)
                                                    <span class="badge rounded-pill bg-warning text-dark"><i class="bi bi-star-fill me-1"></i> Capitán</span>
                                                @else
                                                    <span class="badge rounded-pill" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">Jugador</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                @if(!$plantilla->es_capitan)
                                                    <form action="{{ route('capitan.expulsar', $plantilla->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres expulsar a este jugador?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 35px; height: 35px; padding: 0;">
                                                            <i class="bi bi-person-x-fill"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm rounded-circle" style="width: 35px; height: 35px; padding: 0; background: transparent; border: 1px solid rgba(255,255,255,0.05); color: rgba(255,255,255,0.2);" disabled title="No te puedes expulsar a ti mismo">
                                                        <i class="bi bi-shield-lock-fill"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-person-x display-1 mb-3" style="color: rgba(255,255,255,0.1);"></i>
                            <h4 class="text-white title-font">Plantilla Vacía</h4>
                            <p class="text-muted-custom">No tienes jugadores inscritos en tu equipo todavía.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
