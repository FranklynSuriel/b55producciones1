<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/load.php'; // Asegúrate de incluir el archivo de conexión

// Verificar si la conexión existe
if (!isset($conexion) || !$conexion) {
    die("Error: No se pudo establecer la conexión a la base de datos.");
}

// Consulta para obtener todos los presupuestos
$query = "SELECT p.id AS presupuesto_id, c.client_name, c.client_company, c.created_at, p.project_name
          FROM presupuesto p 
          JOIN clientes c ON p.client_id = c.id";
$result = mysqli_query($conexion, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Presupuestos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    
<div class="container mt-4">
   
    <div class="container mt-5">
        <h1>Lista de Presupuestos</h1>
        <a href="crear_presupuesto.php" class="btn btn-success mb-3">Crear Nuevo Presupuesto</a>
    <ul class="list-group">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>
                    <strong><?php echo htmlspecialchars($row['project_name']); ?></strong> - 
                    <?php echo htmlspecialchars($row['client_name']); ?> (<?php echo htmlspecialchars($row['client_company']); ?>)
                    <br>Fecha de creación: <?php echo htmlspecialchars($row['created_at']); ?>
                </span>
                <div>
                    <a href="ver_presupuesto.php?id=<?php echo $row['presupuesto_id']; ?>" class="btn btn-info btn-sm">
                        Ver
                    </a>
                    <a href="editar_presupuesto.php?id=<?php echo $row['presupuesto_id']; ?>" class="btn btn-warning btn-sm">
                        Editar
                    </a>
                   
                    </a>
                    <a href="eliminar_presupuesto.php?id=<?php echo $row['presupuesto_id']; ?>" 
                       class="btn btn-danger btn-sm" 
                       onclick="return confirm('¿Estás seguro de que deseas eliminar este presupuesto?');">
                        Eliminar
                    </a>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

<div class="text-center">
    <a href="home.php" class="btn btn-primary mt-3">Volver al Inicio</a>
</div>

</html>
