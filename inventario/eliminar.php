<?php

session_start();
require 'conexion.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['mensaje']  = "ID inválido para eliminar.";
    $_SESSION['tipo_msg'] = 'error';
    header('Location: index.php');
    exit;
}

try {
    $pdo = conectar();

    $stament = $pdo->prepare("SELECT nombre FROM productos WHERE id = ?");
    $stament->execute([$id]);
    $producto = $stament->fetch();

    if (!$producto) {
        $_SESSION['mensaje']  = "El producto no fue encontrado.";
        $_SESSION['tipo_msg'] = 'error';
        header('Location: index.php');
        exit;
    }

    $del = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $del->execute([$id]);

    $_SESSION['mensaje']  = "Producto «{$producto['nombre']}» eliminado correctamente.";
    $_SESSION['tipo_msg'] = 'success';

} catch (PDOException $e) {
    error_log("Error al eliminar: " . $e->getMessage());
    $_SESSION['mensaje']  = "Error al eliminar el producto.";
    $_SESSION['tipo_msg'] = 'error';
}

header('Location: index.php');
exit;
?>
