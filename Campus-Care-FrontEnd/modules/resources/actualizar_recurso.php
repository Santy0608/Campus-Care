<?php
$query = $_SERVER['QUERY_STRING'] ?? '';
$destino = '/modules/resources/actualizar_recurso.html' . ($query ? '?' . $query : '');
header('Location: ' . $destino, true, 302);
exit;