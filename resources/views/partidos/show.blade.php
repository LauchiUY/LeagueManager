@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header del Partido -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('partidos.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver a mis partidos
            </a>
            <h2 class="text-white mb-0">Acta Digital - Jornada {{ $partido->jornada }}</h2>
            <p class="text-secondary">{{ $partido->competicion->nombre }} | {{ $partido->fecha_hora->format('d/m/Y H:i') }} | {{ $partido->campo_pista }}</p>
        </div>
        <div>
            @if($partido->estado === 'pendiente')
                <span class="badge bg-secondary fs-6 px-3 py-2">Pendiente</span>
            @elseif($partido->estado === 'en_curso')
                <span class="badge bg-primary fs-6 px-3 py-2">En Curso</span>
            @elseif($partido->estado === 'jugado')
                <span class="badge bg-success fs-6 px-3 py-2">Jugado</span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0 alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger bg-danger text-white border-0 alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Marcador -->
    <div class="card bg-dark border-secondary mb-4 shadow-sm">
        <div class="card-body text-center py-5">
            <div class="row align-items-center">
                <div class="col-4">
                    <h3 class="text-white fw-bold">{{ $partido->equipoLocal ? $partido->equipoLocal->nombre : 'Descansa' }}</h3>
                    <p class="text-secondary mb-0">Local</p>
                </div>
                <div class="col-4">
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        @php
                            // Extraer IDs de jugadores
                            $idsLocal = $jugadoresLocal->pluck('id_usuario')->toArray();
                            $idsVisitante = $jugadoresVisitante->pluck('id_usuario')->toArray();
                            
                            // Calcular goles reales desde los eventos (Goles a favor + Autogoles del rival)
                            $golesLocalCalc = $partido->eventoPartido->where('tipo_evento', 'Gol')->whereIn('id_jugador', $idsLocal)->count()
                                            + $partido->eventoPartido->where('tipo_evento', 'Autogol')->whereIn('id_jugador', $idsVisitante)->count();
                            
                            $golesVisitanteCalc = $partido->eventoPartido->where('tipo_evento', 'Gol')->whereIn('id_jugador', $idsVisitante)->count()
                                                + $partido->eventoPartido->where('tipo_evento', 'Autogol')->whereIn('id_jugador', $idsLocal)->count();
                            
                            // Determinar si hay resolución administrativa
                            $sancionAdministrativa = false;
                            if ($partido->estado === 'jugado') {
                                $golesLocal = $partido->goles_local;
                                $golesVisitante = $partido->goles_visitante;
                                if ($golesLocal !== $golesLocalCalc || $golesVisitante !== $golesVisitanteCalc) {
                                    $sancionAdministrativa = true;
                                }
                            } else {
                                $golesLocal = $golesLocalCalc;
                                $golesVisitante = $golesVisitanteCalc;
                            }
                        @endphp
                        <div class="display-3 fw-bold text-white bg-secondary bg-opacity-25 rounded px-4 py-2">{{ $golesLocal ?? 0 }}</div>
                        <div class="text-secondary fs-4">-</div>
                        <div class="display-3 fw-bold text-white bg-secondary bg-opacity-25 rounded px-4 py-2">{{ $golesVisitante ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-4">
                    <h3 class="text-white fw-bold">{{ $partido->equipoVisitante ? $partido->equipoVisitante->nombre : 'Descansa' }}</h3>
                    <p class="text-secondary mb-0">Visitante</p>
                </div>
            </div>
            @if($sancionAdministrativa)
            <div class="row mt-4 justify-content-center">
                <div class="col-10 col-md-8">
                    <div class="p-3 rounded-4 border d-flex align-items-center text-start" style="background: rgba(220, 53, 69, 0.1); border-color: rgba(220, 53, 69, 0.3) !important; backdrop-filter: blur(10px);">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 45px; height: 45px; background: rgba(220, 53, 69, 0.2);">
                            <i class="bi bi-shield-exclamation text-danger fs-5"></i>
                        </div>
                        <div>
                            <div class="text-white fw-bold mb-1" style="font-family: 'Outfit', sans-serif; letter-spacing: 0.5px;">Resolución Administrativa</div>
                            <div class="text-secondary" style="font-size: 0.85rem;">El marcador oficial fue fijado en 0-3 por sanción disciplinaria (Alineación indebida).</div>
                            <div class="text-secondary" style="font-size: 0.75rem;"><i class="bi bi-info-circle me-1"></i>Resultado original en la cancha: {{ $golesLocalCalc }} - {{ $golesVisitanteCalc }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Izquierda: Formularios -->
        <div class="col-lg-5">
            @if($partido->estado !== 'jugado')
                <!-- Añadir Evento -->
                <div class="card bg-dark border-secondary mb-4 shadow-sm">
                    <div class="card-header border-secondary text-white bg-transparent fw-bold py-3">
                        <i class="bi bi-plus-circle text-primary me-2"></i> Añadir Evento al Acta
                    </div>
                    <div class="card-body">
                        <form action="{{ route('partidos.evento.store', $partido->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label text-secondary">Tipo de Evento</label>
                                <select name="tipo_evento" class="form-select bg-dark text-white border-secondary" required>
                                    <option value="">Selecciona un evento...</option>
                                    <option value="Gol">⚽ Gol</option>
                                    <option value="Autogol">⚽🔄 Autogol</option>
                                    <option value="Amarilla">🟨 Tarjeta Amarilla</option>
                                    <option value="Roja">🟥 Tarjeta Roja</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-secondary">Jugador Involucrado</label>
                                <select name="id_jugador" class="form-select bg-dark text-white border-secondary" required>
                                    <option value="">Selecciona jugador...</option>
                                    <optgroup label="{{ $partido->equipoLocal ? $partido->equipoLocal->nombre : 'Local' }}">
                                        @foreach($jugadoresLocal as $p)
                                            <option value="{{ $p->usuario->id }}">#{{ $p->dorsal }} - {{ $p->usuario->nombre }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="{{ $partido->equipoVisitante ? $partido->equipoVisitante->nombre : 'Visitante' }}">
                                        @foreach($jugadoresVisitante as $p)
                                            <option value="{{ $p->usuario->id }}">#{{ $p->dorsal }} - {{ $p->usuario->nombre }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-secondary">Minuto</label>
                                    <input type="number" name="minuto" class="form-control bg-dark text-white border-secondary" min="1" max="120" required>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="form-label text-secondary">Observaciones (Opcional)</label>
                                    <input type="text" name="observaciones" class="form-control bg-dark text-white border-secondary" placeholder="Ej: Falta grave, Gol de penal...">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-bold">Registrar Evento</button>
                        </form>
                    </div>
                </div>

                <!-- Finalizar Partido -->
                <div class="card bg-dark border-danger mb-4 shadow-sm">
                    <div class="card-header border-danger text-white bg-transparent fw-bold py-3">
                        <i class="bi bi-check-circle text-success me-2"></i> Validar Acta
                    </div>
                    <div class="card-body">
                        <p class="text-secondary small mb-3">El resultado se calcula automáticamente desde los eventos registrados (goles y autogoles). Ya no podrás modificar el acta tras validarla.</p>
                        
                        <div class="row mb-3 text-center">
                            <div class="col-5">
                                <h5 class="text-white fw-bold">{{ $partido->equipoLocal->nombre ?? 'Local' }}</h5>
                                <span class="display-5 text-white fw-bolder">{{ $golesLocalCalc }}</span>
                            </div>
                            <div class="col-2 d-flex align-items-center justify-content-center">
                                <span class="text-secondary fs-3">-</span>
                            </div>
                            <div class="col-5">
                                <h5 class="text-white fw-bold">{{ $partido->equipoVisitante->nombre ?? 'Visitante' }}</h5>
                                <span class="display-5 text-white fw-bolder">{{ $golesVisitanteCalc }}</span>
                            </div>
                        </div>

                        <div class="alert alert-secondary bg-secondary bg-opacity-10 text-secondary border-secondary small text-center mb-3">
                            <i class="bi bi-info-circle me-1"></i> Este resultado se ha calculado a partir de los <strong>{{ $partido->eventoPartido->whereIn('tipo_evento', ['Gol', 'Autogol'])->count() }} eventos de gol</strong> registrados. Si falta algún gol, regístralo antes de validar.
                        </div>

                        <form action="{{ route('partidos.validar', $partido->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de validar el acta con resultado {{ $golesLocalCalc }} - {{ $golesVisitanteCalc }}? Ya no podrás añadir eventos.');">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 fw-bold py-3"><i class="bi bi-lock-fill me-2"></i>Validar Acta ({{ $golesLocalCalc }} - {{ $golesVisitanteCalc }})</button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Foto del Acta Física -->
            <div class="card bg-dark border-secondary shadow-sm">
                <div class="card-header border-secondary text-white bg-transparent fw-bold py-3">
                    <i class="bi bi-camera text-info me-2"></i> Fotografía del Acta Física
                </div>
                <div class="card-body">
                    @if($partido->url_foto_acta)
                        <div class="mb-3">
                            <a href="{{ Storage::url($partido->url_foto_acta) }}" target="_blank" class="btn btn-sm btn-outline-info w-100">
                                <i class="bi bi-image"></i> Ver foto actual
                            </a>
                        </div>
                    @endif
                    
                    @if($partido->estado !== 'jugado' || Auth::user()->rol === 'admin')
                        <form action="{{ route('partidos.acta.upload', $partido->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <input type="file" name="foto_acta" class="form-control bg-dark text-white border-secondary" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-outline-light w-100">Subir Fotografía</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Timeline -->
        <div class="col-lg-7">
            <div class="card bg-dark border-secondary h-100 shadow-sm">
                <div class="card-header border-secondary text-white bg-transparent fw-bold py-3">
                    <i class="bi bi-clock-history text-warning me-2"></i> Timeline del Partido
                </div>
                <div class="card-body p-0">
                    @if($partido->eventoPartido->count() > 0)
                        <ul class="list-group list-group-flush rounded-bottom">
                            @foreach($partido->eventoPartido->sortByDesc('minuto') as $evento)
                                <li class="list-group-item bg-dark border-secondary text-white py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-5 me-3 fw-bold" style="width: 50px; color: #a0a5ba;">
                                            {{ $evento->minuto }}'
                                        </div>
                                        <div class="fs-4 me-3">
                                            @if($evento->tipo_evento === 'Gol')
                                                ⚽
                                            @elseif($evento->tipo_evento === 'Autogol')
                                                ⚽🔄
                                            @elseif($evento->tipo_evento === 'Amarilla')
                                                🟨
                                            @elseif($evento->tipo_evento === 'Roja')
                                                🟥
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold fs-5">{{ $evento->jugador ? $evento->jugador->nombre : 'Jugador Eliminado' }}</div>
                                            @if($evento->observaciones)
                                                <small class="text-secondary">{{ $evento->observaciones }}</small>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            @php
                                                // Identificar si es del equipo local o visitante para la UI
                                                $esLocal = $jugadoresLocal->pluck('id_usuario')->contains($evento->id_jugador);
                                            @endphp
                                            <span class="badge {{ $esLocal ? 'bg-primary' : 'bg-info text-dark' }} opacity-75">
                                                {{ $esLocal ? 'Local' : 'Visitante' }}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-5 text-secondary">
                            <i class="bi bi-clipboard-x display-1 mb-3 opacity-25"></i>
                            <p class="fs-5">No hay eventos registrados en este partido.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
