<?php
require_once 'includes/load.php';

// Verificar si se proporciona un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ID de proyecto no válido.";
    header("Location: index_proyectos.php");
    exit;
}

$id = (int) $_GET['id'];

// Obtener datos del proyecto
$query = "SELECT * FROM proyectos WHERE id = $id";
$proyecto = $db->query($query)->fetch_assoc();

if (!$proyecto) {
    $_SESSION['message'] = "El proyecto no existe o fue eliminado.";
    header("Location: index_proyectos.php");
    exit;
}

// Obtener las tareas relacionadas con el proyecto
$query_tareas = "SELECT * FROM tareas WHERE proyecto_id = $id";
$tareas = $db->query($query_tareas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Proyecto</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Proyecto: <?php echo htmlspecialchars($proyecto['nombre'] ?? 'Sin nombre'); ?></h1>
    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($proyecto['descripcion'] ?? 'Sin descripción'); ?></p>
    <p><strong>Estado:</strong> <?php echo htmlspecialchars($proyecto['estado'] ?? 'Desconocido'); ?></p>
    <p><strong>Presupuesto Asignado:</strong> $<?php echo number_format($proyecto['presupuesto_asignado'] ?? 0, 2); ?></p>
    <p><strong>Costo Real:</strong> $<?php echo number_format($proyecto['costo_real'] ?? 0, 2); ?></p>

    <h3>Tareas</h3>
    <a href="nueva_tarea.php?proyecto_id=<?php echo $id; ?>" class="btn btn-primary mb-3">Nueva Tarea</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Responsable</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($tarea = $tareas->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($tarea['titulo'] ?? 'Sin título'); ?></td>
                <td><?php echo htmlspecialchars($tarea['descripcion'] ?? 'Sin descripción'); ?></td>
                <td><?php echo htmlspecialchars($tarea['responsable'] ?? 'No asignado'); ?></td>
                <td><?php echo htmlspecialchars($tarea['fecha_inicio'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($tarea['fecha_fin'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($tarea['estado'] ?? 'Desconocido'); ?></td>
                <td>
                    <a href="editar_tarea.php?id=<?php echo $tarea['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="eliminar_tarea.php?id=<?php echo $tarea['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta tarea?')">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <div class="text-center">
        <a href="index_proyectos.php" class="btn btn-primary mt-3">Volver a lista de proyectos</a>
    </div>
</div>
</body>
</html>
