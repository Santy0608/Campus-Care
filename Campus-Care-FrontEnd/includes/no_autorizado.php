<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso no autorizado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            text-align: center;
            padding-top: 100px;
        }
        .box {
            background: #fff;
            border: 1px solid #ddd;
            padding: 30px;
            display: inline-block;
            border-radius: 8px;
        }
        h1 {
            color: #c0392b;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>🚫 Acceso no autorizado</h1>
        <p>No tienes permisos para ingresar a esta sección.</p>
        <a href="/index.php">Volver al inicio</a>
    </div>
</body>
</html>
