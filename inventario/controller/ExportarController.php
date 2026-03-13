<?php
// Generar y descargar el CSV del inventario.

session_start();
require_once __DIR__ . '/../model/ProductoModel.php';
require_once __DIR__ . '/../auth.php';

// Cualquier usuario autenticado puede exportar
$pdo      = conectar();
$stmt     = $pdo->query("SELECT codigo, nombre, categoria, proveedor, precio, stock, stock_minimo, fecha_reg FROM productos ORDER BY nombre");
$productos = $stmt->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="inventario_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// BOM para que Excel abra el CSV con tildes correctamente
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['Código', 'Nombre', 'Categoría', 'Proveedor', 'Precio (COP)', 'Stock Actual', 'Stock Mínimo', 'Estado', 'Fecha Registro'], ';');

foreach ($productos as $p) {
    $estado = $p['stock'] == 0 ? 'SIN STOCK' : ($p['stock'] <= $p['stock_minimo'] ? 'STOCK BAJO' : 'DISPONIBLE');
    fputcsv($output, [
        $p['codigo'],
        $p['nombre'],
        $p['categoria'],
        $p['proveedor'],
        number_format($p['precio'], 2, ',', '.'),
        $p['stock'],
        $p['stock_minimo'],
        $estado,
        $p['fecha_reg'],
    ], ';');
}

fclose($output);
exit;