<?php
// Incluir FPDF y otros recursos
require_once('includes/load.php');
require('libs/fpdf/fpdf.php');
require('libs/fpdi/src/autoload.php');

// Validar el ID del presupuesto
if (!isset($_GET['id_presupuesto']) || empty($_GET['id_presupuesto'])) {
    die("Error: No se proporcionó un ID de presupuesto válido.");
}

$presupuesto_id = (int)$_GET['id_presupuesto'];

// Consultar información del cliente
$query_clientes = "SELECT c.* 
                   FROM clientes c 
                   JOIN presupuesto p ON c.id = p.client_id 
                   WHERE p.id = ?";
$stmt_clientes = $db->prepare($query_clientes);
$stmt_clientes->bind_param("i", $presupuesto_id);
$stmt_clientes->execute();
$result_clientes = $stmt_clientes->get_result();
$cliente = $result_clientes->fetch_assoc();

// Consultar información del presupuesto
$query_presupuesto = "SELECT * FROM presupuesto WHERE id = ?";
$stmt_presupuesto = $db->prepare($query_presupuesto);
$stmt_presupuesto->bind_param("i", $presupuesto_id);
$stmt_presupuesto->execute();
$result_presupuesto = $stmt_presupuesto->get_result();
$presupuesto = $result_presupuesto->fetch_assoc();

// Consultar los servicios del presupuesto
$query_servicios = "SELECT partida, SUM(subtotal) as total_partida 
                    FROM servicios_presupuesto 
                    WHERE presupuesto_id = ? 
                    GROUP BY partida";
$stmt_servicios = $db->prepare($query_servicios);
$stmt_servicios->bind_param("i", $presupuesto_id);
$stmt_servicios->execute();
$result_servicios = $stmt_servicios->get_result();
$servicios = $result_servicios->fetch_all(MYSQLI_ASSOC);

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();

// Ruta del primer logo
$logo_path = 'libs/img/logo_billy.jpg';
if (file_exists($logo_path)) {
    $pdf->Image($logo_path, 10, 10, 120); // Logo superior
} else {
    die("Error: No se encontró el archivo del logo en la ruta especificada.");
}

// Título principal
$pdf->Ln(40);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Detalles del Presupuesto'), 0, 1, 'C');

// Información del cliente
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(254, 195, 13);
$pdf->Cell(0, 10, utf8_decode('Información del Cliente:'), 1, 1, 'L', true);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 6, 'Nombre: ' . utf8_decode($cliente['client_name']), 0, 1);
$pdf->Cell(0, 6, 'Empresa: ' . utf8_decode($cliente['client_company']), 0, 1);
$pdf->Cell(0, 6, 'Fecha: ' . $cliente['created_at'], 0, 1);

// Información del presupuesto
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(254, 195, 13);
$pdf->Cell(0, 10, utf8_decode('Información del Presupuesto:'), 1, 1, 'L', true);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 6, 'Proyecto: ' . utf8_decode($presupuesto['project_name']), 0, 1);

// Servicios del presupuesto
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Partidas del Presupuesto:'),1, 1, 'L', true);
$pdf->SetFont('Arial', '', 12);

foreach ($servicios as $servicio) {
    $pdf->Cell(120, 6, utf8_decode($servicio['partida']), 1, 0);
    $pdf->Cell(0, 6, '$' . number_format($servicio['total_partida'], 2), 1, 1, 'R');
}

// Totales
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(254, 195, 13);
$pdf->Cell(0, 6, 'Totales:',1, 1, 'R', true);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 6, 'Subtotal: $' . number_format($presupuesto['global_total'], 2), 0, 1, 'R');
$pdf->Cell(0, 6, 'Imprevistos: $' . number_format($presupuesto['imprevisto'], 2), 0, 1, 'R');
$pdf->Cell(0, 6, 'Impuestos: $' . number_format($presupuesto['impuesto'], 2), 0, 1, 'R');
$pdf->Cell(0, 6, 'Total: $' . number_format($presupuesto['total_presupuestado'], 2), 0, 1, 'R');

// Logo inferior
$pdf->SetY(-30);
$footer_logo = 'libs/img/firma_billy.jpg';
if (file_exists($footer_logo)) {
    $pdf->Image($footer_logo, 30, $pdf->GetY(), 130); // Logo inferior
}

/// Obtener el nombre del proyecto y asegurarse de que sea válido para el sistema de archivos
$nombreProyecto = preg_replace('/[^A-Za-z0-9_\-]/', '_', $presupuesto['project_name']);

// Generar el archivo PDF con el nombre del proyecto
$pdf->Output('I', 'Presupuesto_' . $nombreProyecto . '.pdf');


?>
