<?php
// Verificar autenticación y roles de usuario.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*Verifica que el usuario esté autenticado.*/
function require_auth(): void {
    if (empty($_SESSION['usuario_id'])) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ../controller/LoginController.php');
        exit;
    }
}

/*Verifica que el usuario tenga el rol requerido.
 * Jerarquía: editor < admin*/
function require_role(string $rol_minimo): void {
    require_auth();

    $jerarquia   = ['editor' => 1, 'admin' => 2];
    $rol_usuario = $_SESSION['usuario_rol'] ?? 'editor';

    if (($jerarquia[$rol_usuario] ?? 0) < ($jerarquia[$rol_minimo] ?? 99)) {
        $_SESSION['mensaje']  = "No tienes permisos para realizar esa acción.";
        $_SESSION['tipo_msg'] = 'error';
        header('Location: ../controller/IndexController.php');
        exit;
    }
}

/*Devuelve true si el usuario tiene rol definido*/
function tiene_rol(string $rol_minimo): bool {
    $jerarquia   = ['editor' => 1, 'admin' => 2];
    $rol_usuario = $_SESSION['usuario_rol'] ?? 'editor';
    return ($jerarquia[$rol_usuario] ?? 0) >= ($jerarquia[$rol_minimo] ?? 99);
}

/*Retorna los datos del usuario autenticado desde la sesión.*/
function usuario_actual(): array {
    return [
        'id'     => $_SESSION['usuario_id']     ?? null,
        'nombre' => $_SESSION['usuario_nombre'] ?? 'Invitado',
        'rol'    => $_SESSION['usuario_rol']    ?? 'editor',
        'email'  => $_SESSION['usuario_email']  ?? '',
    ];
}

require_auth();