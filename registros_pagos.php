<?php
require_once 'includes/load.php';

// Consultar facturas para el formulario
$query_facturas = "SELECT id, numero_factura, total, estado FROM facturas";
$result_facturas = $db->query($query_facturas);

// Consultar conceptos (partidas presupuestarias)
$query_partidas = "SELECT partida, descripcion, subtotal FROM servicios_presupuesto";
$result_partidas = $db->query($query_partidas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Pago</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        // Función para actualizar el monto según el concepto seleccionado
        function actualizarMontoPago() {
            const conceptoSelect = document.getElementById('concepto');
            const montoInput = document.getElementById('monto');
            const condicionComercialSelect = document.getElementById('condicion_comercial');
            const montoRealInput = document.getElementById('monto_real');

            // Obtener el monto de la opción seleccionada
            const selectedOption = conceptoSelect.options[conceptoSelect.selectedIndex];
            const monto = parseFloat(selectedOption.getAttribute('data-monto')) || 0;

            // Actualizar el monto mostrado
            montoInput.value = monto.toFixed(2);

            // Recalcular el monto real según la condición comercial
            actualizarMontoReal(monto, condicionComercialSelect.value);
        }

        // Función para calcular el monto real según la condición comercial
        function actualizarMontoReal(monto, condicionComercial) {
            const montoRealInput = document.getElementById('monto_real');
            let montoReal = monto;

            switch (condicionComercial) {
                case 'Informal':
                    montoReal = monto; // Retención del 10%
                    break;
                case 'Persona Física':
                    montoReal = monto; // Retención del 18%
                    break;
                case 'Simplificado':
                    const itbis = monto * 0.18; // ITBIS del 18%
                    const retencionAdicional = (monto ) * 0.10; // Retención adicional
                    montoReal = monto - retencionAdicional;
                    break;
            }

            // Mostrar el monto real calculado
            montoRealInput.value = montoReal.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const conceptoSelect = document.getElementById('concepto');
            const condicionComercialSelect = document.getElementById('condicion_comercial');

            // Eventos para actualizar valores en tiempo real
            conceptoSelect.addEventListener('change', actualizarMontoPago);
            condicionComercialSelect.addEventListener('change', () => {
                const monto = parseFloat(document.getElementById('monto').value) || 0;
                actualizarMontoReal(monto, condicionComercialSelect.value);
            });
        });
    </script>
</head>
<body>
<div class="container mt-5">
    <h1>Registrar Pago</h1>

    <form action="guardar_pagos.php" method="POST">
        <div class="form-group">
            <label for="factura_id">Factura Asociada:</label>
            <select name="factura_id" id="factura_id" class="form-control" required>
                <option value="">Seleccione una factura</option>
                <?php while ($factura = $result_facturas->fetch_assoc()): ?>
                    <option value="<?php echo $factura['id']; ?>">
                        <?php echo htmlspecialchars($factura['numero_factura']); ?> - $<?php echo number_format($factura['total'], 2); ?> 
                        (<?php echo ucfirst(htmlspecialchars($factura['estado'])); ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="a_quien_dirigida">A quien Va Dirigido la Certificacion :</label>
            <input type="text" name="a_quien_dirigida" id="a_quien_dirigida" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="beneficiario">Beneficiario:</label>
            <input type="text" name="beneficiario" id="beneficiario" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="cedula_beneficiario">Cedula/RNC:</label>
            <input type="text" name="cedula_beneficiario" id="cedula_beneficiario" class="form-control" required>
        </div>
        
    
    <div class="form-group">
        <label for="factura_beneficiario">Comprobante:</label>
        <input type="text" name="factura_beneficiario" id="factura_beneficiario" class="form-control" required>
    </div>


        <div class="form-group">
            <label for="concepto">Concepto:</label>
            <select name="concepto" id="concepto" class="form-control" required>
                <option value="">Seleccione un concepto</option>
                <?php while ($partida = $result_partidas->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($partida['partida']); ?>" data-monto="<?php echo $partida['subtotal']; ?>">
                        <?php echo htmlspecialchars($partida['partida'] . " - " . $partida['descripcion']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="monto">Monto a Pagar:</label>
            <input type="text" name="monto" id="monto" class="form-control" readonly required>
        </div>

        <div class="form-group">
            <label for="monto_real">Monto Real:</label>
            <input type="text" name="monto_real" id="monto_real" class="form-control" readonly required>
        </div>

        <div class="form-group">
            <label for="condicion_comercial">Condición Comercial:</label>
            <select name="condicion_comercial" id="condicion_comercial" class="form-control" required>
                <option value="">Seleccione una condición</option>
                <option value="Persona Física">Persona Física</option>
                <option value="Simplificado">Simplificado</option>
                <option value="Informal">Informal</option>
                <option value="Empresa">Empresa</option>
            </select>
        </div>

        <div class="form-group">
            <label for="fecha_pago">Fecha del Pago:</label>
            <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="metodo_pago">Método de Pago:</label>
            <input type="text" name="metodo_pago" id="metodo_pago" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Registrar Pago</button>
        <a href="index_pagos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
