
<?php
session_start();

?>

<!DOCTYPE html>
<html lang="es">
    

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Billy 55 Producciones</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #eef2f3;
            color: #333;
            line-height: 1.6;
        }
        header {
            background: linear-gradient(135deg, #4e73df, #1a3764);
            color: #fff;
            padding: 2rem 1rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        header h1 {
            margin: 0;
            font-size: 2.5rem;
            letter-spacing: 1px;
        }
        header p {
            font-size: 1.1rem;
            margin-top: 0.5rem;
        }
        nav {
            background: #1a3764;
            padding: 0.75rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 1rem;
            font-size: 1rem;
            transition: color 0.3s ease;
        }
        nav a:hover {
            color: #ffcc00;
        }
        main {
            padding: 3rem 1.5rem;
            text-align: center;
        }
        h2 {
            font-size: 2rem;
            color: #1a3764;
            margin-bottom: 1.5rem;
        }
        .services {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
        }
        .card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem 1.5rem;
            width: 300px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .card a {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .card i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #1a3764;
        }
        .card h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #333;
        }
        .card p {
            font-size: 1rem;
            color: #555;
        }
        .user-info {
            margin-bottom: 2rem;
            text-align: center;
        }
        footer {
            background: #1a3764;
            color: #fff;
            text-align: center;
            padding: 1rem 0;
            margin-top: 3rem;
            font-size: 0.9rem;
        }
        @media (max-width: 768px) {
            .services {
                flex-direction: column;
                align-items: center;
            }
            .card {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Bienvenido a Billy 55 Producciones</h1>
        <p>Producciones en general</p>
    </header>
    <nav>
        <a href="#">Inicio</a>
        <a href="#opciones">Servicios</a>
        <a href="logout.php">Cerrar Sesion</a>
    </nav>
    <main>
        <!-- Información del usuario -->
       

        <!-- Menú de opciones -->
        <section id="opciones">
            <h2>Menú de Opciones</h2>
            <div class="services">
                <div class="card">
                    <a href="index_presupuesto.php">
                        <i class="fas fa-calculator"></i>
                        <h3>Presupuestos</h3>
                        <p>Crea tus presupuestos de acuerdo a tus necesidades.</p>
                    </a>
                </div>
                <div class="card">
                    <a href="index_proyectos.php">
                        <i class="fas fa-briefcase"></i>
                        <h3>Control de Proyecto</h3>
                        <p>Ver todos los proyectos en curso, completados y pendientes.</p>
                    </a>
                </div>
                <div class="card">
                    <a href="lista_facturas.php">
                        <i class="fas fa-file-text"></i>
                        <h3>Facturación</h3>
                        <p>Crear y ver todas las facturas pendientes y pagadas.</p>
                    </a>
                </div>
                <div class="card">
                    <a href="index_pagos.php">
                        <i class="fas fa-file-text"></i>
                        <h3>Pagos</h3>
                        <p>Aquí podrás encontrar todos los pagos realizados.</p>
                    </a>
                </div>
                <div class="card">
                    <a href="index_reportes.php">
                        <i class="fas fa-chart-bar"></i>
                        <h3>Reporte de Pagos</h3>
                        <p>Genera y visualiza reportes detallados de todos los pagos realizados.</p>
                    </a>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Wenjostech. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
