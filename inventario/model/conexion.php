<?php
//conexión a la base de datos

define('DB_HOST', 'localhost');
define('DB_PORT', '3307');
define('DB_NAME', 'inventario_db');
define('DB_USER', 'root');
define('DB_PASS', '');

function conectar(): PDO {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, DB_USER, DB_PASS, $opciones);
    } catch (PDOException $e) {
        error_log("Error de conexión: " . $e->getMessage());
        die(json_encode(['error' => 'No se pudo conectar a la base de datos.']));
    }
}
?>