<?php
require_once 'includes/load.php';
require('libs/fpdf/fpdf.php');

// Verificar que se proporciona un ID válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó un ID de factura válido.");
}

$id = (int)$_GET['id'];

// Consulta para obtener los datos de la factura, cliente y presupuesto
$query = "SELECT f.*, c.client_name, c.client_company, c.client_email, c.client_phone,
                 p.project_name, p.total_presupuestado, p.global_total, p.imprevisto, p.impuesto
          FROM facturas f
          JOIN clientes c ON f.cliente_id = c.id
          JOIN presupuesto p ON f.presupuesto_id = p.id
          WHERE f.id = ?";
$stmt_factura = $db->prepare($query);
$stmt_factura->bind_param("i", $id);
$stmt_factura->execute();
$result_factura = $stmt_factura->get_result();

if ($result_factura->num_rows === 0) {
    die("Error: No se encontró la factura.");
}

$factura = $result_factura->fetch_assoc();

// Obtener la cantidad de veces que aparece el project_name
$query_cantidad = "SELECT COUNT(*) as cantidad 
                   FROM presupuesto 
                   WHERE project_name = ?";
$stmt_cantidad = $db->prepare($query_cantidad);
$stmt_cantidad->bind_param("s", $factura['project_name']);
$stmt_cantidad->execute();
$result_cantidad = $stmt_cantidad->get_result();
$cantidad = $result_cantidad->fetch_assoc()['cantidad'];

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

$pdf->Ln(40);

// Información del cliente y proyecto
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 6, utf8_decode('Cliente: ') . utf8_decode($factura['client_name']), 0, 1);
$pdf->Cell(0, 6, utf8_decode('Empresa: ') . utf8_decode($factura['client_company']), 0, 1);
$pdf->Cell(0, 6, utf8_decode('Email: ') . utf8_decode($factura['client_email']), 0, 1);
$pdf->Cell(0, 6, utf8_decode('Teléfono: ') . utf8_decode($factura['client_phone']), 0, 1);
$pdf->Cell(0, 6, utf8_decode('Proyecto: ') . utf8_decode($factura['project_name']), 0, 1);
$fecha_original = $factura['fecha_emision'];
$fecha = new DateTime($fecha_original);

$formatter = new IntlDateFormatter(
    'es_ES',
    IntlDateFormatter::FULL,
    IntlDateFormatter::NONE,
    'America/Santo_Domingo',
    IntlDateFormatter::GREGORIAN,
    "d 'de' MMMM 'de' yyyy"
);
$fecha_formateada = $formatter->format($fecha);
$pdf->Cell(0, 6, utf8_decode('Fecha de emisión: ') . utf8_decode($fecha_formateada), 0, 1);
$pdf->Ln(10);

// Título principal
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetFillColor(254, 195, 13);
$pdf->Cell(0, 10, utf8_decode('COTIZACION'), 1, 1, 'C', true);

// Tabla de detalles
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 10, 'Cantidad', 1, 0, 'C');
$pdf->Cell(100, 10, 'Concepto', 1, 0, 'C');
$pdf->Cell(30, 10, 'ITBIS', 1, 0, 'C');
$pdf->Cell(40, 10, 'Valor', 1, 1, 'C');

// Mostrar los valores
$pdf->SetFont('Arial', '', 12);
$concepto = $factura['project_name'];
$itbis = $factura['impuesto'];
$valor = $factura['global_total'] + $factura['imprevisto'];
$pdf->Ln(10);
$pdf->Cell(20, 10, $cantidad, 0, 0, 'C');
$pdf->Cell(90, 10, utf8_decode($concepto), 0, 0, 'C');
$pdf->Cell(30, 10, '$' . number_format($itbis, 2), 0, 0, 'R');
$pdf->Cell(40, 10, '$' . number_format($valor, 2), 0, 1, 'R');

$pdf->Ln(50);
// Totales de la factura
$pdf->SetFillColor(254, 195, 13);
$pdf->Cell(0, 6, 'Totales:',1, 1, 'R', true);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 6, 'Subtotal: $' . number_format($valor, 2), 0, 1, 'R');
$pdf->Cell(0, 6, 'Impuestos: $' . number_format($itbis, 2), 0, 1, 'R');
$pdf->Cell(0, 6, 'Total: $' . number_format($factura['total_presupuestado'], 2), 0, 1, 'R');

// Cuadro para comentario
$pdf->SetY(-80); // Ajustar para que quede encima del logo
$pdf->SetFont('Arial', 'I', 12);
$pdf->SetFillColor(230, 230, 230); // Color de fondo gris claro
$pdf->MultiCell(0, 10, utf8_decode("Observaciones:\nNo de Cuenta Bancaria RD$:Banco de Reservas  9606245968\n No de Cuenta Bancaria USD$: Banco BHD 08187560021

."), 1, 'L', true);

// Logo inferior
$pdf->SetY(-25);
$footer_logo = 'libs/img/firma_billy.jpg';
if (file_exists($footer_logo)) {
    $pdf->Image($footer_logo, 30, $pdf->GetY(), 130); // Logo inferior
}

// Salida del PDF
$pdf->Output('I', 'Cotizacion_' . $factura['project_name'] . '.pdf');
?>
