<?php
require_once 'includes/load.php';

// Verificar si se proporciona el ID del proyecto
if (!isset($_GET['proyecto_id']) || empty($_GET['proyecto_id'])) {
    $_SESSION['message'] = "ID del proyecto no proporcionado.";
    header("Location: index_proyectos.php");
    exit;
}

$proyecto_id = (int)$_GET['proyecto_id'];

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $db->escape($_POST['titulo']);
    $descripcion = $db->escape($_POST['descripcion']);
    $fecha_inicio = $db->escape($_POST['fecha_inicio']);
    $fecha_fin = $db->escape($_POST['fecha_fin']);
    $estado = $db->escape($_POST['estado']);
    $prioridad = $db->escape($_POST['prioridad']);

    // Insertar la nueva tarea en la base de datos
    $query = "INSERT INTO tareas (proyecto_id, titulo, descripcion, fecha_inicio, fecha_fin, estado, prioridad) 
              VALUES ('$proyecto_id', '$titulo', '$descripcion', '$fecha_inicio', '$fecha_fin', '$estado', '$prioridad')";

    if ($db->query($query)) {
        $_SESSION['message'] = "Tarea creada exitosamente.";
        header("Location: tareas.php?proyecto_id=$proyecto_id");
        exit;
    } else {
        $_SESSION['message'] = "Error al crear la tarea.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Tarea</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Nueva Tarea</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form action="nueva_tarea.php?proyecto_id=<?php echo $proyecto_id; ?>" method="POST">
        <div class="form-group">
            <label for="titulo">Título de la Tarea:</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="responsable">Responsable:</label>
            <input type="text" name="responsable" id="responsable" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="fecha_inicio">Fecha de Inicio:</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="fecha_fin">Fecha de Fin:</label>
            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="estado">Estado:</label>
            <select name="estado" id="estado" class="form-control" required>
                <option value="pendiente">Pendiente</option>
                <option value="en progreso">En Progreso</option>
                <option value="completada">Completada</option>
            </select>
        </div>

        <div class="form-group">
            <label for="prioridad">Prioridad:</label>
            <select name="prioridad" id="prioridad" class="form-control" required>
                <option value="baja">Baja</option>
                <option value="media">Media</option>
                <option value="alta">Alta</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Crear Tarea</button>
        <a href="tareas.php?proyecto_id=<?php echo $proyecto_id; ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>


</html>
