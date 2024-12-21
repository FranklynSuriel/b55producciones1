<?php
require_once 'includes/load.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $presupuesto_id = $db->escape($_POST['presupuesto_id']);
    $numero_factura = $db->escape($_POST['numero_factura']);
    $estado = $db->escape($_POST['estado']);
    $total = (float) $_POST['total'];

    // Verificar que el presupuesto existe y obtener el cliente asociado
    $query_cliente = "SELECT client_id FROM presupuesto WHERE id = '$presupuesto_id'";
    $result_cliente = $db->query($query_cliente);

    if ($result_cliente->num_rows === 0) {
        $_SESSION['message'] = "Error: El presupuesto no existe o no tiene cliente asociado.";
        header('Location: facturas.php');
        exit;
    }

    $cliente_id = $result_cliente->fetch_assoc()['client_id'];

    // Verificar si el número de factura ya existe
    $query_check = "SELECT id FROM facturas WHERE numero_factura = '$numero_factura'";
    $result_check = $db->query($query_check);

    if ($result_check->num_rows > 0) {
        $_SESSION['message'] = "Error: El número de factura ya existe.";
        header('Location: facturas.php');
        exit;
    }

    // Insertar factura
    $query_factura = "INSERT INTO facturas (presupuesto_id, cliente_id, numero_factura, estado, total, fecha_emision) 
                      VALUES ('$presupuesto_id', '$cliente_id', '$numero_factura', '$estado', '$total', NOW())";

    if ($db->query($query_factura)) {
        $_SESSION['message'] = "Factura guardada exitosamente.";
    } else {
        $_SESSION['message'] = "Error al guardar la factura: " . $db->error;
    }

    header('Location: facturas.php');
    exit;
}
?>