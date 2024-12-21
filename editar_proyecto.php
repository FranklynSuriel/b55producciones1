<?php
require_once 'includes/load.php';

// Verificar que se envíe un ID válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "ID de proyecto no proporcionado.";
    header("Location: index_proyectos.php");
    exit;
}

$proyecto_id = (int)$_GET['id'];

// Obtener los datos del proyecto
$query = "SELECT * FROM proyectos WHERE id = '$proyecto_id'";
$result = $db->query($query);

if ($result->num_rows === 0) {
    $_SESSION['message'] = "Proyecto no encontrado.";
    header("Location: index_proyectos.php");
    exit;
}

$proyecto = $result->fetch_assoc();

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $db->escape($_POST['nombre']);
    $descripcion = $db->escape($_POST['descripcion']);
    $fecha_inicio = $db->escape($_POST['fecha_inicio']);
    $fecha_fin = $db->escape($_POST['fecha_fin']);
    $presupuesto_asignado = (float)$_POST['presupuesto_asignado'];
    $costo_real = (float)$_POST['costo_real']; // Asegúrate de manejar este campo

    $update_query = "UPDATE proyectos 
                     SET nombre = '$nombre', 
                         descripcion = '$descripcion', 
                         fecha_inicio = '$fecha_inicio', 
                         fecha_fin = '$fecha_fin', 
                         presupuesto_asignado = '$presupuesto_asignado', 
                         costo_real = '$costo_real' 
                     WHERE id = '$proyecto_id'";

    // Intentar ejecutar la consulta y manejar errores
    if ($db->query($update_query)) {
        $_SESSION['message'] = "Proyecto actualizado exitosamente.";
        header("Location: index_proyectos.php");
        exit;
    } else {
        $_SESSION['message'] = "Error al actualizar el proyecto: " . $db->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proyecto</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Editar Proyecto</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form action="editar_proyecto.php?id=<?php echo $proyecto_id; ?>" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre del Proyecto:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo htmlspecialchars($proyecto['nombre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="4" required><?php echo htmlspecialchars($proyecto['descripcion']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="fecha_inicio">Fecha de Inicio:</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="<?php echo htmlspecialchars($proyecto['fecha_inicio']); ?>" required>
        </div>

        <div class="form-group">
            <label for="fecha_fin">Fecha de Fin:</label>
            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="<?php echo htmlspecialchars($proyecto['fecha_fin']); ?>" required>
        </div>

        <div class="form-group">
            <label for="presupuesto_asignado">Presupuesto Asignado:</label>
            <input type="number" name="presupuesto_asignado" id="presupuesto_asignado" class="form-control" step="0.01" min="0" value="<?php echo htmlspecialchars($proyecto['presupuesto_asignado']); ?>" required>
        </div>

        <div class="form-group">
            <label for="costo_real">Costo Real:</label>
            <input type="number" name="costo_real" id="costo_real" class="form-control" step="0.01" min="0" value="<?php echo htmlspecialchars($proyecto['costo_real']); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="index_proyectos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
