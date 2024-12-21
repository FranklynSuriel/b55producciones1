<?php
require_once 'includes/load.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $nombre = $db->escape($_POST['nombre']);
    $descripcion = $db->escape($_POST['descripcion']);
    $fecha_inicio = $db->escape($_POST['fecha_inicio']);
    $fecha_fin = $db->escape($_POST['fecha_fin']);
    $presupuesto_asignado = (float)$_POST['presupuesto_asignado'];

    // Insertar el nuevo proyecto en la base de datos
    $query = "INSERT INTO proyectos (nombre, descripcion, fecha_inicio, fecha_fin, presupuesto_asignado) 
              VALUES ('$nombre', '$descripcion', '$fecha_inicio', '$fecha_fin', '$presupuesto_asignado')";
    if ($db->query($query)) {
        $_SESSION['message'] = 'Proyecto creado exitosamente.';
        header('Location: index_proyectos.php');
        exit;
    } else {
        $_SESSION['message'] = 'Error al crear el proyecto.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Proyecto</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        // Buscar presupuesto basado en la consulta del usuario
        function buscarPresupuesto() {
            const query = document.getElementById('buscar-presupuesto').value;

            if (query.length > 2) { // Ejecutar búsqueda si hay más de 2 caracteres
                fetch(`buscar_presupuesto.php?q=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        const resultados = document.getElementById('resultados-presupuesto');
                        resultados.innerHTML = '';

                        if (data.length > 0) {
                            data.forEach(presupuesto => {
                                const item = `
                                    <div class="list-group-item">
                                        <strong>${presupuesto.nombre}</strong> - Total: $${presupuesto.presupuesto_asignado}
                                        <button class="btn btn-primary btn-sm float-right" 
                                            onclick="seleccionarPresupuesto(${presupuesto.id}, '${encodeURIComponent(presupuesto.nombre)}', ${presupuesto.presupuesto_asignado})">
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
                    .catch(error => console.error('Error al buscar presupuesto:', error));
            } else {
                document.getElementById('resultados-presupuesto').innerHTML = '';
            }
        }

        function seleccionarPresupuesto(id, nombreCodificado, total) {
            const nombre = decodeURIComponent(nombreCodificado);
            document.getElementById('nombre').value = nombre;
            document.getElementById('presupuesto_asignado').value = total.toFixed(2);
            document.getElementById('resultados-presupuesto').innerHTML = ''; // Limpiar resultados
        }
    </script>
</head>
<body>
<div class="container mt-5">
    <h1>Nuevo Proyecto</h1>

    <!-- Mensaje de sesión -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form action="nuevo_proyecto.php" method="POST">
        <div class="form-group">
            <label for="buscar-presupuesto">Buscar Proyecto Existente:</label>
            <input type="text" id="buscar-presupuesto" class="form-control" placeholder="Buscar por nombre de proyecto" oninput="buscarPresupuesto()">
            <div id="resultados-presupuesto" class="list-group mt-2"></div>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre del Proyecto:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="fecha_inicio">Fecha de Inicio:</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="fecha_fin">Fecha de Fin:</label>
            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="presupuesto_asignado">Presupuesto Asignado:</label>
            <input type="number" name="presupuesto_asignado" id="presupuesto_asignado" class="form-control" step="0.01" min="0" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Proyecto</button>
        <a href="index_proyectos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
