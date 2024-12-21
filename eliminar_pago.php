<?php
require_once 'includes/load.php';

// Verificar que se proporciona un ID válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó un ID de pago válido.");
}

$id = (int)$_GET['id'];

// Eliminar el pago
$query = "DELETE FROM pagos WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Pago eliminado exitosamente.";
} else {
    $_SESSION['message'] = "Error al eliminar el pago.";
}

header("Location: index_pagos.php");
exit;
?>
