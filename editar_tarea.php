<?php
session_start();
require_once('includes/load.php');

// Verificar si el ID es válido
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['message'] = 'ID de tarea no válido.';
    header('Location: lista_tareas.php');
    exit;
}

// Consultar la tarea en la base de datos
$query = "SELECT * FROM tareas WHERE id = '$id'";
$result = $db->query($query);

if (!$result || $result->num_rows === 0) {
    $_SESSION['message'] = 'Tarea no encontrada.';
    header('Location: lista_tareas.php');
    exit;
}

$tarea = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar y recibir datos del formulario
    $titulo = $db->escape($_POST['titulo']);
    $descripcion = $db->escape($_POST['descripcion']);
    $responsable = $db->escape($_POST['responsable']);
    $fecha_inicio = $db->escape($_POST['fecha_inicio']);
    $fecha_fin = $db->escape($_POST['fecha_fin']);
    $estado = $db->escape($_POST['estado']);
    $prioridad = $db->escape($_POST['prioridad']);

    // Verificar que los campos no estén vacíos
    if (empty($titulo) || empty($descripcion) || empty($responsable) || empty($fecha_inicio) || empty($fecha_fin)) {
        $_SESSION['message'] = 'Todos los campos son obligatorios.';
    } else {
        // Actualizar la tarea en la base de datos
        $update_query = "UPDATE tareas 
                         SET titulo = '$titulo', 
                             descripcion = '$descripcion', 
                             responsable = '$responsable', 
                             fecha_inicio = '$fecha_inicio', 
                             fecha_fin = '$fecha_fin', 
                             estado = '$estado', 
                             prioridad = '$prioridad' 
                         WHERE id = '$id'";

        if ($db->query($update_query)) {
            $_SESSION['message'] = 'Tarea actualizada correctamente.';
            header('Location: lista_tareas.php');
            exit;
        } else {
            $_SESSION['message'] = 'Error al actualizar la tarea: ' . $db->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Editar Tarea</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="<?php echo htmlspecialchars($tarea['titulo']); ?>" required>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" class="form-control" required><?php echo htmlspecialchars($tarea['descripcion']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="responsable">Responsable:</label>
            <input type="text" name="responsable" id="responsable" class="form-control" value="<?php echo htmlspecialchars($tarea['responsable']); ?>" required>
        </div>
        <div class="form-group">
            <label for="fecha_inicio">Fecha de Inicio:</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="<?php echo htmlspecialchars($tarea['fecha_inicio']); ?>" required>
        </div>
        <div class="form-group">
            <label for="fecha_fin">Fecha de Fin:</label>
            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="<?php echo htmlspecialchars($tarea['fecha_fin']); ?>" required>
        </div>
        <div class="form-group">
            <label for="estado">Estado:</label>
            <select name="estado" id="estado" class="form-control">
                <option value="pendiente" <?php echo $tarea['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                <option value="en progreso" <?php echo $tarea['estado'] == 'en progreso' ? 'selected' : ''; ?>>En Progreso</option>
                <option value="completada" <?php echo $tarea['estado'] == 'completada' ? 'selected' : ''; ?>>Completada</option>
            </select>
        </div>
        <div class="form-group">
            <label for="prioridad">Prioridad:</label>
            <select name="prioridad" id="prioridad" class="form-control">
                <option value="baja" <?php echo $tarea['prioridad'] == 'baja' ? 'selected' : ''; ?>>Baja</option>
                <option value="media" <?php echo $tarea['prioridad'] == 'media' ? 'selected' : ''; ?>>Media</option>
                <option value="alta" <?php echo $tarea['prioridad'] == 'alta' ? 'selected' : ''; ?>>Alta</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
        <a href="lista_tareas.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
