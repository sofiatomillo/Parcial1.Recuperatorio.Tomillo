<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/auth_admin.php';

if (adminLogueado()) {
    header('Location: admin.php');
    exit;
}

$error = '';
$usuario_input = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_input = trim($_POST['usuario'] ?? '');
    $password      = $_POST['password'] ?? '';
    $csrf          = $_POST['csrf_token'] ?? '';

    if (!verificarCsrfToken($csrf)) {
        $error = 'Token de seguridad inválido. Recargá la página e intentá nuevamente.';
    } elseif (empty($usuario_input) || empty($password)) {
        $error = 'Por favor completá tu usuario/email y contraseña.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "SELECT id, usuario, nombre_completo, email, password_hash, rol, activo
                 FROM administradores
                 WHERE (usuario = ? OR email = ?)
                 LIMIT 1"
            );
            $stmt->execute([$usuario_input, $usuario_input]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && (int)$admin['activo'] === 1 && password_verify($password, $admin['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id']      = (int)$admin['id'];
                $_SESSION['admin_usuario'] = $admin['usuario'];
                $_SESSION['admin_nombre']  = $admin['nombre_completo'];
                $_SESSION['admin_rol']     = $admin['rol'];

                $upd = $pdo->prepare("UPDATE administradores SET ultimo_acceso = NOW() WHERE id = ?");
                $upd->execute([$admin['id']]);

                header('Location: admin.php');
                exit;
            } elseif ($admin && (int)$admin['activo'] !== 1) {
                $error = 'Esta cuenta de administrador se encuentra deshabilitada.';
            } else {
                $error = 'Usuario o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            $error = 'Error de conexión con la tabla de administradores. Verificá haber importado database.sql.';
        }
    }
}

$csrf_token = generarCsrfToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrador | Escuela de Música A Todo Ritmo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Carter+One&family=Mukta:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <style>
        .login-wrapper {
            max-width: 480px;
            margin: 60px auto 80px auto;
            padding: 0 20px;
        }
        .login-card {
            background: #ffffff;
            border: 3px solid #181818;
            border-radius: 24px;
            padding: 36px;
            box-shadow: 6px 6px 0px #181818;
        }
        .login-badge {
            display: inline-block;
            background: #F3D84F;
            color: #181818;
            border: 2px solid #181818;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 12px;
        }
        .login-card h2 {
            font-family: 'Carter One', cursive;
            font-size: 28px;
            color: #181818;
            margin-bottom: 8px;
        }
        .login-card p.sub {
            font-size: 14px;
            color: #555;
            margin-bottom: 24px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #181818;
        }
        .input-login {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #181818;
            border-radius: 12px;
            background: #fffdf1;
            font-family: 'Mukta', sans-serif;
            font-size: 15px;
            outline: none;
        }
        .input-login:focus {
            border-color: #24AB58;
            box-shadow: 0 0 0 3px rgba(36, 171, 88, 0.15);
        }
        .btn-login {
            width: 100%;
            background: #24AB58;
            color: #ffffff;
            border: 2px solid #181818;
            padding: 13px;
            font-size: 16px;
            font-weight: 800;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 4px 4px 0px #181818;
            margin-top: 8px;
            font-family: 'Mukta', sans-serif;
        }
        .btn-login:hover {
            background: #1ea050;
        }
        .alert-error {
            background: #fee2e2;
            border: 2px solid #dc2626;
            color: #991b1b;
            padding: 12px 15px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 18px;
        }
        .login-nota {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px dashed #d1d5db;
            font-size: 12.5px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="login-wrapper">
        <div class="login-card">
            <span class="login-badge">🔒 Área Administrativa</span>
            <h2>Iniciar Sesión</h2>
            <p class="sub">Ingresá con tu cuenta de administrador registrada en la base de datos.</p>

            <?php if (!empty($error)): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="form-group">
                    <label for="usuario">Usuario o Correo Electrónico *</label>
                    <input type="text" id="usuario" name="usuario" class="input-login" value="<?= htmlspecialchars($usuario_input) ?>" placeholder="Ej: admin o admin@atodoritmo.edu.ar" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña *</label>
                    <input type="password" id="password" name="password" class="input-login" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-login">Ingresar al Panel →</button>
            </form>

            <div class="login-nota">
                Acceso exclusivo para dirección y coordinación académica de <strong>A Todo Ritmo</strong>.
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
