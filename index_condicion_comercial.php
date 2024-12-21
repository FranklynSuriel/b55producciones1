<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Pagos</title>
</head>
<body>
    <h1>Generar Reporte de Pagos</h1>
    <form method="POST" action="reporte_condicion_comercial.php">
        <label for="start_date">Fecha Inicio:</label>
        <input type="date" id="start_date" name="start_date" required>

        <label for="end_date">Fecha Fin:</label>
        <input type="date" id="end_date" name="end_date" required>

        <button type="submit">Generar Reporte</button>
    </form>
</body>
</html>
