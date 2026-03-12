<?php

function conectar(): PDO {
    $host    = "localhost";
    $port    = "3307"; 
    $db_name = "inventario_db";
    $user    = "root";
    $pass    = "";

    $dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=utf8mb4";

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $opciones);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Error de conexión: " . $e->getMessage());
        die(json_encode(['error' => 'No se pudo conectar a la base de datos.']));
    }
}
?>
