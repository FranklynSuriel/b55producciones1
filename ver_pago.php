<?php
require_once 'includes/load.php';

// Verificar que se proporciona un ID válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó un ID de pago válido.");
}

$id = (int)$_GET['id'];

// Consulta para obtener los datos del pago
$query = "SELECT p.*, f.numero_factura, c.client_name, c.client_company, f.estado
          FROM pagos p
          JOIN facturas f ON p.factura_id = f.id
          JOIN clientes c ON f.cliente_id = c.id
          WHERE p.id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: No se encontró el pago.");
}

$pago = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Pago</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Detalles del Pago</h1>
    <ul class="list-group">
        <li class="list-group-item"><strong>Factura:</strong> <?php echo $pago['numero_factura']; ?></li>
        <li class="list-group-item"><strong>Cliente:</strong> <?php echo $pago['client_name']; ?></li>
        <li class="list-group-item"><strong>Empresa:</strong> <?php echo $pago['client_company']; ?></li>
        <li class="list-group-item"><strong>Monto:</strong> $<?php echo number_format($pago['monto'], 2); ?></li>
        <li class="list-group-item"><strong>Fecha de Pago:</strong> <?php echo $pago['fecha_pago']; ?></li>
        <li class="list-group-item"><strong>Método de Pago:</strong> <?php echo $pago['metodo_pago'] ?? 'N/A'; ?></li>
        <li class="list-group-item"><strong>Estado de la Factura:</strong> <?php echo ucfirst($pago['estado']); ?></li>
    </ul>
    <a href="index_pagos.php" class="btn btn-secondary mt-3">Volver</a>
</div>
</body>
</html>
