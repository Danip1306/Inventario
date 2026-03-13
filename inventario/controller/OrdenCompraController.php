<?php
// Validar los datos del POST y preparar la orden para mostrarla

session_start();
require_once __DIR__ . '/../auth.php';

require_role('editor');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: IndexController.php');
    exit;
}

$codigo    = trim($_POST['codigo']    ?? '');
$nombre    = trim($_POST['nombre']    ?? '');
$proveedor = trim($_POST['proveedor'] ?? '');
$cantidad  = (int)($_POST['cantidad'] ?? 0);
$notas     = trim($_POST['notas']     ?? '');

if ($cantidad <= 0 || $codigo === '') {
    $_SESSION['mensaje']  = "Datos de orden inválidos.";
    $_SESSION['tipo_msg'] = 'error';
    header('Location: IndexController.php');
    exit;
}

// Preparar datos para la vista
$numero_orden = 'OC-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 5));
$fecha        = date('d/m/Y H:i');

$codigo    = htmlspecialchars($codigo);
$nombre    = htmlspecialchars($nombre);
$proveedor = htmlspecialchars($proveedor);
$notas     = htmlspecialchars($notas);

require_once __DIR__ . '/../view/OrdenCompraView.php';