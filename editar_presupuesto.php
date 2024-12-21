<?php
require_once 'includes/load.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó un ID válido.");
}

$id = (int) $_GET['id'];

// Obtener datos del presupuesto
$query_presupuesto = "SELECT * FROM presupuesto WHERE id = {$id} LIMIT 1";
$result_presupuesto = $db->query($query_presupuesto);

if ($result_presupuesto->num_rows === 0) {
    die("Error: No se encontró el presupuesto.");
}

$presupuesto = $result_presupuesto->fetch_assoc();

// Obtener servicios asociados al presupuesto
$query_servicios = "SELECT * FROM servicios_presupuesto WHERE presupuesto_id = {$id}";
$result_servicios = $db->query($query_servicios);
$servicios = [];
while ($row = $result_servicios->fetch_assoc()) {
    $servicios[$row['partida']][] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y asignar valores
    
  
    $project_name = $db->escape($_POST['project_name'] ?? '');
    $total_presupuestado = $db->escape($_POST['total_presupuestado'] ?? '0.00');
    $due_date = $db->escape($_POST['due_date'] ?? '');

    // Asegúrate de que total_presupuestado sea un decimal válido
    if (!is_numeric($total_presupuestado)) {
        $total_presupuestado = '0.00';
    }

    // Actualizar datos del presupuesto principal
    $update_presupuesto = "UPDATE presupuesto 
    SET project_name = '{$project_name}', 
        total_presupuestado = '{$total_presupuestado}', 
        due_date = '{$due_date}' 
    WHERE id = {$id}";
    

    if ($db->query($update_presupuesto)) {
        // Actualizar servicios
        $db->query("DELETE FROM servicios_presupuesto WHERE presupuesto_id = {$id}");

        foreach ($_POST['descripcion_servicio'] as $partida => $descripciones) {
            foreach ($descripciones as $index => $descripcion) {
                $cantidad = (int) ($_POST['cantidad_servicio'][$partida][$index] ?? 0);
                $precio = (float) ($_POST['precio_servicio'][$partida][$index] ?? 0.0);
        
                $db->query("INSERT INTO servicios_presupuesto (presupuesto_id, partida, descripcion, cantidad, precio) 
                            VALUES ({$id}, '{$partida}', '{$descripcion}', {$cantidad}, {$precio})");
            }
        }
        

        $_SESSION['message'] = "Presupuesto actualizado con éxito.";
        header("Location: index_presupuesto.php");
        exit;
    } else {
        $_SESSION['message'] = "Error al actualizar el presupuesto.";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <form action="" method="POST" id="editarPresupuestoForm">

    <title>Editar Presupuesto</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        // Formatear números con comas como separador de miles
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Agregar una nueva fila de servicio
        function agregarServicio(tablaId, partida) {
            const tablaServicios = document.getElementById(tablaId).querySelector('tbody');
            const nuevaFila = tablaServicios.insertRow(-1);
            nuevaFila.innerHTML = `
                <td><input type="text" name="descripcion_servicio[${partida}][]" placeholder="Descripción del servicio" required></td>
                <td><input type="number" name="cantidad_servicio[${partida}][]" value="1" min="1" oninput="calcularSubtotal(this)" required></td>
                <td><input type="number" name="precio_servicio[${partida}][]" step="0.01" min="0" oninput="calcularSubtotal(this)" required></td>
                <td><span class="subtotal">0.00</span></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarServicio(this)">Eliminar</button></td>
            `;
        }

        // Eliminar una fila de servicio
        function eliminarServicio(boton) {
            const fila = boton.closest('tr');
            const partidaDiv = boton.closest('.tab-pane');
            fila.remove();
            calcularTotalPartida(partidaDiv);
        }

        // Calcular el subtotal de una fila
        function calcularSubtotal(input) {
            const fila = input.closest('tr');
            const cantidad = parseFloat(fila.querySelector('input[name^="cantidad_servicio"]').value) || 0;
            const precio = parseFloat(fila.querySelector('input[name^="precio_servicio"]').value) || 0;
            const subtotal = cantidad * precio;

            fila.querySelector('.subtotal').textContent = formatNumber(subtotal.toFixed(2));
            const partidaDiv = fila.closest('.tab-pane');
            calcularTotalPartida(partidaDiv);
        }

        // Calcular el total de una partida
        function calcularTotalPartida(partidaDiv) {
            const filas = partidaDiv.querySelectorAll('.tabla-servicios tbody tr');
            let totalPartida = 0;

            filas.forEach(fila => {
                const subtotal = parseFloat(fila.querySelector('.subtotal').textContent.replace(/,/g, '')) || 0;
                totalPartida += subtotal;
            });

            partidaDiv.querySelector('.total-partida').textContent = formatNumber(totalPartida.toFixed(2));
            calcularTotalGlobal();
        }

        // Calcular el total global
        function calcularTotalGlobal() {
            const totalesPartida = document.querySelectorAll('.total-partida');
            let totalGlobal = 0;

            totalesPartida.forEach(total => {
                totalGlobal += parseFloat(total.textContent.replace(/,/g, '')) || 0;
            });

            document.getElementById('global_total').textContent = formatNumber(totalGlobal.toFixed(2));
            calcularTotalPresupuestado(); // Actualiza el total presupuestado
        }

        // Calcular el imprevisto (5% del total global)
        function calcularImprevisto() {
            const totalGlobal = parseFloat(document.getElementById('global_total').textContent.replace(/,/g, '')) || 0;
            const imprevisto = totalGlobal * 0.05;
            document.getElementById('imprevisto').textContent = formatNumber(imprevisto.toFixed(2));
            return imprevisto;
        }

        // Calcular el impuesto (18% del total global + imprevisto)
        function calcularImpuesto() {
            const totalGlobal = parseFloat(document.getElementById('global_total').textContent.replace(/,/g, '')) || 0;
            const imprevisto = calcularImprevisto();
            const impuesto = (totalGlobal + imprevisto) * 0.18;
            document.getElementById('impuesto').textContent = formatNumber(impuesto.toFixed(2));
            return impuesto;
        }

        // Calcular el total presupuestado
        function calcularTotalPresupuestado() {
            const totalGlobal = parseFloat(document.getElementById('global_total').textContent.replace(/,/g, '')) || 0;
            const imprevisto = calcularImprevisto();
            const impuesto = calcularImpuesto();
            const totalPresupuestado = totalGlobal + imprevisto + impuesto;

            document.getElementById('total_presupuestado').textContent = formatNumber(totalPresupuestado.toFixed(2));
        }

        // Inicializar cálculos al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.tabla-servicios tbody tr').forEach(row => calcularSubtotal(row.querySelector('input')));
            calcularTotalGlobal();
        });

        // Asignar valor calculado al campo oculto antes de enviar
        document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('editarPresupuestoForm').addEventListener('submit', function () {
        document.getElementById('total_presupuestado_hidden').value = document.getElementById('total_presupuestado').textContent.replace(/,/g, '');
    });
});

