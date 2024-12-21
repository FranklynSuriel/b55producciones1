<?php
session_start();
require_once 'includes/load.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si las claves existen en $_POST antes de acceder a ellas
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Verificar que ambos campos no estén vacíos
    if (!empty($username) && !empty($password)) {
        $query = "SELECT * FROM usuarios WHERE username = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verificar la contraseña
            if (password_verify($password, $user['password'])) {
                // Establecer la sesión del usuario
                $_SESSION['usuario'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'nombre' => $user['nombre'],
                    'rol' => $user['rol']
                ];
                header('Location: home.php');
                exit;
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Usuario no encontrado.";
        }
    } else {
        $error = "Por favor, complete todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background: #f8f9fc; /* Fondo claro */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background: #ffffff;
            color: #333;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-container h1 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            color: #1a3764;
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
        input {
            width: 100%;
            padding: 0.8rem;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: 0.3s;
        }
        input:focus {
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
            background: #1a3764;
            color: #fff;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background: #4e73df;
        }
        .register-link {
            margin-top: 1rem;
            text-align: center;
            font-size: 0.9rem;
        }
        .register-link a {
            color: #1a3764;
            text-decoration: none;
        }
        .register-link a:hover {
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
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
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
            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>
        <div class="register-link">
            <p>¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
        </div>
    </div>
</body>
</html>
