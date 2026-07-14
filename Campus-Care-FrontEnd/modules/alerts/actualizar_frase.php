<?php
$query = $_SERVER['QUERY_STRING'] ?? '';
$destino = '/modules/alerts/actualizar_frase.html' . ($query ? '?' . $query : '');
header('Location: ' . $destino, true, 302);
exit;
