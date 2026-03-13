<?php
// relacionado con la tabla "usuarios" en la base de datos.

require_once __DIR__ . '/conexion.php';

function buscarUsuarioPorUsernameOEmail(string $username): array|false {
    $pdo  = conectar();
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE (username = ? OR email = ?) AND activo = 1");
    $stmt->execute([$username, $username]);
    return $stmt->fetch();
}

function actualizarUltimoAcceso(int $id): void {
    $pdo = conectar();
    $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?")
        ->execute([$id]);
}

function listarUsuarios(): array {
    $pdo = conectar();
    return $pdo->query("SELECT * FROM usuarios ORDER BY rol, nombre")->fetchAll();
}

function crearUsuario(string $nombre, string $email, string $username, string $hash, string $rol): void {
    $pdo = conectar();
    $pdo->prepare("INSERT INTO usuarios (nombre, email, username, password_hash, rol) VALUES (?,?,?,?,?)")
        ->execute([$nombre, $email, $username, $hash, $rol]);
}

function editarUsuario(int $id, string $nombre, string $email, string $username, string $rol, int $activo, string $hash = ''): void {
    $pdo = conectar();
    if ($hash !== '') {
        $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, username=?, rol=?, activo=?, password_hash=? WHERE id=?")
            ->execute([$nombre, $email, $username, $rol, $activo, $hash, $id]);
    } else {
        $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, username=?, rol=?, activo=? WHERE id=?")
            ->execute([$nombre, $email, $username, $rol, $activo, $id]);
    }
}

function eliminarUsuario(int $id): void {
    $pdo = conectar();
    $pdo->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$id]);
}
?>