<?php
// procesar_inscripcion.php - Procesador oficial "A Todo Ritmo"
require_once __DIR__ . '/conexion.php';

$exito = false;
$error_msg = '';
$codigo_inscripcion = '';
$nombre = '';
$apellido = '';
$email = '';
$taller_nombre = '';
$prestamo_texto = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre    = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $apellido  = htmlspecialchars(trim($_POST['apellido'] ?? ''));
    $email     = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $telefono  = htmlspecialchars(trim($_POST['telefono'] ?? ''));
    $edad      = intval($_POST['edad'] ?? 0);
    $genero    = htmlspecialchars(trim($_POST['genero'] ?? 'NB'));
    $taller_id = intval($_POST['taller_id'] ?? 0);
    $modalidad = htmlspecialchars(trim($_POST['modalidad'] ?? 'Presencial'));
    $sede_id   = intval($_POST['sede_id'] ?? 1);
    $comentarios = htmlspecialchars(trim($_POST['comentarios'] ?? ''));

    // Soportamos el nombre del checkbox actual ('solicita_instrumento') y el previo ('necesita_prestamo')
    $necesita_prestamo = (isset($_POST['solicita_instrumento']) || isset($_POST['necesita_prestamo'])) ? 1 : 0;
    $posee_propio      = isset($_POST['tiene_instrumento']) ? 1 : 0;
    $prestamo_texto    = $necesita_prestamo ? 'Sí (Préstamo Solidario reservado)' : 'No requerido';

    $codigo_inscripcion = "ATR-2026-" . str_pad((string)rand(1, 9999), 4, "0", STR_PAD_LEFT);

    try {
        $pdo->beginTransaction();

        // Obtenemos datos del taller elegido
        $stmtTaller = $pdo->prepare("SELECT nombre, dia_semana, horario, vacantes_disponibles FROM talleres WHERE id = ?");
        $stmtTaller->execute([$taller_id]);
        $taller = $stmtTaller->fetch(PDO::FETCH_ASSOC);

        if (!$taller) {
            throw new Exception("El taller seleccionado no se encuentra disponible.");
        }
        if ((int)$taller['vacantes_disponibles'] <= 0) {
            throw new Exception("No quedan vacantes disponibles para este taller.");
        }

        $taller_nombre = $taller['nombre'];
        $horario_sel   = trim($taller['dia_semana'] . ' ' . $taller['horario']);

        // 1. Insertar el registro del alumno
        $sqlInsert = "INSERT INTO inscripciones (codigo_inscripcion, taller_id, nombre, apellido, email, telefono, edad, genero, modalidad, necesita_prestamo_instrumento, posee_instrumento_propio, sede_id, horario_seleccionado, comentarios)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute([
            $codigo_inscripcion, $taller_id, $nombre, $apellido, $email, $telefono,
            $edad, $genero, $modalidad, $necesita_prestamo, $posee_propio, $sede_id,
            $horario_sel, $comentarios
        ]);

        // 2. Decrementar el cupo disponible del taller en MySQL
        $sqlUpdate = "UPDATE talleres SET vacantes_disponibles = vacantes_disponibles - 1 WHERE id = ? AND vacantes_disponibles > 0";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([$taller_id]);

        $pdo->commit();
        $exito = true;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error_msg = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Inscripción | A Todo Ritmo</title>
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
                <h2 style="font-family:'Carter One', cursive; color: #24AB58; margin-top:0; font-size:26px;">¡Matrícula Procesada con Éxito!</h2>
                <p style="font-size:15px; margin-top:8px;">Tu vacante ha sido reservada en el sistema relacional de forma correcta.</p>
                <p style="margin: 20px 0;">
                    <strong>Código de Inscripción Oficial:</strong>
                    <span style="background:#F3D84F; padding: 6px 12px; border-radius:6px; font-family:monospace; font-weight:bold; border: 1px solid #181818;"><?= htmlspecialchars($codigo_inscripcion) ?></span>
                </p>
                <p style="font-size:14px; color:#444; margin-bottom:6px;"><strong>Alumno/a:</strong> <?= $nombre ?> <?= $apellido ?></p>
                <p style="font-size:14px; color:#444; margin-bottom:6px;"><strong>Taller:</strong> <?= htmlspecialchars($taller_nombre) ?></p>
                <p style="font-size:14px; color:#444; margin-bottom:12px;"><strong>Préstamo de Instrumento:</strong> <?= htmlspecialchars($prestamo_texto) ?></p>
                <p style="font-size:14px; color:#444;">Te enviamos una notificación a tu correo (<strong><?= htmlspecialchars($email) ?></strong>) detallando la fecha de inicio de clases y los requisitos para el retiro del instrumento del Banco Solidario.</p>
                <div style="margin-top:24px;">
                    <a href="index.php" style="background:#181818; color:#FFFAE6; padding:12px 24px; text-decoration:none; border-radius:10px; font-weight:bold; display:inline-block; font-size:14px; box-shadow: 2px 2px 0px #24AB58;">Volver al Sitio</a>
                </div>
            </div>
        <?php else: ?>
            <div style="padding: 25px; background:#fee2e2; border:2px solid #ef4444; color:#991b1b; border-radius:16px;">
                <h2 style="font-family:'Carter One', cursive; margin-bottom:10px;">Error del Servidor</h2>
                <p>No se pudo completar la matrícula. <?= htmlspecialchars($error_msg ?: 'Enviá el formulario desde la ficha de inscripción.') ?></p>
                <p style="margin-top:15px;"><a href="inscripcion.php" style="color:#991b1b; font-weight:700;">← Volver a la Ficha de Inscripción</a></p>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
