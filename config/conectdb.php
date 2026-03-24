<?php
// ════════════════════════════════════════════════════════════════════════════════
// CONFIGURACIÓN DE CONEXIÓN A BASE DE DATOS
// - Compatible con XAMPP (localhost) y Docker Compose (servicio `mysql`)
// - Evita depender solo de variables de entorno (PHP-FPM puede limpiar env vars)
// ════════════════════════════════════════════════════════════════════════════════

$dockerEnv = getenv('DOCKER_ENV');

// En Docker Compose el hostname `mysql` debe resolver por DNS interno.
$mysqlResolves = false;
try {
    $resolved = @gethostbyname('mysql');
    $mysqlResolves = is_string($resolved) && $resolved !== '' && $resolved !== 'mysql';
} catch (Throwable $e) {
    $mysqlResolves = false;
}

$isDocker = ($dockerEnv === 'true') || file_exists('/.dockerenv') || $mysqlResolves;

if ($isDocker) {
    $host = 'mysql';
    $usuario = 'root';
    $password = 'root';
    $base = 'restaurante';
} else {
    $host = 'localhost';
    $usuario = 'sant';
    $password = '1234';
    $base = 'restaurante';
}

$conn = new mysqli($host, $usuario, $password, $base);

if ($conn->connect_error) {
    error_log('Error de conexión MySQL: ' . $conn->connect_error);
    http_response_code(500);
    die('Conexión fallida: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

