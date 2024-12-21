<?php
include 'includes/load.php'; // Conexión a la base de datos

if (isset($_POST['id'])) {
    $id_presupuesto = intval($_POST['id']); // Sanitizar el ID

    // Consulta para obtener el presupuesto específico
    $query = "SELECT p.project_name, c.created_at, c.client_name, c.client_company
              FROM presupuesto p
              JOIN clientes c ON p.client_id = c.id
              WHERE p.id = $id_presupuesto";

    $result = mysqli_query($conexion, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Mostrar el formulario con la información del presupuesto
        ?>
        <div class="form-group">
            <label>Nombre del Proyecto</label>
            <input type="text" class="form-control" name="project_name" value="<?php echo htmlspecialchars($row['project_name']); ?>">
        </div>
        <div class="form-group">
            <label>Cliente</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['client_name']); ?>" readonly>
        </div>
        <div class="form-group">
            <label>Compañía</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['client_company']); ?>" readonly>
        </div>
        <div class="form-group">
            <label>Fecha de Creación</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['created_at']); ?>" readonly>
        </div>
        <div class="form-group">
            <label>Detalles</label>
            <textarea class="form-control" name="details" rows="5"><?php echo htmlspecialchars($row['']); ?></textarea>
        </div>
        <input type="hidden" name="id_presupuesto" value="<?php echo $id_presupuesto; ?>">
        <?php
    } else {
        echo "<p>Error: No se encontró el presupuesto.</p>";
    }
} else {
    echo "<p>Error: ID inválido.</p>";
}
?>