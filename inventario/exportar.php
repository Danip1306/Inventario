<?php

require 'conexion.php';

$pdo = conectar();
$stament = $pdo->query("SELECT codigo, nombre, categoria, proveedor, precio, stock, stock_minimo, fecha_reg FROM productos ORDER BY nombre");
$productos = $stament->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="inventario_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['Código', 'Nombre', 'Categoría', 'Proveedor', 'Precio (USD)', 'Stock Actual', 'Stock Mínimo', 'Estado', 'Fecha Registro'], ';');

foreach ($productos as $prod) {
    $estado = $prod['stock'] == 0 ? 'SIN STOCK' : ($prod['stock'] <= $prod['stock_minimo'] ? 'STOCK BAJO' : 'DISPONIBLE');
    fputcsv($output, [
        $prod['codigo'],
        $prod['nombre'],
        $prod['categoria'],
        $prod['proveedor'],
        number_format($prod['precio'], 2, ',', '.'),
        $prod['stock'],
        $prod['stock_minimo'],
        $estado,
        $prod['fecha_reg'],
    ], ';');
}

fclose($output);
exit;
?>
