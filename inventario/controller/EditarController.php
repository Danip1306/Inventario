<?php
// Recibir el POST de edición y actualizar el producto

session_start();
require_once __DIR__ . '/../model/ProductoModel.php';
require_once __DIR__ . '/../auth.php';

require_role('editor');

// Solo acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: IndexController.php');
    exit;
}

$id           = (int)($_POST['id']           ?? 0);
$codigo       = trim($_POST['codigo']        ?? '');
$nombre       = trim($_POST['nombre']        ?? '');
$categoria    = trim($_POST['categoria']     ?? '');
$proveedor    = trim($_POST['proveedor']     ?? '');
$precio       = (float)($_POST['precio']     ?? 0);
$stock        = (int)($_POST['stock']        ?? 0);
$stock_minimo = (int)($_POST['stock_minimo'] ?? 5);

try {
    // Verificar código duplicado
    if (codigoExiste($codigo, $id)) {
        $_SESSION['mensaje']  = "El código «$codigo» ya está en uso por otro producto.";
        $_SESSION['tipo_msg'] = 'error';
        header('Location: IndexController.php');
        exit;
    }

    actualizarProducto($id, $codigo, $nombre, $categoria, $proveedor, $precio, $stock, $stock_minimo);

    $_SESSION['mensaje']  = "Producto «$nombre » actualizado correctamente.";
    $_SESSION['tipo_msg'] = 'success';

} catch (PDOException $e) {
    error_log("Error al editar producto: " . $e->getMessage());
    $_SESSION['mensaje']  = "Error al actualizar el producto. Por favor intenta de nuevo.";
    $_SESSION['tipo_msg'] = 'error';
}

header('Location: IndexController.php');
exit;