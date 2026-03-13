<?php
session_start();
require 'conexion.php';

// Si ya está autenticado, redirigir al panel
if (!empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Por favor completa todos los campos.';
    } else {
        try {
            $pdo  = conectar();
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE (username = ? OR email = ?) AND activo = 1");
            $stmt->execute([$username, $username]);
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                // Regenerar ID de sesión para prevenir session fixation (ataque)
                session_regenerate_id(true);

                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol']    = $usuario['rol'];
                $_SESSION['usuario_email']  = $usuario['email'];
                $_SESSION['usuario_user']   = $usuario['username'];

                // Actualizar último acceso
                $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?")
                    ->execute([$usuario['id']]);

                // Redirigir a la página que intentaba acceder (o al inicio)
                $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirect");
                exit;
            } else {
                $error = 'Usuario o contraseña incorrectos.';
                sleep(1);
            }
        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            $error = 'Error del sistema. Intenta de nuevo.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InventarioPro — Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #4a148c 0%, #7b1fa2 50%, #9c27b0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 430px;
        }

        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #6a1b9a, #9c27b0);
            padding: 36px 32px 28px;
            text-align: center;
            color: #fff;
        }
        .login-header .logo-icon {
            width: 64px; height: 64px;
            background: rgba(255,255,255,0.15);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            font-size: 2rem;
            backdrop-filter: blur(4px);
        }
        .login-header h1 {
            font-size: 1.6rem;
            font-weight: 800;
            margin: 0 0 4px;
            letter-spacing: -0.02em;
        }
        .login-header p {
            font-size: 0.85rem;
            margin: 0;
            opacity: 0.8;
        }

        .login-body {
            padding: 32px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #4a148c;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1.5px solid #e1bee7;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            border-color: #9c27b0;
            box-shadow: 0 0 0 3px rgba(156, 39, 176, 0.12);
            outline: none;
        }

        .input-group .form-control { border-radius: 10px !important; }
        .input-group-text {
            background: #f3e5f5;
            border: 1.5px solid #e1bee7;
            border-right: none;
            border-radius: 10px 0 0 10px !important;
            color: #7b1fa2;
        }
        .input-group .form-control { border-left: none !important; border-radius: 0 10px 10px 0 !important; }
        .input-group .form-control:focus { border-color: #9c27b0; }

        .btn-toggle-pass {
            background: #f3e5f5;
            border: 1.5px solid #e1bee7;
            border-left: none;
            border-radius: 0 10px 10px 0 !important;
            color: #7b1fa2;
            cursor: pointer;
            padding: 0 14px;
            transition: background 0.2s;
        }
        .btn-toggle-pass:hover { background: #ede7f6; }

        .btn-login {
            background: linear-gradient(135deg, #7b1fa2, #9c27b0);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            transition: transform 0.15s, box-shadow 0.15s;
            letter-spacing: 0.02em;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(106, 27, 154, 0.35);
            color: #fff;
        }
        .btn-login:active { transform: translateY(0); }

        .alert-error {
            background: #fce4ec;
            border: 1px solid #f48fb1;
            border-left: 4px solid #e91e63;
            border-radius: 10px;
            color: #880e4f;
            padding: 10px 14px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .login-footer {
            background: #faf7ff;
            border-top: 1px solid #f3e5f5;
            padding: 16px 32px;
            text-align: center;
            font-size: 0.78rem;
            color: #9e9e9e;
        }

        .demo-info {
            background: #ede7f6;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.78rem;
            color: #4a148c;
            margin-bottom: 20px;
        }
        .demo-info strong { font-weight: 700; }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <div class="login-header">
            <div class="logo-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <h1>InventarioPro</h1>
            <p>Sistema de gestión de inventario</p>
        </div>

        <div class="login-body">

            <div class="demo-info">
                <i class="bi bi-info-circle me-1"></i>
                Credenciales por defecto: &nbsp;
                <strong>admin</strong> / <strong>Admin123!</strong>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert-error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-person me-1"></i>Usuario o correo
                    </label>
                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        placeholder="usuario o correo@ejemplo.com"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        autofocus
                        autocomplete="username"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="form-label">
                        <i class="bi bi-lock me-1"></i>Contraseña
                    </label>
                    <div class="input-group">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="btn-toggle-pass" onclick="togglePass()" title="Mostrar contraseña">
                            <i class="bi bi-eye" id="iconOjo"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar al sistema
                </button>
            </form>
        </div>

        <div class="login-footer">
            <i class="bi bi-shield-lock me-1"></i>
            Acceso restringido · Solo personal autorizado
        </div>

    </div>
</div>

<script>
function togglePass() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('iconOjo');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>