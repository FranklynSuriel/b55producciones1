<?php
require_once 'includes/load.php';

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "ID de factura no especificado.";
    header('Location: lista_facturas.php');
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM facturas WHERE id = $id";
$result = $db->query($query);
$factura = $result->fetch_assoc();

if (!$factura) {
    $_SESSION['message'] = "Factura no encontrada.";
    header('Location: lista_facturas.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_factura = $db->escape($_POST['numero_factura']);
    $estado = $db->escape($_POST['estado']);
    $total = (float)$_POST['total'];

    $update_query = "
        UPDATE facturas 
        SET numero_factura = '$numero_factura', estado = '$estado', total = $total 
        WHERE id = $id
    ";

    if ($db->query($update_query)) {
        $_SESSION['message'] = "Factura actualizada correctamente.";
        header('Location: lista_facturas.php');
        exit;
    } else {
        $_SESSION['message'] = "Error al actualizar la factura.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Factura</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Editar Factura</h1>
    <form action="" method="POST">
        <div class="form-group">
            <label for="numero_factura">Número de Factura:</label>
            <input type="text" id="numero_factura" name="numero_factura" class="form-control" value="<?php echo $factura['numero_factura']; ?>" required>
        </div>
        <div class="form-group">
            <label for="estado">Estado:</label>
            <select id="estado" name="estado" class="form-control">
                <option value="pendiente" <?php echo $factura['estado'] === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                <option value="pagada" <?php echo $factura['estado'] === 'pagada' ? 'selected' : ''; ?>>Pagada</option>
            </select>
        </div>
        <div class="form-group">
            <label for="total">Total:</label>
            <input type="number" id="total" name="total" step="0.01" class="form-control" value="<?php echo $factura['total']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="lista_facturas.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
