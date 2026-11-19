<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/auth_admin.php';

requerirAdmin();

$tab = $_GET['tab'] ?? 'inscripciones';
$tabs_validos = ['inscripciones', 'docentes', 'talleres', 'eventos', 'contenido', 'admins'];
if (!in_array($tab, $tabs_validos, true)) {
    $tab = 'inscripciones';
}

$mensaje_ok = '';
$mensaje_error = '';

// =========================================================================
// PROCESAMIENTO DE ACCIONES POST DEL ADMINISTRADOR
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $csrf   = $_POST['csrf_token'] ?? '';

    if (!verificarCsrfToken($csrf)) {
        $mensaje_error = 'Error de validación de seguridad (CSRF). Recargá la página e intentá nuevamente.';
    } else {
        try {
            // 1. GESTIÓN DE INSCRIPCIONES
            if ($accion === 'actualizar_estado_inscripcion') {
                $tab = 'inscripciones';
                $id = intval($_POST['inscripcion_id'] ?? 0);
                $nuevo_estado = $_POST['estado'] ?? 'Confirmada';
                $permitidos = ['Pendiente de Confirmación', 'Confirmada', 'En Lista de Espera'];
                if ($id > 0 && in_array($nuevo_estado, $permitidos, true)) {
                    $stmt = $pdo->prepare("UPDATE inscripciones SET estado = ? WHERE id = ?");
                    $stmt->execute([$nuevo_estado, $id]);
                    $mensaje_ok = 'Estado de inscripción actualizado correctamente.';
                }
            } elseif ($accion === 'eliminar_inscripcion') {
                $tab = 'inscripciones';
                $id = intval($_POST['inscripcion_id'] ?? 0);
                if ($id > 0) {
                    $pdo->beginTransaction();
                    $stmtInfo = $pdo->prepare("SELECT taller_id FROM inscripciones WHERE id = ?");
                    $stmtInfo->execute([$id]);
                    $row = $stmtInfo->fetch(PDO::FETCH_ASSOC);

                    $stmtDel = $pdo->prepare("DELETE FROM inscripciones WHERE id = ?");
                    $stmtDel->execute([$id]);

                    if ($row && !empty($row['taller_id'])) {
                        $stmtUpd = $pdo->prepare("UPDATE talleres SET vacantes_disponibles = LEAST(vacantes_totales, vacantes_disponibles + 1) WHERE id = ?");
                        $stmtUpd->execute([$row['taller_id']]);
                    }
                    $pdo->commit();
                    $mensaje_ok = 'Inscripción eliminada y vacante reintegrada al taller.';
                }

            // 2. GESTIÓN DE DOCENTES (CREAR / MODIFICAR / ELIMINAR)
            } elseif ($accion === 'guardar_docente') {
                $tab = 'docentes';
                $id                    = intval($_POST['docente_id'] ?? 0);
                $nombre                = trim($_POST['nombre'] ?? '');
                $apellido              = trim($_POST['apellido'] ?? '');
                $especialidad          = trim($_POST['especialidad'] ?? '');
                $instrumento_principal = trim($_POST['instrumento_principal'] ?? '');
                $biografia             = trim($_POST['biografia'] ?? '');
                $trayectoria_cv        = trim($_POST['trayectoria_cv'] ?? '');
                $email                 = trim($_POST['email'] ?? '');
                $telefono              = trim($_POST['telefono'] ?? '');
                $imagen                = trim($_POST['imagen'] ?? 'img/default-docente.jpg');
                $activo                = isset($_POST['activo']) ? 1 : 0;

                if ($id > 0) {
                    $sql = "UPDATE docentes SET nombre=?, apellido=?, especialidad=?, instrumento_principal=?, biografia=?, trayectoria_cv=?, email=?, telefono=?, imagen=?, foto_url=?, activo=? WHERE id=?";
                    $pdo->prepare($sql)->execute([$nombre, $apellido, $especialidad, $instrumento_principal, $biografia, $trayectoria_cv, $email, $telefono, $imagen, $imagen, $activo, $id]);
                    $mensaje_ok = "Docente \"$nombre $apellido\" modificado correctamente.";
                } else {
                    $sql = "INSERT INTO docentes (nombre, apellido, especialidad, instrumento_principal, biografia, trayectoria_cv, email, telefono, imagen, foto_url, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $pdo->prepare($sql)->execute([$nombre, $apellido, $especialidad, $instrumento_principal, $biografia, $trayectoria_cv, $email, $telefono, $imagen, $imagen, $activo]);
                    $mensaje_ok = "Nuevo docente \"$nombre $apellido\" cargado en la base de datos.";
                }
            } elseif ($accion === 'eliminar_docente') {
                $tab = 'docentes';
                $id = intval($_POST['docente_id'] ?? 0);
                if ($id > 0) {
                    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM talleres WHERE docente_id = ?");
                    $stmtCheck->execute([$id]);
                    if ((int)$stmtCheck->fetchColumn() > 0) {
                        $mensaje_error = 'No se puede eliminar este docente porque tiene talleres asignados. Reasigná sus talleres o desactivalo.';
                    } else {
                        $pdo->prepare("DELETE FROM docentes WHERE id = ?")->execute([$id]);
                        $mensaje_ok = 'Docente eliminado correctamente.';
                    }
                }

            // 3. GESTIÓN DE TALLERES (CREAR / MODIFICAR / ELIMINAR)
            } elseif ($accion === 'guardar_taller') {
                $tab = 'talleres';
                $id           = intval($_POST['taller_id'] ?? 0);
                $nombre       = trim($_POST['nombre'] ?? '');
                $instrumento  = trim($_POST['instrumento'] ?? '');
                $docente_id   = intval($_POST['docente_id'] ?? 1);
                $nivel        = $_POST['nivel'] ?? 'Inicial / Sin experiencia';
                $edad_minima  = intval($_POST['edad_minima'] ?? 7);
                $dia_semana   = trim($_POST['dia_semana'] ?? '');
                $horario      = trim($_POST['horario'] ?? '');
                $vac_tot      = intval($_POST['vacantes_totales'] ?? 10);
                $vac_disp     = intval($_POST['vacantes_disponibles'] ?? 5);
                $arancel      = floatval($_POST['arancel_mensual'] ?? 0);
                $modalidad    = $_POST['modalidad'] ?? 'Presencial';
                $sede_id      = intval($_POST['sede_id'] ?? 1);
                $descripcion  = trim($_POST['descripcion'] ?? '');
                $requisitos   = trim($_POST['requisitos'] ?? '');
                $prestamo     = intval($_POST['instrumentos_prestamo_disponibles'] ?? 0);
                $imagen_url   = trim($_POST['imagen_url'] ?? 'img/guitarra.jpg');
                $destacado    = isset($_POST['destacado']) ? 1 : 0;

                if ($id > 0) {
                    $sql = "UPDATE talleres SET nombre=?, instrumento=?, docente_id=?, nivel=?, edad_minima=?, dia_semana=?, horario=?, vacantes_totales=?, vacantes_disponibles=?, arancel_mensual=?, modalidad=?, sede_id=?, descripcion=?, requisitos=?, instrumentos_prestamo_disponibles=?, destacado=?, imagen_url=? WHERE id=?";
                    $pdo->prepare($sql)->execute([$nombre, $instrumento, $docente_id, $nivel, $edad_minima, $dia_semana, $horario, $vac_tot, $vac_disp, $arancel, $modalidad, $sede_id, $descripcion, $requisitos, $prestamo, $destacado, $imagen_url, $id]);
                    $mensaje_ok = "Taller \"$nombre\" actualizado correctamente.";
                } else {
                    $sql = "INSERT INTO talleres (nombre, instrumento, docente_id, nivel, edad_minima, dia_semana, horario, vacantes_totales, vacantes_disponibles, arancel_mensual, modalidad, sede_id, descripcion, requisitos, instrumentos_prestamo_disponibles, destacado, imagen_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $pdo->prepare($sql)->execute([$nombre, $instrumento, $docente_id, $nivel, $edad_minima, $dia_semana, $horario, $vac_tot, $vac_disp, $arancel, $modalidad, $sede_id, $descripcion, $requisitos, $prestamo, $destacado, $imagen_url]);
                    $mensaje_ok = "Nuevo taller \"$nombre\" cargado correctamente.";
                }
            } elseif ($accion === 'eliminar_taller') {
                $tab = 'talleres';
                $id = intval($_POST['taller_id'] ?? 0);
                if ($id > 0) {
                    $pdo->prepare("DELETE FROM talleres WHERE id = ?")->execute([$id]);
                    $mensaje_ok = 'Taller eliminado correctamente.';
                }

            // 4. GESTIÓN DE EVENTOS (CREAR / MODIFICAR / ELIMINAR)
            } elseif ($accion === 'guardar_evento') {
                $tab = 'eventos';
                $id          = intval($_POST['evento_id'] ?? 0);
                $titulo      = trim($_POST['titulo'] ?? '');
                $tipo        = $_POST['tipo'] ?? 'Muestra de Alumnos';
                $badge_color = $_POST['badge_color'] ?? 'yellow';
                $fecha       = $_POST['fecha'] ?? date('Y-m-d');
                $hora        = trim($_POST['hora'] ?? '18:30:00');
                $lugar       = trim($_POST['lugar'] ?? '');
                $descripcion = trim($_POST['descripcion'] ?? '');
                $repertorio  = trim($_POST['repertorio'] ?? '');
                $precio      = floatval($_POST['precio_entrada'] ?? 0);
                $cap_total   = intval($_POST['capacidad_total'] ?? 100);
                $ent_disp    = intval($_POST['entradas_disponibles'] ?? 50);
                $estado      = $_POST['estado'] ?? 'proximo';
                $imagen_url  = trim($_POST['imagen_url'] ?? 'muestra-primavera.jpg');

                if ($id > 0) {
                    $sql = "UPDATE eventos SET titulo=?, tipo=?, badge_color=?, fecha=?, hora=?, lugar=?, descripcion=?, repertorio=?, precio_entrada=?, capacidad_total=?, entradas_disponibles=?, estado=?, imagen_url=? WHERE id=?";
                    $pdo->prepare($sql)->execute([$titulo, $tipo, $badge_color, $fecha, $hora, $lugar, $descripcion, $repertorio, $precio, $cap_total, $ent_disp, $estado, $imagen_url, $id]);
                    $mensaje_ok = "Evento \"$titulo\" modificado correctamente.";
                } else {
                    $sql = "INSERT INTO eventos (titulo, tipo, badge_color, fecha, hora, lugar, descripcion, repertorio, precio_entrada, capacidad_total, entradas_disponibles, estado, imagen_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $pdo->prepare($sql)->execute([$titulo, $tipo, $badge_color, $fecha, $hora, $lugar, $descripcion, $repertorio, $precio, $cap_total, $ent_disp, $estado, $imagen_url]);
                    $mensaje_ok = "Nuevo evento \"$titulo\" cargado correctamente.";
                }
            } elseif ($accion === 'eliminar_evento') {
                $tab = 'eventos';
                $id = intval($_POST['evento_id'] ?? 0);
                if ($id > 0) {
                    $pdo->prepare("DELETE FROM eventos WHERE id = ?")->execute([$id]);
                    $mensaje_ok = 'Evento eliminado correctamente.';
                }

            // 5. MODIFICAR INFORMACIÓN DE LA PÁGINA (TEXTOS, SEDES, RESEÑAS, FAQ, CONSULTAS)
            } elseif ($accion === 'guardar_config_pagina') {
                $tab = 'contenido';
                $claves = $_POST['config'] ?? [];
                $stmtCfg = $pdo->prepare("UPDATE configuracion_pagina SET valor = ? WHERE clave = ?");
                foreach ($claves as $clave => $valor) {
                    $stmtCfg->execute([trim((string)$valor), (string)$clave]);
                }
                $mensaje_ok = 'Textos institucionales de la página actualizados correctamente.';
            } elseif ($accion === 'guardar_sede') {
                $tab = 'contenido';
                $id         = intval($_POST['sede_id'] ?? 0);
                $nombre     = trim($_POST['nombre'] ?? '');
                $direccion  = trim($_POST['direccion'] ?? '');
                $barrio     = trim($_POST['barrio'] ?? '');
                $telefono   = trim($_POST['telefono'] ?? '');
                $email      = trim($_POST['email'] ?? '');
                $horarios   = trim($_POST['horarios_atencion'] ?? '');
                $colectivos = trim($_POST['colectivos'] ?? '');
                $subte      = trim($_POST['subte'] ?? '');

                if ($id > 0) {
                    $sql = "UPDATE sedes SET nombre=?, direccion=?, barrio=?, telefono=?, email=?, horarios_atencion=?, colectivos=?, subte=? WHERE id=?";
                    $pdo->prepare($sql)->execute([$nombre, $direccion, $barrio, $telefono, $email, $horarios, $colectivos, $subte, $id]);
                    $mensaje_ok = "Sede \"$nombre\" actualizada correctamente.";
                }
            } elseif ($accion === 'guardar_resena') {
                $tab = 'contenido';
                $id         = intval($_POST['resena_id'] ?? 0);
                $autor      = trim($_POST['autor'] ?? '');
                $rol        = trim($_POST['rol'] ?? '');
                $calif      = max(1, min(5, intval($_POST['calificacion'] ?? 5)));
                $comentario = trim($_POST['comentario'] ?? '');

                if ($id > 0) {
                    $pdo->prepare("UPDATE resenas SET autor=?, rol=?, calificacion=?, comentario=? WHERE id=?")->execute([$autor, $rol, $calif, $comentario, $id]);
                    $mensaje_ok = 'Testimonio actualizado correctamente.';
                } else {
                    $pdo->prepare("INSERT INTO resenas (autor, rol, calificacion, comentario, fecha) VALUES (?, ?, ?, ?, CURDATE())")->execute([$autor, $rol, $calif, $comentario]);
                    $mensaje_ok = 'Nuevo testimonio agregado a la página de inicio.';
                }
            } elseif ($accion === 'eliminar_resena') {
                $tab = 'contenido';
                $id = intval($_POST['resena_id'] ?? 0);
                if ($id > 0) {
                    $pdo->prepare("DELETE FROM resenas WHERE id = ?")->execute([$id]);
                    $mensaje_ok = 'Testimonio eliminado.';
                }
            } elseif ($accion === 'guardar_faq') {
                $tab = 'contenido';
                $id        = intval($_POST['faq_id'] ?? 0);
                $categoria = trim($_POST['categoria'] ?? '');
                $pregunta  = trim($_POST['pregunta'] ?? '');
                $respuesta = trim($_POST['respuesta'] ?? '');
                $orden     = intval($_POST['orden'] ?? 1);

                if ($id > 0) {
                    $pdo->prepare("UPDATE preguntas_frecuentes SET categoria=?, pregunta=?, respuesta=?, orden=? WHERE id=?")->execute([$categoria, $pregunta, $respuesta, $orden, $id]);
                    $mensaje_ok = 'Pregunta frecuente modificada correctamente.';
                } else {
                    $pdo->prepare("INSERT INTO preguntas_frecuentes (categoria, pregunta, respuesta, orden) VALUES (?, ?, ?, ?)")->execute([$categoria, $pregunta, $respuesta, $orden]);
                    $mensaje_ok = 'Nueva pregunta frecuente agregada.';
                }
            } elseif ($accion === 'eliminar_faq') {
                $tab = 'contenido';
                $id = intval($_POST['faq_id'] ?? 0);
                if ($id > 0) {
                    $pdo->prepare("DELETE FROM preguntas_frecuentes WHERE id = ?")->execute([$id]);
                    $mensaje_ok = 'Pregunta frecuente eliminada.';
                }
            } elseif ($accion === 'marcar_consulta_leida') {
                $tab = 'contenido';
                $id = intval($_POST['consulta_id'] ?? 0);
                $leido = intval($_POST['leido'] ?? 1);
                if ($id > 0) {
                    $pdo->prepare("UPDATE consultas SET leido = ? WHERE id = ?")->execute([$leido, $id]);
                    $mensaje_ok = 'Estado de consulta actualizado.';
                }

            // 6. GESTIÓN DE USUARIOS ADMINISTRADORES EN LA BD
            } elseif ($accion === 'crear_admin') {
                $tab = 'admins';
                $usuario = trim($_POST['usuario'] ?? '');
                $nombre  = trim($_POST['nombre_completo'] ?? '');
                $email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
                $pass    = $_POST['password'] ?? '';
                $rol     = ($_POST['rol'] ?? 'admin') === 'superadmin' ? 'superadmin' : 'admin';

                if (strlen($pass) < 6) {
                    $mensaje_error = 'La contraseña debe tener al menos 6 caracteres.';
                } else {
                    $hash = password_hash($pass, PASSWORD_BCRYPT);
                    $pdo->prepare("INSERT INTO administradores (usuario, nombre_completo, email, password_hash, rol, activo) VALUES (?, ?, ?, ?, ?, 1)")
                        ->execute([$usuario, $nombre, $email, $hash, $rol]);
                    $mensaje_ok = "Administrador \"$usuario\" cargado en la base de datos correctamente.";
                }
            } elseif ($accion === 'toggle_admin') {
                $tab = 'admins';
                $id = intval($_POST['admin_id'] ?? 0);
                $activo = intval($_POST['activo'] ?? 1);
                if ($id === (int)$_SESSION['admin_id']) {
                    $mensaje_error = 'No podés desactivar tu propia sesión actual.';
                } elseif ($id > 0) {
                    $pdo->prepare("UPDATE administradores SET activo = ? WHERE id = ?")->execute([$activo, $id]);
                    $mensaje_ok = 'Estado de administrador actualizado.';
                }
            }
        } catch (PDOException $e) {
            $mensaje_error = 'Error de base de datos: ' . $e->getMessage();
        }
    }
}

