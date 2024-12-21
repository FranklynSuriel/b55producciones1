<?php
require_once 'includes/load.php';

// Consulta para obtener todos los pagos asociados a facturas
$query = "
    SELECT p.id AS pago_id,
            p.factura_id, 
           p.beneficiario, 
           f.numero_factura, 
           p.monto 
    FROM pagos p
    JOIN facturas f ON p.factura_id = f.id
    ORDER BY p.fecha_pago DESC
";
$result_pagos = $db->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Cartas de Retención</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
   
        
    <tbody>
<div class="container mt-4">
    <h1>Listado de Cartas de Retención</h1>
    <div class="container mt-5">
       
        <a href="index_condicion_comercial.php" class="btn btn-success mb-3">Generar Reporte por condicion Comercial</a>
    <!-- Mensajes de sesión -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Beneficiario</th>
                <th>Factura</th>
                <th>Monto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result_pagos->num_rows > 0): ?>
            <?php while ($pago = $result_pagos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $pago['pago_id']; ?></td>
                    <td><?php echo htmlspecialchars($pago['beneficiario']); ?></td>
                    <td><?php echo htmlspecialchars($pago['numero_factura']); ?></td>
                    <td>$<?php echo number_format($pago['monto'], 2); ?></td>
                    <td>
                        <a href="ver_carta_retencion.php?id=<?php echo $pago['pago_id']; ?>" class="btn btn-sm btn-info">Ver</a>
                        <a href="editar_carta_retencion.php?id=<?php echo $pago['pago_id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="eliminar_carta_retencion.php?id=<?php echo $pago['pago_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este registro?');">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No hay pagos registrados asociados a facturas.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="index_reportes.php" class="btn btn-secondary">Volver</a>
</div>
</body>
</html>
