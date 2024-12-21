<?php
session_start();
require_once('includes/load.php');

// Asegurarse de que la sesión esté iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos del formulario
    $client_name = $db->escape($_POST['client_name']);
    $client_company = $db->escape($_POST['client_company']);
    $client_email = $db->escape($_POST['client_email']);
    $client_phone = $db->escape($_POST['client_phone']);
    $project_name = $db->escape($_POST['project_name']);
    $creation_date = $db->escape($_POST['creation_date']);
    $global_total = $db->escape($_POST['global_total']);
    $impuesto = $db->escape($_POST['impuesto']);
    $imprevisto = $db->escape($_POST['imprevisto']);
    $total_presupuestado = $db->escape($_POST['total_presupuestado']);
    $due_date = $db->escape($_POST['due_date']);

    // Validación de campos obligatorios
    if (empty($client_name) || empty($project_name) || empty($creation_date)) {
        $_SESSION['message'] = 'Por favor completa los campos obligatorios.';
        header('Location: crear_presupuesto.php');
        exit;
    }

    // Insertar cliente en la base de datos
    $query = "INSERT INTO clientes (client_name, client_company, client_email, client_phone) 
              VALUES ('$client_name', '$client_company', '$client_email', '$client_phone')";
    if (!$db->query($query)) {
        $_SESSION['message'] = 'Error al insertar cliente: ' . $db->error;
        header('Location: crear_presupuesto.php');
        exit;
    }
    $client_id = $db->insert_id();

    // Insertar presupuesto (incluyendo total global, impuesto y total presupuestado)
    $query_presupuesto = "INSERT INTO presupuesto (client_id, project_name, creation_date, global_total, imprevisto, impuesto, total_presupuestado, due_date) 
                          VALUES ('$client_id', '$project_name', '$creation_date', '$global_total', '$imprevisto', '$impuesto', '$total_presupuestado', '$due_date')";
    if (!$db->query($query_presupuesto)) {
        $_SESSION['message'] = 'Error al guardar el presupuesto: ' . $db->error;
        header('Location: crear_presupuesto.php');
        exit;
    }
    $presupuesto_id = $db->insert_id();

    // Guardar servicios por partida
    $partidas = ["preproduccion", "produccion", "direccion", "fotografia", "arte", "ayb", "miscelaneos", 
                 "sonido", "vestuarioymaquillaje", "talento", "gastos_produccion", "edicionypostproduccion"];

    foreach ($partidas as $partida) {
        if (isset($_POST["descripcion_servicio"][$partida])) {
            $descripciones = $_POST["descripcion_servicio"][$partida];
            $cantidades = $_POST["cantidad_servicio"][$partida];
            $precios = $_POST["precio_servicio"][$partida];

            foreach ($descripciones as $index => $descripcion) {
                $descripcion = $db->escape($descripcion);
                $cantidad = (int)$cantidades[$index];
                $precio = (float)$precios[$index];
                $subtotal = $cantidad * $precio;

                $query_servicio = "INSERT INTO servicios_presupuesto (presupuesto_id, partida, descripcion, cantidad, precio) 
                                   VALUES ('$presupuesto_id', '$partida', '$descripcion', '$cantidad', '$precio')";
                if (!$db->query($query_servicio)) {
                    $_SESSION['message'] = 'Error al guardar servicio: ' . $db->error;
                    header('Location: crear_presupuesto.php');
                    exit;
                }
            }
        }
    }

    // Confirmación de éxito
    $_SESSION['message'] = 'Presupuesto creado exitosamente.';
    header('Location: crear_presupuesto.php?id=' . $presupuesto_id);
    exit;
} else {
    $_SESSION['message'] = 'Error al procesar el presupuesto.';
    header('Location: crear_presupuesto.php');
    exit;
}
?>