// =========================================================================
// CONSULTAS DE LECTURA PARA EL DASHBOARD Y PESTAÑAS
// =========================================================================
$contadores = [
    'inscripciones' => 0,
    'docentes'      => 0,
    'talleres'      => 0,
    'eventos'       => 0,
    'consultas_sin' => 0,
];

try {
    $contadores['inscripciones'] = (int)$pdo->query("SELECT COUNT(*) FROM inscripciones")->fetchColumn();
    $contadores['docentes']      = (int)$pdo->query("SELECT COUNT(*) FROM docentes")->fetchColumn();
    $contadores['talleres']      = (int)$pdo->query("SELECT COUNT(*) FROM talleres")->fetchColumn();
    $contadores['eventos']       = (int)$pdo->query("SELECT COUNT(*) FROM eventos")->fetchColumn();
    $contadores['consultas_sin'] = (int)$pdo->query("SELECT COUNT(*) FROM consultas WHERE leido = 0")->fetchColumn();
} catch (PDOException $e) {
}

// Listas base siempre útiles
$docentes_lista = $pdo->query("SELECT * FROM docentes ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
$sedes_lista    = $pdo->query("SELECT * FROM sedes ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
$talleres_lista = $pdo->query("SELECT t.*, CONCAT(d.nombre, ' ', d.apellido) AS docente_nombre, s.nombre AS sede_nombre FROM talleres t LEFT JOIN docentes d ON t.docente_id = d.id LEFT JOIN sedes s ON t.sede_id = s.id ORDER BY t.id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Datos específicos de cada pestaña
$filtro_taller_ins = intval($_GET['filtro_taller'] ?? 0);
$filtro_estado_ins = trim($_GET['filtro_estado'] ?? '');
$buscar_alumno     = trim($_GET['buscar_alumno'] ?? '');

$inscripciones_lista = [];
if ($tab === 'inscripciones') {
    $sqlIns = "SELECT i.*, t.nombre AS taller_nombre, t.instrumento, s.nombre AS sede_nombre
               FROM inscripciones i
               INNER JOIN talleres t ON i.taller_id = t.id
               LEFT JOIN sedes s ON i.sede_id = s.id
               WHERE 1=1";
    $paramsIns = [];
    if ($filtro_taller_ins > 0) {
        $sqlIns .= " AND i.taller_id = ?";
        $paramsIns[] = $filtro_taller_ins;
    }
    if (!empty($filtro_estado_ins)) {
        $sqlIns .= " AND i.estado = ?";
        $paramsIns[] = $filtro_estado_ins;
    }
    if (!empty($buscar_alumno)) {
        $sqlIns .= " AND (i.nombre LIKE ? OR i.apellido LIKE ? OR i.email LIKE ? OR i.codigo_inscripcion LIKE ?)";
        $like = '%' . $buscar_alumno . '%';
        array_push($paramsIns, $like, $like, $like, $like);
    }
    $sqlIns .= " ORDER BY i.fecha_inscripcion DESC, i.id DESC";
    $stmtIns = $pdo->prepare($sqlIns);
    $stmtIns->execute($paramsIns);
    $inscripciones_lista = $stmtIns->fetchAll(PDO::FETCH_ASSOC);
}

// Edición pre-cargada según query param
$docente_editar = null;
if ($tab === 'docentes' && !empty($_GET['editar_docente'])) {
    $st = $pdo->prepare("SELECT * FROM docentes WHERE id = ?");
    $st->execute([intval($_GET['editar_docente'])]);
    $docente_editar = $st->fetch(PDO::FETCH_ASSOC);
}

$taller_editar = null;
if ($tab === 'talleres' && !empty($_GET['editar_taller'])) {
    $st = $pdo->prepare("SELECT * FROM talleres WHERE id = ?");
    $st->execute([intval($_GET['editar_taller'])]);
    $taller_editar = $st->fetch(PDO::FETCH_ASSOC);
}

$evento_editar = null;
$eventos_lista = [];
if ($tab === 'eventos') {
    $eventos_lista = $pdo->query("SELECT e.*, COALESCE(SUM(ee.cantidad), 0) AS entradas_vendidas FROM eventos e LEFT JOIN entradas_eventos ee ON e.id = ee.evento_id GROUP BY e.id ORDER BY e.fecha ASC")->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($_GET['editar_evento'])) {
        $st = $pdo->prepare("SELECT * FROM eventos WHERE id = ?");
        $st->execute([intval($_GET['editar_evento'])]);
        $evento_editar = $st->fetch(PDO::FETCH_ASSOC);
    }
}

$config_lista = [];
$resenas_lista = [];
$faqs_lista = [];
$consultas_lista = [];
if ($tab === 'contenido') {
    $config_lista    = $pdo->query("SELECT * FROM configuracion_pagina ORDER BY seccion ASC, clave ASC")->fetchAll(PDO::FETCH_ASSOC);
    $resenas_lista   = $pdo->query("SELECT * FROM resenas ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    $faqs_lista      = $pdo->query("SELECT * FROM preguntas_frecuentes ORDER BY orden ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    $consultas_lista = $pdo->query("SELECT * FROM consultas ORDER BY fecha DESC")->fetchAll(PDO::FETCH_ASSOC);
}

$admins_lista = [];
if ($tab === 'admins') {
    $admins_lista = $pdo->query("SELECT id, usuario, nombre_completo, email, rol, activo, ultimo_acceso, created_at FROM administradores ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
}

$csrf_token = generarCsrfToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador | Escuela de Música A Todo Ritmo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Carter+One&family=Mukta:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <style>
        .admin-wrap { max-width: 1240px; margin: 30px auto 70px auto; padding: 0 20px; }
        .admin-topbar {
            background: #181818;
            color: #FFFAE6;
            border: 3px solid #F3D84F;
            border-radius: 22px;
            padding: 22px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
            box-shadow: 5px 5px 0px #24AB58;
        }
        .admin-topbar h2 { font-family: 'Carter One', cursive; color: #F3D84F; font-size: 26px; margin: 0; }
        .admin-topbar p { font-size: 13.5px; color: #d1d5db; margin: 2px 0 0 0; }
        .admin-user-actions { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .btn-logout { background: #dc2626; color: #fff; border: 2px solid #FFFAE6; padding: 8px 16px; border-radius: 10px; font-weight: 700; text-decoration: none; font-size: 13px; }
        .btn-ver-sitio { background: #F3D84F; color: #181818; border: 2px solid #181818; padding: 8px 16px; border-radius: 10px; font-weight: 700; text-decoration: none; font-size: 13px; }

        /* Métricas */
        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; margin-bottom: 24px; }
        .stat-box { background: #FFFDF5; border: 2px solid #181818; border-radius: 16px; padding: 14px 18px; box-shadow: 3px 3px 0px #181818; }
        .stat-box span { font-size: 11px; font-weight: 800; text-transform: uppercase; color: #555; display: block; }
        .stat-box strong { font-family: 'Carter One', cursive; font-size: 26px; color: #24AB58; }

        /* Tabs */
        .admin-tabs { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 24px; }
        .tab-link {
            background: #ffffff;
            color: #181818;
            border: 2px solid #181818;
            padding: 10px 18px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 3px 3px 0px #181818;
        }
        .tab-link.activo { background: #24AB58; color: #ffffff; }

        /* Alertas */
        .alerta-ok { background: #dcfce7; border: 2px solid #16a34a; color: #14532d; padding: 14px 18px; border-radius: 14px; font-weight: 700; margin-bottom: 20px; }
        .alerta-err { background: #fee2e2; border: 2px solid #dc2626; color: #7f1d1d; padding: 14px 18px; border-radius: 14px; font-weight: 700; margin-bottom: 20px; }

        /* Paneles y Formularios */
        .panel-box { background: #ffffff; border: 2px solid #181818; border-radius: 20px; padding: 24px; box-shadow: 5px 5px 0px #181818; margin-bottom: 26px; }
        .panel-box h3 { font-family: 'Carter One', cursive; font-size: 20px; margin: 0 0 14px 0; color: #181818; }
        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .campo { margin-bottom: 14px; }
        .campo label { display: block; font-size: 13px; font-weight: 700; margin-bottom: 4px; color: #181818; }
        .input-admin {
            width: 100%;
            padding: 10px 13px;
            border: 2px solid #181818;
            border-radius: 10px;
            background: #fffdf1;
            font-family: 'Mukta', sans-serif;
            font-size: 14px;
        }
        .btn-guardar {
            background: #24AB58;
            color: #fff;
            border: 2px solid #181818;
            padding: 10px 22px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 3px 3px 0px #181818;
        }
        .btn-amarillo {
            background: #F3D84F;
            color: #181818;
            border: 2px solid #181818;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12.5px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }
        .btn-rojo {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #991b1b;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12.5px;
            cursor: pointer;
        }

        /* Tablas */
        .tabla-scroll { overflow-x: auto; }
        table.admin-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        table.admin-table th { background: #181818; color: #F3D84F; text-align: left; padding: 11px 12px; font-weight: 700; }
        table.admin-table td { padding: 11px 12px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        table.admin-table tr:hover td { background: #fffdf4; }
        .pill { display: inline-block; padding: 3px 9px; border-radius: 20px; font-size: 11.5px; font-weight: 700; border: 1px solid #181818; }
        .pill-ok { background: #dcfce7; color: #166534; }
        .pill-warn { background: #fef9c3; color: #854d0e; }
        .pill-off { background: #fee2e2; color: #991b1b; }

        @media (max-width: 960px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="admin-wrap">
        <!-- BARRA SUPERIOR DE SESIÓN -->
        <div class="admin-topbar">
            <div>
                <h2>Panel de Administración — A Todo Ritmo</h2>
                <p>Sesión iniciada como <strong><?= htmlspecialchars($_SESSION['admin_nombre']) ?></strong> (@<?= htmlspecialchars($_SESSION['admin_usuario']) ?> • <?= htmlspecialchars($_SESSION['admin_rol']) ?>)</p>
            </div>
            <div class="admin-user-actions">
                <a href="index.php" class="btn-ver-sitio">Ver Sitio Público</a>
                <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>

        <!-- RESUMEN -->
        <div class="stats-grid">
            <div class="stat-box"><span>Alumnos Inscriptos</span><strong><?= $contadores['inscripciones'] ?></strong></div>
            <div class="stat-box"><span>Docentes</span><strong><?= $contadores['docentes'] ?></strong></div>
            <div class="stat-box"><span>Talleres</span><strong><?= $contadores['talleres'] ?></strong></div>
            <div class="stat-box"><span>Eventos</span><strong><?= $contadores['eventos'] ?></strong></div>
            <div class="stat-box"><span>Consultas Sin Leer</span><strong><?= $contadores['consultas_sin'] ?></strong></div>
        </div>

        <!-- NAVEGACIÓN POR PESTAÑAS -->
        <nav class="admin-tabs">
            <a href="admin.php?tab=inscripciones" class="tab-link <?= $tab === 'inscripciones' ? 'activo' : '' ?>">🎓 Alumnos Inscriptos</a>
            <a href="admin.php?tab=docentes" class="tab-link <?= $tab === 'docentes' ? 'activo' : '' ?>">👩‍🏫 Docentes</a>
            <a href="admin.php?tab=talleres" class="tab-link <?= $tab === 'talleres' ? 'activo' : '' ?>">🎸 Talleres</a>
            <a href="admin.php?tab=eventos" class="tab-link <?= $tab === 'eventos' ? 'activo' : '' ?>">🎭 Eventos</a>
            <a href="admin.php?tab=contenido" class="tab-link <?= $tab === 'contenido' ? 'activo' : '' ?>">📝 Contenido de la Página</a>
            <a href="admin.php?tab=admins" class="tab-link <?= $tab === 'admins' ? 'activo' : '' ?>">🔐 Usuarios Admin</a>
        </nav>

        <?php if (!empty($mensaje_ok)): ?>
            <div class="alerta-ok">✓ <?= htmlspecialchars($mensaje_ok) ?></div>
        <?php endif; ?>
        <?php if (!empty($mensaje_error)): ?>
            <div class="alerta-err">⚠ <?= htmlspecialchars($mensaje_error) ?></div>
        <?php endif; ?>

        <!-- ===============================================================
             TAB 1: VISUALIZAR ALUMNOS INSCRIPTOS
             =============================================================== -->
        <?php if ($tab === 'inscripciones'): ?>
            <section class="panel-box">
                <h3>Alumnos Inscriptos</h3>
                <form method="GET" action="admin.php" style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px;">
                    <input type="hidden" name="tab" value="inscripciones">
                    <input type="text" name="buscar_alumno" class="input-admin" style="max-width:260px;" placeholder="Buscar alumno, email o código..." value="<?= htmlspecialchars($buscar_alumno) ?>">
                    <select name="filtro_taller" class="input-admin" style="max-width:260px;">
                        <option value="0">-- Todos los talleres --</option>
                        <?php foreach ($talleres_lista as $tl): ?>
                            <option value="<?= (int)$tl['id'] ?>" <?= $filtro_taller_ins === (int)$tl['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tl['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="filtro_estado" class="input-admin" style="max-width:220px;">
                        <option value="">-- Todos los estados --</option>
                        <option value="Confirmada" <?= $filtro_estado_ins === 'Confirmada' ? 'selected' : '' ?>>Confirmada</option>
                        <option value="Pendiente de Confirmación" <?= $filtro_estado_ins === 'Pendiente de Confirmación' ? 'selected' : '' ?>>Pendiente de Confirmación</option>
                        <option value="En Lista de Espera" <?= $filtro_estado_ins === 'En Lista de Espera' ? 'selected' : '' ?>>En Lista de Espera</option>
                    </select>
                    <button type="submit" class="btn-guardar">Filtrar</button>
                    <a href="admin.php?tab=inscripciones" class="btn-amarillo" style="display:flex;align-items:center;">Limpiar</a>
                </form>

                <div class="tabla-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Alumno / Contacto</th>
                                <th>Edad</th>
                                <th>Taller & Sede</th>
                                <th>Modalidad / Préstamo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($inscripciones_lista)): ?>
                                <?php foreach ($inscripciones_lista as $ins): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($ins['codigo_inscripcion']) ?></strong><br><small style="color:#666;"><?= date('d/m/Y', strtotime($ins['fecha_inscripcion'])) ?></small></td>
                                        <td>
                                            <strong><?= htmlspecialchars($ins['nombre'] . ' ' . $ins['apellido']) ?></strong> (<?= htmlspecialchars($ins['genero']) ?>)<br>
                                            <small><?= htmlspecialchars($ins['email']) ?></small><br>
                                            <small>📞 <?= htmlspecialchars($ins['telefono']) ?></small>
                                            <?php if (!empty($ins['comentarios'])): ?>
                                                <br><small style="color:#555;"><em>"<?= htmlspecialchars($ins['comentarios']) ?>"</em></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= (int)$ins['edad'] ?> años</td>
                                        <td>
                                            <strong><?= htmlspecialchars($ins['taller_nombre']) ?></strong><br>
                                            <small><?= htmlspecialchars($ins['sede_nombre'] ?? 'Sede Central') ?></small>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($ins['modalidad']) ?><br>
                                            <?php if ((int)$ins['necesita_prestamo_instrumento'] === 1): ?>
                                                <span class="pill pill-warn">Solicita préstamo</span>
                                            <?php else: ?>
                                                <span class="pill pill-ok">Sin préstamo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="POST" action="admin.php?tab=inscripciones" style="display:flex; gap:6px; align-items:center;">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                                <input type="hidden" name="accion" value="actualizar_estado_inscripcion">
                                                <input type="hidden" name="inscripcion_id" value="<?= (int)$ins['id'] ?>">
                                                <select name="estado" class="input-admin" style="padding:5px 8px; font-size:12.5px; min-width:155px;">
                                                    <option value="Confirmada" <?= $ins['estado'] === 'Confirmada' ? 'selected' : '' ?>>Confirmada</option>
                                                    <option value="Pendiente de Confirmación" <?= $ins['estado'] === 'Pendiente de Confirmación' ? 'selected' : '' ?>>Pendiente de Confirmación</option>
                                                    <option value="En Lista de Espera" <?= $ins['estado'] === 'En Lista de Espera' ? 'selected' : '' ?>>En Lista de Espera</option>
                                                </select>
                                                <button type="submit" class="btn-amarillo">OK</button>
                                            </form>
                                        </td>
                                        <td>
                                            <form method="POST" action="admin.php?tab=inscripciones" onsubmit="return confirm('¿Eliminar esta inscripción? Se restaurará 1 vacante al taller.');">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                                <input type="hidden" name="accion" value="eliminar_inscripcion">
                                                <input type="hidden" name="inscripcion_id" value="<?= (int)$ins['id'] ?>">
                                                <button type="submit" class="btn-rojo">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" style="text-align:center; padding:30px; color:#666;">Todavía no hay alumnos inscriptos con ese criterio.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>

        <!-- ===============================================================
             TAB 2: CARGAR, VISUALIZAR Y MODIFICAR DOCENTES
             =============================================================== -->
        <?php if ($tab === 'docentes'): ?>
            <section class="panel-box">
                <h3><?= $docente_editar ? 'Modificar Docente #' . (int)$docente_editar['id'] : 'Cargar Nuevo Docente' ?></h3>
                <form method="POST" action="admin.php?tab=docentes">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="accion" value="guardar_docente">
                    <input type="hidden" name="docente_id" value="<?= (int)($docente_editar['id'] ?? 0) ?>">

                    <div class="form-grid-3">
                        <div class="campo">
                            <label>Nombre *</label>
                            <input type="text" name="nombre" class="input-admin" required value="<?= htmlspecialchars($docente_editar['nombre'] ?? '') ?>">
                        </div>
                        <div class="campo">
                            <label>Apellido *</label>
                            <input type="text" name="apellido" class="input-admin" required value="<?= htmlspecialchars($docente_editar['apellido'] ?? '') ?>">
                        </div>
                        <div class="campo">
                            <label>Instrumento Principal *</label>
                            <input type="text" name="instrumento_principal" class="input-admin" required value="<?= htmlspecialchars($docente_editar['instrumento_principal'] ?? '') ?>" placeholder="Ej: Guitarra">
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="campo">
                            <label>Especialidad *</label>
                            <input type="text" name="especialidad" class="input-admin" required value="<?= htmlspecialchars($docente_editar['especialidad'] ?? '') ?>">
                        </div>
                        <div class="campo">
                            <label>Email *</label>
                            <input type="email" name="email" class="input-admin" required value="<?= htmlspecialchars($docente_editar['email'] ?? '') ?>">
                        </div>
                        <div class="campo">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" class="input-admin" value="<?= htmlspecialchars($docente_editar['telefono'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="campo">
                            <label>Biografía *</label>
                            <textarea name="biografia" class="input-admin" rows="3" required><?= htmlspecialchars($docente_editar['biografia'] ?? '') ?></textarea>
                        </div>
                        <div class="campo">
                            <label>Trayectoria Pedagógica / CV *</label>
                            <textarea name="trayectoria_cv" class="input-admin" rows="3" required><?= htmlspecialchars($docente_editar['trayectoria_cv'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="form-grid-2" style="align-items:center;">
                        <div class="campo">
                            <label>Ruta o URL de Imagen</label>
                            <input type="text" name="imagen" class="input-admin" value="<?= htmlspecialchars($docente_editar['imagen'] ?? ($docente_editar['foto_url'] ?? 'img/default-docente.jpg')) ?>">
                        </div>
                        <div class="campo" style="padding-top:16px;">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="activo" value="1" <?= (!isset($docente_editar['activo']) || (int)$docente_editar['activo'] === 1) ? 'checked' : '' ?>>
                                Docente activo (visible en la web)
                            </label>
                        </div>
                    </div>

                    <div style="display:flex; gap:10px;">
                        <button type="submit" class="btn-guardar"><?= $docente_editar ? 'Guardar Cambios' : 'Cargar Docente' ?></button>
                        <?php if ($docente_editar): ?>
                            <a href="admin.php?tab=docentes" class="btn-amarillo" style="display:flex;align-items:center;">Cancelar Edición</a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <section class="panel-box">
                <h3>Listado de Docentes Cargados</h3>
                <div class="tabla-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Docente</th>
                                <th>Instrumento / Especialidad</th>
                                <th>Contacto</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($docentes_lista as $doc): ?>
                                <tr>
                                    <td>#<?= (int)$doc['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($doc['nombre'] . ' ' . $doc['apellido']) ?></strong></td>
                                    <td><strong><?= htmlspecialchars($doc['instrumento_principal']) ?></strong><br><small><?= htmlspecialchars($doc['especialidad']) ?></small></td>
                                    <td><?= htmlspecialchars($doc['email']) ?><br><small><?= htmlspecialchars($doc['telefono'] ?? '') ?></small></td>
                                    <td><?= (int)$doc['activo'] === 1 ? '<span class="pill pill-ok">Activo</span>' : '<span class="pill pill-off">Inactivo</span>' ?></td>
                                    <td style="display:flex; gap:6px;">
                                        <a href="admin.php?tab=docentes&editar_docente=<?= (int)$doc['id'] ?>" class="btn-amarillo">Editar</a>
                                        <form method="POST" action="admin.php?tab=docentes" onsubmit="return confirm('¿Eliminar este docente?');">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                            <input type="hidden" name="accion" value="eliminar_docente">
                                            <input type="hidden" name="docente_id" value="<?= (int)$doc['id'] ?>">
                                            <button type="submit" class="btn-rojo">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>

        <!-- ===============================================================
             TAB 3: CARGAR, VISUALIZAR Y MODIFICAR TALLERES
             =============================================================== -->
        <?php if ($tab === 'talleres'): ?>
            <section class="panel-box">
                <h3><?= $taller_editar ? 'Modificar Taller #' . (int)$taller_editar['id'] : 'Cargar Nuevo Taller' ?></h3>
                <form method="POST" action="admin.php?tab=talleres">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="accion" value="guardar_taller">
                    <input type="hidden" name="taller_id" value="<?= (int)($taller_editar['id'] ?? 0) ?>">

                    <div class="form-grid-3">
                        <div class="campo">
                            <label>Nombre del Taller *</label>
                            <input type="text" name="nombre" class="input-admin" required value="<?= htmlspecialchars($taller_editar['nombre'] ?? '') ?>">
                        </div>
                        <div class="campo">
                            <label>Instrumento *</label>
                            <input type="text" name="instrumento" class="input-admin" required value="<?= htmlspecialchars($taller_editar['instrumento'] ?? '') ?>">
                        </div>
                        <div class="campo">
                            <label>Docente a Cargo *</label>
                            <select name="docente_id" class="input-admin" required>
                                <?php foreach ($docentes_lista as $d): ?>
                                    <option value="<?= (int)$d['id'] ?>" <?= ((int)($taller_editar['docente_id'] ?? 0) === (int)$d['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($d['nombre'] . ' ' . $d['apellido']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="campo">
                            <label>Nivel *</label>
                            <select name="nivel" class="input-admin">
                                <?php foreach (['Inicial / Sin experiencia', 'Intermedio', 'Avanzado', 'Ensamble'] as $nv): ?>
                                    <option value="<?= $nv ?>" <?= (($taller_editar['nivel'] ?? '') === $nv) ? 'selected' : '' ?>><?= $nv ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="campo">
                            <label>Modalidad *</label>
                            <select name="modalidad" class="input-admin">
                                <?php foreach (['Presencial', 'Online', 'Híbrida'] as $mod): ?>
                                    <option value="<?= $mod ?>" <?= (($taller_editar['modalidad'] ?? '') === $mod) ? 'selected' : '' ?>><?= $mod ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="campo">
                            <label>Sede *</label>
                            <select name="sede_id" class="input-admin">
                                <?php foreach ($sedes_lista as $s): ?>
                                    <option value="<?= (int)$s['id'] ?>" <?= ((int)($taller_editar['sede_id'] ?? 1) === (int)$s['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="campo">
                            <label>Días de Cursada *</label>
                            <input type="text" name="dia_semana" class="input-admin" required value="<?= htmlspecialchars($taller_editar['dia_semana'] ?? '') ?>" placeholder="Ej: Martes y Jueves">
                        </div>
                        <div class="campo">
                            <label>Horario *</label>
                            <input type="text" name="horario" class="input-admin" required value="<?= htmlspecialchars($taller_editar['horario'] ?? '') ?>" placeholder="Ej: 18:00 a 19:30 hs">
                        </div>
                        <div class="campo">
                            <label>Arancel Mensual ($) *</label>
                            <input type="number" step="0.01" name="arancel_mensual" class="input-admin" required value="<?= htmlspecialchars((string)($taller_editar['arancel_mensual'] ?? '15000')) ?>">
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="campo">
                            <label>Vacantes Totales *</label>
                            <input type="number" name="vacantes_totales" class="input-admin" required value="<?= htmlspecialchars((string)($taller_editar['vacantes_totales'] ?? '12')) ?>">
                        </div>
                        <div class="campo">
                            <label>Vacantes Disponibles *</label>
                            <input type="number" name="vacantes_disponibles" class="input-admin" required value="<?= htmlspecialchars((string)($taller_editar['vacantes_disponibles'] ?? '6')) ?>">
                        </div>
                        <div class="campo">
                            <label>Edad Mínima / Inst. Préstamo</label>
                            <div style="display:flex; gap:8px;">
                                <input type="number" name="edad_minima" class="input-admin" value="<?= htmlspecialchars((string)($taller_editar['edad_minima'] ?? '7')) ?>" title="Edad mínima">
                                <input type="number" name="instrumentos_prestamo_disponibles" class="input-admin" value="<?= htmlspecialchars((string)($taller_editar['instrumentos_prestamo_disponibles'] ?? '5')) ?>" title="Instrumentos para préstamo">
                            </div>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="campo">
                            <label>Descripción *</label>
                            <textarea name="descripcion" class="input-admin" rows="2" required><?= htmlspecialchars($taller_editar['descripcion'] ?? '') ?></textarea>
                        </div>
                        <div class="campo">
                            <label>Requisitos</label>
                            <textarea name="requisitos" class="input-admin" rows="2"><?= htmlspecialchars($taller_editar['requisitos'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="form-grid-2" style="align-items:center;">
                        <div class="campo">
                            <label>Imagen (ruta local o URL)</label>
                            <input type="text" name="imagen_url" class="input-admin" value="<?= htmlspecialchars($taller_editar['imagen_url'] ?? 'img/guitarra.jpg') ?>">
                        </div>
                        <div class="campo" style="padding-top:16px;">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="destacado" value="1" <?= !empty($taller_editar['destacado']) ? 'checked' : '' ?>>
                                Mostrar como taller destacado en Home
                            </label>
                        </div>
                    </div>

                    <div style="display:flex; gap:10px;">
                        <button type="submit" class="btn-guardar"><?= $taller_editar ? 'Guardar Cambios' : 'Cargar Taller' ?></button>
                        <?php if ($taller_editar): ?>
                            <a href="admin.php?tab=talleres" class="btn-amarillo" style="display:flex;align-items:center;">Cancelar Edición</a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <section class="panel-box">
                <h3>Listado de Talleres Cargados</h3>
                <div class="tabla-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Taller</th>
                                <th>Docente / Sede</th>
                                <th>Horario</th>
                                <th>Vacantes</th>
                                <th>Arancel</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($talleres_lista as $tl): ?>
                                <tr>
                                    <td>#<?= (int)$tl['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($tl['nombre']) ?></strong><br><small><?= htmlspecialchars($tl['instrumento']) ?> • <?= htmlspecialchars($tl['nivel']) ?></small></td>
                                    <td><?= htmlspecialchars($tl['docente_nombre'] ?? '') ?><br><small><?= htmlspecialchars($tl['sede_nombre'] ?? '') ?></small></td>
                                    <td><?= htmlspecialchars($tl['dia_semana']) ?><br><small><?= htmlspecialchars($tl['horario']) ?></small></td>
                                    <td><span class="pill pill-ok"><?= (int)$tl['vacantes_disponibles'] ?> / <?= (int)$tl['vacantes_totales'] ?></span></td>
                                    <td><strong>$<?= number_format((float)$tl['arancel_mensual'], 0, ',', '.') ?></strong></td>
                                    <td style="display:flex; gap:6px;">
                                        <a href="admin.php?tab=talleres&editar_taller=<?= (int)$tl['id'] ?>" class="btn-amarillo">Editar</a>
                                        <form method="POST" action="admin.php?tab=talleres" onsubmit="return confirm('¿Eliminar este taller y sus inscripciones asociadas?');">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                            <input type="hidden" name="accion" value="eliminar_taller">
                                            <input type="hidden" name="taller_id" value="<?= (int)$tl['id'] ?>">
                                            <button type="submit" class="btn-rojo">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>

        <!-- ===============================================================
             TAB 4: CARGAR, VISUALIZAR Y MODIFICAR EVENTOS
             =============================================================== -->
        <?php if ($tab === 'eventos'): ?>
            <section class="panel-box">
                <h3><?= $evento_editar ? 'Modificar Evento #' . (int)$evento_editar['id'] : 'Cargar Nuevo Evento' ?></h3>
                <form method="POST" action="admin.php?tab=eventos">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="accion" value="guardar_evento">
                    <input type="hidden" name="evento_id" value="<?= (int)($evento_editar['id'] ?? 0) ?>">

                    <div class="form-grid-3">
                        <div class="campo">
                            <label>Título del Evento *</label>
                            <input type="text" name="titulo" class="input-admin" required value="<?= htmlspecialchars($evento_editar['titulo'] ?? '') ?>">
                        </div>
                        <div class="campo">
                            <label>Tipo de Evento *</label>
                            <select name="tipo" class="input-admin">
                                <?php foreach (['Muestra de Alumnos', 'Concierto Abierto', 'Masterclass', 'Jam Session'] as $tp): ?>
                                    <option value="<?= $tp ?>" <?= (($evento_editar['tipo'] ?? '') === $tp) ? 'selected' : '' ?>><?= $tp ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="campo">
                            <label>Color Etiqueta</label>
                            <select name="badge_color" class="input-admin">
                                <option value="yellow" <?= (($evento_editar['badge_color'] ?? '') === 'yellow') ? 'selected' : '' ?>>Amarillo</option>
                                <option value="orange" <?= (($evento_editar['badge_color'] ?? '') === 'orange') ? 'selected' : '' ?>>Naranja</option>
                                <option value="blue" <?= (($evento_editar['badge_color'] ?? '') === 'blue') ? 'selected' : '' ?>>Celeste</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="campo">
                            <label>Fecha *</label>
                            <input type="date" name="fecha" class="input-admin" required value="<?= htmlspecialchars($evento_editar['fecha'] ?? date('Y-m-d')) ?>">
                        </div>
                        <div class="campo">
                            <label>Hora *</label>
                            <input type="text" name="hora" class="input-admin" required value="<?= htmlspecialchars($evento_editar['hora'] ?? '18:30:00') ?>" placeholder="18:30:00">
                        </div>
                        <div class="campo">
                            <label>Lugar *</label>
                            <input type="text" name="lugar" class="input-admin" required value="<?= htmlspecialchars($evento_editar['lugar'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="campo">
                            <label>Precio Entrada ($)</label>
                            <input type="number" step="0.01" name="precio_entrada" class="input-admin" value="<?= htmlspecialchars((string)($evento_editar['precio_entrada'] ?? '0')) ?>">
                        </div>
                        <div class="campo">
                            <label>Capacidad Total / Disponibles</label>
                            <div style="display:flex; gap:8px;">
                                <input type="number" name="capacidad_total" class="input-admin" value="<?= htmlspecialchars((string)($evento_editar['capacidad_total'] ?? '100')) ?>">
                                <input type="number" name="entradas_disponibles" class="input-admin" value="<?= htmlspecialchars((string)($evento_editar['entradas_disponibles'] ?? '80')) ?>">
                            </div>
                        </div>
                        <div class="campo">
                            <label>Estado</label>
                            <select name="estado" class="input-admin">
                                <?php foreach (['proximo', 'previo', 'agotado'] as $est): ?>
                                    <option value="<?= $est ?>" <?= (($evento_editar['estado'] ?? '') === $est) ? 'selected' : '' ?>><?= ucfirst($est) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="campo">
                            <label>Descripción *</label>
                            <textarea name="descripcion" class="input-admin" rows="2" required><?= htmlspecialchars($evento_editar['descripcion'] ?? '') ?></textarea>
                        </div>
                        <div class="campo">
                            <label>Repertorio *</label>
                            <textarea name="repertorio" class="input-admin" rows="2" required><?= htmlspecialchars($evento_editar['repertorio'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="campo">
                        <label>Imagen (archivo en img/ o URL)</label>
                        <input type="text" name="imagen_url" class="input-admin" value="<?= htmlspecialchars($evento_editar['imagen_url'] ?? 'muestra-primavera.jpg') ?>">
                    </div>

                    <div style="display:flex; gap:10px;">
                        <button type="submit" class="btn-guardar"><?= $evento_editar ? 'Guardar Cambios' : 'Cargar Evento' ?></button>
                        <?php if ($evento_editar): ?>
                            <a href="admin.php?tab=eventos" class="btn-amarillo" style="display:flex;align-items:center;">Cancelar Edición</a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <section class="panel-box">
                <h3>Eventos Registrados en Agenda</h3>
                <div class="tabla-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Título / Tipo</th>
                                <th>Lugar</th>
                                <th>Precio</th>
                                <th>Butacas (Disp. / Reservadas)</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($eventos_lista as $ev): ?>
                                <tr>
                                    <td><strong><?= date('d/m/Y', strtotime($ev['fecha'])) ?></strong><br><small><?= htmlspecialchars($ev['hora']) ?></small></td>
                                    <td><strong><?= htmlspecialchars($ev['titulo']) ?></strong><br><small><?= htmlspecialchars($ev['tipo']) ?> (<?= htmlspecialchars($ev['estado']) ?>)</small></td>
                                    <td><?= htmlspecialchars($ev['lugar']) ?></td>
                                    <td><?= (float)$ev['precio_entrada'] > 0 ? '$' . number_format((float)$ev['precio_entrada'], 0, ',', '.') : 'Gratis' ?></td>
                                    <td><span class="pill pill-ok"><?= (int)$ev['entradas_disponibles'] ?> disp.</span> <small>(<?= (int)$ev['entradas_vendidas'] ?> res.)</small></td>
                                    <td style="display:flex; gap:6px;">
                                        <a href="admin.php?tab=eventos&editar_evento=<?= (int)$ev['id'] ?>" class="btn-amarillo">Editar</a>
                                        <form method="POST" action="admin.php?tab=eventos" onsubmit="return confirm('¿Eliminar este evento?');">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                            <input type="hidden" name="accion" value="eliminar_evento">
                                            <input type="hidden" name="evento_id" value="<?= (int)$ev['id'] ?>">
                                            <button type="submit" class="btn-rojo">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>

        <!-- ===============================================================
             TAB 5: MODIFICAR INFORMACIÓN CARGADA EN LA PÁGINA
             =============================================================== -->
        <?php if ($tab === 'contenido'): ?>
            <!-- A. Textos Institucionales -->
            <section class="panel-box">
                <h3>Textos Generales del Sitio (Home, Barra Superior, Nosotros, Contacto)</h3>
                <form method="POST" action="admin.php?tab=contenido">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="accion" value="guardar_config_pagina">
                    <?php foreach ($config_lista as $cfg): ?>
                        <div class="campo">
                            <label>[<?= htmlspecialchars($cfg['seccion']) ?>] <?= htmlspecialchars($cfg['titulo']) ?></label>
                            <?php if (strlen($cfg['valor']) > 90): ?>
                                <textarea name="config[<?= htmlspecialchars($cfg['clave']) ?>]" class="input-admin" rows="2"><?= htmlspecialchars($cfg['valor']) ?></textarea>
                            <?php else: ?>
                                <input type="text" name="config[<?= htmlspecialchars($cfg['clave']) ?>]" class="input-admin" value="<?= htmlspecialchars($cfg['valor']) ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn-guardar">Guardar Textos de la Página</button>
                </form>
            </section>

            <!-- B. Sedes Físicas -->
            <section class="panel-box">
                <h3>Modificar Sedes Físicas</h3>
                <?php foreach ($sedes_lista as $sede): ?>
                    <form method="POST" action="admin.php?tab=contenido" style="border:1px solid #e5e7eb; border-radius:14px; padding:16px; margin-bottom:16px; background:#fffdf7;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="accion" value="guardar_sede">
                        <input type="hidden" name="sede_id" value="<?= (int)$sede['id'] ?>">
                        <div class="form-grid-3">
                            <div class="campo"><label>Nombre</label><input type="text" name="nombre" class="input-admin" value="<?= htmlspecialchars($sede['nombre']) ?>"></div>
                            <div class="campo"><label>Dirección</label><input type="text" name="direccion" class="input-admin" value="<?= htmlspecialchars($sede['direccion']) ?>"></div>
                            <div class="campo"><label>Barrio</label><input type="text" name="barrio" class="input-admin" value="<?= htmlspecialchars($sede['barrio']) ?>"></div>
                        </div>
                        <div class="form-grid-3">
                            <div class="campo"><label>Teléfono</label><input type="text" name="telefono" class="input-admin" value="<?= htmlspecialchars($sede['telefono']) ?>"></div>
                            <div class="campo"><label>Email</label><input type="email" name="email" class="input-admin" value="<?= htmlspecialchars($sede['email']) ?>"></div>
                            <div class="campo"><label>Horarios de Atención</label><input type="text" name="horarios_atencion" class="input-admin" value="<?= htmlspecialchars($sede['horarios_atencion']) ?>"></div>
                        </div>
                        <div class="form-grid-2">
                            <div class="campo"><label>Colectivos</label><input type="text" name="colectivos" class="input-admin" value="<?= htmlspecialchars($sede['colectivos'] ?? '') ?>"></div>
                            <div class="campo"><label>Subte</label><input type="text" name="subte" class="input-admin" value="<?= htmlspecialchars($sede['subte'] ?? '') ?>"></div>
                        </div>
                        <button type="submit" class="btn-amarillo">Actualizar Sede</button>
                    </form>
                <?php endforeach; ?>
            </section>

            <!-- C. Testimonios / Reseñas -->
            <section class="panel-box">
                <h3>Modificar Testimonios (Home)</h3>
                <?php foreach ($resenas_lista as $res): ?>
                    <form method="POST" action="admin.php?tab=contenido" style="border:1px solid #e5e7eb; border-radius:14px; padding:14px; margin-bottom:12px;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="accion" value="guardar_resena">
                        <input type="hidden" name="resena_id" value="<?= (int)$res['id'] ?>">
                        <div class="form-grid-3">
                            <div class="campo"><label>Autor</label><input type="text" name="autor" class="input-admin" value="<?= htmlspecialchars($res['autor']) ?>"></div>
                            <div class="campo"><label>Rol / Taller</label><input type="text" name="rol" class="input-admin" value="<?= htmlspecialchars($res['rol']) ?>"></div>
                            <div class="campo"><label>Calificación (1-5)</label><input type="number" min="1" max="5" name="calificacion" class="input-admin" value="<?= (int)$res['calificacion'] ?>"></div>
                        </div>
                        <div class="campo"><label>Comentario</label><input type="text" name="comentario" class="input-admin" value="<?= htmlspecialchars($res['comentario']) ?>"></div>
                        <button type="submit" class="btn-amarillo">Guardar Testimonio</button>
                    </form>
                <?php endforeach; ?>
            </section>

            <!-- D. Preguntas Frecuentes -->
            <section class="panel-box">
                <h3>Modificar Preguntas Frecuentes (Consultas)</h3>
                <?php foreach ($faqs_lista as $faq): ?>
                    <form method="POST" action="admin.php?tab=contenido" style="border:1px solid #e5e7eb; border-radius:14px; padding:14px; margin-bottom:12px;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="accion" value="guardar_faq">
                        <input type="hidden" name="faq_id" value="<?= (int)$faq['id'] ?>">
                        <div class="form-grid-3">
                            <div class="campo"><label>Categoría</label><input type="text" name="categoria" class="input-admin" value="<?= htmlspecialchars($faq['categoria']) ?>"></div>
                            <div class="campo"><label>Pregunta</label><input type="text" name="pregunta" class="input-admin" value="<?= htmlspecialchars($faq['pregunta']) ?>"></div>
                            <div class="campo"><label>Orden</label><input type="number" name="orden" class="input-admin" value="<?= (int)$faq['orden'] ?>"></div>
                        </div>
                        <div class="campo"><label>Respuesta</label><textarea name="respuesta" class="input-admin" rows="2"><?= htmlspecialchars($faq['respuesta']) ?></textarea></div>
                        <button type="submit" class="btn-amarillo">Actualizar FAQ</button>
                    </form>
                <?php endforeach; ?>
            </section>

            <!-- E. Consultas recibidas -->
            <section class="panel-box">
                <h3>Mensajes de Consulta Recibidos</h3>
                <div class="tabla-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr><th>Fecha</th><th>Remitente</th><th>Motivo</th><th>Mensaje</th><th>Estado</th></tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($consultas_lista)): ?>
                                <?php foreach ($consultas_lista as $c): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($c['fecha'])) ?></td>
                                        <td><strong><?= htmlspecialchars($c['nombre']) ?></strong><br><small><?= htmlspecialchars($c['email']) ?></small></td>
                                        <td><?= htmlspecialchars($c['motivo']) ?></td>
                                        <td><?= htmlspecialchars($c['mensaje']) ?></td>
                                        <td>
                                            <form method="POST" action="admin.php?tab=contenido">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                                <input type="hidden" name="accion" value="marcar_consulta_leida">
                                                <input type="hidden" name="consulta_id" value="<?= (int)$c['id'] ?>">
                                                <input type="hidden" name="leido" value="<?= (int)$c['leido'] === 1 ? 0 : 1 ?>">
                                                <button type="submit" class="btn-amarillo"><?= (int)$c['leido'] === 1 ? '✓ Leída' : 'Marcar leída' ?></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align:center; padding:20px; color:#666;">No hay mensajes de consulta registrados todavía.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>

        <!-- ===============================================================
             TAB 6: GESTIÓN DE USUARIOS ADMINISTRADORES EN MYSQL
             =============================================================== -->
        <?php if ($tab === 'admins'): ?>
            <section class="panel-box">
                <h3>Cargar Nuevo Usuario Administrador en la Base de Datos</h3>
                <form method="POST" action="admin.php?tab=admins">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="accion" value="crear_admin">

                    <div class="form-grid-3">
                        <div class="campo"><label>Nombre de Usuario *</label><input type="text" name="usuario" class="input-admin" required placeholder="Ej: sofia.admin"></div>
                        <div class="campo"><label>Nombre Completo *</label><input type="text" name="nombre_completo" class="input-admin" required placeholder="Ej: Sofía Tomillo"></div>
                        <div class="campo"><label>Correo Electrónico *</label><input type="email" name="email" class="input-admin" required placeholder="admin@atodoritmo.edu.ar"></div>
                    </div>
                    <div class="form-grid-2">
                        <div class="campo"><label>Contraseña (mínimo 6 caracteres) *</label><input type="password" name="password" class="input-admin" required minlength="6"></div>
                        <div class="campo">
                            <label>Rol</label>
                            <select name="rol" class="input-admin">
                                <option value="admin">Administrador</option>
                                <option value="superadmin">Super Administrador</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn-guardar">Registrar Administrador en MySQL</button>
                </form>
            </section>

            <section class="panel-box">
                <h3>Administradores Cargados</h3>
                <div class="tabla-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Último Acceso</th><th>Estado</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins_lista as $ad): ?>
                                <tr>
                                    <td>#<?= (int)$ad['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($ad['usuario']) ?></strong></td>
                                    <td><?= htmlspecialchars($ad['nombre_completo']) ?></td>
                                    <td><?= htmlspecialchars($ad['email']) ?></td>
                                    <td><?= htmlspecialchars($ad['rol']) ?></td>
                                    <td><?= !empty($ad['ultimo_acceso']) ? date('d/m/Y H:i', strtotime($ad['ultimo_acceso'])) : 'Sin ingresos' ?></td>
                                    <td>
                                        <form method="POST" action="admin.php?tab=admins">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                            <input type="hidden" name="accion" value="toggle_admin">
                                            <input type="hidden" name="admin_id" value="<?= (int)$ad['id'] ?>">
                                            <input type="hidden" name="activo" value="<?= (int)$ad['activo'] === 1 ? 0 : 1 ?>">
                                            <button type="submit" class="btn-amarillo"><?= (int)$ad['activo'] === 1 ? '✓ Activo' : 'Inactivo' ?></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
