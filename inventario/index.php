<?php
require 'conexion.php';
require 'auth.php';   // ← protección de sesión (redirige a login.php si no está autenticado)
$pdo = conectar();

// session_start() ya lo llama auth.php
$usuario_sesion = usuario_actual();
$mensaje = $_SESSION['mensaje'] ?? null;
$tipo_msg = $_SESSION['tipo_msg'] ?? 'success';
unset($_SESSION['mensaje'], $_SESSION['tipo_msg']);

$busqueda  = trim($_GET['buscar'] ?? '');
$categoria = $_GET['categoria'] ?? '';
$filtro_stock = $_GET['stock'] ?? '';

$sql = "SELECT * FROM productos WHERE 1=1";
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

$stament = $pdo->prepare($sql);
$stament->execute($params);
$productos = $stament->fetchAll();

$stament_alertas = $pdo->query("SELECT COUNT(*) FROM productos WHERE stock <= stock_minimo");
$total_alertas = $stament_alertas->fetchColumn();

$stament_cats = $pdo->query("SELECT DISTINCT categoria FROM productos ORDER BY categoria");
$categorias = $stament_cats->fetchAll(PDO::FETCH_COLUMN);

$stats = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(stock * precio) as valor_total,
        SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as sin_stock,
        SUM(CASE WHEN stock > 0 AND stock <= stock_minimo THEN 1 ELSE 0 END) as stock_bajo
    FROM productos
