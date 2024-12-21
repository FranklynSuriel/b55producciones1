<?php
require 'vendor/autoload.php'; // Asegúrate de tener DOMPDF instalado

use Dompdf\Dompdf;
use Dompdf\Options;

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "b55producciones");

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Recibir parámetros de fechas desde el formulario
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

// Consulta SQL para obtener los datos combinados de pagos y facturas
$sql = "
SELECT 
    p.condicion_comercial AS condicion_comercial, 
    p.beneficiario, 
    p.monto, 
    p.fecha_pago, 
    f.numero_factura
FROM 
    pagos p
JOIN 
    facturas f ON p.factura_id = f.id
WHERE 
    p.fecha_pago BETWEEN ? AND ?
ORDER BY 
    p.condicion_comercial, p.fecha_pago
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Agrupar resultados por condición comercial
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[$row['condicion_comercial']][] = $row;
}

$stmt->close();
$conexion->close();

// Generar HTML del reporte
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Pagos por Condición Comercial</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        h2 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Reporte de Pagos</h1>
    <p>Rango de fechas: <?= htmlspecialchars($start_date); ?> a <?= htmlspecialchars($end_date); ?></p>

    <?php foreach ($data as $condicion_comercial => $pagos): ?>
        <h2><?= htmlspecialchars($condicion_comercial); ?></h2>
        <table>
            <thead>
                <tr>
                    <th>Beneficiario</th>
                    <th>Monto</th>
                    <th>Fecha de Pago</th>
                    <th>Número de Factura</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagos as $pago): ?>
                    <tr>
                        <td><?= htmlspecialchars($pago['beneficiario']); ?></td>
                        <td><?= number_format($pago['monto'], 2); ?></td>
                        <td><?= htmlspecialchars($pago['fecha_pago']); ?></td>
                        <td><?= htmlspecialchars($pago['numero_factura']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

    <form method="POST" action="generar_listado_condicion.php">
        <input type="hidden" name="start_date" value="<?= htmlspecialchars($start_date); ?>">
        <input type="hidden" name="end_date" value="<?= htmlspecialchars($end_date); ?>">
        <button type="submit">Descargar PDF</button>
    </form>
</body>
</html>
<?php
$html = ob_get_clean();
echo $html;
