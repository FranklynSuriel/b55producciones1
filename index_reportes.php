<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Billy 55 Producciones</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #eef2f3;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            padding: 20px;
            text-align: center;
        }
        .card i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #4e73df;
        }
        .card h3 {
            margin-bottom: 10px;
        }
        .card a {
            text-decoration: none;
            color: #fff;
            background-color: #4e73df;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .card a:hover {
            background-color: #2e59d9;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <h1 class="text-center">Opciones de Reportes</h1>
        <div class="row mt-4">
            <!-- Reporte General de Pagos -->
            <div class="col-md-4">
                <div class="card">
                    <i class="fas fa-money-check-alt"></i>
                    <h3>Reporte de Pagos</h3>
                    <p>Consulta todos los pagos realizados.</p>
                    <a href="index_reporte_pago.php">Generar Reporte</a>
                </div>
            </div>
            <!-- Reporte Detallado por Cliente -->
            <div class="col-md-4">
                <div class="card">
                    <i class="fas fa-user"></i>
                    <h3>Reporte de Cliente</h3>
                    <p>Obtén un informe detallado de un cliente.</p>
                    <a href="reporte_cliente.php">Generar Reporte</a>
                </div>
                
            </div>
            <!-- Otros Reportes -->
            <div class="col-md-4">
                <div class="card">
                    <i class="fas fa-chart-bar"></i>
                    <h3>Cartas de Retecion.</h3>
                    <p>Certificaciones de retencion.</p>
                    <a href="index_carta_retencion.php">ver Cartas</a>
                </div>
            </div>
        </div>
    </div>
</body>
<div class="text-center">
    <a href="home.php" class="btn btn-primary mt-3">Volver al inicio</a>
</div>
</html>
