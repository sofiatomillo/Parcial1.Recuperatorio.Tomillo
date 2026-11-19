<?php
// procesar_entrada.php - Reserva de entradas para eventos "A Todo Ritmo"
require_once __DIR__ . '/conexion.php';

$exito = false;
$error_msg = '';
$codigo_ticket = '';
$nombre_comprador = '';
$email_comprador = '';
$cantidad = 1;
$total_abonado = 0;
$evento_titulo = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $evento_id        = intval($_POST['evento_id'] ?? 0);
    $nombre_comprador = htmlspecialchars(trim($_POST['nombre_comprador'] ?? ''));
    $email_comprador  = filter_var($_POST['email_comprador'] ?? '', FILTER_SANITIZE_EMAIL);
    $cantidad         = max(1, intval($_POST['cantidad'] ?? 1));
    $metodo_pago      = htmlspecialchars(trim($_POST['metodo_pago'] ?? 'Transferencia'));
    $codigo_ticket    = "TKT-ATR-" . str_pad((string)rand(1, 9999), 4, "0", STR_PAD_LEFT);

    try {
        $pdo->beginTransaction();

        $stmtEv = $pdo->prepare("SELECT titulo, precio_entrada, entradas_disponibles FROM eventos WHERE id = ?");
        $stmtEv->execute([$evento_id]);
        $ev = $stmtEv->fetch(PDO::FETCH_ASSOC);

        if (!$ev) {
            throw new Exception("El evento seleccionado no existe en la base de datos.");
        }
        if ((int)$ev['entradas_disponibles'] < $cantidad) {
            throw new Exception("No hay suficientes butacas disponibles para la cantidad solicitada.");
        }

        $evento_titulo = $ev['titulo'];
        $total_abonado = (float)$ev['precio_entrada'] * $cantidad;

        $sqlIns = "INSERT INTO entradas_eventos (codigo_ticket, evento_id, nombre_comprador, email_comprador, cantidad, total_abonado, metodo_pago)
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtIns = $pdo->prepare($sqlIns);
        $stmtIns->execute([$codigo_ticket, $evento_id, $nombre_comprador, $email_comprador, $cantidad, $total_abonado, $metodo_pago]);

        $sqlUpd = "UPDATE eventos SET entradas_disponibles = entradas_disponibles - ? WHERE id = ? AND entradas_disponibles >= ?";
        $stmtUpd = $pdo->prepare($sqlUpd);
        $stmtUpd->execute([$cantidad, $evento_id, $cantidad]);

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
    <title>Reserva de Entradas | A Todo Ritmo</title>
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
                <h2 style="font-family:'Carter One', cursive; color: #24AB58; margin-top:0; font-size:26px;">¡Entradas Reservadas con Éxito!</h2>
                <p style="font-size:15px; margin-top:10px;">Tu reserva para <strong><?= htmlspecialchars($evento_titulo) ?></strong> fue registrada en el sistema.</p>
                <p style="margin: 20px 0;">
                    <strong>Código de Ticket:</strong>
                    <span style="background:#F3D84F; padding: 6px 12px; border-radius:6px; font-family:monospace; font-weight:bold; border: 1px solid #181818;"><?= htmlspecialchars($codigo_ticket) ?></span>
                </p>
                <p style="font-size:14px; color:#444;"><strong>Titular:</strong> <?= $nombre_comprador ?> (<?= htmlspecialchars($email_comprador) ?>)</p>
                <p style="font-size:14px; color:#444;"><strong>Cantidad:</strong> <?= $cantidad ?> entrada(s) &nbsp;|&nbsp; <strong>Total:</strong> $<?= number_format($total_abonado, 0, ',', '.') ?></p>
                <div style="margin-top: 25px;">
                    <a href="eventos.php" class="btn btn-y" style="text-decoration:none;">Volver a la Agenda Cultural</a>
                </div>
            </div>
        <?php else: ?>
            <div style="padding: 30px; background:#fee2e2; border: 2px solid #ef4444; color:#991b1b; border-radius:16px;">
                <h2 style="font-family:'Carter One', cursive; margin-bottom:10px;">No se pudo completar la reserva</h2>
                <p><?= htmlspecialchars($error_msg ?: 'Acceso inválido al procesador de entradas.') ?></p>
                <p style="margin-top:15px;"><a href="eventos.php" style="color:#991b1b; font-weight:700;">← Volver a Eventos</a></p>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