var_dump($_POST['total_presupuestado']);
exit;

    </script>
</head>
<body>
<div class="container mt-5">
    <h1>Editar Presupuesto</h1>

    <form action="" method="POST">
        <!-- Información del Cliente -->
        <div class="form-group">
            <label>Nombre del Proyecto:</label>
            <input type="text" name="project_name" class="form-control" value="<?php echo htmlspecialchars($presupuesto['project_name']); ?>">
        </div>
        <div class="form-group">
            <label>Fecha de Creación presupuesto:</label>
            <input type="date" name="creation_date" class="form-control" value="<?php echo htmlspecialchars($presupuesto['creation_date']); ?>">
        </div>
        <div class="form-group">
            <label>Fecha de Vencimiento Presupuesto:</label>
            <input type="date" name="due_date" class="form-control" value="<?php echo htmlspecialchars($presupuesto['due_date']); ?>">
        </div>

        <!-- Campo oculto para total presupuestado -->
        <input type="hidden" name="total_presupuestado" id="total_presupuestado_hidden">

        <!-- Tabs de Partidas -->
        <ul class="nav nav-tabs" id="partidaTabs" role="tablist">
            <?php 
            $partidas = ["preproduccion", "produccion", "direccion", "fotografia", "arte", "ayb", "miscelaneos", 
                         "sonido", "vestuarioymaquillaje", "talento", "gastos_produccion", "edicionypostproduccion"];
            foreach ($partidas as $index => $partida): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" id="tab-<?php echo $index; ?>" data-toggle="tab" href="#partida-<?php echo $index; ?>" role="tab">
                        <?php echo $partida; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content border p-3">
            <?php foreach ($partidas as $index => $partida): ?>
                <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>" id="partida-<?php echo $index; ?>" role="tabpanel">
                    <h3><?php echo ucfirst($partida); ?></h3>
                    <button type="button" class="btn btn-primary btn-sm mb-2" onclick="agregarServicio('tabla-<?php echo $index; ?>', '<?php echo $partida; ?>')">Agregar Servicio</button>
                    <table class="table tabla-servicios" id="tabla-<?php echo $index; ?>">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($servicios[$partida])): ?>
                                <?php foreach ($servicios[$partida] as $servicio): ?>
                                    <tr>
                                        <td><input type="text" name="descripcion_servicio[<?php echo $partida; ?>][]" value="<?php echo htmlspecialchars($servicio['descripcion']); ?>" required></td>
                                        <td><input type="number" name="cantidad_servicio[<?php echo $partida; ?>][]" value="<?php echo htmlspecialchars($servicio['cantidad']); ?>" min="1" oninput="calcularSubtotal(this)" required></td>
                                        <td><input type="number" name="precio_servicio[<?php echo $partida; ?>][]" value="<?php echo htmlspecialchars($servicio['precio']); ?>" step="0.01" min="0" oninput="calcularSubtotal(this)" required></td>
                                        <td><span class="subtotal">$<?php echo number_format($servicio['subtotal'], 2); ?></span></td>
                                        <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarServicio(this)">Eliminar</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <p>Total de la Partida: $<span class="total-partida">0.00</span></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Total Global -->
        <fieldset class="border p-3 mt-3">
            <legend class="w-auto">Subtotales</legend>
            <p>Total global partidas: $<span id="global_total">0.00</span></p>
            <p>Imprevisto (5%): $<span id="imprevisto">0.00</span></p>
            <p>Impuesto (18%): $<span id="impuesto">0.00</span></p>
        </fieldset>

        <!-- Total Presupuestado -->
        <fieldset class="border p-3 mt-3">
            <legend class="w-auto">Total Presupuestado</legend>
            <p>Total Presupuestado: $<span id="total_presupuestado">0.00</span></p>
        </fieldset>

        <button type="submit" class="btn btn-success">Guardar Cambios</button>
        <a href="index_presupuesto.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
