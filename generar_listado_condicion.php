<?php
// Incluir FPDF y otros recursos
require_once('libs/fpdf/fpdf.php');

// Recibir parámetros de fechas
if (!isset($_POST['start_date']) || !isset($_POST['end_date'])) {
    die("Error: Las fechas no fueron proporcionadas.");
}

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "b55producciones");

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Consulta SQL
$sql = "
SELECT 
    p.condicion_comercial AS tipo_pago, 
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

// Agrupar resultados por tipo de pago
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[$row['tipo_pago']][] = $row;
}

$stmt->close();
$conexion->close();

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();

// Logo superior
$logo_path = 'libs/img/logo_billy.jpg';
if (file_exists($logo_path)) {
    $pdf->Image($logo_path, 10, 10, 120); // Logo superior
}

// Título principal
$pdf->Ln(40);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Reporte de Condición Comercial'), 0, 1, 'C');

// Rango de fechas
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 6, 'Rango de fechas: ' . $start_date . ' a ' . $end_date, 0, 1, 'C');

// Datos agrupados por condición comercial
foreach ($data as $tipo_pago => $pagos) {
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(254, 195, 13);
    $pdf->Cell(0, 10, utf8_decode('Condición Comercial: ') . utf8_decode($tipo_pago), 1, 1, 'L', true);

    // Tabla de datos
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(70, 8, 'Beneficiario', 1, 0);
    $pdf->Cell(40, 8, 'Monto', 1, 0, 'L');
    $pdf->Cell(40, 8, 'Fecha de Pago', 1, 0);
    $pdf->Cell(40, 8, 'Numero de Factura', 1, 1);

    foreach ($pagos as $pago) {
        $pdf->Cell(70, 8, utf8_decode($pago['beneficiario']), 1, 0);
        $pdf->Cell(40, 8, '$' . number_format($pago['monto'], 2), 1, 0, 'L');
        $pdf->Cell(40, 8, $pago['fecha_pago'], 1, 0);
        $pdf->Cell(40, 8, utf8_decode($pago['numero_factura']), 1, 1);
    }
}



// Generar el archivo PDF
$pdf->Output('I', 'Reporte_Condicion_Comercial.pdf');
