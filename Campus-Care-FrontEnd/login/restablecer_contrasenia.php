<?php
require_once(__DIR__ . '/../includes/db.php');
include (__DIR__ . '/../includes/header.php');


if (!isset($_GET['token'])) {
    die("Token no proporcionado.");
}

$token = $_GET['token'];

$stmt = $pdo->prepare("SELECT * FROM restablecimientos_contrasenia WHERE token = ?");
$stmt->execute([$token]);
$data = $stmt->fetch();

if (!$data) {
    die("Token inválido o ya fue usado.");
}

if (strtotime($data['fecha_vencimiento']) < time()) {
    die("El token expiró. Solicita uno nuevo.");
}
?>

<br/>
<br/>

    <style>
        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-size: cover;
            background-position: center center;
            background-attachment: fixed;
            position: relative;
            /* --- FIN ESTILOS DE FONDO --- */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
            z-index: 1;
        }
        
        .restablece-contrasenia-container {
            width: 90%;
            max-width: 420px;
            padding: 30px 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            background-color: rgba(255, 255, 255, 0.9); 
            text-align: center;
            border-top: 6px solid #007bff;
            animation: fadeIn 0.8s ease-out;
        }

        .restablece-contrasenia-container h2 {
            color: #343040;
            margin-bottom: 30px;
            font-size: 1.8em;
        }
        
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
        .formulario input[type="email"],
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
            background-color: rgba(255, 255, 255, 0.95); 
        }

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

        .formulario button[type="submit"]:active {
            transform: translateY(0);
            background-color: #004085;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2) inset;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .mensaje-exito {
            color: #28a745;
            font-weight: bold;
            padding: 12px;
            border-radius: 6px;
            background-color: #d4edda;
            margin-bottom: 20px;
            animation: pulse 1.5s infinite alternate;
        }

        @keyframes pulse {
            from { transform: scale(1); }
            to { transform: scale(1.01); }
        }

        .restablece-contrasenia-container p {
            margin-top: 25px;
            color: #6c757d;
        }

        .restablece-contrasenia-container p a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .restablece-contrasenia-container p a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>

    <br/>   
    <br/>

    <div class="main-content">
        <div class="restablece-contrasenia-container">
            <h2>Restablecer Contraseña</h2>
            <div class="formulario">
                <form action="procesar_nueva_contrasenia.php" method="POST">
                    <input type="hidden" name="token" value="<?= $_GET['token'] ?>">
                    <input type="password" name="contrasenia" require placeholder="Nueva Contraseña">
                    <button type="submit">Cambiar Contraseña</button>
                </form>
            </div>
        </div>
    </div>