<?php
session_start();

require_once(__DIR__ . '/../includes/db.php');

$mensaje = '';
if (isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST['nombre_usuario'] ?? '';
    $contrasenia = $_POST['contrasenia'] ?? '';

    if (!empty($usuario) && !empty($contrasenia)) {
        $query = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$usuario]);
        $user = $stmt->fetch();

        if ($user && password_verify($contrasenia, $user['contrasenia'])) {
            // Guarda sesión
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['role'] = $user['role'];

            // Redirige a header después del login
            header("Location: ../index.php");
            exit;
        } else {
            $mensaje = "Usuario o contraseña incorrectos.";
        }
    } else {
        $mensaje = "Por favor complete todos los campos.";
    }
}

// Incluir el header después de toda la lógica de redirección
include (__DIR__ . '/../includes/header.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <style>
        /* 1. Estilos base del cuerpo */
        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            /* --- ESTILOS DE FONDO APLICADOS --- */
            background-image: url('../assets/images/registrologin.jpg'); /* ⬅️ RUTA DE TU IMAGEN */
            background-size: cover;
            background-position: center center;
            background-attachment: fixed;
            position: relative;
            /* --- FIN ESTILOS DE FONDO --- */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Pseudo-elemento para el oscurecimiento/opacidad del fondo */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.35); /* Capa semi-transparente negra (35% de opacidad) */
            z-index: -1;
        }

        /* 2. Contenedor principal que se expande entre el header y el footer */
        .main-content {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
            z-index: 1;
        }
        
        /* 3. Estilo para el contenedor del formulario (la 'tarjeta' central) */
        .form-container {
            width: 90%;
            max-width: 400px;
            padding: 30px 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            /* Fondo blanco semi-transparente */
            background-color: rgba(255, 255, 255, 0.9); 
            text-align: center;
            border-top: 6px solid #007bff;
            animation: fadeIn 0.8s ease-out;
        }

        .form-container h2 {
            color: #343040;
            margin-bottom: 30px;
            font-size: 1.8em;
        }
        
        /* 4. Estilos de los inputs y botón */
        .formulario label {
            text-align: left;
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .formulario label span.icon {
            font-size: 1.2em;
            line-height: 1;
            color: #007bff;
        }

        .formulario input[type="text"],
        .formulario input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            font-size: 1em;
            color: #495057;
            background-color: rgba(255, 255, 255, 0.95); /* Para que el input se vea bien sobre el fondo semi-transparente */
        }

        /* Sombra al enfocar */
        .formulario input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.25);
            outline: none;
        }
        
        .formulario button[type="submit"] {
            width: 100%;
            padding: 14px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.1em;
            transition: transform 0.2s ease-in-out, background-color 0.2s ease, box-shadow 0.2s ease;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
        }

        .formulario button[type="submit"]:hover {
            transform: translateY(-3px);
            background-color: #0056b3;
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.3);
        }

        /* Efecto al presionar el botón */
        .formulario button[type="submit"]:active {
            transform: translateY(0);
            background-color: #004085;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2) inset;
        }

        /* 5. Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* 6. Estilo para el mensaje de ERROR de login */
        .mensaje-error {
            color: #721c24; 
            background-color: #f8d7da; 
            border: 1px solid #f5c6cb; 
            font-weight: bold;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-container p {
            margin-top: 25px;
            color: #6c757d;
        }

        .form-container p a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .form-container p a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <?php /* El header.php se muestra aquí al inicio */ ?>

    <div class="main-content">
        <div class="form-container">
            <h2>Iniciar Sesión</h2>

            <?php if ($mensaje): ?>
                <p class="mensaje-error"><?= $mensaje ?></p>
            <?php endif; ?>
            
            <div class="formulario">
                <form method="POST" action="">
                    
                    <label><span class="icon">🏷️</span> Nombre de usuario:</label>
                    <input type="text" name="nombre_usuario" placeholder="Ingresa tu usuario" required>

                    <label><span class="icon">🔒</span> Contraseña:</label>
                    <input type="password" name="contrasenia" placeholder="Ingresa tu contraseña" required>

                    <button type="submit">Ingresar</button>
                </form>
            </div>

            <p>¿No tienes cuenta? <a href="registro.php">Registrarse</a></p>
            <p><a href="olvida-contrasenia.php">¿Olvidaste tu contraseña?</a></p>
        </div>
    </div> 

    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row gy-3 align-items-center">
                <div class="col-md-4 text-center text-md-start">
                    <h5 class="mb-0 text-white fw-semibold">Bienestar Estudiantil - Grupo 4 Ambiente Web Cliente Servidor</h5>
                    <small class="text">© 2025 Todos los derechos reservados</small>
                </div>

                <div class="col-md-4 text-center">
                    <a href="/acerca" class="text-white text-decoration-none me-3">Acerca de</a>
                    <a href="/contacto" class="text-white text-decoration-none me-3">Contacto</a>
                    <a href="/privacidad" class="text-white text-decoration-none">Privacidad</a>
                </div>

                <div class="col-md-4 text-center text-md-end">
                    <small class="text-white">Versión 1.0 • Proyecto académico</small>
                </div>
            </div>
        </div>
    </footer>
    
</body>
</html>