<?php
session_start();
require 'conexion.php';
require 'auth.php';

require_role('admin');

$pdo     = conectar();
$usuario = usuario_actual();
$error   = $_SESSION['mensaje_usuarios'] ?? null;
$tipo    = $_SESSION['tipo_usuarios']    ?? 'success';
unset($_SESSION['mensaje_usuarios'], $_SESSION['tipo_usuarios']);

/* crear usuario */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'crear') {
    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $username = trim($_POST['username'] ?? '');
    $pass     = $_POST['password']      ?? '';
    $rol      = $_POST['rol']           ?? 'visor';

    if ($nombre && $email && $username && strlen($pass) >= 6) {
        try {
            $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
            $pdo->prepare("INSERT INTO usuarios (nombre, email, username, password_hash, rol) VALUES (?,?,?,?,?)")
                ->execute([$nombre, $email, $username, $hash, $rol]);
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
    header('Location: usuarios.php');
    exit;
}

/* editar usuario */
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
                header('Location: usuarios.php');
                exit;
            }
            $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
            $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, username=?, rol=?, activo=?, password_hash=? WHERE id=?")
                ->execute([$nombre, $email, $username, $rol, $activo, $hash, $id]);
        } else {
            $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, username=?, rol=?, activo=? WHERE id=?")
                ->execute([$nombre, $email, $username, $rol, $activo, $id]);
        }
        $_SESSION['mensaje_usuarios'] = "Usuario actualizado correctamente.";
        $_SESSION['tipo_usuarios']    = 'success';
    } catch (PDOException $e) {
        $_SESSION['mensaje_usuarios'] = "Error: el usuario o email ya está en uso.";
        $_SESSION['tipo_usuarios']    = 'error';
    }
    header('Location: usuarios.php');
    exit;
}

/* eliminar usuario*/
if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    if ($id === (int)$usuario['id']) {
        $_SESSION['mensaje_usuarios'] = "No puedes eliminar tu propio usuario.";
        $_SESSION['tipo_usuarios']    = 'error';
    } else {
        $pdo->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$id]);
        $_SESSION['mensaje_usuarios'] = "Usuario eliminado.";
        $_SESSION['tipo_usuarios']    = 'success';
    }
    header('Location: usuarios.php');
    exit;
}

/*lista */
$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY rol, nombre")->fetchAll();

