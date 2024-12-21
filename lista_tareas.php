<?php
session_start();
require_once('includes/load.php');

// Consultar todas las tareas
$query = "SELECT * FROM tareas";
$result = $db->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tareas</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Lista de Tareas</h1>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Resposable</th>
                <th>Estado</th>
                <th>Prioridad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($tarea = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $tarea['id']; ?></td>
                    <td><?php echo $tarea['titulo']; ?></td>
                    <td><?php echo $tarea['responsable']; ?></td>
                    <td><?php echo ucfirst($tarea['estado']); ?></td>
                    <td><?php echo ucfirst($tarea['prioridad']); ?></td>
                    <td>
                        <a href="editar_tarea.php?id=<?php echo $tarea['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="eliminar_tarea.php?id=<?php echo $tarea['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta tarea?');">Eliminar</a>
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
