<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Equipo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2>Crear Nuevo Equipo</h2>
        <form action="{{ route('equipos.store') }}" method="POST" class="bg-white p-4 rounded shadow-sm">
            @csrf
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Equipo</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="id_capitan" class="form-label">ID del Capitán (Usa el 1 para prueba)</label>
                <input type="number" class="form-control" id="id_capitan" name="id_capitan" value="1" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Equipo</button>
        </form>
    </div>
</body>
</html>