$badge_rol = [
    'admin'  => '<span class="badge bg-purple">Admin</span>',
    'editor' => '<span class="badge bg-indigo">Editor</span>',
    'visor'  => '<span class="badge bg-secondary">Visor</span>',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios — InventarioPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Nunito',sans-serif; background:#f5f0ff; }
        .navbar-custom { background-color:#6a1b9a; }
        .card { border:none; border-radius:14px; box-shadow:0 2px 10px rgba(106,27,154,.08); }

        .bg-purple { background-color:#7b1fa2 !important; }
        .bg-indigo  { background-color:#3949ab !important; }

        .table thead th {
            background-color:#f3e5f5; color:#6a1b9a;
            font-weight:600; font-size:.82rem;
            text-transform:uppercase; letter-spacing:.04em;
        }
        .table { border-radius:14px; overflow:hidden; }

        .btn-morado { background:#7b1fa2; color:#fff; border:none; border-radius:8px; }
        .btn-morado:hover { background:#6a1b9a; color:#fff; }
        .btn-outline-morado { border:1px solid #ab47bc; color:#7b1fa2; border-radius:8px; background:#fff; }
        .btn-outline-morado:hover { background:#f3e5f5; }

        .flash-success { background:#e8f5e9; border:1px solid #a5d6a7; border-left:4px solid #43a047; border-radius:10px; color:#1b5e20; }
        .flash-danger  { background:#fce4ec; border:1px solid #f48fb1; border-left:4px solid #e91e63; border-radius:10px; color:#880e4f; }

        .modal-content { border:none; border-radius:16px; }
        .modal-header  { background:#7b1fa2; color:#fff; border-radius:16px 16px 0 0; }
        .btn-close-white { filter:invert(1) brightness(2); }

        .form-control, .form-select {
            border:1.5px solid #e1bee7; border-radius:9px;
        }
        .form-control:focus, .form-select:focus {
            border-color:#9c27b0; box-shadow:0 0 0 3px rgba(156,39,176,.12);
        }

        .avatar {
            width:38px; height:38px; border-radius:50%;
            background:linear-gradient(135deg,#7b1fa2,#e91e63);
            display:inline-flex; align-items:center; justify-content:center;
            color:#fff; font-weight:700; font-size:.95rem;
        }
        .yo-badge { font-size:.7rem; background:#e3f2fd; color:#1565c0; border:1px solid #90caf9; border-radius:20px; padding:2px 8px; }

        .rol-tag {
            display:inline-block; border-radius:20px; padding:3px 10px;
            font-size:.75rem; font-weight:700;
        }
        .rol-admin  { background:#ede7f6; color:#4a148c; border:1px solid #ce93d8; }
        .rol-editor { background:#e8eaf6; color:#283593; border:1px solid #9fa8da; }
        .activo-yes { color:#2e7d32; font-weight:600; }
        .activo-no  { color:#c62828; font-weight:600; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-custom navbar-dark py-2">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-box-seam me-2"></i>InventarioPro
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white-50 small d-none d-md-inline">
                <i class="bi bi-person-circle me-1"></i>
                <?= htmlspecialchars($usuario['nombre']) ?>
                <span class="rol-tag rol-<?= $usuario['rol'] ?> ms-1"><?= ucfirst($usuario['rol']) ?></span>
            </span>
            <a href="index.php" class="btn btn-sm btn-outline-light border-0 px-3">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
            <a href="logout.php" class="btn btn-sm btn-light text-danger fw-semibold px-3">
                <i class="bi bi-box-arrow-right me-1"></i>Salir
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-4">

    <!-- Título -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-0 fw-bold" style="color:#4a148c;">
                <i class="bi bi-people-fill me-2"></i>Gestión de Usuarios
            </h4>
            <small class="text-muted"><?= count($usuarios) ?> usuario(s) registrados</small>
        </div>
        <button class="btn btn-morado" data-bs-toggle="modal" data-bs-target="#modalCrear">
            <i class="bi bi-person-plus me-1"></i>Nuevo Usuario
        </button>
    </div>

    <!-- Flash -->
    <?php if ($error): ?>
        <div class="p-3 mb-3 flash-<?= $tipo === 'success' ? 'success' : 'danger' ?>" id="flashMsg">
            <i class="bi bi-<?= $tipo === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Tabla -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:44px"></th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último acceso</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td>
                            <div class="avatar"><?= strtoupper(mb_substr($u['nombre'],0,1)) ?></div>
                        </td>
                        <td class="fw-semibold">
                            <?= htmlspecialchars($u['nombre']) ?>
                            <?php if ($u['id'] == $usuario['id']): ?>
                                <span class="yo-badge ms-1">Tú</span>
                            <?php endif; ?>
                        </td>
                        <td><code><?= htmlspecialchars($u['username']) ?></code></td>
                        <td class="text-muted small"><?= htmlspecialchars($u['email']) ?></td>
                        <td><span class="rol-tag rol-<?= $u['rol'] ?>"><?= ucfirst($u['rol']) ?></span></td>
                        <td>
                            <?php if ($u['activo']): ?>
                                <span class="activo-yes"><i class="bi bi-check-circle-fill me-1"></i>Activo</span>
                            <?php else: ?>
                                <span class="activo-no"><i class="bi bi-x-circle-fill me-1"></i>Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small">
                            <?= $u['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($u['ultimo_acceso'])) : '—' ?>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-morado"
                                title="Editar"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar"
                                data-id="<?= $u['id'] ?>"
                                data-nombre="<?= htmlspecialchars($u['nombre']) ?>"
                                data-email="<?= htmlspecialchars($u['email']) ?>"
                                data-username="<?= htmlspecialchars($u['username']) ?>"
                                data-rol="<?= $u['rol'] ?>"
                                data-activo="<?= $u['activo'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <?php if ($u['id'] != $usuario['id']): ?>
                            <a href="usuarios.php?eliminar=<?= $u['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               title="Eliminar"
                               onclick="return confirm('¿Eliminar al usuario «<?= htmlspecialchars($u['nombre']) ?>»? Esta acción no se puede deshacer.')">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- roles -->
    <div class="mt-3 d-flex gap-3 flex-wrap" style="font-size:.8rem; color:#666;">
        <span><span class="rol-tag rol-admin">Admin</span> Acceso total</span>
        <span><span class="rol-tag rol-editor">Editor</span> Agregar y editar productos</span>
    </div>
</div>


<!-- CREAR -->
<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" novalidate>
                <input type="hidden" name="accion" value="crear">
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nombre completo *</label>
                        <input type="text" name="nombre" class="form-control" required maxlength="100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Usuario *</label>
                        <input type="text" name="username" class="form-control" required maxlength="50" autocomplete="off">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Correo *</label>
                        <input type="email" name="email" class="form-control" required maxlength="150">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contraseña * <small class="text-muted fw-normal">(mín. 6)</small></label>
                        <input type="password" name="password" class="form-control" required minlength="6" autocomplete="new-password">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Rol *</label>
                        <select name="rol" class="form-select">
                            <option value="editor">Editor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-morado"><i class="bi bi-check-lg me-1"></i>Crear usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- EDITAR -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" novalidate>
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nombre completo *</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required maxlength="100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Usuario *</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required maxlength="50">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Correo *</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required maxlength="150">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nueva contraseña <small class="text-muted fw-normal">(dejar vacío = sin cambio)</small></label>
                        <input type="password" name="password" class="form-control" minlength="6" autocomplete="new-password">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Rol *</label>
                        <select name="rol" id="edit_rol" class="form-select">
                            <option value="editor">Editor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end pb-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="activo" id="edit_activo" value="1">
                            <label class="form-check-label fw-semibold" for="edit_activo">Activo</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-morado"><i class="bi bi-check-lg me-1"></i>Guardar cambios</button>
                </div>
            </form>
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

// editar
document.getElementById('modalEditar').addEventListener('show.bs.modal', e => {
    const b = e.relatedTarget;
    document.getElementById('edit_id').value       = b.dataset.id;
    document.getElementById('edit_nombre').value   = b.dataset.nombre;
    document.getElementById('edit_email').value    = b.dataset.email;
    document.getElementById('edit_username').value = b.dataset.username;
    document.getElementById('edit_rol').value      = b.dataset.rol;
    document.getElementById('edit_activo').checked = b.dataset.activo === '1';
});
</script>
</body>
</html>