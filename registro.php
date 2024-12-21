<?php
require_once 'includes/load.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nombre = $_POST['nombre'];
    $rol = $_POST['rol'];

    $query = "INSERT INTO usuarios (username, password, nombre, rol) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ssss", $username, $password, $nombre, $rol);

    if ($stmt->execute()) {
        header('Location: login.php');
        exit;
    } else {
        $error = "Error al registrar usuario.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background: #f8f9fc; /* Fondo claro para mejor contraste */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .register-container {
            background: #ffffff; /* Blanco para un diseño limpio */
            color: #333; /* Texto oscuro para visibilidad */
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .register-container h1 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            color: #1a3764; /* Azul oscuro para encabezado */
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            font-weight: 500;
            font-size: 0.9rem;
            color: #333;
            display: block;
            margin-bottom: 0.5rem;
        }
        input, select {
            width: 100%;
            padding: 0.8rem;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: 0.3s;
        }
        input:focus, select:focus {
            border-color: #4e73df;
            outline: none;
            box-shadow: 0 0 5px rgba(78, 115, 223, 0.5);
        }
        .btn {
            display: block;
            width: 100%;
            padding: 0.8rem;
            font-size: 1rem;
            font-weight: 700;
            text-align: center;
            border: none;
            border-radius: 5px;
            background: #1a3764; /* Azul oscuro para el botón */
            color: #fff; /* Texto blanco */
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background: #4e73df; /* Azul más claro al pasar el ratón */
        }
        .back-link {
            margin-top: 1rem;
            text-align: center;
            font-size: 0.9rem;
        }
        .back-link a {
            color: #1a3764; /* Azul oscuro */
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Registrar Usuario</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" name="nombre" id="nombre" required>
            </div>
            <div class="form-group">
                <label for="rol">Rol:</label>
                <select name="rol" id="rol" required>
                    <option value="Usuario">Usuario</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn">Registrar</button>
        </form>
        <div class="back-link">
            <p>¿Ya tienes cuenta? <a href="login.php">Inicia Sesión</a></p>
        </div>
    </div>
</body>
</html>
