<?php
require_once 'includes/load.php';

// Verificar que se proporcione el ID del proyecto
if (!isset($_GET['proyecto_id']) || empty($_GET['proyecto_id'])) {
    $_SESSION['message'] = "ID del proyecto no proporcionado.";
    header("Location: index_proyectos.php");
    exit;
}

$proyecto_id = (int)$_GET['proyecto_id'];
$query = "SELECT * FROM tareas WHERE proyecto_id = '$proyecto_id'";
$result = $db->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas del Proyecto</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Tareas del Proyecto</h1>
       
    </div>

    <a href="nueva_tarea.php?proyecto_id=<?php echo $proyecto_id; ?>" class="btn btn-primary mb-3">Nueva Tarea</a>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Responsable</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Estado</th>
                <th>Prioridad</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($tarea = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($tarea['titulo']); ?></td>
                    <td><?php echo htmlspecialchars($tarea['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($tarea['responsable']); ?></td>
                    <td><?php echo htmlspecialchars($tarea['fecha_inicio']); ?></td>
                    <td><?php echo htmlspecialchars($tarea['fecha_fin']); ?></td>
                    <td><?php echo ucfirst(htmlspecialchars($tarea['estado'])); ?></td>
                    <td><?php echo ucfirst(htmlspecialchars($tarea['prioridad'])); ?></td>
                    <td>
                        <a href="editar_tarea.php?id=<?php echo $tarea['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="eliminar_tarea.php?id=<?php echo $tarea['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar esta tarea?');">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
<div class="text-center">
    <a href="index_proyectos.php" class="btn btn-primary mt-3">Volver a lista de proyectos</a>
</div>
</html>
