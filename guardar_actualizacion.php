<?php
include 'includes/load.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_presupuesto = intval($_POST['client_id']);
    $project_name = mysqli_real_escape_string($conexion, $_POST['project_name']);
    $details = mysqli_real_escape_string($conexion, $_POST['details']);

    // Actualizar presupuesto
    $query = "UPDATE presupuesto 
              SET project_name = '$project_name', details = '$details'
              WHERE id = $id_presupuesto";

    if (mysqli_query($conexion, $query)) {
        echo "Presupuesto actualizado correctamente.";
        header("Location: ver_presupuesto.php");
    } else {
        echo "Error al actualizar: " . mysqli_error($conexion);
    }
}
?>
