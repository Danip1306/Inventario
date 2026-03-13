<?php
// Recibir el formulario, validar, coordinar modelo y decidir qué vista mostrar.

session_start();
require_once __DIR__ . '/../model/UsuarioModel.php';

// Si ya está autenticado, redirigir al panel
if (!empty($_SESSION['usuario_id'])) {
    header('Location: IndexController.php');
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
            $usuario = buscarUsuarioPorUsernameOEmail($username);

            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                // Regenerar ID de sesión para prevenir session fixation (ataques)
                session_regenerate_id(true);

                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol']    = $usuario['rol'];
                $_SESSION['usuario_email']  = $usuario['email'];
                $_SESSION['usuario_user']   = $usuario['username'];

                actualizarUltimoAcceso($usuario['id']);

                $redirect = $_SESSION['redirect_after_login'] ?? 'IndexController.php';
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

// Pasar datos a la vista
$username_previo = htmlspecialchars($_POST['username'] ?? '');
require_once __DIR__ . '/../view/LoginView.php';
?>