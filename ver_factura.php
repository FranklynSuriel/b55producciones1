<?php
require_once 'includes/load.php';

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "ID de factura no especificado.";
    header('Location: lista_facturas.php');
    exit;
}

$id = (int)$_GET['id'];
$query = "
    SELECT 
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
    WHERE 
        f.id = $id
";
$result = $db->query($query);
$factura = $result->fetch_assoc();

if (!$factura) {
    $_SESSION['message'] = "Factura no encontrada.";
    header('Location: lista_facturas.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Factura</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Detalles de Factura</h1>
    <table class="table table-bordered">
        <tr>
            <th>Número de Factura</th>
            <td><?php echo $factura['numero_factura']; ?></td>
        </tr>
        <tr>
            <th>Proyecto</th>
            <td><?php echo $factura['proyecto']; ?></td>
        </tr>
        <tr>
            <th>Cliente</th>
            <td><?php echo $factura['cliente']; ?></td>
        </tr>
        <tr>
            <th>Fecha de Emisión</th>
            <td><?php echo $factura['fecha_emision']; ?></td>
        </tr>
        <tr>
            <th>Estado</th>
            <td><?php echo ucfirst($factura['estado']); ?></td>
        </tr>
        <tr>
            <th>Total</th>
            <td>$<?php echo number_format($factura['total'], 2); ?></td>
        </tr>
    </table>
    <div class="mt-4">
        <a href="lista_facturas.php" class="btn btn-secondary">Volver</a>
        <!-- Botón para generar PDF -->
        <a href="generar_factura_pdf.php?id=<?php echo $id; ?>" class="btn btn-primary">
            <i class="fas fa-print"></i> Imprimir PDF
        </a>
    </div>
</div>
</body>
</html>
