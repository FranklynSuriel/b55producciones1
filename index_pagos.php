<?php
require_once 'includes/load.php';

// Consultar todos los pagos registrados
$query_pagos = "
    SELECT p.id, f.numero_factura, c.client_name, p.monto, p.fecha_pago, p.metodo_pago, f.estado
    FROM pagos p
    JOIN facturas f ON p.factura_id = f.id
    JOIN clientes c ON f.cliente_id = c.id
    ORDER BY p.fecha_pago DESC";
$result_pagos = $db->query($query_pagos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Pagos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Listado de Pagos</h1>
    
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
    
        <!-- Botones adicionales -->
        <div class="mb-3">    
        <a href="registros_pagos.php" class="btn btn-success mb-3">Registrar Nuevo Pago</a>
           
            <a href="index_costos.php" class="btn btn-success mb-3">Control de Costos</a>
    
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Factura</th>
                    <th>Cliente</th>
                    <th>Monto</th>
                    <th>Fecha de Pago</th>
                    <th>Método de Pago</th>
                    <th>Estado de la Factura</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_pagos->num_rows > 0): ?>
                    <?php while ($pago = $result_pagos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $pago['id']; ?></td>
                            <td><?php echo $pago['numero_factura']; ?></td>
                            <td><?php echo htmlspecialchars($pago['client_name']); ?></td>
                            <td>$<?php echo number_format($pago['monto'], 2); ?></td>
                            <td><?php echo $pago['fecha_pago']; ?></td>
                            <td><?php echo htmlspecialchars($pago['metodo_pago'] ?? 'N/A'); ?></td>
                            <td><?php echo ucfirst($pago['estado']); ?></td>
                            <td>
                                <a href="ver_pago.php?id=<?php echo $pago['id']; ?>" class="btn btn-sm btn-info">Ver</a>
                                <a href="editar_pago.php?id=<?php echo $pago['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="eliminar_pago.php?id=<?php echo $pago['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este pago?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No hay pagos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    

<div class="text-center">
    <a href="home.php" class="btn btn-primary mt-3">Volver al Inicio</a>
</div>
</body>
</html>
