<?php
session_start();
require_once 'includes/load.php';

// Validar y sanitizar el parámetro `id` de la factura
$id_factura = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verificar si la factura existe en la base de datos
$query_factura = "SELECT id, total, estado FROM facturas WHERE id = '$id_factura'";
$result_factura = $db->query($query_factura);

if ($result_factura->num_rows === 0) {
    $_SESSION['message'] = 'Factura no encontrada.';
    header('Location: index_facturas.php');
    exit;
}

// Obtener los datos de la factura
$factura = $result_factura->fetch_assoc();
$total_factura = (float)$factura['total'];
$estado_factura = $factura['estado'];

// Procesar el formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_pago = $db->escape($_POST['fecha_pago']);
    $monto = isset($_POST['monto']) ? (float)$_POST['monto'] : 0;

    // Validar los datos ingresados
    if ($monto <= 0) {
        $_SESSION['message'] = 'El monto del pago debe ser mayor que cero.';
        header("Location: registrar_pago.php?id=$id_factura");
        exit;
    }

    // Insertar el pago en la base de datos
    $query_pago = "INSERT INTO pagos (factura_id, fecha_pago, monto) VALUES ('$id_factura', '$fecha_pago', '$monto')";
    if ($db->query($query_pago)) {
        // Verificar el total de pagos realizados
        $query_total_pagado = "SELECT COALESCE(SUM(monto), 0) AS total_pagado FROM pagos WHERE factura_id = '$id_factura'";
        $result_total_pagado = $db->query($query_total_pagado);
        $total_pagado = (float)$result_total_pagado->fetch_assoc()['total_pagado'];

        // Actualizar el estado de la factura si se ha pagado completamente
        if ($total_pagado >= $total_factura) {
            $query_update_factura = "UPDATE facturas SET estado = 'pagada' WHERE id = '$id_factura'";
            $db->query($query_update_factura);
        } elseif ($estado_factura === 'pagada' && $total_pagado < $total_factura) {
            // Si la factura estaba marcada como pagada pero el monto no es suficiente
            $query_update_factura = "UPDATE facturas SET estado = 'pendiente' WHERE id = '$id_factura'";
            $db->query($query_update_factura);
        }

        $_SESSION['message'] = 'Pago registrado correctamente.';
        header('Location: index_facturas.php');
        exit;
    } else {
        $_SESSION['message'] = 'Error al registrar el pago: ' . $db->error;
        header("Location: registrar_pago.php?id=$id_factura");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Pago</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Registrar Pago para la Factura #<?php echo htmlspecialchars($id_factura); ?></h1>

    <!-- Mensaje de sesión -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <p><strong>Total Factura:</strong> $<?php echo number_format($total_factura, 2); ?></p>
    <p><strong>Estado Actual:</strong> <?php echo ucfirst($estado_factura); ?></p>

    <form action="registrar_pago.php?id=<?php echo $id_factura; ?>" method="POST">
        <div class="form-group">
            <label for="fecha_pago">Fecha del Pago:</label>
            <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="monto">Monto del Pago:</label>
            <input type="number" name="monto" id="monto" class="form-control" step="0.01" min="0.01" required>
        </div>

        <button type="submit" class="btn btn-primary">Registrar Pago</button>
        <a href="index_facturas.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
