<?php
require_once 'includes/load.php';

// Validar el parámetro `id` recibido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Error: No se proporcionó un ID válido.";
    header("Location: index_presupuesto.php");
    exit;
}

$id = (int) $_GET['id'];

// Verificar si hay registros relacionados en la tabla `pagos`
$query_check_relations = $db->prepare("SELECT COUNT(*) AS total FROM pagos WHERE factura_id = ?");
$query_check_relations->bind_param('i', $id);
$query_check_relations->execute();
$result = $query_check_relations->get_result();

if ($result) {
    $row = $result->fetch_assoc();
    if ($row['total'] > 0) {
        $_SESSION['message'] = "No se puede eliminar el presupuesto porque tiene pagos relacionados.";
        header("Location: index_presupuesto.php");
        exit;
    }
}

// Si no hay registros relacionados, proceder a eliminar el presupuesto
$query_delete_presupuesto = $db->prepare("DELETE FROM presupuesto WHERE id = ?");
$query_delete_presupuesto->bind_param('i', $id);

if ($query_delete_presupuesto->execute()) {
    $_SESSION['message'] = "Presupuesto eliminado con éxito.";
} else {
    $_SESSION['message'] = "Error al eliminar el presupuesto: " . $db->error;
}

header("Location: index_presupuesto.php");
exit;
?>
