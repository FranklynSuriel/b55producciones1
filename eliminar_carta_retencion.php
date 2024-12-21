<?php
require_once 'includes/load.php';

// Validar el ID del pago
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó un ID válido.");
}

$id = (int)$_GET['id'];

// Eliminar el registro
$query = "DELETE FROM pagos WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Pago eliminado correctamente.";
} else {
    $_SESSION['message'] = "Error al eliminar el pago.";
}

header('Location: index_carta_retencion.php');
exit;
?>
