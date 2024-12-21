<?php
require_once 'includes/load.php';

if (isset($_GET['q'])) {
    $q = $db->escape($_GET['q']); // Escapar entrada del usuario

    // Consulta para buscar proyectos que coincidan
    $query = "SELECT id, project_name, total_presupuestado 
              FROM presupuesto 
              WHERE project_name LIKE '%$q%'";

    $result = $db->query($query);

    $presupuestos = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $presupuestos[] = [
                'id' => $row['id'],
                'nombre' => $row['project_name'], // Cambiar clave a 'nombre' para coincidir con el JS
                'presupuesto_asignado' => number_format($row['total_presupuestado'], 2, '.', '')
            ];
        }
    }

    echo json_encode($presupuestos);
    exit;
}
?>
