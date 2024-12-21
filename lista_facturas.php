<?php
require_once 'includes/load.php'; // Asegúrate de que este archivo carga la conexión a la base de datos

// Consulta para obtener todas las facturas
$query = "
    SELECT 
        f.id AS factura_id, 
        f.numero_factura, 
        p.project_name AS proyecto, 
        c.client_name AS cliente, 
        f.fecha_emision, 
        f.estado, 
        f.total 
    FROM 
        facturas f
    INNER JOIN 
        presupuesto p ON f.presupuesto_id = p.id
    INNER JOIN 
        clientes c ON f.cliente_id = c.id
    ORDER BY 
        f.fecha_emision DESC
";

// Ejecutar la consulta y manejar posibles errores
$result = $db->query($query);
if (!$result) {
    die("Error al obtener las facturas: " . $db->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Facturas</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Lista de Facturas</h1>
    <a href="facturas.php" class="btn btn-success mb-3">Crear Nueva Factura</a>
    <table class="table table-bordered">
        <thead class="thead-dark">
        <tr>
            <th># Factura</th>
            <th>Proyecto</th>
            <th>Cliente</th>
            <th>Fecha de Emisión</th>
            <th>Estado</th>
            <th>Total</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['numero_factura']); ?></td>
                    <td><?php echo htmlspecialchars($row['proyecto']); ?></td>
                    <td><?php echo htmlspecialchars($row['cliente']); ?></td>
                    <td><?php echo htmlspecialchars($row['fecha_emision']); ?></td>
                    <td><?php echo ucfirst(htmlspecialchars($row['estado'])); ?></td>
                    <td>$<?php echo number_format((float)$row['total'], 2); ?></td>
                    <td>
                        <a href="ver_factura.php?id=<?php echo $row['factura_id']; ?>" class="btn btn-info btn-sm">Ver</a>
                        <a href="editar_factura.php?id=<?php echo $row['factura_id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="eliminar_factura.php?id=<?php echo $row['factura_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta factura?')">Eliminar</a>
                        <a href="generar_cotizacion.php?id=<?php echo $row['factura_id']; ?>" class="btn btn-secondary btn-sm">Cotización</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">No hay facturas registradas</td>
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
