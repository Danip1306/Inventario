<!DOCTYPE html>
<!-- Responsabilidad: Mostrar la tabla de usuarios, crear/editar. -->
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios — InventarioPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --plum-950:#1a0a2e; --plum-900:#240f3d; --plum-800:#3b1760;
            --plum-700:#5a2082; --plum-600:#7c3aad; --plum-500:#9b4dcb;
            --plum-400:#b97ee8; --plum-300:#d4a8f5; --plum-200:#e8d5fb;
            --plum-100:#f5effe; --plum-50:#fbf8ff;
            --indigo-700:#3730a3; --indigo-50:#eef2ff; --indigo-100:#e0e7ff;
            --rose-500:#e8567a;
            --emerald-700:#047857; --emerald-50:#ecfdf5; --emerald-200:#a7f3d0;
            --red-700:#b91c1c; --red-50:#fef2f2; --red-200:#fecaca;
            --white:#ffffff; --surface:#f8f5ff; --border:#ede8f8;
            --sidebar-w:240px;
        }
        * { box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: var(--surface); color: var(--plum-950); min-height: 100vh; display: flex; margin: 0; }

        /* ══ SIDEBAR ══ */
        .sidebar { width: var(--sidebar-w); min-height: 100vh; background: var(--plum-900); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0; z-index: 200; box-shadow: 2px 0 20px rgba(0,0,0,0.18); }
        .sidebar-brand { display: flex; align-items: center; gap: 10px; padding: 22px 20px 18px; border-bottom: 1px solid rgba(255,255,255,0.06); text-decoration: none; }
        .sidebar-brand-icon { width: 36px; height: 36px; background: var(--plum-700); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--plum-300); font-size: 1rem; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
        .sidebar-brand-name { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--white); letter-spacing: -0.01em; }
        .sidebar-section { padding: 20px 20px 6px; font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.25); }
        .sidebar-nav { flex: 1; padding: 8px 12px; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 10px; font-size: 0.85rem; font-weight: 500; color: rgba(255,255,255,0.55); text-decoration: none; cursor: pointer; border: none; background: none; width: 100%; text-align: left; transition: background 0.15s, color 0.15s; margin-bottom: 2px; }
        .nav-item i { font-size: 1rem; width: 20px; flex-shrink: 0; }
        .nav-item:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.85); }
        .nav-item.active { background: var(--plum-700); color: var(--white); font-weight: 600; }
        .sidebar-footer { padding: 12px; border-top: 1px solid rgba(255,255,255,0.06); }
        .user-block { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); margin-bottom: 8px; }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg,var(--plum-500),var(--rose-500)); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; color: var(--white); flex-shrink: 0; }
        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 0.8rem; font-weight: 600; color: rgba(255,255,255,0.85); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 0.65rem; color: var(--plum-400); font-weight: 500; }
        .btn-logout { display: flex; align-items: center; justify-content: center; gap: 7px; width: 100%; padding: 8px; border-radius: 9px; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #fca5a5; font-size: 0.8rem; font-weight: 500; text-decoration: none; cursor: pointer; transition: background 0.15s; }
        .btn-logout:hover { background: rgba(239,68,68,0.18); color: #fca5a5; }

        /* ══ MAIN ══ */
        .main-wrapper { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .main-content { padding: 28px 32px 48px; flex: 1; }

        .page-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; }
        .page-title { font-family: 'Syne', sans-serif; font-size: 1.5rem; font-weight: 700; color: var(--plum-900); letter-spacing: -0.02em; margin-bottom: 3px; }
        .page-sub { font-size: 0.82rem; color: #9ca3af; }

        .flash { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 12px; font-size: 0.85rem; font-weight: 500; margin-bottom: 20px; }
        .flash-success { background: var(--emerald-50); border: 1px solid var(--emerald-200); border-left: 3px solid var(--emerald-700); color: #14532d; }
        .flash-danger  { background: var(--red-50);     border: 1px solid var(--red-200);     border-left: 3px solid var(--red-700);     color: #7f1d1d; }

        .table-card { background: var(--white); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; box-shadow: 0 2px 12px rgba(90,32,130,0.06); }
        .table { margin-bottom: 0; }
        .table thead th { background: var(--plum-50); color: var(--plum-700); font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; border-bottom: 1px solid var(--border); border-top: none; padding: 12px 16px; white-space: nowrap; }
        .table tbody td { padding: 13px 16px; border-color: var(--border); vertical-align: middle; font-size: 0.875rem; }
        .table-hover tbody tr:hover { background: var(--plum-50) !important; }

        .user-cell { display: flex; align-items: center; gap: 12px; }
        .avatar-lg { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg,var(--plum-600),var(--rose-500)); display: inline-flex; align-items: center; justify-content: center; color: var(--white); font-weight: 700; font-size: .9rem; box-shadow: 0 2px 8px rgba(124,58,173,.25); flex-shrink: 0; }
        .u-name { font-weight: 600; font-size: .875rem; }
        .u-email { font-size: .72rem; color: #9ca3af; }
        .you-tag { font-size: .62rem; background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; border-radius: 50px; padding: 1px 6px; font-weight: 600; }
        .ucode { font-family: monospace; font-size: .75rem; background: var(--plum-100); color: var(--plum-700); padding: 3px 8px; border-radius: 6px; font-weight: 600; }
        .role-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 50px; font-size: .72rem; font-weight: 700; }
        .r-admin  { background: var(--plum-100); color: var(--plum-800); border: 1px solid var(--plum-200); }
        .r-editor { background: var(--indigo-50); color: var(--indigo-700); border: 1px solid var(--indigo-100); }
        .st-badge { display: inline-flex; align-items: center; gap: 4px; font-size: .78rem; font-weight: 600; }
        .st-ok  { color: var(--emerald-700); }
        .st-no  { color: var(--red-700); }

        .btn-primary-plum { display: inline-flex; align-items: center; gap: 7px; background: var(--plum-700); color: var(--white); border: none; border-radius: 10px; padding: 9px 18px; font-family: 'DM Sans', sans-serif; font-size: .85rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background .15s, transform .12s; }
        .btn-primary-plum:hover { background: var(--plum-600); color: var(--white); transform: translateY(-1px); }
        .btn-icon { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: .85rem; cursor: pointer; transition: background .15s; border: 1px solid; text-decoration: none; }
        .btn-edit  { background: var(--plum-50);  border-color: var(--plum-200); color: var(--plum-700); }
        .btn-edit:hover  { background: var(--plum-100); }
        .btn-delete { background: #fff1f5; border-color: #fecdd7; color: var(--rose-500); }
        .btn-delete:hover { background: #ffe4ed; }

        .role-legend { display: flex; gap: 16px; margin-top: 14px; font-size: .75rem; color: #6b7280; align-items: center; flex-wrap: wrap; }
        .role-legend span { display: flex; align-items: center; gap: 5px; }

        /* Modals */
        .modal-content { border: none; border-radius: 20px; box-shadow: 0 24px 64px rgba(0,0,0,.18); overflow: hidden; }
        .modal-header  { background: var(--plum-900); color: var(--white); border-bottom: none; padding: 20px 24px; }
        .modal-title   { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; }
        .modal-header .btn-close { filter: invert(1) brightness(1.5); }
        .modal-body  { padding: 24px; }
        .modal-footer { border-top: 1px solid var(--border); padding: 16px 24px; background: var(--plum-50); }
        .modal .form-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--plum-700); margin-bottom: 6px; }
        .form-control, .form-select { border: 1.5px solid var(--border); border-radius: 10px; font-family: 'DM Sans', sans-serif; font-size: .875rem; background: #fafafa; transition: border-color .2s, box-shadow .2s; }
        .form-control:focus, .form-select:focus { border-color: var(--plum-500); box-shadow: 0 0 0 3px rgba(155,77,203,.12); background: var(--white); outline: none; }
        .btn-cancel { background: var(--white); border: 1.5px solid var(--border); color: #6b7280; border-radius: 10px; padding: 8px 18px; font-family: 'DM Sans', sans-serif; font-size: .85rem; cursor: pointer; }
        .form-check-input:checked { background-color: var(--plum-600); border-color: var(--plum-600); }
        .form-check-input:focus { box-shadow: 0 0 0 3px rgba(155,77,203,.2); }

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

<button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')">
    <i class="bi bi-list"></i>
</button>

<!-- ═══ SIDEBAR ═══ -->
<aside class="sidebar">
    <a href="../controller/IndexController.php" class="sidebar-brand">
        <div class="sidebar-brand-icon"><i class="bi bi-box-seam-fill"></i></div>
        <span class="sidebar-brand-name">InventarioPro</span>
    </a>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Inventario</div>
        <a href="../controller/IndexController.php" class="nav-item">
            <i class="bi bi-box-seam"></i> Productos
        </a>
        <a href="../controller/IndexController.php?stock=critico" class="nav-item">
            <i class="bi bi-exclamation-triangle"></i> Stock crítico
        </a>
        <a href="../controller/ExportarController.php" class="nav-item">
            <i class="bi bi-file-earmark-spreadsheet"></i> Exportar CSV
        </a>
        <div class="sidebar-section">Administración</div>
        <a href="../controller/UsuariosController.php" class="nav-item active">
            <i class="bi bi-people"></i> Usuarios
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-block">
            <div class="user-avatar"><?= strtoupper(mb_substr($usuario['nombre'],0,1)) ?></div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($usuario['nombre']) ?></div>
                <div class="user-role"><?= ucfirst($usuario['rol']) ?></div>
            </div>
        </div>
        <a href="../controller/LogoutController.php" class="btn-logout">
            <i class="bi bi-box-arrow-right"></i> Cerrar sesión
        </a>
    </div>
</aside>

<!-- ═══ MAIN ═══ -->
<div class="main-wrapper">
<div class="main-content">

    <div class="page-header">
        <div>
            <div class="page-title">Gestión de Usuarios</div>
            <div class="page-sub"><?= count($usuarios) ?> usuario(s) registrados</div>
        </div>
        <button class="btn-primary-plum" data-bs-toggle="modal" data-bs-target="#modalCrear">
            <i class="bi bi-person-plus"></i> Nuevo Usuario
        </button>
    </div>

    <?php if ($mensaje): ?>
        <div class="flash flash-<?= $tipo === 'success' ? 'success' : 'danger' ?>" id="flashMsg">
            <i class="bi bi-<?= $tipo === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill' ?>"></i>
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Usuario</th><th>Username</th><th>Rol</th><th>Estado</th><th>Último acceso</th><th style="text-align:right">Acciones</th></tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="avatar-lg"><?= strtoupper(mb_substr($u['nombre'],0,1)) ?></div>
                                <div>
                                    <div class="u-name"><?= htmlspecialchars($u['nombre']) ?><?php if($u['id']==$usuario['id']): ?><span class="you-tag ms-1">Tú</span><?php endif; ?></div>
                                    <div class="u-email"><?= htmlspecialchars($u['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="ucode"><?= htmlspecialchars($u['username']) ?></span></td>
                        <td><?php if($u['rol']==='admin'): ?><span class="role-badge r-admin"><i class="bi bi-shield-fill"></i>Admin</span><?php else: ?><span class="role-badge r-editor"><i class="bi bi-pencil"></i>Editor</span><?php endif; ?></td>
                        <td><?php if($u['activo']): ?><span class="st-badge st-ok"><i class="bi bi-check-circle-fill"></i>Activo</span><?php else: ?><span class="st-badge st-no"><i class="bi bi-x-circle-fill"></i>Inactivo</span><?php endif; ?></td>
                        <td style="color:#9ca3af;font-size:.8rem"><?= $u['ultimo_acceso'] ? date('d/m/Y H:i',strtotime($u['ultimo_acceso'])) : '—' ?></td>
                        <td>
                            <div style="display:flex;gap:4px;justify-content:flex-end">
                                <button class="btn-icon btn-edit" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditar" data-id="<?=$u['id']?>" data-nombre="<?=htmlspecialchars($u['nombre'])?>" data-email="<?=htmlspecialchars($u['email'])?>" data-username="<?=htmlspecialchars($u['username'])?>" data-rol="<?=$u['rol']?>" data-activo="<?=$u['activo']?>"><i class="bi bi-pencil"></i></button>
                                <?php if($u['id']!=$usuario['id']): ?>
                                    <a href="UsuariosController.php?eliminar=<?=$u['id']?>" class="btn-icon btn-delete" title="Eliminar" onclick="return confirm('¿Eliminar al usuario «<?=htmlspecialchars($u['nombre'])?>»?')"><i class="bi bi-trash"></i></a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="role-legend">
        <span><span class="role-badge r-admin"><i class="bi bi-shield-fill"></i>Admin</span> Acceso total al sistema</span>
        <span><span class="role-badge r-editor"><i class="bi bi-pencil"></i>Editor</span> Agregar y editar productos</span>
    </div>

</div>
</div>

<!-- MODAL CREAR -->
<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Nuevo Usuario</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" novalidate>
                <input type="hidden" name="accion" value="crear">
                <div class="modal-body row g-3">
                    <div class="col-12"><label class="form-label">Nombre completo *</label><input type="text" name="nombre" class="form-control" required maxlength="100"></div>
                    <div class="col-md-6"><label class="form-label">Usuario *</label><input type="text" name="username" class="form-control" required maxlength="50" autocomplete="off"></div>
                    <div class="col-md-6"><label class="form-label">Correo *</label><input type="email" name="email" class="form-control" required maxlength="150"></div>
                    <div class="col-md-6"><label class="form-label">Contraseña * <small style="font-size:.7rem;color:#9ca3af;text-transform:none;letter-spacing:0">(mín. 6)</small></label><input type="password" name="password" class="form-control" required minlength="6" autocomplete="new-password"></div>
                    <div class="col-md-6"><label class="form-label">Rol *</label><select name="rol" class="form-select"><option value="editor">Editor</option><option value="admin">Admin</option></select></div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-plum"><i class="bi bi-check-lg me-1"></i>Crear usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Usuario</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" novalidate>
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body row g-3">
                    <div class="col-12"><label class="form-label">Nombre completo *</label><input type="text" name="nombre" id="edit_nombre" class="form-control" required maxlength="100"></div>
                    <div class="col-md-6"><label class="form-label">Usuario *</label><input type="text" name="username" id="edit_username" class="form-control" required maxlength="50"></div>
                    <div class="col-md-6"><label class="form-label">Correo *</label><input type="email" name="email" id="edit_email" class="form-control" required maxlength="150"></div>
                    <div class="col-md-6"><label class="form-label">Nueva contraseña <small style="font-size:.7rem;color:#9ca3af;text-transform:none;letter-spacing:0">(vacío = sin cambio)</small></label><input type="password" name="password" class="form-control" minlength="6" autocomplete="new-password"></div>
                    <div class="col-md-4"><label class="form-label">Rol *</label><select name="rol" id="edit_rol" class="form-select"><option value="editor">Editor</option><option value="admin">Admin</option></select></div>
                    <div class="col-md-2 d-flex align-items-end pb-1"><div class="form-check"><input class="form-check-input" type="checkbox" name="activo" id="edit_activo" value="1"><label class="form-check-label fw-semibold" for="edit_activo" style="font-size:.85rem">Activo</label></div></div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-plum"><i class="bi bi-check-lg me-1"></i>Guardar cambios</button>
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

document.getElementById('modalEditar').addEventListener('show.bs.modal',e=>{
    const b=e.relatedTarget;
    document.getElementById('edit_id').value=b.dataset.id;
    document.getElementById('edit_nombre').value=b.dataset.nombre;
    document.getElementById('edit_email').value=b.dataset.email;
    document.getElementById('edit_username').value=b.dataset.username;
    document.getElementById('edit_rol').value=b.dataset.rol;
    document.getElementById('edit_activo').checked=b.dataset.activo==='1';
});
</script>
</body>
</html>