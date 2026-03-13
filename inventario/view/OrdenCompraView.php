<!DOCTYPE html>
<!-- Responsabilidad: Mostrar la orden de compra generada lista para imprimir. -->
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Compra <?= $numero_orden ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --plum-950:#1a0a2e; --plum-900:#240f3d; --plum-800:#3b1760;
            --plum-700:#5a2082; --plum-600:#7c3aad; --plum-500:#9b4dcb;
            --plum-400:#b97ee8; --plum-300:#d4a8f5; --plum-200:#e8d5fb;
            --plum-100:#f5effe; --plum-50:#fbf8ff;
            --rose-500:#e8567a;
            --amber-900:#78350f; --amber-700:#b45309; --amber-200:#fde68a; --amber-50:#fffbeb;
            --white:#ffffff; --surface:#f8f5ff; --border:#ede8f8;
            --sidebar-w:240px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: var(--surface); color: var(--plum-950); min-height: 100vh; display: flex; }

        /*  SIDEBAR  */
        .sidebar { width: var(--sidebar-w); min-height: 100vh; background: var(--plum-900); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0; z-index: 200; box-shadow: 2px 0 20px rgba(0,0,0,0.18); }
        .sidebar-brand { display: flex; align-items: center; gap: 10px; padding: 22px 20px 18px; border-bottom: 1px solid rgba(255,255,255,0.06); text-decoration: none; }
        .sidebar-brand-icon { width: 36px; height: 36px; background: var(--plum-700); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--plum-300); font-size: 1rem; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
        .sidebar-brand-name { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--white); letter-spacing: -0.01em; }
        .sidebar-section { padding: 20px 20px 6px; font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.25); }
        .sidebar-nav { flex: 1; padding: 8px 12px; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 10px; font-size: 0.85rem; font-weight: 500; color: rgba(255,255,255,0.55); text-decoration: none; border: none; background: none; width: 100%; text-align: left; transition: background 0.15s, color 0.15s; margin-bottom: 2px; cursor: pointer; }
        .nav-item i { font-size: 1rem; width: 20px; flex-shrink: 0; }
        .nav-item:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.85); }
        .nav-item.active { background: var(--plum-700); color: var(--white); font-weight: 600; }
        .sidebar-footer { padding: 12px; border-top: 1px solid rgba(255,255,255,0.06); }
        .user-block { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); margin-bottom: 8px; }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg,var(--plum-500),var(--rose-500)); display: flex; align-items: center; justify-content: center; font-size: .7rem; font-weight: 700; color: var(--white); flex-shrink: 0; }
        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: .8rem; font-weight: 600; color: rgba(255,255,255,.85); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: .65rem; color: var(--plum-400); font-weight: 500; }
        .btn-logout { display: flex; align-items: center; justify-content: center; gap: 7px; width: 100%; padding: 8px; border-radius: 9px; background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.2); color: #fca5a5; font-size: .8rem; font-weight: 500; text-decoration: none; cursor: pointer; transition: background .15s; }
        .btn-logout:hover { background: rgba(239,68,68,.18); color: #fca5a5; }

        /*  MAIN  */
        .main-wrapper { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .main-content { padding: 32px; flex: 1; display: flex; flex-direction: column; align-items: flex-start; }

        /* Document */
        .doc-card { background: var(--white); border: 1px solid var(--border); border-radius: 24px; overflow: hidden; box-shadow: 0 8px 32px rgba(90,32,130,.1); max-width: 680px; width: 100%; }
        .doc-header { background: var(--plum-900); padding: 24px 28px; display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; }
        .doc-eyebrow { font-size: .65rem; text-transform: uppercase; letter-spacing: .1em; color: var(--plum-400); font-weight: 600; margin-bottom: 5px; }
        .doc-title { font-family: 'Syne', sans-serif; font-size: 1.4rem; font-weight: 700; color: var(--white); letter-spacing: -.02em; margin-bottom: 5px; }
        .doc-meta { font-size: .74rem; color: rgba(255,255,255,.4); }
        .doc-meta strong { color: rgba(255,255,255,.65); }
        .crit-badge { display: inline-flex; align-items: center; gap: 5px; background: rgba(232,86,122,.15); border: 1px solid rgba(232,86,122,.3); color: #fda4af; border-radius: 50px; padding: 5px 13px; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; white-space: nowrap; }
        .doc-body { padding: 28px; }
        .sec-lbl { font-size: .65rem; text-transform: uppercase; letter-spacing: .1em; color: var(--plum-500); font-weight: 700; margin-bottom: 10px; padding-bottom: 7px; border-bottom: 1px solid var(--border); }
        .info-tbl { width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; margin-bottom: 24px; }
        .info-tbl tr td { padding: 10px 14px; font-size: .83rem; border-bottom: 1px solid var(--border); }
        .info-tbl tr:last-child td { border-bottom: none; }
        .info-tbl .lbl { background: var(--plum-50); color: var(--plum-700); font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; width: 36%; }
        .pcode { font-family: monospace; font-size: .75rem; background: var(--plum-100); color: var(--plum-700); padding: 2px 7px; border-radius: 5px; font-weight: 600; }
        .qty-spot { background: var(--amber-50); border: 1px solid var(--amber-200); border-radius: 14px; padding: 24px; text-align: center; margin-bottom: 24px; }
        .qty-ey { font-size: .65rem; text-transform: uppercase; letter-spacing: .12em; color: var(--amber-700); font-weight: 700; margin-bottom: 8px; }
        .qty-num { font-family: 'Syne', sans-serif; font-size: 3.5rem; font-weight: 800; color: var(--amber-900); line-height: 1; margin-bottom: 6px; letter-spacing: -.03em; }
        .qty-desc { font-size: .82rem; color: var(--amber-700); }
        .notes-box { background: var(--plum-50); border: 1px solid var(--plum-200); border-left: 3px solid var(--plum-400); border-radius: 0 10px 10px 0; padding: 11px 14px; font-size: .83rem; color: var(--plum-800); line-height: 1.6; margin-bottom: 24px; }
        .doc-footer { background: var(--plum-50); border-top: 1px solid var(--border); padding: 18px 28px; display: flex; justify-content: flex-end; gap: 9px; }
        .btn-sec { display: inline-flex; align-items: center; gap: 6px; background: var(--white); color: var(--plum-700); border: 1.5px solid var(--plum-200); border-radius: 9px; padding: 8px 16px; font-size: .82rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: background .15s; }
        .btn-sec:hover { background: var(--plum-50); }
        .btn-pri { display: inline-flex; align-items: center; gap: 6px; background: var(--plum-700); color: var(--white); border: none; border-radius: 9px; padding: 8px 16px; font-size: .82rem; font-weight: 600; cursor: pointer; transition: background .15s; }
        .btn-pri:hover { background: var(--plum-600); }

        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .sidebar { display: none !important; }
            .main-wrapper { margin-left: 0 !important; }
            .doc-card { box-shadow: none; border: 1px solid #ddd; border-radius: 0; max-width: 100%; }
            .main-content { padding: 0; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform .25s; }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .mobile-toggle { display: flex !important; }
        }
        .mobile-toggle { display: none; position: fixed; top: 12px; left: 12px; z-index: 300; width: 38px; height: 38px; background: var(--plum-800); border: none; border-radius: 9px; color: #fff; font-size: 1.1rem; align-items: center; justify-content: center; cursor: pointer; }
    </style>
</head>
<body>

<button class="mobile-toggle no-print" onclick="document.querySelector('.sidebar').classList.toggle('open')">
    <i class="bi bi-list"></i>
</button>

<!-- SIDEBAR  -->
<aside class="sidebar no-print">
    <a href="../controller/IndexController.php" class="sidebar-brand">
        <div class="sidebar-brand-icon"><i class="bi bi-box-seam-fill"></i></div>
        <span class="sidebar-brand-name">InventarioPro</span>
    </a>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Inventario</div>
        <a href="../controller/IndexController.php" class="nav-item">
            <i class="bi bi-box-seam"></i> Productos
        </a>
        <a href="../controller/IndexController.php?stock=critico" class="nav-item active">
            <i class="bi bi-cart-check"></i> Órdenes de Compra
        </a>
        <a href="../controller/ExportarController.php" class="nav-item">
            <i class="bi bi-file-earmark-spreadsheet"></i> Exportar CSV
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-block">
            <div class="user-avatar">A</div>
            <div class="user-info">
                <div class="user-name">Administrador</div>
                <div class="user-role">Admin</div>
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

    <div class="doc-card">
        <div class="doc-header">
            <div>
                <div class="doc-eyebrow"><i class="bi bi-cart-check"></i> Orden de Compra</div>
                <div class="doc-title"><?= $nombre ?></div>
                <div class="doc-meta"><strong><?= $numero_orden ?></strong> &nbsp;·&nbsp; <?= $fecha ?></div>
            </div>
            <div class="crit-badge"><i class="bi bi-exclamation-circle"></i> Stock Crítico</div>
        </div>
        <div class="doc-body">
            <div class="sec-lbl">Información del Producto</div>
            <table class="info-tbl">
                <tr><td class="lbl">Código</td><td><span class="pcode"><?= $codigo ?></span></td></tr>
                <tr><td class="lbl">Nombre</td><td style="font-weight:600"><?= $nombre ?></td></tr>
                <tr><td class="lbl">Proveedor</td><td><?= $proveedor ?></td></tr>
                <tr><td class="lbl">Fecha de Solicitud</td><td><?= $fecha ?></td></tr>
            </table>

            <div class="sec-lbl">Cantidad Solicitada</div>
            <div class="qty-spot">
                <div class="qty-ey">Unidades requeridas</div>
                <div class="qty-num"><?= $cantidad ?></div>
                <div class="qty-desc">unidades de <?= $nombre ?></div>
            </div>

            <?php if ($notas !== ''): ?>
                <div class="sec-lbl">Notas adicionales</div>
                <div class="notes-box"><?= $notas ?></div>
            <?php endif; ?>
        </div>
        <div class="doc-footer no-print">
            <a href="../controller/IndexController.php" class="btn-sec"><i class="bi bi-arrow-left"></i> Volver al sistema</a>
            <button onclick="window.print()" class="btn-pri"><i class="bi bi-printer"></i> Imprimir / PDF</button>
        </div>
    </div>

</div>
</div>

</body>
</html>