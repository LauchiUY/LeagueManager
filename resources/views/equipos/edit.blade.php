@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('equipos.show', $equipo->id) }}" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="bi bi-arrow-left"></i> Volver a la Ficha
                    </a>
                    <h1 class="text-white mb-0">Editar Equipo</h1>
                </div>
            </div>

            <div class="card bg-dark border-secondary shadow-sm">
                <div class="card-body">
                    <form action="{{ route('equipos.update', $equipo->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label text-secondary">Nombre del Equipo</label>
                            <input type="text" name="nombre" class="form-control bg-dark text-white border-secondary @error('nombre') is-invalid @enderror" value="{{ old('nombre', $equipo->nombre) }}" required>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary">Capitán (Representante)</label>
                            <select name="id_capitan" class="form-select bg-dark text-white border-secondary @error('id_capitan') is-invalid @enderror" required>
                                @foreach($capitanes as $capitan)
                                    <option value="{{ $capitan->id }}" {{ old('id_capitan', $equipo->id_capitan) == $capitan->id ? 'selected' : '' }}>
                                        {{ $capitan->nombre }} ({{ $capitan->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_capitan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-secondary">Puntos de Sanción (Descuentos en Liga)</label>
                            <input type="number" name="puntos_sancion" class="form-control bg-dark text-white border-secondary @error('puntos_sancion') is-invalid @enderror" min="0" value="{{ old('puntos_sancion', $equipo->puntos_sancion) }}" required>
                            <div class="form-text text-danger">Estos puntos se restarán automáticamente en las tablas de clasificación de las ligas donde participe.</div>
                            @error('puntos_sancion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('equipos.show', $equipo->id) }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
