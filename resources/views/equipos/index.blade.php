<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Equipos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2>Listado de Equipos</h2>
        <a href="{{ route('equipos.create') }}" class="btn btn-success mb-3">+ Nuevo Equipo</a>
        <ul class="list-group">
            @foreach($equipos as $equipo)
                <li class="list-group-item">
                    {{ $equipo->nombre }} (Capitán ID: {{ $equipo->id_capitan }})
                </li>
            @endforeach
        </ul>
    </div>
</body>
</html>