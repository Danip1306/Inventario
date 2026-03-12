<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$codigo    = htmlspecialchars(trim($_POST['codigo']    ?? ''));
$nombre    = htmlspecialchars(trim($_POST['nombre']    ?? ''));
$proveedor = htmlspecialchars(trim($_POST['proveedor'] ?? ''));
$cantidad  = (int)($_POST['cantidad'] ?? 0);
$notas     = htmlspecialchars(trim($_POST['notas']     ?? ''));

if ($cantidad <= 0 || $codigo === '') {
    $_SESSION['mensaje']  = "Datos de orden inválidos.";
    $_SESSION['tipo_msg'] = 'error';
    header('Location: index.php');
    exit;
}

$numero_orden = 'OC-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 5));
$fecha = date('d/m/Y H:i');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Compra <?= $numero_orden ?></title>
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
            border-radius: 16px;
            box-shadow: 0 3px 14px rgba(106, 27, 154, 0.1);
            max-width: 700px;
            margin: 0 auto;
        }

        .card-header-orden {
            background-color: #7b1fa2;
            color: #fff;
            border-radius: 16px 16px 0 0;
            padding: 20px 24px;
        }
        .card-header-orden .numero-orden {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.6);
            margin-top: 3px;
        }

        .badge-critico {
            background-color: #fce4ec;
            color: #c62828;
            border: 1px solid #f48fb1;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .section-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #9c27b0;
            font-weight: 700;
            margin-bottom: 8px;
            border-bottom: 1px solid #f3e5f5;
            padding-bottom: 5px;
        }

        .table thead th {
            background-color: #f3e5f5;
            color: #6a1b9a;
            font-weight: 600;
            font-size: 0.82rem;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        code {
            background-color: #ede7f6;
            color: #6a1b9a;
            padding: 2px 7px;
            border-radius: 5px;
            font-size: 0.82rem;
        }

        .cantidad-box {
            background-color: #fce4ec;
            border: 1px solid #f48fb1;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }
        .cantidad-box .num {
            font-size: 3rem;
            font-weight: 700;
            color: #ad1457;
            line-height: 1;
        }
        .cantidad-box .desc {
            color: #880e4f;
            font-size: 0.85rem;
            margin-top: 4px;
        }

        .notas-box {
            background-color: #fff;
            border-left: 3px solid #ab47bc;
            border-radius: 0 8px 8px 0;
            padding: 10px 14px;
            font-size: 0.875rem;
            color: #4a148c;
        }

        .firma-linea {
            border-top: 1px solid #ce93d8;
            padding-top: 8px;
        }
        .firma-linea small {
            color: #9c27b0;
            font-size: 0.75rem;
        }

        .card-footer-orden {
            background-color: #faf7ff;
            border-top: 1px solid #f3e5f5;
            border-radius: 0 0 16px 16px;
            padding: 14px 24px;
        }

        .btn-volver {
            border: 1px solid #ce93d8;
            color: #7b1fa2;
            border-radius: 8px;
            background: #fff;
        }
        .btn-volver:hover {
            background-color: #f3e5f5;
            color: #4a148c;
            border-color: #ab47bc;
        }

        .btn-imprimir {
            background-color: #7b1fa2;
            color: #fff;
            border: none;
            border-radius: 8px;
        }
        .btn-imprimir:hover {
            background-color: #6a1b9a;
            color: #fff;
        }

        @media print {
            body { background: #fff; }
            .no-print { display: none; }
            .card {
                box-shadow: none;
                border: 1px solid #ddd;
                border-radius: 0;
                max-width: 100%;
            }
            .card-header-orden { border-radius: 0; }
            .card-footer-orden { border-radius: 0; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-custom navbar-dark mb-4 py-3 no-print">
    <div class="container">
        <span class="navbar-brand fw-bold">
            <i class="bi bi-box-seam me-2"></i>InventarioPro
        </span>
    </div>
</nav>

<div class="container pb-5" style="max-width: 760px;">

    <div class="card">

        <div class="card-header-orden d-flex justify-content-between align-items-start">
            <div>
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-cart-check me-2"></i>Orden de Compra
                </h5>
                <div class="numero-orden"><?= $numero_orden ?> &nbsp;·&nbsp; <?= $fecha ?></div>
            </div>
            <span class="badge-critico">
                <i class="bi bi-exclamation-circle me-1"></i>STOCK CRÍTICO
            </span>
        </div>

        <div class="card-body px-4 py-4">

            <div class="section-label">Información del Producto</div>
            <table class="table table-bordered table-sm mb-4">
                <tr>
                    <th class="table-light" style="width:38%">Código</th>
                    <td><code><?= $codigo ?></code></td>
                </tr>
                <tr>
                    <th class="table-light">Nombre</th>
                    <td class="fw-semibold"><?= $nombre ?></td>
                </tr>
                <tr>
                    <th class="table-light">Proveedor</th>
                    <td><?= $proveedor ?></td>
                </tr>
                <tr>
                    <th class="table-light">Fecha de Solicitud</th>
                    <td><?= $fecha ?></td>
                </tr>
            </table>

            <div class="cantidad-box mb-4">
                <div class="text-uppercase small fw-semibold" style="color:#880e4f; letter-spacing:0.06em;">
                    Cantidad Solicitada
                </div>
                <div class="num"><?= $cantidad ?></div>
                <div class="desc">unidades de <?= $nombre ?></div>
            </div>

            <?php if ($notas !== ''): ?>
                <div class="section-label">Notas adicionales</div>
                <div class="notas-box mb-4"><?= $notas ?></div>
            <?php endif; ?>

        </div>

        <div class="card-footer-orden d-flex justify-content-end gap-2 no-print">
            <a href="index.php" class="btn btn-volver btn-sm px-3">
                <i class="bi bi-arrow-left me-1"></i> Volver al sistema
            </a>
            <button onclick="window.print()" class="btn btn-imprimir btn-sm px-3">
                <i class="bi bi-printer me-1"></i> Imprimir / Guardar PDF
            </button>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>