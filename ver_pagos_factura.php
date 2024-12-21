<?php
require_once 'includes/load.php';

// Validar el ID de la factura
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó un ID válido.");
}

$factura_id = (int)$_GET['id'];

// Obtener datos de la factura
$query_factura = "
    SELECT f.numero_factura, f.fecha_emision, f.total, p.project_name
    FROM facturas f
    JOIN presupuesto p ON f.presupuesto_id = p.id
    WHERE f.id = ?
";
$stmt_factura = $db->prepare($query_factura);
$stmt_factura->bind_param("i", $factura_id);
$stmt_factura->execute();
$result_factura = $stmt_factura->get_result();

if ($result_factura->num_rows === 0) {
    die("Error: No se encontró la factura.");
}
$factura = $result_factura->fetch_assoc();

// Cálculo del ITBIS y Total Real
$itbis = $factura['total'] * 0.18;
$total_real = $factura['total'] - $itbis;

// Obtener los pagos asociados a la factura
$query_pagos = "
    SELECT p.fecha_pago, p.monto, p.metodo_pago, p.beneficiario, p.monto_real
    FROM pagos p
    WHERE p.factura_id = ?
    ORDER BY p.fecha_pago ASC
";
$stmt_pagos = $db->prepare($query_pagos);
$stmt_pagos->bind_param("i", $factura_id);
$stmt_pagos->execute();
$result_pagos = $stmt_pagos->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos de Factura</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Pagos Asociados</h1>
    <h3>Factura: <?php echo htmlspecialchars($factura['numero_factura']); ?></h3>
    <p><strong>Proyecto:</strong> <?php echo htmlspecialchars($factura['project_name']); ?></p>
    <p><strong>Fecha de Emisión:</strong> <?php echo htmlspecialchars($factura['fecha_emision']); ?></p>
    <p><strong>Monto Total:</strong> $<?php echo number_format($factura['total'], 2); ?></p>
    <p><strong>Monto Total Real (sin Impuestos):</strong> $<?php echo number_format($total_real, 2); ?></p>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Fecha de Pago</th>
                <th>Método de Pago</th>
                <th>Beneficiario</th>
                <th>Monto Real</th>
                <th>Monto Restante</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_pagado = 0;
            $contador = 1;
            while ($pago = $result_pagos->fetch_assoc()): 
                $total_pagado += $pago['monto_real'];
                $total_restante = $total_real - $total_pagado;
            ?>
                <tr>
                    <td><?php echo $contador++; ?></td>
                    <td><?php echo htmlspecialchars($pago['fecha_pago']); ?></td>
                    <td><?php echo htmlspecialchars($pago['metodo_pago']); ?></td>
                    <td><?php echo htmlspecialchars($pago['beneficiario']); ?></td>
                    <td>$<?php echo number_format($pago['monto_real'], 2); ?></td>
                    <td>$<?php echo number_format($total_restante, 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total Pagado:</th>
                <th>$<?php echo number_format($total_pagado, 2); ?></th>
            </tr>
        </tfoot>
    </table>

    <a href="index_costos.php" class="btn btn-secondary">Volver</a>
</div>
</body>
</html>
