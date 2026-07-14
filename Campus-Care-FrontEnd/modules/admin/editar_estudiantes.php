<?php
$query = $_SERVER['QUERY_STRING'] ?? '';
$destino = '/modules/admin/editar_estudiantes.html' . ($query ? '?' . $query : '');
header('Location: ' . $destino, true, 302);
exit;
