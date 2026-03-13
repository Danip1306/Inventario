<?php
// validar y guardar el nuevo producto.

session_start();
require_once __DIR__ . '/../model/ProductoModel.php';
require_once __DIR__ . '/../auth.php';

require_role('editor');

// Solo acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: IndexController.php');
    exit;
}

$codigo       = trim($_POST['codigo']        ?? '');
$nombre       = trim($_POST['nombre']        ?? '');
$categoria    = trim($_POST['categoria']     ?? '');
$proveedor    = trim($_POST['proveedor']     ?? '');
$precio       = (float)($_POST['precio']     ?? 0);
$stock        = (int)($_POST['stock']        ?? 0);
$stock_minimo = (int)($_POST['stock_minimo'] ?? 5);

try {
    // Verificar código duplicado
    if (codigoExiste($codigo)) {
        $_SESSION['mensaje']  = "Ya existe un producto con el código «$codigo». Usa uno diferente.";
        $_SESSION['tipo_msg'] = 'error';
        header('Location: IndexController.php');
        exit;
    }

    crearProducto($codigo, $nombre, $categoria, $proveedor, $precio, $stock, $stock_minimo);

    $_SESSION['mensaje']  = "Producto «$nombre » agregado correctamente.";
    $_SESSION['tipo_msg'] = 'success';

} catch (PDOException $e) {
    error_log("Error al agregar producto: " . $e->getMessage());
    $_SESSION['mensaje']  = "Error al guardar el producto. Por favor intenta de nuevo.";
    $_SESSION['tipo_msg'] = 'error';
}

header('Location: IndexController.php');
exit;