<?php
require_once 'includes/load.php';

if (isset($_GET['q'])) {
    $q = trim($_GET['q']); // Eliminar espacios en blanco alrededor del término de búsqueda

    if (!empty($q)) {
        try {
            // Preparar la consulta para evitar inyecciones SQL
            $query = $db->prepare("
                SELECT id, project_name, total_presupuestado 
                FROM presupuesto 
                WHERE project_name LIKE ?
            ");
            $searchTerm = "%{$q}%";
            $query->bind_param("s", $searchTerm);
            $query->execute();
            $result = $query->get_result();

            $presupuestos = [];
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $presupuestos[] = [
                        'id' => $row['id'],
                        'project_name' => $row['project_name'],
                        'total_presupuestado' => number_format((float)$row['total_presupuestado'], 2, '.', '')
                    ];
                }
            }

            // Enviar resultados como JSON
            echo json_encode([
                'success' => true,
                'data' => $presupuestos
            ]);
        } catch (Exception $e) {
            // Capturar errores y devolver respuesta JSON
            echo json_encode([
                'success' => false,
                'message' => 'Error en la consulta: ' . $e->getMessage()
            ]);
        }
    } else {
        // Si el término de búsqueda está vacío
        echo json_encode([
            'success' => false,
            'message' => 'El término de búsqueda está vacío.'
        ]);
    }
    exit;
} else {
    // Si no se proporciona el parámetro 'q'
    echo json_encode([
        'success' => false,
        'message' => 'No se proporcionó un parámetro de búsqueda.'
    ]);
    exit;
}
