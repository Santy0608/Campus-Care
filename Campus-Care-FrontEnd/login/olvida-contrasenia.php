<?php

require_once(__DIR__ . '/../includes/db.php');
include (__DIR__ . '/../includes/header.php');


?>

    <style>
        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;

            background-size: cover;
            background-position: center center;
            background-attachment: fixed;
            position: relative;
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
        
        .olvida-contrasenia-container {
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

        .olvida-contrasenia-container h2 {
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
            background-color: rgba(255, 255, 255, 0.95); /* Para que el input se vea bien sobre el fondo semi-transparente */
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

        .olvida-contrasenia-container p {
            margin-top: 25px;
            color: #6c757d;
        }

        .olvida-contrasenia-container p a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .olvida-contrasenia-container p a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>


    <br/>
    <br/>

    <div class="main-content">
        <div class="olvida-contrasenia-container">
            <h2>Recuperar Contraseña</h2>
            <div class="formulario">
                <form action="proceso_contrasenia.php" method="POST">
                    <label><span class="icon">📧</span> Email:</label>
                    <input type="email" name="email" placeholder="Ingresa tu correo electrónico" required>
                    <button type="submit">Enviar enlace</button>
                </form>
            </div>
        </div>
   </div>

   <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row gy-3 align-items-center">
                <div class="col-md-4 text-center text-md-start">
                    <h5 class="mb-0">Bienestar Estudiantil - Grupo 4 Ambiente Web Cliente Servidor</h5>
                    <small class="text">© 2025 Todos los derechos reservados</small>
                </div>

                <div class="col-md-4 text-center">
                    <a href="/acerca" class="text-white text-decoration-none me-3">Acerca de</a>
                    <a href="/contacto" class="text-white text-decoration-none me-3">Contacto</a>
                    <a href="/privacidad" class="text-white text-decoration-none">Privacidad</a>
                </div>

                <div class="col-md-4 text-center text-md-end">
                    <small class="text-muted">Versión 1.0 • Proyecto académico</small>
                </div>
            </div>
        </div>
    </footer>

</html>
