<?php
require_once 'includes/load.php';

// Verificar si se ha proporcionado el ID del proyecto
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = 'Error: No se proporcionó un proyecto válido para eliminar.';
    header("Location: index_proyectos.php");
    exit;
}

$proyecto_id = (int)$_GET['id'];

// Intentar eliminar el proyecto y sus relaciones
try {
    // Eliminar el proyecto de la base de datos
    $query = "DELETE FROM proyectos WHERE id = $proyecto_id";
    $result = $db->query($query);

    if ($result) {
        $_SESSION['message'] = 'Proyecto eliminado exitosamente.';
    } else {
        $_SESSION['message'] = 'Error al intentar eliminar el proyecto.';
    }
} catch (Exception $e) {
    $_SESSION['message'] = 'Error: ' . $e->getMessage();
}

// Redirigir a la lista de proyectos
header("Location: index_proyectos.php");
exit;
?>
