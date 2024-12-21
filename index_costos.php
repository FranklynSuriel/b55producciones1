<?php
require_once 'includes/load.php';

// Consulta para obtener facturas y sus datos asociados
$query_facturas = "
    SELECT 
        f.id AS factura_id,
        f.numero_factura,
        f.fecha_emision,
        f.total,
        p.project_name,
        (SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE factura_id = f.id) AS total_pagado
    FROM facturas f
    JOIN presupuesto p ON f.presupuesto_id = p.id
    ORDER BY f.fecha_emision DESC
";
$result_facturas = $db->query($query_facturas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Costos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Listado de Costos</h1>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Proyecto</th>
                <th>Número de Factura</th>
                <th>Fecha de Emisión</th>
                <th>Monto Total</th>
                <th>Total Pagado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_facturas && $result_facturas->num_rows > 0): ?>
                <?php while ($factura = $result_facturas->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($factura['factura_id']); ?></td>
                        <td><?php echo htmlspecialchars($factura['project_name']); ?></td>
                        <td><?php echo htmlspecialchars($factura['numero_factura']); ?></td>
                        <td><?php echo htmlspecialchars($factura['fecha_emision']); ?></td>
                        <td>$<?php echo number_format($factura['total'] ?? 0, 2); ?></td>
                        <td>$<?php echo number_format($factura['total_pagado'] ?? 0, 2); ?></td>
                        <td>
                            <a href="ver_pagos_factura.php?id=<?php echo htmlspecialchars($factura['factura_id']); ?>" class="btn btn-info btn-sm">Ver Pagos</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No se encontraron facturas registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="index_pagos.php" class="btn btn-secondary">Volver</a>
</div>
</body>
</html>
