<?php
require_once 'includes/load.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó un ID válido.");
}

$id = (int) $_GET['id'];

// Obtener datos del presupuesto
$query = "SELECT p.*, c.client_name, c.client_company 
          FROM presupuesto p 
          JOIN clientes c ON p.client_id = c.id 
          WHERE p.id = {$id} LIMIT 1";
$result = $db->query($query);

if ($result->num_rows === 0) {
    die("Error: No se encontró el presupuesto.");
}

$presupuesto = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Presupuesto</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Detalles del Presupuesto</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($presupuesto['project_name']); ?></h5>
            <p class="card-text"><strong>Cliente:</strong> <?php echo htmlspecialchars($presupuesto['client_name']); ?> (<?php echo htmlspecialchars($presupuesto['client_company']); ?>)</p>
            <p class="card-text"><strong>Total Presupuestado:</strong> $<?php echo number_format($presupuesto['total_presupuestado'], 2); ?></p>
            <p class="card-text"><strong>Fecha de Creación:</strong> <?php echo htmlspecialchars($presupuesto['creation_date']); ?></p>
            <p class="card-text"><strong>Fecha de Vencimiento:</strong> <?php echo htmlspecialchars($presupuesto['due_date']); ?></p>
            <div class="mt-4">
                <a href="index_presupuesto.php" class="btn btn-secondary">Volver</a>
                <!-- Botón para generar PDF -->
                <a href="generar_pdf.php?id_presupuesto=<?php echo $presupuesto['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-print"></i> Imprimir PDF
                </a>
                
            </div>
        </div>
    </div>
</div>
</body>
</html>
