@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-white mb-0">Gestión de Usuarios</h1>
            <p class="text-secondary">Administra los roles y permisos del sistema</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light"><i class="bi bi-arrow-left"></i> Volver al Dashboard</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card bg-dark border-secondary">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol Actual</th>
                            <th>Cambiar Rol</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                            <tr>
                                <td class="text-secondary">#{{ $usuario->id }}</td>
                                <td>
                                    <strong>{{ $usuario->nombre }}</strong>

                                    {{-- Indicadores visuales de dependencias --}}
                                    @if($usuario->rol === 'capitan')
                                        @php
                                            $esCapitanActivo = \App\Models\PlantillaJugador::where('id_usuario', $usuario->id)
                                                ->where('es_capitan', true)
                                                ->exists();
                                        @endphp
                                        @if($esCapitanActivo)
                                            <span class="badge bg-warning text-dark ms-2" title="Capitán activo en un equipo (No se puede cambiar el rol)">
                                                <i class="bi bi-shield-fill"></i> Activo
                                            </span>
                                        @endif
                                    @elseif($usuario->rol === 'arbitro')
                                        @php
                                            $partidosPendientesArbitro = \App\Models\Partido::where('id_arbitro', $usuario->id)
                                                ->whereIn('estado', ['pendiente', 'en_curso', 'aplazado'])
                                                ->count();
                                        @endphp
                                        @if($partidosPendientesArbitro > 0)
                                            <span class="badge bg-info text-dark ms-2" title="Tiene {{ $partidosPendientesArbitro }} partido(s) asignado(s) (No se puede cambiar el rol)">
                                                <i class="bi bi-clock-fill"></i> {{ $partidosPendientesArbitro }} partido(s)
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-secondary">{{ $usuario->email }}</td>
                                <td>
                                    <span class="badge 
                                        @if($usuario->rol === 'admin') bg-danger
                                        @elseif($usuario->rol === 'capitan') bg-warning text-dark
                                        @elseif($usuario->rol === 'arbitro') bg-info text-dark
                                        @else bg-secondary
                                        @endif">
                                        {{ ucfirst($usuario->rol) }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.usuarios.rol', $usuario->id) }}" method="POST" class="d-flex align-items-center">
                                        @csrf
                                        <select name="rol" class="form-select form-select-sm bg-dark text-white border-secondary me-2" style="width: 130px;" {{ $usuario->id === Auth::id() ? 'disabled' : '' }}>
                                            <option value="jugador" {{ $usuario->rol == 'jugador' ? 'selected' : '' }}>Jugador</option>
                                            <option value="capitan" {{ $usuario->rol == 'capitan' ? 'selected' : '' }}>Capitán</option>
                                            <option value="arbitro" {{ $usuario->rol == 'arbitro' ? 'selected' : '' }}>Árbitro</option>
                                            <option value="admin" {{ $usuario->rol == 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary" {{ $usuario->id === Auth::id() ? 'disabled' : '' }}>
                                            <i class="bi bi-save"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 d-flex justify-content-center">
                {{ $usuarios->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
