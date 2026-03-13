<?php
/**
 * control de roles
 *
 * Para páginas que requieren un rol mínimo:
 *   require_role('admin');
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica que el usuario esté autenticado.
 * Si no lo está, redirige al login.
 */
function require_auth(): void {
    if (empty($_SESSION['usuario_id'])) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

/**
 * Verifica que el usuario tenga el rol requerido.
 * Jerarquía: admin > editor 
 */
function require_role(string $rol_minimo): void {
    require_auth();

    $jerarquia = ['editor' => 1, 'admin' => 2];
    $rol_usuario = $_SESSION['usuario_rol'] ?? 'editor';

    if (($jerarquia[$rol_usuario] ?? 0) < ($jerarquia[$rol_minimo] ?? 99)) {
        $_SESSION['mensaje']  = "No tienes permisos para realizar esa acción.";
        $_SESSION['tipo_msg'] = 'error';
        header('Location: index.php');
        exit;
    }
}

/**
 * Devuelve verdadero si el usuario actual tiene al menos el rol definido.
 */
function tiene_rol(string $rol_minimo): bool {
    $jerarquia = ['editor' => 1, 'admin' => 2];
    $rol_usuario = $_SESSION['usuario_rol'] ?? 'editor';
    return ($jerarquia[$rol_usuario] ?? 0) >= ($jerarquia[$rol_minimo] ?? 99);
}

/**
 * Datos del usuario en sesión .
 */
function usuario_actual(): array {
    return [
        'id'     => $_SESSION['usuario_id']     ?? null,
        'nombre' => $_SESSION['usuario_nombre'] ?? 'Invitado',
        'rol'    => $_SESSION['usuario_rol']    ?? 'editor',
        'email'  => $_SESSION['usuario_email']  ?? '',
    ];
}

require_auth();