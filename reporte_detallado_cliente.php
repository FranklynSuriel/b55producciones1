<?php
require_once 'includes/load.php';

// Validar el ID del cliente
if (!isset($_GET['cliente_id']) || empty($_GET['cliente_id'])) {
    die("Error: No se proporcionó un ID de cliente válido.");
}

$cliente_id = (int)$_GET['cliente_id'];

// Consultar la información del cliente
$query_cliente = "SELECT * FROM clientes WHERE id = ?";
$stmt = $db->prepare($query_cliente);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result_cliente = $stmt->get_result();

if ($result_cliente->num_rows === 0) {
    die("Error: No se encontró el cliente.");
}
$cliente = $result_cliente->fetch_assoc();

// Consultar pagos del cliente
$query_pagos = "SELECT p.*, f.numero_factura 
                FROM pagos p
                JOIN facturas f ON p.factura_id = f.id
                WHERE f.cliente_id = ?";
$stmt_pagos = $db->prepare($query_pagos);
$stmt_pagos->bind_param("i", $cliente_id);
$stmt_pagos->execute();
$result_pagos = $stmt_pagos->get_result();

// Consultar facturas del cliente
$query_facturas = "SELECT * FROM facturas WHERE cliente_id = ?";
$stmt_facturas = $db->prepare($query_facturas);
$stmt_facturas->bind_param("i", $cliente_id);
$stmt_facturas->execute();
$result_facturas = $stmt_facturas->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Detallado de Cliente</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Reporte Detallado de Cliente</h1>
    
    <h2>Información del Cliente</h2>
    <p><strong>Nombre:</strong> <?php echo $cliente['client_name']; ?></p>
    <p><strong>Empresa:</strong> <?php echo $cliente['client_company']; ?></p>
    <p><strong>Email:</strong> <?php echo $cliente['client_email']; ?></p>
    <p><strong>Teléfono:</strong> <?php echo $cliente['client_phone']; ?></p>

    <h2>Pagos Realizados</h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Número de Factura</th>
            <th>Fecha de Pago</th>
            <th>Monto</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($pago = $result_pagos->fetch_assoc()): ?>
            <tr>
                <td><?php echo $pago['id']; ?></td>
                <td><?php echo $pago['numero_factura']; ?></td>
                <td><?php echo $pago['fecha_pago']; ?></td>
                <td>$<?php echo number_format($pago['monto'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Facturas Asociadas</h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Número de Factura</th>
            <th>Fecha de Emisión</th>
            <th>Estado</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($factura = $result_facturas->fetch_assoc()): ?>
            <tr>
                <td><?php echo $factura['id']; ?></td>
                <td><?php echo $factura['numero_factura']; ?></td>
                <td><?php echo $factura['fecha_emision']; ?></td>
                <td><?php echo ucfirst($factura['estado']); ?></td>
                <td>$<?php echo number_format($factura['total'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>


</html>
