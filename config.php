<?php
// Detectar automáticamente la URL base
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Si el script está en un subdirectorio (como /netlosllanos/), lo incluye en la URL base
$base_path = strpos($script_name, 'index.php') !== false ? dirname($script_name) : $script_name;
if (substr($base_path, -1) !== '/') {
    // Si es un archivo, obtener el directorio
    $base_path = dirname($base_path);
}

// Limpiar la ruta base para que sea consistente
$base_path = rtrim($base_path, '/\\');

// Define la constante BASE_URL
// En desarrollo será http://localhost/netlosllanos
// En producción será http://tusitio.com
define('BASE_URL', $protocol . $host . $base_path);
?>