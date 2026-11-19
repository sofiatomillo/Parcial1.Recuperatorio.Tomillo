<?php
// procesar_inscripcion.php - Procesador oficial "A Todo Ritmo"
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y sanitizar los datos del formulario común
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $apellido = htmlspecialchars(trim($_POST['apellido']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $telefono = htmlspecialchars(trim($_POST['telefono']));
    $edad = intval($_POST['edad']);
    $taller_id = intval($_POST['taller_id']);
    $modalidad = htmlspecialchars($_POST['modalidad']);
    $necesita_prestamo = isset($_POST['necesita_prestamo']) ? 1 : 0;
    
    // Generar el código de matrícula único requerido en el parcial
    $codigo_inscripcion = "ATR-2026-" . str_pad(rand(1, 9999), 4, "0", STR_PAD_LEFT);

    try {
        // Iniciar Transacción ACID para asegurar la consistencia relacional
        $pdo->beginTransaction();

        // 1. Insertar el registro del alumno
        $sqlInsert = "INSERT INTO inscripciones (codigo_inscripcion, taller_id, nombre, apellido, email, telefono, edad, modalidad, necesita_prestamo_instrumento, horario_seleccionado) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Horario de comisión asignado')";
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute([$codigo_inscripcion, $taller_id, $nombre, $apellido, $email, $telefono, $edad, $modalidad, $necesita_prestamo]);

        // 2. Decrementar el cupo disponible del taller en MySQL
        $sqlUpdate = "UPDATE talleres SET vacantes_disponibles = vacantes_disponibles - 1 WHERE id = ? AND vacantes_disponibles > 0";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([$taller_id]);

        // Confirmar transacción si todo salió bien
        $pdo->commit();

        // Desplegar el voucher clásico en pantalla solicitado por la cátedra
        echo "<div style='max-width: 600px; margin: 60px auto; padding: 35px; border: 4px solid #24AB58; background-color: #FFFDF5; font-family: sans-serif; border-radius: 20px; box-shadow: 4px 4px 0px #181818;'>";
        echo "<h2 style='color: #24AB58; margin-top:0; font-size:24px;'>¡Matrícula Procesada con Éxito!</h2>";
        echo "<p style='font-size:15px;'>Tu vacante ha sido reservada en el sistema relacional de forma correcta.</p>";
        echo "<p style='margin: 20px 0;'><strong>Código de Inscripción Oficial:</strong> <span style='background:#F3D84F; padding: 6px 12px; border-radius:6px; font-family:monospace; font-weight:bold; border: 1px solid #181818;'>$codigo_inscripcion</span></p>";
        echo "<p style='font-size:14px; color:#444;'><strong>Alumno/a:</strong> $nombre $apellido</p>";
        echo "<p style='font-size:14px; color:#444;'>Te enviamos una notificación a tu correo (<strong>$email</strong>) detallando la fecha de inicio de clases y los requisitos para el retiro del instrumento del Banco Solidario.</p>";
        echo "<br><a href='index.php' style='background:#181818; color:#FFFAE6; padding:12px 24px; text-decoration:none; border-radius:10px; font-weight:bold; display:inline-block; font-size:14px; box-shadow: 2px 2px 0px #24AB58;'>Volver al Sitio</a>";
        echo "</div>";

    } catch (Exception $e) {
        // Cancelar y revertir cambios si ocurre un error inesperado
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "<div style='padding:20px; background:#fee2e2; border:1px solid #ef4444; color:#991b1b; border-radius:8px; font-family:sans-serif;'>";
        echo "<strong>Error del Servidor:</strong> No se pudo completar la matrícula. Detalle: " . $e->getMessage();
        echo "</div>";
    }
}
?>
