<?php
require_once 'includes/load.php';

// Validar el ID del pago
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó un ID válido.");
}

$id = (int)$_GET['id'];

// Consultar los detalles del pago
$query = "
    SELECT p.*, f.numero_factura , p.factura_beneficiario
    FROM pagos p
    JOIN facturas f ON p.factura_id = f.id
    WHERE p.id = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: No se encontró el registro.");
}

$pago = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Carta de Retención</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Detalle de Carta de Retención</h1>
    <p><strong>Beneficiario:</strong> <?php echo htmlspecialchars($pago['beneficiario']); ?></p>
    <p><strong>Factura de beneficiario:</strong> <?php echo htmlspecialchars($pago['factura_beneficiario']); ?></p>
    <p><strong>Monto:</strong> $<?php echo number_format($pago['monto'], 2); ?></p>
    <p><strong>Fecha de Pago:</strong> <?php echo $pago['fecha_pago']; ?></p>
    <p><strong>Método de Pago:</strong> <?php echo htmlspecialchars($pago['metodo_pago']); ?></p>

    <a href="<?php 
    // Determinar el archivo PHP basado en la condición comercial
    if ($pago['condicion_comercial'] === 'Informal') {
        echo 'generar_carta_informal.php?id=' . $pago['id'];
    } elseif ($pago['condicion_comercial'] === 'Persona Física') {
        echo 'generar_carta_pdf.php?id=' . $pago['id'];
    } elseif ($pago['condicion_comercial'] === 'Simplificado') {
        echo 'generar_carta_pdf.php?id=' . $pago['id'];
    } else {
        echo 'generar_carta_default.php?id=' . $pago['id']; // Archivo por defecto si no coincide
    }
        ?>" class="btn btn-primary">Imprimir Carta en PDF</a>

    <a href="index_carta_retencion.php" class="btn btn-secondary">Volver</a>
</div>
</body>
</html>
