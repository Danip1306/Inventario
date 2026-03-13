<?php

session_start();
require 'conexion.php';
require 'auth.php';
require_role('editor'); 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$codigo       = trim($_POST['codigo']       ?? '');
$nombre       = trim($_POST['nombre']       ?? '');
$categoria    = trim($_POST['categoria']    ?? '');
$proveedor    = trim($_POST['proveedor']    ?? '');
$precio       = $_POST['precio']      ?? '';
$stock        = $_POST['stock']       ?? '';
$stock_minimo = $_POST['stock_minimo'] ?? 5;

try {
    $pdo = conectar();

    $check = $pdo->prepare("SELECT id FROM productos WHERE codigo = ?");
    $check->execute([$codigo]);
    if ($check->fetch()) {
        $_SESSION['mensaje']  = "Ya existe un producto con el código « $codigo ». Usa uno diferente.";
        $_SESSION['tipo_msg'] = 'error';
        header('Location: index.php');
        exit;
    }

    $satment = $pdo->prepare("
        INSERT INTO productos (codigo, nombre, categoria, precio, stock, stock_minimo, proveedor)
        VALUES (:codigo, :nombre, :categoria, :precio, :stock, :stock_minimo, :proveedor)
    ");

    $satment->execute([
        ':codigo'       => $codigo,
        ':nombre'       => $nombre,
        ':categoria'    => $categoria,
        ':precio'       => (float)$precio,
        ':stock'        => (int)$stock,
        ':stock_minimo' => (int)$stock_minimo,
        ':proveedor'    => $proveedor,
    ]);

    $_SESSION['mensaje']  = "Producto « $nombre » agregado correctamente.";
    $_SESSION['tipo_msg'] = 'success';

} catch (PDOException $e) {
    error_log("Error al agregar producto: " . $e->getMessage());
    $_SESSION['mensaje']  = "Error al guardar el producto. Por favor intenta de nuevo.";
    $_SESSION['tipo_msg'] = 'error';
}

header('Location: index.php');
exit;
?>