<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Presupuesto</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .total-partida, #total-global { font-weight: bold; }
        .nav-tabs .nav-link.active { background-color: #007bff; color: white; }
        .partida-container { margin-top: 20px; }
    </style>
    <script>
        // Función para formatear los números con coma como separador de miles
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Agregar un nuevo servicio a la tabla
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

        // Eliminar un servicio de la tabla
        function eliminarServicio(boton) {
            const fila = boton.closest('tr');
            const partidaDiv = boton.closest('.tab-pane');
            fila.remove();
            calcularTotalPartida(partidaDiv);
        }

        // Calcular el subtotal de una fila de servicio
        function calcularSubtotal(input) {
            const fila = input.closest('tr');
            const cantidadInput = fila.querySelector('input[name^="cantidad_servicio"]');
            const precioInput = fila.querySelector('input[name^="precio_servicio"]');

            if (!cantidadInput || !precioInput) {
                console.error("No se encuentran los inputs de cantidad o precio en la fila.");
                return;
            }

            const cantidad = parseFloat(cantidadInput.value) || 0;
            const precio = parseFloat(precioInput.value) || 0;
            const subtotal = cantidad * precio;

            fila.querySelector('.subtotal').textContent = formatNumber(subtotal.toFixed(2));

            const partidaDiv = fila.closest('.tab-pane');
            calcularTotalPartida(partidaDiv);
        }

        // Calcular el total de la partida
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
            const partidas = document.querySelectorAll('.total-partida');
            let totalGlobal = 0;

            partidas.forEach(total => {
                totalGlobal += parseFloat(total.textContent.replace(/,/g, '')) || 0;
            });

            document.getElementById('global_total').textContent = formatNumber(totalGlobal.toFixed(2));
            calcularTotalPresupuestado(); // Llama a la función principal para actualizar todo
        }

        // Calcular el imprevisto (5% del total global)
        function calcularImprevisto() {
            const totalGlobal = parseFloat(document.getElementById('global_total').textContent.replace(/,/g, '')) || 0;
            const imprevisto = totalGlobal * 0.05;

            document.getElementById('imprevisto').textContent = formatNumber(imprevisto.toFixed(2));
            return imprevisto; // Devuelve el valor calculado
        }

        // Calcular el impuesto (18% del total global + imprevisto)
        function calcularImpuesto() {
            const totalGlobal = parseFloat(document.getElementById('global_total').textContent.replace(/,/g, '')) || 0;
            const imprevisto = calcularImprevisto(); // Se asegura de que el imprevisto esté actualizado
            const impuesto = (totalGlobal + imprevisto) * 0.18;

            document.getElementById('impuesto').textContent = formatNumber(impuesto.toFixed(2));
            return impuesto; // Devuelve el valor calculado
        }

        // Calcular el total presupuestado
        function calcularTotalPresupuestado() {
            const totalGlobal = parseFloat(document.getElementById('global_total').textContent.replace(/,/g, '')) || 0;
            const imprevisto = calcularImprevisto();
            const impuesto = calcularImpuesto();

            const totalPresupuestado = totalGlobal + imprevisto + impuesto;
            document.getElementById('total_presupuestado').textContent = formatNumber(totalPresupuestado.toFixed(2));
        }

    </script>
</head>
<body>
<div class="container mt-4">
    <h1>Crear Presupuesto</h1>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form action="guardar_presupuesto.php" method="POST">
        <!-- Información del Cliente -->
        <fieldset class="border p-3 mb-3">
            <legend class="w-auto">Información del Cliente</legend>
            <div class="form-group">
                <label>Nombre del Cliente:</label>
                <input type="text" name="client_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Empresa:</label>
                <input type="text" name="client_company" class="form-control">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="client_email" class="form-control">
            </div>
            <div class="form-group">
                <label>Teléfono:</label>
                <input type="text" name="client_phone" class="form-control">
            </div>
            <div class="form-group">
                <label>Nombre del Proyecto:</label>
                <input type="text" name="project_name" class="form-control">
            </div>
            <div class="form-group">
                <label>Fecha de Creación presupuesto:</label>
                <input type="date" name="creation_date" class="form-control">
            </div>
            <div class="form-group">
                <label>Fecha de Vencimiento Presupuesto:</label>
                <input type="date" name="due_date" class="form-control">
            </div>
        </fieldset>

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
                        <tbody></tbody>
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
        
        <input type="hidden" name="global_total" id="global_total_hidden" value="...">
        <input type="hidden" name="imprevisto" id="imprevisto_hidden">
        <input type="hidden" name="impuesto" id="impuesto_hidden">
        <input type="hidden" name="total_presupuestado" id="total_presupuestado_hidden">

        <button type="submit" class="btn btn-success">Guardar Presupuesto</button>
    </form>
</div>

<div class="text-center">
    <a href="index_presupuesto.php" class="btn btn-primary mt-3">Volver a la lista de presupuestos</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.querySelector('form').addEventListener('submit', function(event) {
        document.getElementById('global_total_hidden').value = document.getElementById('global_total').textContent.replace(/,/g, '');
        document.getElementById('imprevisto_hidden').value = document.getElementById('imprevisto').textContent.replace(/,/g, '');
        document.getElementById('impuesto_hidden').value = document.getElementById('impuesto').textContent.replace(/,/g, '');
        document.getElementById('total_presupuestado_hidden').value = document.getElementById('total_presupuestado').textContent.replace(/,/g, '');
    });
</script>
</body>
</html>
