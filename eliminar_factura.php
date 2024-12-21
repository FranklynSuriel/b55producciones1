<?php
require_once 'includes/load.php';

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "ID de factura no especificado.";
    header('Location: lista_facturas.php');
    exit;
}

$id = (int)$_GET['id'];
$query = "DELETE FROM facturas WHERE id = $id";

if ($db->query($query)) {
    $_SESSION['message'] = "Factura eliminada correctamente.";
} else {
    $_SESSION['message'] = "Error al eliminar la factura.";
}

header('Location: lista_facturas.php');
exit;
?>
