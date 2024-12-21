<?php
require_once 'includes/load.php'; // Archivo de conexión a la base de datos

$query = "SELECT * FROM proyectos";
$result = $db->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proyectos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <body>
        <div class="container mt-5">
            <h1>Listas de Proyectos</h1>
            <a href="nuevo_proyecto.php" class="btn btn-success mb-3">Crear Nuevo Proyecto</a>
            <table class="table table-bordered">
                <thead class="thead-dark">
            <tr>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Presupuesto</th>
                <th>Costo Real</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['estado']); ?></td>
                <td><?php echo htmlspecialchars($row['fecha_inicio']); ?></td>
                <td><?php echo htmlspecialchars($row['fecha_fin']); ?></td>
                <td>$<?php echo number_format($row['presupuesto_asignado'], 2); ?></td>
                <td>$<?php echo number_format($row['costo_real'], 2); ?></td>
                <td>
                    <a href="ver_proyecto.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">Ver</a>
                    <a href="editar_proyecto.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="eliminar_proyecto.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este proyecto?')">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
<div class="text-center">
    <a href="home.php" class="btn btn-primary mt-3">Volver al Inicio</a>
</div>

</html>
