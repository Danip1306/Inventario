<?php

session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id           = (int)($_POST['id']           ?? 0);
$codigo       = trim($_POST['codigo']        ?? '');
$nombre       = trim($_POST['nombre']        ?? '');
$categoria    = trim($_POST['categoria']     ?? '');
$proveedor    = trim($_POST['proveedor']     ?? '');
$precio       = $_POST['precio']       ?? '';
$stock        = $_POST['stock']        ?? '';
$stock_minimo = $_POST['stock_minimo'] ?? 5;

try {
    $pdo = conectar();

    $check = $pdo->prepare("SELECT id FROM productos WHERE codigo = ? AND id != ?");
    $check->execute([$codigo, $id]);
    if ($check->fetch()) {
        $_SESSION['mensaje']  = "El código « $codigo » ya está en uso por otro producto.";
        $_SESSION['tipo_msg'] = 'error';
        header('Location: index.php');
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE productos
        SET codigo       = :codigo,
            nombre       = :nombre,
            categoria    = :categoria,
            precio       = :precio,
            stock        = :stock,
            stock_minimo = :stock_minimo,
            proveedor    = :proveedor
        WHERE id = :id
    ");

    $stmt->execute([
        ':codigo'       => $codigo,
        ':nombre'       => $nombre,
        ':categoria'    => $categoria,
        ':precio'       => (float)$precio,
        ':stock'        => (int)$stock,
        ':stock_minimo' => (int)$stock_minimo,
        ':proveedor'    => $proveedor,
        ':id'           => $id,
    ]);

    $_SESSION['mensaje']  = "Producto « $nombre » actualizado correctamente.";
    $_SESSION['tipo_msg'] = 'success';

} catch (PDOException $e) {
    error_log("Error al editar producto: " . $e->getMessage());
    $_SESSION['mensaje']  = "Error al actualizar el producto. Por favor intenta de nuevo.";
    $_SESSION['tipo_msg'] = 'error';
}

header('Location: index.php');
exit;
?>
