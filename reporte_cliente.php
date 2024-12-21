<?php
require_once 'includes/load.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar el ID del cliente
    $cliente_id = isset($_POST['cliente_id']) ? (int)$_POST['cliente_id'] : 0;

    if ($cliente_id > 0) {
        // Redirigir al reporte detallado
        header("Location: reporte_detallado_cliente.php?cliente_id=$cliente_id");
        exit;
    } else {
        $error = "Debe seleccionar un cliente válido.";
    }
}

// Obtener la lista de clientes
$query = "SELECT id, client_name, client_company FROM clientes";
$result = $db->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Detallado de Cliente</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Reporte Detallado de Cliente</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="form-group">
            <label for="cliente_id">Selecciona un Cliente:</label>
            <select name="cliente_id" id="cliente_id" class="form-control" required>
                <option value="">-- Seleccione --</option>
                <?php while ($cliente = $result->fetch_assoc()): ?>
                    <option value="<?php echo $cliente['id']; ?>">
                        <?php echo $cliente['client_name'] . " (" . $cliente['client_company'] . ")"; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Generar Reporte</button>
        <a href="index_reportes.php" class="btn btn-secondary">Volver</a>
    </form>
</div>
</body>
</html>
