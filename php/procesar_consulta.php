<?php
// procesar_consulta.php - Guardado de mensajes de contacto en MySQL con PDO
require_once __DIR__ . '/conexion.php';

$exito = false;
$error_msg = '';
$nombre = '';
$email = '';
$motivo = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre   = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $email    = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));
    $motivo   = htmlspecialchars(trim($_POST['motivo'] ?? 'Consulta general'));
    $mensaje  = htmlspecialchars(trim($_POST['mensaje'] ?? ''));

    if (!empty($nombre) && !empty($email) && !empty($mensaje)) {
        try {
            $sql = "INSERT INTO consultas (nombre, email, telefono, motivo, mensaje) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $email, $telefono, $motivo, $mensaje]);
            $exito = true;
        } catch (PDOException $e) {
            $error_msg = $e->getMessage();
        }
    } else {
        $error_msg = "Por favor completá todos los campos obligatorios.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Enviada | A Todo Ritmo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Carter+One&family=Mukta:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main style="max-width: 650px; margin: 60px auto; padding: 0 20px;">
        <?php if ($exito): ?>
            <div style="padding: 35px; border: 4px solid #24AB58; background-color: #FFFDF5; border-radius: 20px; box-shadow: 6px 6px 0px #181818;">
                <h2 style="font-family:'Carter One', cursive; color: #24AB58; margin-top:0; font-size:26px;">¡Consulta Recibida!</h2>
                <p style="font-size:15px; margin-top:10px;">Gracias <strong><?= $nombre ?></strong>. Tu mensaje sobre <em><?= $motivo ?></em> fue registrado en secretaría.</p>
                <p style="font-size:14px; color:#444; margin-top:10px;">Te responderemos a la brevedad al correo <strong><?= htmlspecialchars($email) ?></strong>.</p>
                <div style="margin-top: 25px;">
                    <a href="consultas.php" class="btn btn-y" style="text-decoration:none;">Volver a Consultas</a>
                </div>
            </div>
        <?php else: ?>
            <div style="padding: 30px; background:#fee2e2; border: 2px solid #ef4444; color:#991b1b; border-radius:16px;">
                <h2 style="font-family:'Carter One', cursive; margin-bottom:10px;">No se pudo enviar tu consulta</h2>
                <p><?= htmlspecialchars($error_msg ?: 'Enviá el formulario desde la sección de Consultas.') ?></p>
                <p style="margin-top:15px;"><a href="consultas.php" style="color:#991b1b; font-weight:700;">← Volver a Consultas</a></p>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