")->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InventarioPro — Panel de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f5f0ff;
        }

        .navbar-custom {
            background-color: #6a1b9a;
        }

        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(106, 27, 154, 0.08);
        }
        .card-footer {
            border-radius: 0 0 14px 14px !important;
            background-color: #faf7ff;
        }

        .table thead th {
            background-color: #f3e5f5;
            color: #6a1b9a;
            font-weight: 600;
            border-bottom: 2px solid #e1bee7;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .table {
            border-radius: 14px;
            overflow: hidden;
        }
        .table-hover tbody tr:hover {
            background-color: #fce4ec;
        }

        .btn-morado {
            background-color: #7b1fa2;
            color: #fff;
            border: none;
            border-radius: 8px;
        }
        .btn-morado:hover {
            background-color: #6a1b9a;
            color: #fff;
        }

        .btn-outline-morado {
            border: 1px solid #ab47bc;
            color: #7b1fa2;
            border-radius: 8px;
            background: #fff;
        }
        .btn-outline-morado:hover {
            background-color: #f3e5f5;
            color: #6a1b9a;
            border-color: #7b1fa2;
        }

        .btn-outline-primary { border-radius: 7px; }
        .btn-outline-danger  { border-radius: 7px; }
        .btn-warning         { border-radius: 7px; }

        .alerta-stock {
            background-color: #fff8e1;
            border: 1px solid #ffe082;
            border-left: 4px solid #ffa000;
            border-radius: 10px;
            color: #5d4037;
        }

        .flash-success {
            background-color: #e8f5e9;
            border: 1px solid #a5d6a7;
            border-left: 4px solid #43a047;
            border-radius: 10px;
            color: #1b5e20;
        }
        .flash-danger {
            background-color: #fce4ec;
            border: 1px solid #f48fb1;
            border-left: 4px solid #e91e63;
            border-radius: 10px;
            color: #880e4f;
        }

        code {
            background-color: #ede7f6;
            color: #6a1b9a;
            padding: 2px 7px;
            border-radius: 5px;
            font-size: 0.82rem;
        }

        .badge-disponible {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-bajo {
            background-color: #fff8e1;
            color: #f57f17;
            border: 1px solid #ffe082;
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-cero {
            background-color: #fce4ec;
            color: #c62828;
            border: 1px solid #f48fb1;
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .modal-content {
            border: none;
            border-radius: 16px;
        }
        .modal-header {
            background-color: #7b1fa2;
            color: #fff;
            border-radius: 16px 16px 0 0;
            border-bottom: none;
        }
        .modal-header .btn-close {
            filter: invert(1);
        }
        .modal-footer {
            border-top: 1px solid #f3e5f5;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #ab47bc;
            box-shadow: 0 0 0 0.2rem rgba(171, 71, 188, 0.2);
        }

        .card-filtros {
            background-color: #fff;
            border: 1px solid #e1bee7 !important;
        }

        .table-footer-info {
            background-color: #faf7ff;
            color: #7b1fa2;
            font-size: 0.83rem;
            padding: 10px 16px;
            border-top: 1px solid #f3e5f5;
            border-radius: 0 0 14px 14px;
        }

        .btn-outline-secondary { border-radius: 8px; }
        .btn-outline-warning   { border-radius: 8px; }

        .section-title {
            font-weight: 700;
            color: #4a148c;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-custom navbar-dark mb-4 py-3">
    <div class="container">
        <span class="navbar-brand fw-bold fs-5 mb-0">
            <i class="bi bi-box-seam me-2"></i>InventarioPro
        </span>
        <div class="d-flex align-items-center gap-2 ms-auto">
            <span class="text-white-50 small d-none d-md-inline">
                <i class="bi bi-person-circle me-1"></i>
                <?= htmlspecialchars($usuario_sesion['nombre']) ?>
                <span class="badge ms-1" style="background:rgba(255,255,255,0.15);font-size:.7rem;">
                    <?= ucfirst($usuario_sesion['rol']) ?>
                </span>
            </span>
            <?php if (tiene_rol('admin')): ?>
            <a href="usuarios.php" class="btn btn-sm btn-outline-light border-0 px-3" title="Gestión de usuarios">
                <i class="bi bi-people me-1"></i><span class="d-none d-md-inline">Usuarios</span>
            </a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-sm btn-light text-danger fw-semibold px-3">
                <i class="bi bi-box-arrow-right me-1"></i><span class="d-none d-sm-inline">Salir</span>
            </a>
        </div>
    </div>
</nav>

<div class="container mb-5">

    <!-- MENSAJE RAPIDO -->
    <?php if ($mensaje): ?>
        <div id="flashMsg" class="flash-<?= $tipo_msg === 'success' ? 'success' : 'danger' ?> d-flex align-items-center gap-2 p-3 mb-3">
            <i class="bi bi-<?= $tipo_msg === 'success' ? 'check-circle-fill' : 'x-circle-fill' ?>"></i>
            <?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close ms-auto" onclick="document.getElementById('flashMsg').remove()"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="section-title"><i class="bi bi-table me-1"></i> Lista de Productos</span>
        <?php if (tiene_rol('editor')): ?>
        <button class="btn btn-morado btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
        </button>
        <?php endif; ?>
    </div>

    <!-- FILTROS -->
    <div class="card card-filtros mb-3">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-1 fw-semibold text-secondary">Buscar</label>
                    <input type="text" name="buscar" class="form-control form-control-sm"
                           placeholder="Nombre, código o proveedor..."
                           value="<?= htmlspecialchars($busqueda) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1 fw-semibold text-secondary">Categoría</label>
                    <select name="categoria" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $categoria === $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1 fw-semibold text-secondary">Stock</label>
                    <select name="stock" class="form-select form-select-sm">
                        <option value="">Todo</option>
                        <option value="critico" <?= $filtro_stock === 'critico' ? 'selected' : '' ?>>Stock crítico</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-morado btn-sm w-100">Filtrar</button>
                    <?php if ($busqueda || $categoria || $filtro_stock): ?>
                        <a href="index.php" class="btn btn-outline-morado btn-sm" title="Limpiar filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLA DE PRODUCTOS -->
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Proveedor</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-search me-1"></i>
                                No se encontraron productos con esos filtros.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($productos as $p):
                            $sin_stock  = $p['stock'] == 0;
                            $stock_bajo = $p['stock'] > 0 && $p['stock'] <= $p['stock_minimo'];
                        ?>
                            <tr class="<?= $sin_stock ? 'table-danger' : ($stock_bajo ? 'table-warning' : '') ?>">
                                <td><code><?= htmlspecialchars($p['codigo']) ?></code></td>
                                <td class="fw-semibold"><?= htmlspecialchars($p['nombre']) ?></td>
                                <td>
                                    <span class="badge rounded-pill" style="background:#ede7f6; color:#6a1b9a; font-weight:600; font-size:0.75rem;">
                                        <?= htmlspecialchars($p['categoria']) ?>
                                    </span>
                                </td>
                                <td class="text-muted small"><?= htmlspecialchars($p['proveedor'] ?? '—') ?></td>
                                <td class="fw-semibold">$<?= number_format($p['precio'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="fw-bold"><?= $p['stock'] ?></span>
                                    <small class="text-muted">/ mín <?= $p['stock_minimo'] ?></small>
                                </td>
                                <td>
                                    <?php if ($sin_stock): ?>
                                        <span class="badge-cero"><i class="bi bi-x-circle me-1"></i>Sin stock</span>
                                    <?php elseif ($stock_bajo): ?>
                                        <span class="badge-bajo"><i class="bi bi-exclamation-circle me-1"></i>Stock bajo</span>
                                    <?php else: ?>
                                        <span class="badge-disponible"><i class="bi bi-check-circle me-1"></i>Disponible</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <button class="btn btn-outline-primary btn-sm"
                                                title="Editar"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditar"
                                                data-id="<?= $p['id'] ?>"
                                                data-codigo="<?= htmlspecialchars($p['codigo']) ?>"
                                                data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                                data-categoria="<?= htmlspecialchars($p['categoria']) ?>"
                                                data-precio="<?= $p['precio'] ?>"
                                                data-stock="<?= $p['stock'] ?>"
                                                data-minimo="<?= $p['stock_minimo'] ?>"
                                                data-proveedor="<?= htmlspecialchars($p['proveedor']) ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="eliminar.php?id=<?= $p['id'] ?>"
                                           class="btn btn-outline-danger btn-sm"
                                           title="Eliminar"
                                           onclick="return confirm('¿Eliminar el producto «<?= htmlspecialchars($p['nombre']) ?>»?')">
                                            <i class="bi bi-trash3"></i>
                                        </a>
                                        <?php if ($sin_stock || $stock_bajo): ?>
                                            <button class="btn btn-warning btn-sm"
                                                    title="Generar orden de compra"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalOrden"
                                                    data-producto="<?= htmlspecialchars($p['nombre']) ?>"
                                                    data-codigo="<?= htmlspecialchars($p['codigo']) ?>"
                                                    data-proveedor="<?= htmlspecialchars($p['proveedor'] ) ?>"
                                                    data-stock="<?= $p['stock'] ?>"
                                                    data-minimo="<?= $p['stock_minimo'] ?>">
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="table-footer-info">
            Mostrando <strong><?= count($productos) ?></strong> producto(s)
            &nbsp;·&nbsp; Valor total del inventario:
            <strong>$<?= number_format($stats['valor_total'] ?? 0, 0, ',', '.') ?></strong>
        </div>
    </div>

    <!-- OTRAS FUNCIONES -->
    <div class="mt-3 d-flex gap-2">
        <a href="exportar.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Exportar CSV
        </a>
        <a href="index.php?stock=critico" class="btn btn-outline-warning btn-sm">
            <i class="bi bi-exclamation-triangle me-1"></i> Ver stock crítico
            <?php if ($total_alertas > 0): ?>
                <span class="badge bg-danger ms-1"><?= $total_alertas ?></span>
            <?php endif; ?>
        </a>
    </div>

</div>


<!-- AGREGAR PRODUCTO -->
<div class="modal fade" id="modalAgregar" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Agregar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="agregar.php" method="POST" id="formAgregar" novalidate>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Código *</label>
                            <input type="text" name="codigo" class="form-control" placeholder="PROD-009" required maxlength="20">
                            <div class="invalid-feedback">El código es obligatorio.</div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nombre del Producto *</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Collar Ajustable para Perro" required maxlength="100">
                            <div class="invalid-feedback">El nombre es obligatorio.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoría *</label>
                            <input type="text" name="categoria" class="form-control" placeholder="Ej: Accesorios" required list="lista-categorias">
                            <datalist id="lista-categorias">
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>">
                                <?php endforeach; ?>
                            </datalist>
                            <div class="invalid-feedback">La categoría es obligatoria.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Proveedor *</label>
                            <input type="text" name="proveedor" class="form-control" placeholder="Nombre del proveedor" maxlength="100" required>
                            <div class="invalid-feedback">El proveedor es obligatorio.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Precio (COP) *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="precio" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                            </div>
                            <div class="invalid-feedback">Ingresa un precio válido.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stock Inicial *</label>
                            <input type="number" name="stock" class="form-control" placeholder="0" min="0" required>
                            <div class="invalid-feedback">El stock no puede ser negativo.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stock Mínimo *</label>
                            <input type="number" name="stock_minimo" class="form-control" value="5" min="1" required>
                            <div class="invalid-feedback">El mínimo debe ser ≥ 1.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-morado">
                        <i class="bi bi-check-lg me-1"></i> Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!--EDITAR PRODUCTO -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="editar.php" method="POST" id="formEditar" novalidate>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Código *</label>
                            <input type="text" name="codigo" id="edit_codigo" class="form-control" required maxlength="20">
                            <div class="invalid-feedback">El código es obligatorio.</div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nombre del Producto *</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required maxlength="100">
                            <div class="invalid-feedback">El nombre es obligatorio.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoría *</label>
                            <input type="text" name="categoria" id="edit_categoria" class="form-control" required list="lista-categorias">
                            <div class="invalid-feedback">La categoría es obligatoria.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Proveedor</label>
                            <input type="text" name="proveedor" id="edit_proveedor" class="form-control" maxlength="100" required>
                            <div class="invalid-feedback">El proveedor es obligatorio.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Precio (USD) *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="precio" id="edit_precio" class="form-control" step="0.01" min="0" required>
                            </div>
                            <div class="invalid-feedback">Precio inválido.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stock *</label>
                            <input type="number" name="stock" id="edit_stock" class="form-control" min="0" required>
                            <div class="invalid-feedback">El stock no puede ser negativo.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stock Mínimo *</label>
                            <input type="number" name="stock_minimo" id="edit_minimo" class="form-control" min="1" required>
                            <div class="invalid-feedback">Mínimo ≥ 1.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-morado">
                        <i class="bi bi-check-lg me-1"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ORDEN DE COMPRA -->
<div class="modal fade" id="modalOrden" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cart-check me-2"></i>Generar Orden de Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered mb-3" style="border-radius:10px; overflow:hidden;">
                    <tr><th class="table-light" style="width:40%">Producto</th><td id="orden_nombre">—</td></tr>
                    <tr><th class="table-light">Código</th><td id="orden_codigo">—</td></tr>
                    <tr><th class="table-light">Proveedor</th><td id="orden_proveedor">—</td></tr>
                    <tr><th class="table-light">Stock actual</th><td id="orden_stock">—</td></tr>
                    <tr><th class="table-light">Stock mínimo</th><td id="orden_minimo">—</td></tr>
                </table>
                <form action="orden_compra.php" method="POST" id="formOrden" novalidate>
                    <input type="hidden" name="codigo" id="orden_codigo_h">
                    <input type="hidden" name="nombre" id="orden_nombre_h">
                    <input type="hidden" name="proveedor" id="orden_proveedor_h">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cantidad a Solicitar *</label>
                        <input type="number" name="cantidad" id="orden_cantidad" class="form-control" min="1" value="10" required>
                        <div class="invalid-feedback">Ingresa una cantidad válida.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notas adicionales</label>
                        <textarea name="notas" class="form-control" rows="2" placeholder="Urgente, condiciones especiales..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-semibold">
                        <i class="bi bi-send me-1"></i> Confirmar Orden de Compra
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>

const flash = document.getElementById('flashMsg');
if (flash) {
    setTimeout(function () {
        flash.style.transition = 'opacity 0.5s ease';
        flash.style.opacity = '0';
        setTimeout(function () { flash.remove(); }, 500);
    }, 3000);
}

/* EDITAR */
document.getElementById('modalEditar').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('edit_id').value        = btn.dataset.id;
    document.getElementById('edit_codigo').value    = btn.dataset.codigo;
    document.getElementById('edit_nombre').value    = btn.dataset.nombre;
    document.getElementById('edit_categoria').value = btn.dataset.categoria;
    document.getElementById('edit_precio').value    = btn.dataset.precio;
    document.getElementById('edit_stock').value     = btn.dataset.stock;
    document.getElementById('edit_minimo').value    = btn.dataset.minimo;
    document.getElementById('edit_proveedor').value = btn.dataset.proveedor;
});

/* ORDEN DE COMPRA */
document.getElementById('modalOrden').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('orden_nombre').textContent    = btn.dataset.producto;
    document.getElementById('orden_codigo').textContent    = btn.dataset.codigo;
    document.getElementById('orden_proveedor').textContent = btn.dataset.proveedor;
    document.getElementById('orden_stock').textContent     = btn.dataset.stock + ' unidades';
    document.getElementById('orden_minimo').textContent    = btn.dataset.minimo + ' unidades';
    document.getElementById('orden_codigo_h').value        = btn.dataset.codigo;
    document.getElementById('orden_nombre_h').value        = btn.dataset.producto;
    document.getElementById('orden_proveedor_h').value     = btn.dataset.proveedor;
});

/*FORMULARIOS */
['formAgregar', 'formEditar', 'formOrden'].forEach(function(id) {
    const form = document.getElementById(id);
    if (!form) return;
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
</body>
</html>