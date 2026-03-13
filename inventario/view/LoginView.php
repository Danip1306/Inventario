<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InventarioPro — Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --plum-950:#1a0a2e; --plum-900:#240f3d; --plum-800:#3b1760; --plum-700:#5a2082; --plum-600:#7c3aad; --plum-500:#9b4dcb; --plum-400:#b97ee8; --plum-300:#d4a8f5; --plum-200:#e8d5fb; --plum-100:#f5effe; --plum-50:#fbf8ff; --rose-500:#e8567a; --white:#ffffff; --gray-400:#9ca3af; --gray-500:#6b7280; }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body { font-family:'DM Sans',sans-serif; min-height:100vh; background-color:var(--plum-950); display:flex; align-items:center; justify-content:center; padding:24px; position:relative; overflow:hidden; }

        body::before { content:''; position:fixed; top:-20%; right:-10%; width:600px; height:600px; background:radial-gradient(circle,rgba(124,58,173,0.3) 0%,transparent 70%); pointer-events:none; }
        body::after  { content:''; position:fixed; bottom:-15%; left:-10%; width:500px; height:500px; background:radial-gradient(circle,rgba(232,86,122,0.1) 0%,transparent 70%); pointer-events:none; }

        .scene { display:flex; width:100%; max-width:900px; min-height:520px; border-radius:28px; overflow:hidden; box-shadow:0 40px 100px rgba(0,0,0,0.6),0 0 0 1px rgba(255,255,255,0.06); animation:rise 0.55s cubic-bezier(0.22,1,0.36,1) both; }

        @keyframes rise { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

        .panel-left { flex:1; background:linear-gradient(160deg,var(--plum-800) 0%,var(--plum-900) 60%,var(--plum-950) 100%); padding:56px 52px; display:flex; flex-direction:column; justify-content:center; position:relative; overflow:hidden; }
        .panel-left::before { content:''; position:absolute; bottom:-80px; right:-80px; width:320px; height:320px; border-radius:50%; border:1px solid rgba(185,126,232,0.1); pointer-events:none; }
        .panel-left::after  { content:''; position:absolute; bottom:-40px; right:-40px; width:200px; height:200px; border-radius:50%; border:1px solid rgba(185,126,232,0.08); pointer-events:none; }

        .pl-inner { position:relative; z-index:1; }

        .logo-row  { display:flex; align-items:center; gap:12px; margin-bottom:52px; }
        .logo-icon { width:42px; height:42px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.12); border-radius:11px; display:flex; align-items:center; justify-content:center; color:var(--plum-300); font-size:1.1rem; }
        .logo-name { font-family:'Syne',sans-serif; font-size:1rem; font-weight:700; color:rgba(255,255,255,0.7); letter-spacing:-0.01em; }

        .panel-headline         { font-family:'Syne',sans-serif; font-size:2.8rem; font-weight:800; color:var(--white); line-height:1.08; letter-spacing:-0.035em; margin-bottom:0; }
        .panel-headline .accent { color:var(--plum-300); display:block; }

        .panel-divider { width:40px; height:2px; background:var(--plum-500); border-radius:2px; margin:28px 0 20px; opacity:0.7; }
        .panel-tagline { color:rgba(255,255,255,0.38); font-size:0.82rem; line-height:1.6; max-width:220px; }

        .panel-right { width:410px; background:var(--white); padding:52px 44px; display:flex; flex-direction:column; justify-content:center; }

        .form-title    { font-family:'Syne',sans-serif; font-size:1.65rem; font-weight:700; color:var(--plum-950); letter-spacing:-0.02em; margin-bottom:5px; }
        .form-subtitle { font-size:0.84rem; color:var(--gray-500); margin-bottom:32px; }

        .demo-pill { display:inline-flex; align-items:center; gap:8px; background:var(--plum-100); border:1px solid var(--plum-200); border-radius:50px; padding:6px 14px; font-size:0.75rem; color:var(--plum-700); font-weight:500; margin-bottom:28px; }
        .demo-dot  { width:6px; height:6px; border-radius:50%; background:var(--plum-500); animation:pulse 2s ease-in-out infinite; }

        @keyframes pulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:0.5; transform:scale(0.85); } }

        .field-group { margin-bottom:18px; }
        .field-label { display:block; font-size:0.75rem; font-weight:600; color:var(--plum-800); margin-bottom:7px; text-transform:uppercase; letter-spacing:0.06em; }
        .field-wrap  { position:relative; display:flex; align-items:center; }
        .field-icon  { position:absolute; left:14px; color:var(--plum-400); font-size:0.95rem; pointer-events:none; }

        .field-input { width:100%; border:1.5px solid #e5e7eb; border-radius:11px; padding:11px 14px 11px 40px; font-family:'DM Sans',sans-serif; font-size:0.88rem; color:var(--plum-950); background:#fafafa; transition:border-color 0.2s,box-shadow 0.2s,background 0.2s; outline:none; }
        .field-input::placeholder { color:#c4c9d4; }
        .field-input:focus { border-color:var(--plum-500); background:var(--white); box-shadow:0 0 0 4px rgba(155,77,203,0.1); }

        .toggle-pass { position:absolute; right:12px; background:none; border:none; color:var(--gray-400); cursor:pointer; padding:4px; font-size:0.95rem; transition:color 0.2s; }
        .toggle-pass:hover { color:var(--plum-600); }

        .alert-error { display:flex; align-items:center; gap:10px; background:#fff1f5; border:1px solid #fecdd7; border-left:3px solid var(--rose-500); border-radius:10px; padding:10px 14px; font-size:0.82rem; color:#9f1239; font-weight:500; margin-bottom:20px; }

        .btn-submit { width:100%; background:var(--plum-700); color:var(--white); border:none; border-radius:11px; padding:12px; font-family:'Syne',sans-serif; font-size:0.92rem; font-weight:600; letter-spacing:0.02em; cursor:pointer; transition:background 0.2s,transform 0.15s,box-shadow 0.2s; margin-top:8px; display:flex; align-items:center; justify-content:center; gap:8px; }
        .btn-submit:hover  { background:var(--plum-600); transform:translateY(-1px); box-shadow:0 8px 24px rgba(92,32,130,0.28); }
        .btn-submit:active { transform:translateY(0); }

        .footer-note { display:flex; align-items:center; gap:6px; margin-top:26px; padding-top:18px; border-top:1px solid #f0f0f5; font-size:0.72rem; color:var(--gray-400); }

        @media (max-width:768px) { .panel-left { display:none; } .panel-right { width:100%; padding:40px 32px; } .scene { max-width:440px; border-radius:20px; } }
    </style>
</head>
<body>

<div class="scene">

<!--Panel izquierdo--> 
    <div class="panel-left">
        <div class="pl-inner">
            <div class="logo-row">
                <div class="logo-icon"><i class="bi bi-box-seam-fill"></i></div>
                <span class="logo-name">InventarioPro</span>
            </div>
            <div class="panel-headline">
                Tu inventario,<br>
                bajo <span class="accent">control.</span>
            </div>
            <div class="panel-divider"></div>
            <p class="panel-tagline">Gestión de productos, stock y proveedores en un solo lugar.</p>
        </div>
    </div>
    <!--Panel derecho--> 
    <div class="panel-right">

        <p class="form-title">Iniciar sesión</p>
        <p class="form-subtitle">Ingresa tus credenciales para continuar</p>

        <div class="demo-pill">
            <span class="demo-dot"></span>
            Default: <strong>admin</strong> / <strong>Admin123!</strong>
        </div>

        <?php if ($error !== ''): ?>
            <div class="alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>

            <div class="field-group">
                <label class="field-label">Usuario o correo</label>
                <div class="field-wrap">
                    <i class="bi bi-person field-icon"></i>
                    <input type="text" name="username" class="field-input" placeholder="usuario o correo@ejemplo.com" value="<?= $username_previo ?>" autofocus autocomplete="username" required>
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">Contraseña</label>
                <div class="field-wrap">
                    <i class="bi bi-lock field-icon"></i>
                    <input type="password" name="password" id="password" class="field-input" placeholder="••••••••" autocomplete="current-password" required>
                    <button type="button" class="toggle-pass" onclick="togglePass()" title="Mostrar contraseña">
                        <i class="bi bi-eye" id="iconOjo"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-arrow-right-circle"></i>
                Ingresar al sistema
            </button>

        </form>

        <div class="footer-note">
            <i class="bi bi-shield-check" style="color:var(--plum-400)"></i>
            Acceso restringido · Solo personal autorizado
        </div>

    </div>

</div>

<script>
function togglePass() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('iconOjo');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'bi bi-eye-slash'; }
    else                           { input.type = 'password'; icon.className = 'bi bi-eye'; }
}
</script>
</body>
</html>