<?php
session_start();
require_once 'includes/load.php';

// Verificar si el método de solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados desde el formulario
    $factura_id = isset($_POST['factura_id']) ? (int)$_POST['factura_id'] : 0;
    $a_quien_dirigida = isset($_POST['a_quien_dirigida']) ? $db->escape($_POST['a_quien_dirigida']) : '';
    $beneficiario = isset($_POST['beneficiario']) ? $db->escape($_POST['beneficiario']) : '';
    $cedula_beneficiario = isset($_POST['cedula_beneficiario']) ? $db->escape($_POST['cedula_beneficiario']) : '';
    $factura_beneficiario = isset($_POST['factura_beneficiario']) ? $db->escape($_POST['factura_beneficiario']) : '';
    $concepto = isset($_POST['concepto']) ? $db->escape($_POST['concepto']) : '';
    $monto = isset($_POST['monto']) ? (float)$_POST['monto'] : 0;
    $monto_real = isset($_POST['monto_real']) ? (float)$_POST['monto_real'] : 0;
    $condicion_comercial = isset($_POST['condicion_comercial']) ? $db->escape($_POST['condicion_comercial']) : '';
    $fecha_pago = isset($_POST['fecha_pago']) ? $db->escape($_POST['fecha_pago']) : '';
    $metodo_pago = isset($_POST['metodo_pago']) ? $db->escape($_POST['metodo_pago']) : '';

    // Validar campos obligatorios
    if ($factura_id <= 0 || empty($a_quien_dirigida) || empty($beneficiario) || empty($cedula_beneficiario) ||
        empty($factura_beneficiario) || empty($concepto) || $monto <= 0 || empty($fecha_pago) || empty($metodo_pago)) {
        $_SESSION['message'] = 'Todos los campos obligatorios deben ser completados.';
        header('Location: registrar_pago.php');
        exit;
    }

    // Insertar el pago en la base de datos
    $query = "
        INSERT INTO pagos (factura_id, a_quien_dirigida, beneficiario, cedula_beneficiario, factura_beneficiario, 
                           concepto, monto, monto_real, condicion_comercial, fecha_pago, metodo_pago)
        VALUES ('$factura_id', '$a_quien_dirigida', '$beneficiario', '$cedula_beneficiario', '$factura_beneficiario', 
                '$concepto', '$monto', '$monto_real', '$condicion_comercial', '$fecha_pago', '$metodo_pago')
    ";

    if ($db->query($query)) {
        // Actualizar el estado de la factura si el total de pagos cubre el monto total
        $query_total_pagado = "SELECT COALESCE(SUM(monto), 0) AS total_pagado FROM pagos WHERE factura_id = '$factura_id'";
        $result_total_pagado = $db->query($query_total_pagado);
        $total_pagado = $result_total_pagado->fetch_assoc()['total_pagado'];

        // Obtener el total de la factura
        $query_factura_total = "SELECT total FROM facturas WHERE id = '$factura_id'";
        $result_factura_total = $db->query($query_factura_total);
        $total_factura = $result_factura_total->fetch_assoc()['total'];

        // Cambiar el estado de la factura a 'pagada' si corresponde
        if ($total_pagado >= $total_factura) {
            $db->query("UPDATE facturas SET estado = 'pagada' WHERE id = '$factura_id'");
        }

        $_SESSION['message'] = 'Pago registrado correctamente.';
        header('Location: index_pagos.php');
        exit;
    } else {
        $_SESSION['message'] = 'Error al registrar el pago: ' . $db->error;
        header('Location: registrar_pago.php');
        exit;
    }
} else {
    $_SESSION['message'] = 'Método de solicitud inválido.';
    header('Location: registrar_pago.php');
    exit;
}
?>
