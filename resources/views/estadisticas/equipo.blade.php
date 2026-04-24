@extends('layouts.app')

@section('title', 'Estadísticas - ' . $equipo->nombre)

@section('styles')
<style>
    .stat-card { border-radius: 12px; border: none; }
    .stat-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: white; }
    .chart-container { position: relative; height: 300px; }
    .goleador-row { transition: background 0.2s; }
    .goleador-row:hover { background: #fff3f3; }
</style>
@endsection

@section('content')
<a href="javascript:history.back()" class="text-decoration-none text-muted mb-2 d-block">
    <i class="bi bi-arrow-left me-1"></i>Volver a la clasificación
</a>

<div class="d-flex align-items-center mb-4 flex-wrap gap-3">
    <div class="rounded-circle d-flex align-items-center justify-content-center"
         style="width:60px; height:60px; background: linear-gradient(135deg, #1a1a2e, #16213e); color: #dc3545; font-size:1.6rem;">
        <i class="bi bi-shield-fill"></i>
    </div>
    <div>
        <h1 class="fw-bold mb-0">{{ $equipo->nombre }}</h1>
        <span class="text-muted">Capitán: {{ $equipo->capitan->nombre ?? 'Sin asignar' }}</span>
    </div>
</div>

{{-- Tarjetas de resumen --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #28a745, #20c997);">
                    <i class="bi bi-bullseye"></i>
                </div>
                <div>
                    <div class="text-muted small">Goles</div>
                    <div class="fs-4 fw-bold">{{ $totalGoles }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #ffc107, #ff9800);">
                    <i class="bi bi-card-text"></i>
                </div>
                <div>
                    <div class="text-muted small">Amarillas</div>
                    <div class="fs-4 fw-bold">{{ $totalAmarillas }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #dc3545, #c82333);">
                    <i class="bi bi-card-text"></i>
                </div>
                <div>
                    <div class="text-muted small">Rojas</div>
                    <div class="fs-4 fw-bold">{{ $totalRojas }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="stat-icon me-3" style="background: linear-gradient(135deg, #6f42c1, #5a32a3);">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <div class="text-muted small">Partidos</div>
                    <div class="fs-4 fw-bold">{{ $partidos->count() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Gráficas --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-graph-up text-primary me-2"></i>Puntos Acumulados por Jornada
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="chartPuntosAcum"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-bullseye text-success me-2"></i>Goles por Jornada
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="chartGoles"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Goleadores --}}
@if($goleadores->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-white fw-bold">
        <i class="bi bi-star-fill text-warning me-2"></i>Goleadores del Equipo
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Jugador</th>
                        <th class="text-center">Goles</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($goleadores as $index => $goleador)
                    <tr class="goleador-row">
                        <td class="fw-bold">{{ $index + 1 }}</td>
                        <td>
                            <i class="bi bi-person-fill text-muted me-1"></i>{{ $goleador['jugador'] }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success fs-6">{{ $goleador['goles'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Historial de partidos --}}
<div class="card mb-4">
    <div class="card-header bg-white fw-bold">
        <i class="bi bi-clock-history text-secondary me-2"></i>Historial de Partidos
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Jornada</th>
                        <th>Rival</th>
                        <th class="text-center">Resultado</th>
                        <th class="text-center">Condición</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($partidos as $partido)
                    @php
                        $esLocal = $partido->id_local == $equipo->id;
                        $gf = $esLocal ? $partido->goles_local : $partido->goles_visitante;
                        $gc = $esLocal ? $partido->goles_visitante : $partido->goles_local;
                        $rival = $esLocal ? ($partido->equipoVisitante->nombre ?? '?') : ($partido->equipoLocal->nombre ?? '?');
                        $resultado = $gf > $gc ? 'V' : ($gf < $gc ? 'D' : 'E');
                        $badgeClass = match($resultado) { 'V' => 'bg-success', 'D' => 'bg-danger', 'E' => 'bg-warning text-dark' };
                    @endphp
                    <tr>
                        <td class="fw-bold">J{{ $partido->jornada }}</td>
                        <td>{{ $rival }}</td>
                        <td class="text-center">
                            <span class="badge {{ $badgeClass }} px-3">{{ $gf }} - {{ $gc }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark">{{ $esLocal ? 'Local' : 'Visitante' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const chartData = @json($chartData);

    // Puntos acumulados
    new Chart(document.getElementById('chartPuntosAcum'), {
        type: 'line',
        data: {
            labels: chartData.jornadas,
            datasets: [{
                label: 'Puntos acumulados',
                data: chartData.puntos_acumulados,
                borderColor: '#6f42c1',
                backgroundColor: 'rgba(111, 66, 193, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#6f42c1',
                pointRadius: 6,
                pointHoverRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Goles por jornada
    new Chart(document.getElementById('chartGoles'), {
        type: 'bar',
        data: {
            labels: chartData.jornadas,
            datasets: [
                {
                    label: 'Goles a Favor',
                    data: chartData.goles_favor,
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                },
                {
                    label: 'Goles en Contra',
                    data: chartData.goles_contra,
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>
@endsection
