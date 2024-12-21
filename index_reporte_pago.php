<?php
require_once 'includes/load.php';

// Procesar filtros (opcional)
$cliente_id = isset($_GET['cliente_id']) ? (int)$_GET['cliente_id'] : 0;
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

// Consulta base para obtener pagos
$query = "SELECT p.id AS pago_id, p.fecha_pago, p.monto, 
                 c.client_name, f.numero_factura 
          FROM pagos p
          JOIN facturas f ON p.factura_id = f.id
          JOIN clientes c ON f.cliente_id = c.id
          WHERE 1=1";

// Agregar filtros a la consulta
if ($cliente_id > 0) {
    $query .= " AND c.id = '$cliente_id'";
}

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $query .= " AND p.fecha_pago BETWEEN '$fecha_inicio' AND '$fecha_fin'";
} elseif (isset($_GET['fecha_inicio']) || isset($_GET['fecha_fin'])) {
    $error = "Por favor, proporciona ambas fechas para filtrar por rango.";
}

$query .= " ORDER BY p.fecha_pago DESC";

$result = $db->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte General de Pagos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Reporte General de Pagos</h1>

    <!-- Mostrar errores -->
    <?php if (isset($error)): ?>
        <div class="alert alert-warning">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- Filtros -->
    <form method="GET" action="index_reporte_pago.php" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="cliente_id">Cliente</label>
                <select name="cliente_id" id="cliente_id" class="form-control">
                    <option value="0">Todos</option>
                    <?php
                    $clientes = $db->query("SELECT id, client_name FROM clientes");
                    while ($cliente = $clientes->fetch_assoc()): ?>
                        <option value="<?php echo $cliente['id']; ?>" 
                            <?php echo $cliente_id == $cliente['id'] ? 'selected' : ''; ?>>
                            <?php echo $cliente['client_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="fecha_inicio">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="<?php echo $fecha_inicio; ?>">
            </div>
            <div class="form-group col-md-3">
                <label for="fecha_fin">Fecha Fin</label>
                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="<?php echo $fecha_fin; ?>">
            </div>
            <div class="form-group col-md-2 align-self-end">
                <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Tabla de pagos -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fecha de Pago</th>
                <th>Cliente</th>
                <th>Factura</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $total_pagos = 0;
        while ($pago = $result->fetch_assoc()): 
            $total_pagos += $pago['monto'];
        ?>
            <tr>
                <td><?php echo $pago['fecha_pago']; ?></td>
                <td><?php echo $pago['client_name']; ?></td>
                <td><?php echo $pago['numero_factura']; ?></td>
                <td>$<?php echo number_format($pago['monto'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total de Pagos:</th>
                <th>$<?php echo number_format($total_pagos, 2); ?></th>
            </tr>
        </tfoot>
    </table>

    <a href="index_reportes.php" class="btn btn-secondary">Volver</a>
</div>
</body>
</html>
