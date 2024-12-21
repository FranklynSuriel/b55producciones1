<?php
require_once 'includes/load.php';
require('libs/fpdf/fpdf.php');

// Validar filtro
$cliente_id = isset($_GET['cliente_id']) ? (int)$_GET['cliente_id'] : 0;
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

// Consulta para obtener los pagos
$query = "SELECT p.id AS pago_id, p.fecha_pago, p.monto, c.client_name, f.numero_factura 
          FROM pagos p
          JOIN facturas f ON p.factura_id = f.id
          JOIN clientes c ON f.cliente_id = c.id
          WHERE 1=1";

if ($cliente_id > 0) {
    $query .= " AND c.id = '$cliente_id'";
}
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $query .= " AND p.fecha_pago BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}
$query .= " ORDER BY p.fecha_pago DESC";

$result = $db->query($query);

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Reporte de Pagos'), 0, 1, 'C');
$pdf->Ln(10);

// Agregar encabezado de tabla
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Cliente', 1, 0, 'C');
$pdf->Cell(40, 10, 'Factura', 1, 0, 'C');
$pdf->Cell(50, 10, 'Fecha de Pago', 1, 0, 'C');
$pdf->Cell(40, 10, 'Monto', 1, 1, 'C');

// Agregar datos
$pdf->SetFont('Arial', '', 12);
$total_pagos = 0;
while ($pago = $result->fetch_assoc()) {
    $total_pagos += $pago['monto'];
    $pdf->Cell(60, 10, utf8_decode($pago['client_name']), 1, 0, 'C');
    $pdf->Cell(40, 10, $pago['numero_factura'], 1, 0, 'C');
    $pdf->Cell(50, 10, $pago['fecha_pago'], 1, 0, 'C');
    $pdf->Cell(40, 10, '$' . number_format($pago['monto'], 2), 1, 1, 'C');
}

// Total
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(150, 10, 'Total Pagos:', 1, 0, 'R');
$pdf->Cell(40, 10, '$' . number_format($total_pagos, 2), 1, 1, 'C');

// Descargar PDF
$pdf->Output('I', 'Reporte_Pagos.pdf');
?>
