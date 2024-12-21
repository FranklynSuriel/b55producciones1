<?php
require_once 'includes/load.php';
require('libs/fpdf/fpdf.php');

// Validar el ID del pago
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó un ID válido.");
}

$id = (int)$_GET['id'];

// Consultar los detalles del pago
$query = "
    SELECT p.*, f.numero_factura, c.client_name, c.client_company, 
           p.condicion_comercial, pres.project_name
    FROM pagos p
    JOIN facturas f ON p.factura_id = f.id
    JOIN clientes c ON f.cliente_id = c.id
    JOIN presupuesto pres ON f.presupuesto_id = pres.id
    WHERE p.id = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: No se encontró el registro.");
}

$pago = $result->fetch_assoc();

// Cálculos
$monto = $pago['monto'];
$itbis = $monto * 0.18; // 18% de ITBIS

// Determinar la retención según la condición comercial
if ($pago['condicion_comercial'] === 'Informal') {
    $retencion = $monto * 0.10; // Retención del 10%
} elseif ($pago['condicion_comercial'] === 'Persona Física') {
    $retencion = $monto * 0.18; // Retención del 18%
} elseif ($pago['condicion_comercial'] === 'Simplificado') {
    $retencion = $itbis + ($monto * 0.10); // Retención adicional
} else {
    $retencion = 0; // Sin retención
}

// Total (Monto + ITBIS)
$monto_total = $monto + $itbis;

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();

// Agregar logo
$logo_path = 'libs/img/logo_billy.jpg';
if (file_exists($logo_path)) {
    $pdf->Image($logo_path, 10, 10, 120);
}

$pdf->Ln(35);

// Información principal
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, utf8_decode('Santo Domingo, Rep. Dom.'), 0, 1, 'R');

// Formatear la fecha en español
setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');
$fecha_original = $pago['fecha_pago'];
$timestamp = strtotime($fecha_original);
$fecha_formateada = strftime('%e de %B de %Y', $timestamp); // Ejemplo: 5 de noviembre de 2024

$pdf->Cell(0, 10, utf8_decode($fecha_formateada), 0, 1, 'R');
$pdf->Cell(0, 10, utf8_decode('Señores:'), 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('DIRECCION GENERAL DE IMPUESTOS INTERNOS'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, utf8_decode('Ave. Mexico No. 58'), 0, 1, 'L');
$pdf->Cell(0, 10, utf8_decode('Ciudad'), 0, 1, 'L');
$pdf->Cell(0, 10, utf8_decode('Al: ' . $pago['a_quien_dirigida']), 0, 1);
$pdf->Cell(0, 6, utf8_decode('Director General de Impuestos Internos'), 0, 1, 'L');
$pdf->Cell(0, 10, utf8_decode('Asunto: Certificación de Retención al 100% de ITBIS.'), 0, 1, 'L');

$pdf->Ln(5);

// Encabezado
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'CERTIFICACION DE RETENCION', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);

// Cuerpo de la carta
$carta = "
BILLY55 PRODUCCIONES SRL, sociedad comercial constituida de acuerdo con las leyes de la República Dominicana, con su domicilio social en el No. 21 de la avenida Theodoro Chasseriau, Sector el Millón, de esta ciudad, con RNC No. 13292559-9, debidamente representada por su Gerente, Sr. BILLY JOEL SEGURA, portador de la cédula No. 001-1521451-2, por medio de la presente, certifica que mediante {$pago['metodo_pago']} de fecha $fecha_formateada, por valor de RD$" . number_format($monto_total, 2) . ", procedimos a pagar la factura de fecha $fecha_formateada, por un monto de RD$" . number_format($monto, 2) . " más un ITBIS de RD$" . number_format($itbis, 2) . ", emitida por {$pago['beneficiario']}, portador de la cédula No. {$pago['cedula_beneficiario']}, por concepto de {$pago['concepto']} del proyecto '{$pago['project_name']}'. En tal virtud, procedimos a retener la suma de RD$" . number_format($retencion, 2) . ", correspondiente al 100% del ITBIS facturado. 
Sin más nada, queda de ustedes, muy atentamente.";
$pdf->MultiCell(0, 7, utf8_decode($carta));

// Firma
$pdf->Ln(20);
$pdf->Cell(30, 18, 'Billy Joel Segura', 0, 1, 'L');
$pdf->Cell(30, 0, 'Presidente Ejecutivo', 0, 1, 'L');

// Agregar logo inferior
$pdf->SetY(-70);
$footer_logo = 'libs/img/firma3.jpg';
if (file_exists($footer_logo)) {
    $pdf->Image($footer_logo, 0, $pdf->GetY(), 70);
}

// Generar el PDF
ob_clean();
$pdf->Output('I', 'Carta_Retencion_' . $pago['id'] . '.pdf');
?>
