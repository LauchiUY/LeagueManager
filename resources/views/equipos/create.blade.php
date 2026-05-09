@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('equipos.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <h1 class="text-white mb-0">Registrar Equipo</h1>
                </div>
            </div>

            <div class="card bg-dark border-secondary shadow-sm">
                <div class="card-body">
                    <form action="{{ route('equipos.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-secondary">Nombre del Equipo</label>
                            <input type="text" name="nombre" class="form-control bg-dark text-white border-secondary @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej: Tigres FC">
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-secondary">Capitán (Representante)</label>
                            <select name="id_capitan" class="form-select bg-dark text-white border-secondary @error('id_capitan') is-invalid @enderror" required>
                                <option value="">Selecciona al capitán...</option>
                                @foreach($capitanes as $capitan)
                                    <option value="{{ $capitan->id }}" {{ old('id_capitan') == $capitan->id ? 'selected' : '' }}>
                                        {{ $capitan->nombre }} ({{ $capitan->email }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-secondary">Solo los usuarios con rol 'capitan' aparecen aquí.</div>
                            @error('id_capitan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('equipos.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Registrar Equipo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection