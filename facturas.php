<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Facturas</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        // Buscar presupuesto basado en la consulta del usuario
        function buscarPresupuesto() {
            const query = document.getElementById('buscar-presupuesto').value.trim();

            if (query.length > 2) { // Ejecutar búsqueda si hay más de 2 caracteres
                const resultados = document.getElementById('resultados-presupuesto');
                resultados.innerHTML = '<p>Buscando...</p>'; // Mostrar mensaje de búsqueda

                fetch(`buscar_proyecto_factura.php?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        resultados.innerHTML = ''; // Limpiar resultados anteriores

                        if (data.success && data.data.length > 0) {
                            data.data.forEach(presupuesto => {
                                const item = `
                                    <div class="list-group-item">
                                        <strong>${presupuesto.project_name}</strong> - Total: $${presupuesto.total_presupuestado}
                                        <button class="btn btn-primary btn-sm float-right" 
                                            onclick="seleccionarPresupuesto(${presupuesto.id}, '${encodeURIComponent(presupuesto.project_name)}', ${presupuesto.total_presupuestado})">
                                            Seleccionar
                                        </button>
                                    </div>
                                `;
                                resultados.innerHTML += item;
                            });
                        } else {
                            resultados.innerHTML = '<p class="text-danger">No se encontraron resultados.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error al buscar presupuesto:', error);
                        resultados.innerHTML = '<p class="text-danger">Error al realizar la búsqueda.</p>';
                    });
            } else {
                document.getElementById('resultados-presupuesto').innerHTML = '';
            }
        }

        function seleccionarPresupuesto(id, nombreCodificado, total) {
            const nombre = decodeURIComponent(nombreCodificado);
            document.getElementById('presupuesto_id').value = id;
            document.getElementById('project_name').value = nombre;
            document.getElementById('total_presupuestado').value = total.toFixed(2);
            document.getElementById('resultados-presupuesto').innerHTML = ''; // Limpiar resultados
            document.querySelector('button[type="submit"]').disabled = false; // Habilitar el botón de guardar
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h1>Gestión de Facturas</h1>

    <!-- Mensaje de sesión -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Búsqueda de presupuesto -->
    <div class="form-group">
        <label for="buscar-presupuesto">Buscar Presupuesto:</label>
        <input type="text" id="buscar-presupuesto" class="form-control" onkeyup="buscarPresupuesto()" placeholder="Ingresa el nombre del proyecto">
    </div>
    <div id="resultados-presupuesto" class="list-group mb-3"></div>

    <!-- Formulario para crear factura -->
    <form action="guardar_factura.php" method="POST">
        <input type="hidden" id="presupuesto_id" name="presupuesto_id">
        <div class="form-group">
            <label for="project_name">Nombre del Proyecto:</label>
            <input type="text" id="project_name" class="form-control" disabled>
        </div>
        <div class="form-group">
            <label for="total_presupuestado">Total Presupuestado:</label>
            <input type="text" id="total_presupuestado" class="form-control" disabled>
        </div>
        <div class="form-group">
            <label for="numero_factura">Número de Factura:</label>
            <input type="text" id="numero_factura" name="numero_factura" class="form-control" placeholder="Ingresa el número de factura inicial" required>
        </div>
        <div class="form-group">
            <label for="estado">Estado de la Factura:</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="pendiente">Pendiente</option>
                <option value="pagada">Pagada</option>
            </select>
        </div>
        <div class="form-group">
            <label for="total">Monto Total:</label>
            <input type="number" id="total" name="total" step="0.01" class="form-control" placeholder="Ingresa el monto total" required>
        </div>
        <button type="submit" class="btn btn-success" disabled>Guardar Factura</button>
    </form>
</div>
</body>
<div class="text-center">
    <a href="lista_facturas.php" class="btn btn-primary mt-3">Volver a Lista de Factura</a>
</div>

</html>
