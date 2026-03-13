<?php
// relacionado con la tabla "productos" en la base de datos.

require_once __DIR__ . '/conexion.php';

/*Lista productos aplicando filtros*/
function listarProductos(string $busqueda = '', string $categoria = '', string $filtro_stock = ''): array {
    $pdo    = conectar();
    $sql    = "SELECT * FROM productos WHERE 1=1";
    $params = [];

    if ($busqueda !== '') {
        $sql .= " AND (nombre LIKE ? OR codigo LIKE ? OR proveedor LIKE ?)";
        $params[] = "%$busqueda%";
        $params[] = "%$busqueda%";
        $params[] = "%$busqueda%";
    }
    if ($categoria !== '') {
        $sql .= " AND categoria = ?";
        $params[] = $categoria;
    }
    if ($filtro_stock === 'critico') {
        $sql .= " AND stock <= stock_minimo";
    }

    $sql .= " ORDER BY stock ASC, nombre ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/*Retorna el total de productos con stock crítico */
function contarAlertas(): int {
    $pdo = conectar();
    return (int) $pdo->query("SELECT COUNT(*) FROM productos WHERE stock <= stock_minimo")->fetchColumn();
}

/*Retorna la lista de categorías únicas existentes en la tabla.*/
function listarCategorias(): array {
    $pdo = conectar();
    return $pdo->query("SELECT DISTINCT categoria FROM productos ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);
}

/*Verifica si ya existe un producto con ese código */
function codigoExiste(string $codigo, int $excluir_id = 0): bool {
    $pdo  = conectar();
    $stmt = $pdo->prepare("SELECT id FROM productos WHERE codigo = ? AND id != ?");
    $stmt->execute([$codigo, $excluir_id]);
    return (bool) $stmt->fetch();
}

/*Inserta un nuevo producto en la base de datos.*/
function crearProducto(string $codigo, string $nombre, string $categoria, string $proveedor, float $precio, int $stock, int $stock_minimo): void {
    $pdo = conectar();
    $pdo->prepare("
        INSERT INTO productos (codigo, nombre, categoria, precio, stock, stock_minimo, proveedor)
        VALUES (:codigo, :nombre, :categoria, :precio, :stock, :stock_minimo, :proveedor)
    ")->execute([
        ':codigo'       => $codigo,
        ':nombre'       => $nombre,
        ':categoria'    => $categoria,
        ':precio'       => $precio,
        ':stock'        => $stock,
        ':stock_minimo' => $stock_minimo,
        ':proveedor'    => $proveedor,
    ]);
}

/*Actualiza un producto existente.*/
function actualizarProducto(int $id, string $codigo, string $nombre, string $categoria, string $proveedor, float $precio, int $stock, int $stock_minimo): void {
    $pdo = conectar();
    $pdo->prepare("
        UPDATE productos
        SET codigo=:codigo, nombre=:nombre, categoria=:categoria,
            precio=:precio, stock=:stock, stock_minimo=:stock_minimo, proveedor=:proveedor
        WHERE id=:id
    ")->execute([
        ':codigo'       => $codigo,
        ':nombre'       => $nombre,
        ':categoria'    => $categoria,
        ':precio'       => $precio,
        ':stock'        => $stock,
        ':stock_minimo' => $stock_minimo,
        ':proveedor'    => $proveedor,
        ':id'           => $id,
    ]);
}

/*Busca un producto por ID*/
function buscarProductoPorId(int $id): array|false {
    $pdo  = conectar();
    $stmt = $pdo->prepare("SELECT nombre FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/*Elimina un producto por ID*/
function eliminarProducto(int $id): void {
    $pdo = conectar();
    $pdo->prepare("DELETE FROM productos WHERE id = ?")->execute([$id]);
}

/*Retorna informacion general del inventario.*/
function obtenerStats(): array {
    $pdo = conectar();
    return $pdo->query("
        SELECT
            COUNT(*) as total,
            SUM(stock * precio) as valor_total,
            SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as sin_stock,
            SUM(CASE WHEN stock > 0 AND stock <= stock_minimo THEN 1 ELSE 0 END) as stock_bajo
        FROM productos
    ")->fetch();
}
?>