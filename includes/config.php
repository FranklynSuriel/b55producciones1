<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');  // Dirección del servidor
define('DB_USER', 'root');       // Usuario de la base de datos
define('DB_PASS', '');           // Contraseña de la base de datos
define('DB_NAME', 'b55producciones'); // Nombre de la base de datos

// Crear la conexión
$conexion = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar la conexión
if (!$conexion) {
    die("Error: No se pudo establecer la conexión a la base de datos. " . mysqli_connect_error());
}

// Opcional: Configurar la codificación de caracteres
mysqli_set_charset($conexion, "utf8");
?>
