@extends('layouts.app')

@section('title', 'Clasificación - ' . $competicion->nombre)

@section('styles')
<style>
    .table-clasificacion th { background: #1a1a2e; color: white; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .table-clasificacion td { vertical-align: middle; font-weight: 500; }
    .table-clasificacion tr:hover { background-color: #fff3f3; }
    .posicion-badge { width: 30px; height: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; color: white; }
    .pos-1 { background: linear-gradient(135deg, #FFD700, #FFA500); }
    .pos-2 { background: linear-gradient(135deg, #C0C0C0, #A0A0A0); }
    .pos-3 { background: linear-gradient(135deg, #CD7F32, #A0522D); }
    .pos-default { background: #6c757d; }
    .stat-card { border-left: 4px solid; border-radius: 8px; }
    .chart-container { position: relative; height: 350px; }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('clasificacion.competiciones') }}" class="text-decoration-none text-muted mb-1 d-block">
            <i class="bi bi-arrow-left me-1"></i>Volver a competiciones
        </a>
        <h1 class="fw-bold mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i>{{ $competicion->nombre }}</h1>
        <span class="badge bg-info mt-1">{{ $competicion->deporte }}</span>
    </div>
    <a href="{{ route('clasificacion.pdf', $competicion->id) }}" class="btn btn-danger fw-semibold">
        <i class="bi bi-file-earmark-pdf me-1"></i>Exportar PDF
    </a>
</div>

{{-- Tabla de clasificación --}}
<div class="card mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-clasificacion table-hover mb-0" id="tablaClasificacion">
                <thead>
                    <tr>
                        <th class="text-center" style="width:50px;">#</th>
                        <th>Equipo</th>
                        <th class="text-center">PJ</th>
                        <th class="text-center">PG</th>
                        <th class="text-center">PE</th>
                        <th class="text-center">PP</th>
                        <th class="text-center">GF</th>
                        <th class="text-center">GC</th>
                        <th class="text-center">DG</th>
                        <th class="text-center fw-bold">PTS</th>
                        <th class="text-center" style="width:80px;">Info</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clasificacion as $index => $equipo)
                    <tr>
                        <td class="text-center">
                            @php $posClass = match($index + 1) { 1 => 'pos-1', 2 => 'pos-2', 3 => 'pos-3', default => 'pos-default' }; @endphp
                            <span class="posicion-badge {{ $posClass }}">{{ $index + 1 }}</span>
                        </td>
                        <td class="fw-bold">{{ $equipo['nombre'] }}</td>
                        <td class="text-center">{{ $equipo['pj'] }}</td>
                        <td class="text-center text-success fw-semibold">{{ $equipo['pg'] }}</td>
                        <td class="text-center text-warning fw-semibold">{{ $equipo['pe'] }}</td>
                        <td class="text-center text-danger fw-semibold">{{ $equipo['pp'] }}</td>
                        <td class="text-center">{{ $equipo['gf'] }}</td>
                        <td class="text-center">{{ $equipo['gc'] }}</td>
                        <td class="text-center">
                            <span class="{{ $equipo['dg'] > 0 ? 'text-success' : ($equipo['dg'] < 0 ? 'text-danger' : '') }}">
                                {{ $equipo['dg'] > 0 ? '+' : '' }}{{ $equipo['dg'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-dark fs-6 px-3">{{ $equipo['puntos'] }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('estadisticas.equipo', $equipo['id']) }}" class="btn btn-sm btn-outline-primary" title="Ver estadísticas">
                                <i class="bi bi-graph-up"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Gráficas --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-bar-chart-fill text-primary me-2"></i>Puntos por Equipo
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="chartPuntos"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-bullseye text-danger me-2"></i>Goles a Favor vs En Contra
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="chartGoles"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const chartData = @json($chartData);

    // Gráfica de puntos
    new Chart(document.getElementById('chartPuntos'), {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Puntos',
                data: chartData.puntos,
                backgroundColor: [
                    'rgba(255, 215, 0, 0.8)',
                    'rgba(192, 192, 192, 0.8)',
                    'rgba(205, 127, 50, 0.8)',
                    'rgba(220, 53, 69, 0.6)',
                    'rgba(108, 117, 125, 0.6)',
                    'rgba(108, 117, 125, 0.4)',
                ],
                borderColor: [
                    'rgba(255, 215, 0, 1)',
                    'rgba(192, 192, 192, 1)',
                    'rgba(205, 127, 50, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(108, 117, 125, 1)',
                    'rgba(108, 117, 125, 1)',
                ],
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // Gráfica de goles
    new Chart(document.getElementById('chartGoles'), {
        type: 'bar',
        data: {
            labels: chartData.labels,
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
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>
@endsection
