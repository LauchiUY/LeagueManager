<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clasificación - {{ $competicion->nombre }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; font-size: 12px; }

        .header {
            background: #1a1a2e;
            color: white;
            padding: 20px 30px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 22px;
            color: #dc3545;
            margin-bottom: 4px;
        }
        .header p {
            font-size: 11px;
            color: #aaa;
        }
        .header .fecha {
            float: right;
            color: #ccc;
            font-size: 10px;
            margin-top: -30px;
        }

        .info-bar {
            padding: 8px 30px;
            background: #f8f9fa;
            border-bottom: 2px solid #dc3545;
            margin-bottom: 15px;
            font-size: 11px;
            color: #666;
        }

        .tabla-container { padding: 0 30px; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        table thead th {
            background: #1a1a2e;
            color: white;
            padding: 10px 6px;
            text-align: center;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table thead th:nth-child(2) { text-align: left; }
        table tbody td {
            padding: 8px 6px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        table tbody td:nth-child(2) { text-align: left; font-weight: bold; }
        table tbody tr:nth-child(even) { background: #f8f9fa; }
        table tbody tr:first-child { background: #fff9e6; }

        .posicion {
            display: inline-block;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            text-align: center;
            line-height: 22px;
            font-weight: bold;
            font-size: 10px;
            color: white;
        }
        .pos-1 { background: #FFD700; color: #333; }
        .pos-2 { background: #C0C0C0; color: #333; }
        .pos-3 { background: #CD7F32; }
        .pos-default { background: #6c757d; }

        .puntos { font-weight: bold; font-size: 13px; color: #1a1a2e; }
        .dg-positivo { color: #28a745; font-weight: bold; }
        .dg-negativo { color: #dc3545; font-weight: bold; }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px 30px;
            background: #1a1a2e;
            color: #aaa;
            font-size: 9px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LeagueManager</h1>
        <p>{{ $competicion->nombre }} &mdash; {{ $competicion->deporte }}</p>
        <div class="fecha">Generado: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="info-bar">
        <strong>Sistema de puntos:</strong> Victoria = {{ $competicion->puntos_victoria }} pts | Empate = {{ $competicion->puntos_empate }} pt | Derrota = 0 pts
    </div>

    <div class="tabla-container">
        <table>
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Equipo</th>
                    <th>PJ</th>
                    <th>PG</th>
                    <th>PE</th>
                    <th>PP</th>
                    <th>GF</th>
                    <th>GC</th>
                    <th>DG</th>
                    <th style="width:50px;">PTS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clasificacion as $index => $equipo)
                <tr>
                    <td>
                        @php $posClass = match($index + 1) { 1 => 'pos-1', 2 => 'pos-2', 3 => 'pos-3', default => 'pos-default' }; @endphp
                        <span class="posicion {{ $posClass }}">{{ $index + 1 }}</span>
                    </td>
                    <td>{{ $equipo['nombre'] }}</td>
                    <td>{{ $equipo['pj'] }}</td>
                    <td>{{ $equipo['pg'] }}</td>
                    <td>{{ $equipo['pe'] }}</td>
                    <td>{{ $equipo['pp'] }}</td>
                    <td>{{ $equipo['gf'] }}</td>
                    <td>{{ $equipo['gc'] }}</td>
                    <td class="{{ $equipo['dg'] > 0 ? 'dg-positivo' : ($equipo['dg'] < 0 ? 'dg-negativo' : '') }}">
                        {{ $equipo['dg'] > 0 ? '+' : '' }}{{ $equipo['dg'] }}
                    </td>
                    <td class="puntos">{{ $equipo['puntos'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        LeagueManager &copy; 2026 &mdash; Lautaro, Ayman & Marcos | Documento generado automáticamente
    </div>
</body>
</html>
