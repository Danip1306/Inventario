<?php

session_start();
require_once __DIR__ . '/../model/ProductoModel.php';
require_once __DIR__ . '/../auth.php';

// auth.php ya llama require_auth(), así que si no hay sesión redirige al login

$usuario_sesion = usuario_actual();

// Leer mensaje flash de sesión 
$mensaje  = $_SESSION['mensaje']  ?? null;
$tipo_msg = $_SESSION['tipo_msg'] ?? 'success';
unset($_SESSION['mensaje'], $_SESSION['tipo_msg']);

// Leer filtros del GET
$busqueda     = trim($_GET['buscar']    ?? '');
$categoria    = $_GET['categoria']     ?? '';
$filtro_stock = $_GET['stock']         ?? '';

// Consultar modelo con los filtros
$productos      = listarProductos($busqueda, $categoria, $filtro_stock);
$total_alertas  = contarAlertas();
$categorias     = listarCategorias();
$stats          = obtenerStats();

// Pasar datos a la vista
require_once __DIR__ . '/../view/IndexView.php';
?>