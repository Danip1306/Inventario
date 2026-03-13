<!DOCTYPE html>
<!-- Responsabilidad: Mostrar la tabla de productos, filtros y modales. -->
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InventarioPro — Panel de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --plum-950:#1a0a2e; --plum-900:#240f3d; --plum-800:#3b1760;
            --plum-700:#5a2082; --plum-600:#7c3aad; --plum-500:#9b4dcb;
            --plum-400:#b97ee8; --plum-300:#d4a8f5; --plum-200:#e8d5fb;
            --plum-100:#f5effe; --plum-50:#fbf8ff;
            --rose-500:#e8567a; --rose-600:#be3b5f;
            --emerald-600:#059669;
            --white:#ffffff; --surface:#f8f5ff; --border:#ede8f8;
            --sidebar-w:240px;
        }
        * { box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            color: var(--plum-950);
            min-height: 100vh;
            display: flex;
            margin: 0;
        }

        /*  SIDEBAR  */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--plum-900);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 200;
            box-shadow: 2px 0 20px rgba(0,0,0,0.18);
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            text-decoration: none;
        }
        .sidebar-brand-icon {
            width: 36px; height: 36px;
            background: var(--plum-700);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: var(--plum-300);
            font-size: 1rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .sidebar-brand-name {
            font-family: 'Syne', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--white);
            letter-spacing: -0.01em;
        }
        .sidebar-section {
            padding: 20px 20px 6px;
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.25);
        }
        .sidebar-nav { flex: 1; padding: 8px 12px; }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 500;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            transition: background 0.15s, color 0.15s;
            margin-bottom: 2px;
        }
        .nav-item i { font-size: 1rem; width: 20px; flex-shrink: 0; }
        .nav-item:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.85); }
        .nav-item.active { background: var(--plum-700); color: var(--white); font-weight: 600; }
        .nav-badge {
            margin-left: auto;
            background: #ef4444;
            color: #fff;
            font-size: 0.62rem;
            font-weight: 700;
            border-radius: 50px;
            padding: 1px 6px;
            line-height: 1.5;
        }
        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .user-block {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 8px;
        }
        .user-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--plum-500), var(--rose-500));
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700; color: var(--white);
            flex-shrink: 0;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-name {
            font-size: 0.8rem; font-weight: 600;
            color: rgba(255,255,255,0.85);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .user-role { font-size: 0.65rem; color: var(--plum-400); font-weight: 500; }
        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            width: 100%;
            padding: 8px;
            border-radius: 9px;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            color: #fca5a5;
            font-size: 0.8rem; font-weight: 500;
            text-decoration: none; cursor: pointer;
            transition: background 0.15s;
        }
        .btn-logout:hover { background: rgba(239,68,68,0.18); color: #fca5a5; }

        /*  MAIN  */
        .main-wrapper { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .main-content { padding: 28px 32px 48px; flex: 1; }

        .page-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; }
        .page-title { font-family: 'Syne', sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--plum-900); letter-spacing: -0.02em; margin-bottom: 3px; }
        .page-sub { font-size: 0.82rem; color: #9ca3af; }

        .flash { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 0.85rem; font-weight: 500; margin-bottom: 20px; }
        .flash-success { background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 3px solid var(--emerald-600); color: #14532d; }
        .flash-danger  { background: #fff1f5; border: 1px solid #fecdd7; border-left: 3px solid var(--rose-500);     color: #9f1239; }

        .filter-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 16px 20px; margin-bottom: 16px; box-shadow: 0 1px 4px rgba(90,32,130,0.05); }
        .filter-card .form-label { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: var(--plum-700); margin-bottom: 6px; }
        .form-control, .form-select { border: 1.5px solid var(--border); border-radius: 10px; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; color: var(--plum-950); background: #fafafa; transition: border-color 0.2s, box-shadow 0.2s; }
        .form-control:focus, .form-select:focus { border-color: var(--plum-500); box-shadow: 0 0 0 3px rgba(155,77,203,0.12); background: var(--white); outline: none; }

        .table-card { background: var(--white); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; box-shadow: 0 2px 12px rgba(90,32,130,0.06); }
        .table { margin-bottom: 0; }
        .table thead th { background: var(--plum-50); color: var(--plum-700); font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; border-bottom: 1px solid var(--border); border-top: none; padding: 12px 16px; white-space: nowrap; }
        .table tbody td { padding: 13px 16px; border-color: var(--border); vertical-align: middle; font-size: 0.875rem; }
        .table-hover tbody tr { transition: background 0.12s; }
        .table-hover tbody tr:hover { background: var(--plum-50) !important; }
        tr.row-sin-stock td  { background: #fff7f9 !important; }
        tr.row-stock-bajo td { background: #fffbeb !important; }
        tr.row-sin-stock:hover td  { background: #fff0f5 !important; }
        tr.row-stock-bajo:hover td { background: #fff8e0 !important; }

        .product-code { font-family: 'SFMono-Regular','Fira Code',monospace; font-size: 0.75rem; background: var(--plum-100); color: var(--plum-700); padding: 3px 8px; border-radius: 6px; font-weight: 600; }
        .cat-pill { display: inline-block; background: var(--plum-50); color: var(--plum-700); border: 1px solid var(--plum-200); border-radius: 50px; padding: 3px 10px; font-size: 0.72rem; font-weight: 600; }
        .badge-status { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 50px; font-size: 0.72rem; font-weight: 700; white-space: nowrap; }
        .badge-ok  { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .badge-low { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .badge-out { background: #fff1f5; color: #9f1239; border: 1px solid #fecdd7; }
        .stock-num { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--plum-900); }
        .stock-min { font-size: 0.72rem; color: #9ca3af; }

        .action-wrap { display: flex; gap: 4px; }
        .btn-icon { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; cursor: pointer; transition: background 0.15s, transform 0.1s; border: 1px solid; text-decoration: none; }
        .btn-icon:active { transform: scale(0.93); }
        .btn-edit   { background: var(--plum-50);  border-color: var(--plum-200); color: var(--plum-700); }
        .btn-edit:hover { background: var(--plum-100); color: var(--plum-800); border-color: var(--plum-300); }
        .btn-delete { background: #fff1f5; border-color: #fecdd7; color: var(--rose-500); }
        .btn-delete:hover { background: #ffe4ed; }
        .btn-order  { background: #fffbeb; border-color: #fde68a; color: #92400e; }
        .btn-order:hover { background: #fef3c7; }

        .btn-primary-plum { display: inline-flex; align-items: center; gap: 7px; background: var(--plum-700); color: var(--white); border: none; border-radius: 10px; padding: 9px 18px; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.15s, transform 0.12s, box-shadow 0.15s; }
        .btn-primary-plum:hover { background: var(--plum-600); color: var(--white); transform: translateY(-1px); box-shadow: 0 6px 16px rgba(92,32,130,0.25); }
        .btn-outline-plum { display: inline-flex; align-items: center; gap: 7px; background: var(--white); color: var(--plum-700); border: 1.5px solid var(--plum-200); border-radius: 10px; padding: 8px 16px; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: background 0.15s; }
        .btn-outline-plum:hover { background: var(--plum-50); color: var(--plum-800); }

        .table-footer { background: var(--plum-50); border-top: 1px solid var(--border); padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: var(--plum-700); }
        .table-footer strong { color: var(--plum-900); }

        /* Modals */
        .modal-content { border: none; border-radius: 20px; box-shadow: 0 24px 64px rgba(0,0,0,0.18); overflow: hidden; }
        .modal-header  { background: var(--plum-900); color: var(--white); border-bottom: none; padding: 20px 24px; }
        .modal-title   { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; }
        .modal-header .btn-close { filter: invert(1) brightness(1.5); }
        .modal-body  { padding: 24px; }
        .modal-footer { border-top: 1px solid var(--border); padding: 16px 24px; background: var(--plum-50); }
        .modal .form-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--plum-700); margin-bottom: 6px; }
        .input-group-text { background: var(--plum-50); border: 1.5px solid var(--border); border-right: none; color: var(--plum-600); font-size: 0.875rem; }
        .input-group .form-control { border-left: none; }
        .btn-cancel { background: var(--white); border: 1.5px solid var(--border); color: #6b7280; border-radius: 10px; padding: 8px 18px; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; cursor: pointer; }
        .orden-info-table { border-radius: 12px; overflow: hidden; border: 1px solid var(--border) !important; }
        .orden-info-table th { background: var(--plum-50); color: var(--plum-700); font-size: 0.78rem; font-weight: 600; padding: 10px 14px; border-color: var(--border) !important; }
        .orden-info-table td { font-size: 0.85rem; padding: 10px 14px; border-color: var(--border) !important; }
        .btn-confirm-order { width: 100%; background: #92400e; color: var(--white); border: none; border-radius: 10px; padding: 11px; font-weight: 600; font-size: 0.9rem; cursor: pointer; }
        .btn-confirm-order:hover { background: #78350f; }
        .empty-state { text-align: center; padding: 56px 24px; color: #9ca3af; }
        .empty-state i { font-size: 2rem; margin-bottom: 12px; display: block; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.25s; }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .mobile-toggle { display: flex !important; }
        }
        .mobile-toggle { display: none; position: fixed; top: 12px; left: 12px; z-index: 300; width: 38px; height: 38px; background: var(--plum-800); border: none; border-radius: 9px; color: #fff; font-size: 1.1rem; align-items: center; justify-content: center; cursor: pointer; }
    </style>
</head>
<body>

<button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')">
    <i class="bi bi-list"></i>
</button>

<!-- SIDEBAR  -->
<aside class="sidebar">
    <a href="#" class="sidebar-brand">
        <div class="sidebar-brand-icon"><i class="bi bi-box-seam-fill"></i></div>
        <span class="sidebar-brand-name">InventarioPro</span>
    </a>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Inventario</div>

        <a href="../controller/IndexController.php" class="nav-item active">
            <i class="bi bi-box-seam"></i> Productos
        </a>
        <a href="../controller/IndexController.php?stock=critico" class="nav-item">
            <i class="bi bi-exclamation-triangle"></i> Stock crítico
            <?php if ($total_alertas > 0): ?>
                <span class="nav-badge"><?= $total_alertas ?></span>
            <?php endif; ?>
        </a>
        <a href="../controller/ExportarController.php" class="nav-item">
            <i class="bi bi-file-earmark-spreadsheet"></i> Exportar CSV
        </a>

        <?php if (tiene_rol('admin')): ?>
            <div class="sidebar-section">Administración</div>
            <a href="../controller/UsuariosController.php" class="nav-item">
                <i class="bi bi-people"></i> Usuarios
            </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-block">
            <div class="user-avatar"><?= strtoupper(mb_substr($usuario_sesion['nombre'],0,1)) ?></div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($usuario_sesion['nombre']) ?></div>
                <div class="user-role"><?= ucfirst($usuario_sesion['rol']) ?></div>
            </div>
        </div>
        <a href="../controller/LogoutController.php" class="btn-logout">
            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
        </a>
    </div>
</aside>

<!--  MAIN  -->
<div class="main-wrapper">
<div class="main-content">

    <?php if ($mensaje): ?>
        <div id="flashMsg" class="flash flash-<?= $tipo_msg === 'success' ? 'success' : 'danger' ?>">
            <i class="bi bi-<?= $tipo_msg === 'success' ? 'check-circle-fill' : 'x-circle-fill' ?>"></i>
            <?= htmlspecialchars($mensaje) ?>
            <button type="button" style="background:none;border:none;margin-left:auto;cursor:pointer;opacity:.5" onclick="document.getElementById('flashMsg').remove()"><i class="bi bi-x-lg" style="font-size:.75rem"></i></button>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <div>
            <div class="page-title">Lista de Productos</div>
            <div class="page-sub">Gestiona tu inventario en tiempo real</div>
        </div>
        <?php if (tiene_rol('editor')): ?>
            <button class="btn-primary-plum" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                <i class="bi bi-plus-lg"></i> Nuevo Producto
            </button>
        <?php endif; ?>
    </div>

    <div class="filter-card">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Buscar</label>
                <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Nombre, código o proveedor..." value="<?= htmlspecialchars($busqueda) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Categoría</label>
                <select name="categoria" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $categoria === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Stock</label>
                <select name="stock" class="form-select form-select-sm">
                    <option value="">Todo</option>
                    <option value="critico" <?= $filtro_stock === 'critico' ? 'selected' : '' ?>>Stock crítico</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn-primary-plum w-100" style="padding:8px 12px;font-size:.82rem;"><i class="bi bi-search"></i> Filtrar</button>
                <?php if ($busqueda || $categoria || $filtro_stock): ?>
                    <a href="../controller/IndexController.php" class="btn-outline-plum" style="padding:8px 10px;" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Código</th><th>Producto</th><th>Categoría</th><th>Proveedor</th><th>Precio</th><th>Stock</th><th>Estado</th><th style="text-align:right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                        <tr><td colspan="8"><div class="empty-state"><i class="bi bi-search"></i><p>No se encontraron productos con esos filtros.</p></div></td></tr>
                    <?php else: ?>
                        <?php foreach ($productos as $p):
                            $sin_stock  = $p['stock'] == 0;
                            $stock_bajo = $p['stock'] > 0 && $p['stock'] <= $p['stock_minimo'];
                            $row_class  = $sin_stock ? 'row-sin-stock' : ($stock_bajo ? 'row-stock-bajo' : '');
                        ?>
                        <tr class="<?= $row_class ?>">
                            <td><span class="product-code"><?= htmlspecialchars($p['codigo']) ?></span></td>
                            <td><span style="font-weight:600"><?= htmlspecialchars($p['nombre']) ?></span></td>
                            <td><span class="cat-pill"><?= htmlspecialchars($p['categoria']) ?></span></td>
                            <td style="color:#9ca3af;font-size:.82rem"><?= htmlspecialchars($p['proveedor'] ?? '—') ?></td>
                            <td style="font-weight:600;font-family:'Syne',sans-serif">$<?= number_format($p['precio'], 2, ',', '.') ?></td>
                            <td><span class="stock-num"><?= $p['stock'] ?></span> <span class="stock-min">/ <?= $p['stock_minimo'] ?> mín</span></td>
                            <td>
                                <?php if ($sin_stock): ?>
                                    <span class="badge-status badge-out"><i class="bi bi-x-circle"></i>Sin stock</span>
                                <?php elseif ($stock_bajo): ?>
                                    <span class="badge-status badge-low"><i class="bi bi-exclamation-circle"></i>Stock bajo</span>
                                <?php else: ?>
                                    <span class="badge-status badge-ok"><i class="bi bi-check-circle"></i>Disponible</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-wrap justify-content-end">
                                    <button class="btn-icon btn-edit" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditar" data-id="<?= $p['id'] ?>" data-codigo="<?= htmlspecialchars($p['codigo']) ?>" data-nombre="<?= htmlspecialchars($p['nombre']) ?>" data-categoria="<?= htmlspecialchars($p['categoria']) ?>" data-precio="<?= $p['precio'] ?>" data-stock="<?= $p['stock'] ?>" data-minimo="<?= $p['stock_minimo'] ?>" data-proveedor="<?= htmlspecialchars($p['proveedor']) ?>"><i class="bi bi-pencil"></i></button>
                                    <a href="../controller/EliminarController.php?id=<?= $p['id'] ?>" class="btn-icon btn-delete" title="Eliminar" onclick="return confirm('¿Eliminar el producto «<?= htmlspecialchars($p['nombre']) ?>»?')"><i class="bi bi-trash3"></i></a>
                                    <?php if ($sin_stock || $stock_bajo): ?>
                                        <button class="btn-icon btn-order" title="Generar orden de compra" data-bs-toggle="modal" data-bs-target="#modalOrden" data-producto="<?= htmlspecialchars($p['nombre']) ?>" data-codigo="<?= htmlspecialchars($p['codigo']) ?>" data-proveedor="<?= htmlspecialchars($p['proveedor']) ?>" data-stock="<?= $p['stock'] ?>" data-minimo="<?= $p['stock_minimo'] ?>"><i class="bi bi-cart-plus"></i></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <span>Mostrando <strong><?= count($productos) ?></strong> producto(s)</span>
            <span>Valor total: <strong>$<?= number_format($stats['valor_total'] ?? 0, 0, ',', '.') ?></strong></span>
        </div>
    </div>

</div>
</div>

<!-- AGREGAR PRODUCTO-->
<div class="modal fade" id="modalAgregar" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Agregar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../controller/AgregarController.php" method="POST" id="formAgregar" novalidate>
                <div class="modal-body"><div class="row g-3">
                    <div class="col-md-4"><label class="form-label">Código *</label><input type="text" name="codigo" class="form-control" placeholder="PROD-009" required maxlength="20"><div class="invalid-feedback">El código es obligatorio.</div></div>
                    <div class="col-md-8"><label class="form-label">Nombre del Producto *</label><input type="text" name="nombre" class="form-control" required maxlength="100"><div class="invalid-feedback">El nombre es obligatorio.</div></div>
                    <div class="col-md-6"><label class="form-label">Categoría *</label><input type="text" name="categoria" class="form-control" required list="lista-categorias"><div class="invalid-feedback">La categoria es obligatorio.</div><datalist id="lista-categorias"><?php foreach ($categorias as $cat): ?><option value="<?= htmlspecialchars($cat) ?>"><?php endforeach; ?></datalist></div>
                    <div class="col-md-6"><label class="form-label">Proveedor *</label><input type="text" name="proveedor" class="form-control" maxlength="100" required><div class="invalid-feedback">El proveedor es obligatorio.</div></div>
                    <div class="col-md-4"><label class="form-label">Precio (COP) *</label><div class="input-group"><span class="input-group-text">$</span><input type="number" name="precio" class="form-control" step="0.01" min="1" required><div class="invalid-feedback">No puede valer cero.</div></div></div>
                    <div class="col-md-4"><label class="form-label">Stock Inicial *</label><input type="number" name="stock" class="form-control" placeholder="0" min="0" required><div class="invalid-feedback">No puede ser valor negativo.</div></div>
                    <div class="col-md-4"><label class="form-label">Stock Mínimo *</label><input type="number" name="stock_minimo" class="form-control" value="5" min="1" required></div>
                </div></div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-plum"><i class="bi bi-check-lg me-1"></i>Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDITAR PRODUCTO -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../controller/EditarController.php" method="POST" id="formEditar" novalidate>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body"><div class="row g-3">
                    <div class="col-md-4"><label class="form-label">Código *</label><input type="text" name="codigo" id="edit_codigo" class="form-control" required maxlength="20"><div class="invalid-feedback">El código es obligatorio.</div></div>
                    <div class="col-md-8"><label class="form-label">Nombre del Producto *</label><input type="text" name="nombre" id="edit_nombre" class="form-control" required maxlength="100"><div class="invalid-feedback">El nombre es obligatorio.</div></div>
                    <div class="col-md-6"><label class="form-label">Categoría *</label><input type="text" name="categoria" id="edit_categoria" class="form-control" required list="lista-categorias"><div class="invalid-feedback">La categoria es obligatorio.</div></div>
                    <div class="col-md-6"><label class="form-label">Proveedor</label><input type="text" name="proveedor" id="edit_proveedor" class="form-control" maxlength="100" required><div class="invalid-feedback">El proveedor es obligatorio.</div></div>
                    <div class="col-md-4"><label class="form-label">Precio (COP) *</label><div class="input-group"><span class="input-group-text">$</span><input type="number" name="precio" id="edit_precio" class="form-control" step="0.01" min="1" required><div class="invalid-feedback">No puede valer cero.</div></div></div>
                    <div class="col-md-4"><label class="form-label">Stock *</label><input type="number" name="stock" id="edit_stock" class="form-control" min="0" required><div class="invalid-feedback">No puede ser valor negativo.</div></div>
                    <div class="col-md-4"><label class="form-label">Stock Mínimo *</label><input type="number" name="stock_minimo" id="edit_minimo" class="form-control" min="1" required></div>
                </div></div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-plum"><i class="bi bi-check-lg me-1"></i>Actualizar</button>
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
                <table class="table table-sm table-bordered orden-info-table mb-3">
                    <tr><th style="width:40%">Producto</th><td id="orden_nombre">—</td></tr>
                    <tr><th>Código</th><td id="orden_codigo">—</td></tr>
                    <tr><th>Proveedor</th><td id="orden_proveedor">—</td></tr>
                    <tr><th>Stock actual</th><td id="orden_stock">—</td></tr>
                    <tr><th>Stock mínimo</th><td id="orden_minimo">—</td></tr>
                </table>
                <form action="../controller/OrdenCompraController.php" method="POST" id="formOrden" novalidate>
                    <input type="hidden" name="codigo" id="orden_codigo_h">
                    <input type="hidden" name="nombre" id="orden_nombre_h">
                    <input type="hidden" name="proveedor" id="orden_proveedor_h">
                    <div class="mb-3"><label class="form-label">Cantidad a Solicitar *</label><input type="number" name="cantidad" id="orden_cantidad" class="form-control" min="1" value="10" required></div>
                    <div class="mb-3"><label class="form-label">Notas adicionales</label><textarea name="notas" class="form-control" rows="2" placeholder="Urgente, condiciones especiales..."></textarea></div>
                    <button type="submit" class="btn-confirm-order"><i class="bi bi-send me-1"></i> Confirmar Orden de Compra</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const flash = document.getElementById('flashMsg');
if (flash) {
    setTimeout(() => {
        flash.style.transition = 'opacity .5s';
        flash.style.opacity = '0';
        setTimeout(() => flash.remove(), 500);
    }, 3500);
}
//editar
document.getElementById('modalEditar').addEventListener('show.bs.modal', function(e){
    const b=e.relatedTarget;
    document.getElementById('edit_id').value=b.dataset.id;
    document.getElementById('edit_codigo').value=b.dataset.codigo;
    document.getElementById('edit_nombre').value=b.dataset.nombre;
    document.getElementById('edit_categoria').value=b.dataset.categoria;
    document.getElementById('edit_precio').value=b.dataset.precio;
    document.getElementById('edit_stock').value=b.dataset.stock;
    document.getElementById('edit_minimo').value=b.dataset.minimo;
    document.getElementById('edit_proveedor').value=b.dataset.proveedor;
});
//orden de compra
document.getElementById('modalOrden').addEventListener('show.bs.modal', function(e){
    const b=e.relatedTarget;
    document.getElementById('orden_nombre').textContent=b.dataset.producto;
    document.getElementById('orden_codigo').textContent=b.dataset.codigo;
    document.getElementById('orden_proveedor').textContent=b.dataset.proveedor;
    document.getElementById('orden_stock').textContent=b.dataset.stock+' unidades';
    document.getElementById('orden_minimo').textContent=b.dataset.minimo+' unidades';
    document.getElementById('orden_codigo_h').value=b.dataset.codigo;
    document.getElementById('orden_nombre_h').value=b.dataset.producto;
    document.getElementById('orden_proveedor_h').value=b.dataset.proveedor;
});
['formAgregar','formEditar','formOrden'].forEach(function(id){
    const f=document.getElementById(id); if(!f)return;
    f.addEventListener('submit',function(e){if(!f.checkValidity()){e.preventDefault();e.stopPropagation();}f.classList.add('was-validated');});
});
</script>
</body>
</html>