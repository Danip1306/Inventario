<?php
// Recibir acciones del usuario, validar datos 

session_start();
require_once __DIR__ . '/../model/UsuarioModel.php';
require_once __DIR__ . '/../auth.php';

require_role('admin');

$usuario = usuario_actual();
$mensaje = $_SESSION['mensaje_usuarios'] ?? null;
$tipo    = $_SESSION['tipo_usuarios']    ?? 'success';
unset($_SESSION['mensaje_usuarios'], $_SESSION['tipo_usuarios']);

/* Crear usuario */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'crear') {
    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $username = trim($_POST['username'] ?? '');
    $pass     = $_POST['password']      ?? '';
    $rol      = $_POST['rol']           ?? 'visor';

    if ($nombre && $email && $username && strlen($pass) >= 6) {
        try {
            $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
            crearUsuario($nombre, $email, $username, $hash, $rol);
            $_SESSION['mensaje_usuarios'] = "Usuario «$username» creado correctamente.";
            $_SESSION['tipo_usuarios']    = 'success';
        } catch (PDOException $e) {
            $_SESSION['mensaje_usuarios'] = "Error: el usuario o email ya existe.";
            $_SESSION['tipo_usuarios']    = 'error';
        }
    } else {
        $_SESSION['mensaje_usuarios'] = "Completa todos los campos (contraseña mín. 6 caracteres).";
        $_SESSION['tipo_usuarios']    = 'error';
    }
    header('Location: UsuariosController.php');
    exit;
}

/* Editar usuario */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'editar') {
    $id       = (int)$_POST['id'];
    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $username = trim($_POST['username'] ?? '');
    $rol      = $_POST['rol']           ?? 'visor';
    $activo   = isset($_POST['activo']) ? 1 : 0;
    $pass     = $_POST['password']      ?? '';

    // No permitir desactivar el propio usuario
    if ($id === (int)$usuario['id']) $activo = 1;

    try {
        if ($pass !== '') {
            if (strlen($pass) < 6) {
                $_SESSION['mensaje_usuarios'] = "La contraseña debe tener al menos 6 caracteres.";
                $_SESSION['tipo_usuarios']    = 'error';
                header('Location: UsuariosController.php');
                exit;
            }
            $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
            editarUsuario($id, $nombre, $email, $username, $rol, $activo, $hash);
        } else {
            editarUsuario($id, $nombre, $email, $username, $rol, $activo);
        }
        $_SESSION['mensaje_usuarios'] = "Usuario actualizado correctamente.";
        $_SESSION['tipo_usuarios']    = 'success';
    } catch (PDOException $e) {
        $_SESSION['mensaje_usuarios'] = "Error: el usuario o email ya está en uso.";
        $_SESSION['tipo_usuarios']    = 'error';
    }
    header('Location: UsuariosController.php');
    exit;
}

/* Eliminar usuario */
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    if ($id === (int)$usuario['id']) {
        $_SESSION['mensaje_usuarios'] = "No puedes eliminar tu propio usuario.";
        $_SESSION['tipo_usuarios']    = 'error';
    } else {
        eliminarUsuario($id);
        $_SESSION['mensaje_usuarios'] = "Usuario eliminado.";
        $_SESSION['tipo_usuarios']    = 'success';
    }
    header('Location: UsuariosController.php');
    exit;
}

/* Listar usuarios (GET normal) */
$usuarios = listarUsuarios();

// Pasar datos a la vista
require_once __DIR__ . '/../view/UsuariosView.php';
?>