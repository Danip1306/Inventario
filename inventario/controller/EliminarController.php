<?php
// Recibir el GET con el ID, verificar que existe y eliminar el producto

session_start();
require_once __DIR__ . '/../model/ProductoModel.php';
require_once __DIR__ . '/../auth.php';

require_role('admin'); // Solo admin puede eliminar

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['mensaje']  = "ID inválido para eliminar.";
    $_SESSION['tipo_msg'] = 'error';
    header('Location: IndexController.php');
    exit;
}

try {
    $producto = buscarProductoPorId($id);

    if (!$producto) {
        $_SESSION['mensaje']  = "El producto no fue encontrado.";
        $_SESSION['tipo_msg'] = 'error';
        header('Location: IndexController.php');
        exit;
    }

    eliminarProducto($id);

    $_SESSION['mensaje']  = "Producto «{$producto['nombre']}» eliminado correctamente.";
    $_SESSION['tipo_msg'] = 'success';

} catch (PDOException $e) {
    error_log("Error al eliminar: " . $e->getMessage());
    $_SESSION['mensaje']  = "Error al eliminar el producto.";
    $_SESSION['tipo_msg'] = 'error';
}

header('Location: IndexController.php');
exit;