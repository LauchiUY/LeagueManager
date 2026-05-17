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
    .input-group-text-custom {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-right: none;
        color: #a0a5ba;
        border-radius: 12px 0 0 12px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('equipos.index') }}" class="btn btn-outline-light rounded-circle me-3" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center; border-color: rgba(255,255,255,0.1); color: #a0a5ba;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="text-white fw-bold m-0 title-font">Registrar Nuevo Equipo</h2>
            </div>

            <div class="card glass-card shadow-lg">
                <div class="card-body p-5">
                    <form action="{{ route('equipos.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="nombre" class="form-label text-muted-custom fw-bold" style="letter-spacing: 1px; font-size: 0.9rem; text-transform: uppercase;">Nombre del Equipo</label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-custom">
                                    <i class="bi bi-shield-shaded"></i>
                                </span>
                                <input type="text" class="form-control form-control-custom border-start-0 ps-0 @error('nombre') is-invalid @enderror" style="border-radius: 0 12px 12px 0;" id="nombre" name="nombre" placeholder="Ej: Tigres FC" value="{{ old('nombre') }}" required>
                            </div>
                            @error('nombre') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="id_capitan" class="form-label text-muted-custom fw-bold" style="letter-spacing: 1px; font-size: 0.9rem; text-transform: uppercase;">Capitán (Representante)</label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-custom">
                                    <i class="bi bi-person-badge"></i>
                                </span>
                                <select name="id_capitan" class="form-control form-control-custom border-start-0 ps-0 @error('id_capitan') is-invalid @enderror" style="border-radius: 0 12px 12px 0;" required>
                                    <option value="">Selecciona al capitán...</option>
                                    @foreach($capitanes as $capitan)
                                        <option value="{{ $capitan->id }}" {{ old('id_capitan') == $capitan->id ? 'selected' : '' }}>
                                            {{ $capitan->nombre }} ({{ $capitan->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-text mt-2" style="color: rgba(255,255,255,0.4);"><i class="bi bi-info-circle me-1"></i> Solo los usuarios con rol 'capitan' aparecen aquí.</div>
                            @error('id_capitan') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-gradient py-3 fs-5">
                                <i class="bi bi-check2-circle me-2"></i> Registrar Equipo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection