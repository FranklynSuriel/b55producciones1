<?php
require_once 'includes/load.php';

// Verificar que se proporciona un ID válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó un ID de pago válido.");
}

$id = (int)$_GET['id'];

// Obtener datos del pago actual
$query = "SELECT * FROM pagos WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: No se encontró el pago.");
}

$pago = $result->fetch_assoc();

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $monto = (float)$_POST['monto'];
    $fecha_pago = $_POST['fecha_pago'];
    $metodo_pago = $_POST['metodo_pago'];
    $condicion_comercial = $_POST['condicion_comercial'];

    $query_update = "UPDATE pagos SET monto = ?, fecha_pago = ?, metodo_pago = ?, condicion_comercial = ? WHERE id = ?";
    $stmt_update = $db->prepare($query_update);
    $stmt_update->bind_param("dsssi", $monto, $fecha_pago, $metodo_pago, $condicion_comercial, $id);

    if ($stmt_update->execute()) {
        $_SESSION['message'] = "Pago actualizado exitosamente.";
        header("Location: index_pagos.php");
        exit;
    } else {
        $_SESSION['message'] = "Error al actualizar el pago.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pago</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Editar Pago</h1>
    <form method="POST">
        <div class="form-group">
            <label for="monto">Monto:</label>
            <input type="number" name="monto" id="monto" class="form-control" step="0.01" value="<?php echo $pago['monto']; ?>" required>
        </div>
        <div class="form-group">
            <label for="fecha_pago">Fecha de Pago:</label>
            <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" value="<?php echo $pago['fecha_pago']; ?>" required>
        </div>
        <div class="form-group">
            <label for="metodo_pago">Método de Pago:</label>
            <input type="text" name="metodo_pago" id="metodo_pago" class="form-control" value="<?php echo $pago['metodo_pago']; ?>">
        </div>
        <div class="form-group">
            <label for="condicion_comercial">Condición Comercial:</label>
            <select name="condicion_comercial" id="condicion_comercial" class="form-control" required>
                <option value="">Seleccione una condición</option>
                <option value="Persona Física" <?php echo $pago['condicion_comercial'] === 'Persona Física' ? 'selected' : ''; ?>>Persona Física</option>
                <option value="Simplificado" <?php echo $pago['condicion_comercial'] === 'Simplificado' ? 'selected' : ''; ?>>Simplificado</option>
                <option value="Informal" <?php echo $pago['condicion_comercial'] === 'Informal' ? 'selected' : ''; ?>>Informal</option>
                <option value="Informal" <?php echo $pago['condicion_comercial'] === 'Empresa' ? 'selected' : ''; ?>>Empresa</option>
                
            </select>
        </div>
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
        <a href="index_pagos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>

