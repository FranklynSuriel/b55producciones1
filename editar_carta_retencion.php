<?php
require_once 'includes/load.php';

// Validar el ID del pago
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó un ID válido.");
}

$id = (int)$_GET['id'];

// Consultar los detalles del pago
$query = "SELECT * FROM pagos WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: No se encontró el registro.");
}

$pago = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar la información
    $beneficiario = $db->escape($_POST['beneficiario']);
    $monto = (float)$_POST['monto'];
    $fecha_pago = $db->escape($_POST['fecha_pago']);
    $metodo_pago = $db->escape($_POST['metodo_pago']);

    $query_update = "
        UPDATE pagos 
        SET beneficiario = '$beneficiario', monto = '$monto', fecha_pago = '$fecha_pago', metodo_pago = '$metodo_pago' 
        WHERE id = '$id'
    ";

    if ($db->query($query_update)) {
        $_SESSION['message'] = "Pago actualizado correctamente.";
        header('Location: index_carta_retencion.php');
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
    <title>Editar Carta de Retención</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Editar Carta de Retención</h1>
    <form method="POST">
        <div class="form-group">
            <label for="beneficiario">Beneficiario:</label>
            <input type="text" name="beneficiario" id="beneficiario" class="form-control" value="<?php echo htmlspecialchars($pago['beneficiario']); ?>" required>
        </div>
        <div class="form-group">
            <label for="monto">Monto:</label>
            <input type="number" name="monto" id="monto" class="form-control" value="<?php echo $pago['monto']; ?>" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="fecha_pago">Fecha de Pago:</label>
            <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" value="<?php echo $pago['fecha_pago']; ?>" required>
        </div>
        <div class="form-group">
            <label for="metodo_pago">Método de Pago:</label>
            <input type="text" name="metodo_pago" id="metodo_pago" class="form-control" value="<?php echo htmlspecialchars($pago['metodo_pago']); ?>">
        </div>
        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="index_carta_retencion.php" class="btn btn-secondary">Volver</a>
    </form>
</div>
</body>
</html>
