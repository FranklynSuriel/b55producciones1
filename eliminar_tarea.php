<?php
session_start();
require_once('includes/load.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['message'] = 'ID de tarea no válido.';
    header('Location: lista_tareas.php');
    exit;
}

$query = "DELETE FROM tareas WHERE id = '$id'";

if ($db->query($query)) {
    $_SESSION['message'] = 'Tarea eliminada correctamente.';
} else {
    $_SESSION['message'] = 'Error al eliminar la tarea.';
}

header('Location: lista_tareas.php');
exit;
?>
