<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/auth_admin.php';

$cfg = obtenerConfigPagina($pdo);
$hero_etiqueta    = $cfg['hero_etiqueta']    ?? '✨ Inscripciones Ciclo Lectivo 2026';
$hero_titulo_1    = $cfg['hero_titulo_1']    ?? 'Sentí la música,';
$hero_titulo_2    = $cfg['hero_titulo_2']    ?? 'descubrí tu ritmo.';
$hero_descripcion = $cfg['hero_descripcion'] ?? 'Una escuela de música inclusiva en el corazón del barrio. Clases de guitarra, violín, flauta, batería, piano y canto para todas las edades. ¿No tenés instrumento? Te lo prestamos nosotros.';

try {
    $sql_talleres = "SELECT * FROM talleres WHERE destacado = 1 ORDER BY id ASC LIMIT 4";
    $stmt_talleres = $pdo->query($sql_talleres);
    $talleres_destacados = $stmt_talleres->fetchAll(PDO::FETCH_ASSOC);

    $sql_eventos = "SELECT * FROM eventos WHERE fecha >= CURDATE() ORDER BY fecha ASC LIMIT 1";
    $stmt_eventos = $pdo->query($sql_eventos);
    $proximo_evento = $stmt_eventos->fetch(PDO::FETCH_ASSOC);

    $sql_resenas = "SELECT * FROM resenas ORDER BY id ASC LIMIT 3";
    $stmt_resenas = $pdo->query($sql_resenas);
    $lista_resenas = $stmt_resenas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $talleres_destacados = [];
    $proximo_evento = null;
    $lista_resenas = [];
}

if (empty($talleres_destacados)) {
    $talleres_destacados = [
        ['id' => 1, 'instrumento' => 'Guitarra', 'nombre' => 'Taller de Guitarra Criolla', 'descripcion' => 'Acordes, rasgueos folclóricos y ritmos de rock desde el primer día en clase grupal.', 'vacantes_disponibles' => 4, 'arancel_mensual' => 14500, 'imagen_url' => 'img/guitarra.jpg'],
        ['id' => 2, 'instrumento' => 'Violín', 'nombre' => 'Taller de Violín', 'descripcion' => 'Postura corporal relajada, arco, afinación y melodías sencillas adaptadas.', 'vacantes_disponibles' => 3, 'arancel_mensual' => 15500, 'imagen_url' => 'img/violin.jpg'],
        ['id' => 3, 'instrumento' => 'Batería', 'nombre' => 'Taller de Batería', 'descripcion' => 'Coordinación motriz, rock, funk y cumbia en sets acústicos reales.', 'vacantes_disponibles' => 2, 'arancel_mensual' => 16000, 'imagen_url' => 'img/bateria.jpg'],
        ['id' => 6, 'instrumento' => 'Piano', 'nombre' => 'Taller de Piano', 'descripcion' => 'Lectura fluida en clave de sol y fa, armonía práctica y acompañamiento.', 'vacantes_disponibles' => 5, 'arancel_mensual' => 15500, 'imagen_url' => 'img/piano.jpg'],
    ];
}

if (empty($proximo_evento)) {
    $proximo_evento = [
        'titulo' => 'Gran Muestra de Primavera: Todos al Escenario',
        'descripcion' => 'Presentación en vivo de los ensambles de iniciales y avanzados. Los alumnos de guitarra, batería, violín, flauta y coro mostrarán los proyectos trabajados.',
        'fecha' => '2026-10-17',
        'hora' => '18:30:00',
        'lugar' => 'Auditorio Sala Mayor - San Telmo',
        'precio' => 1500
    ];
}

if (empty($lista_resenas)) {
    $lista_resenas = [
        ['autor' => 'Esteban Méndez', 'rol' => 'Alumno — Guitarra', 'comentario' => '"Empecé de cero a los 42 años sin tener guitarra. Julián y la escuela me prestaron una y en 3 meses ya estaba tocando."', 'calificacion' => 5],
        ['autor' => 'Marcela V.', 'rol' => 'Familiar — Violín', 'comentario' => '"En otras escuelas nos pedían comprar un violín carísimo antes de empezar. Acá le adaptaron un violín 1/2 con paciencia admirable."', 'calificacion' => 5],
        ['autor' => 'Lucas Benítez', 'rol' => 'Egresado — Batería', 'comentario' => '"La cuota es accesible y no sentís esa competencia de otros lados. Acá aprendés a escuchar al de al lado y disfrutar en grupo."', 'calificacion' => 5],
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escuela de Música A Todo Ritmo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Carter+One&family=Mukta:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container">

        <!-- HERO / BANNER PRINCIPAL -->
        <section class="hero pattern-dots-yellow">
            <div style="display:inline-flex; align-items:center; gap:8px; background-color:rgba(36, 171, 88, 0.3); border:1px solid #24AB58; color:#F3D84F; padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700; margin-bottom:20px; text-transform:uppercase;">
                <?= htmlspecialchars($hero_etiqueta) ?>
            </div>
            <h2><?= htmlspecialchars($hero_titulo_1) ?><br><span style="color: #F3D84F;"><?= htmlspecialchars($hero_titulo_2) ?></span></h2>
            <p><?= htmlspecialchars($hero_descripcion) ?></p>
            <div class="flex-center">
                <a href="inscripcion.php" class="btn btn-y" style="text-decoration:none;">¡Quiero Inscribirme!</a>
                <a href="talleres.php" class="btn btn-outline" style="text-decoration:none;">Explorar Talleres 2026</a>
            </div>
        </section>

        <!-- SECCIÓN FILOSOFÍA -->
        <span style="text-align:center; display:block; font-size:12px; font-weight:bold; color:#24AB58; text-transform:uppercase; letter-spacing:1px;">Nuestra Filosofía</span>
        <h2 class="seccion-titulo" style="margin-top:4px;">¿Por qué estudiar en "A Todo Ritmo"?</h2>
        <p class="sub-titulo">Rompemos las barreras de los conservatorios tradicionales para que el arte sea un espacio de encuentro y disfrute.</p>

        <div class="grid-4-horizontal">
            <div class="card-comic"><div class="icon-box">🤝</div><h3>Banco de Instrumentos</h3><p>No dejes de aprender por no poder comprar un instrumento caro. La escuela cuenta con stock para uso gratuito.</p></div>
            <div class="card-comic"><div class="icon-box yellow">✓</div><h3>Vacantes Reales</h3><p>Sin audiciones elitistas ni filtros frustrantes. Todos tenemos derecho a la música; organizamos grupos reducidos por edades.</p></div>
            <div class="card-comic"><div class="icon-box">👥</div><h3>Docentes de Vocación</h3><p>Profesores graduados de conservatorios nacionales con una pedagogía cercana, alegre y respetuosa de cada tiempo.</p></div>
            <div class="card-comic"><div class="icon-box yellow">🎵</div><h3>Ensambles y Muestras</h3><p>No tocás solo: desde los primeros meses participás en ensambles colectivos y muestras en vivo en el escenario.</p></div>
        </div>

        <!-- TALLERES DESTACADOS -->
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:25px; margin-top: 50px; flex-wrap:wrap; gap:10px;">
            <div>
                <span style="font-size:12px; font-weight:bold; color:#24AB58; text-transform:uppercase;">Nuestra Oferta Educativa</span>
                <h3 style="font-family:'Carter One', cursive; font-size:24px; margin:4px 0 0 0;">Talleres Destacados</h3>
                <p style="margin:4px 0 0 0; font-size:14px; color:#555;">Conocé las disciplinas más elegidas por nuestros alumnos.</p>
            </div>
            <a href="talleres.php" style="color:#24AB58; font-weight:bold; text-decoration:underline; font-size:14px;">Ver todos los talleres →</a>
        </div>

        <div class="grid-4-horizontal">
            <?php foreach ($talleres_destacados as $td):
                $img_t = 'img/guitarra.jpg';
                if (!empty($td['imagen_url'])) {
                    $img_t = $td['imagen_url'];
                } elseif (stripos($td['instrumento'], 'viol') !== false) {
                    $img_t = 'img/violin.jpg';
                } elseif (stripos($td['instrumento'], 'bat') !== false) {
                    $img_t = 'img/bateria.jpg';
                } elseif (stripos($td['instrumento'], 'piano') !== false) {
                    $img_t = 'img/piano.jpg';
                }
            ?>
            <div class="taller-mini-card">
                <div>
                    <div class="taller-img-container">
                        <img src="<?= htmlspecialchars($img_t) ?>" alt="<?= htmlspecialchars($td['instrumento']) ?>">
                    </div>
                    <div class="taller-mini-body">
                        <div class="taller-badge"><?= htmlspecialchars($td['instrumento']) ?></div>
                        <h4><?= htmlspecialchars($td['nombre']) ?></h4>
                        <p><?= htmlspecialchars($td['descripcion']) ?></p>
                    </div>
                </div>
                <div class="taller-mini-footer">
                    <span style="color:#24AB58; font-weight:bold;"><?= (int)$td['vacantes_disponibles'] ?> libres</span>
                    <strong>$<?= number_format((float)$td['arancel_mensual'], 0, ',', '.') ?></strong>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- PRÓXIMO EVENTO -->
        <?php
            $ev_precio = isset($proximo_evento['precio']) ? (float)$proximo_evento['precio'] : (float)($proximo_evento['precio_entrada'] ?? 1500);
            $ev_fecha_fmt = !empty($proximo_evento['fecha']) ? date('d/m/Y', strtotime($proximo_evento['fecha'])) : '17/10/2026';
            $ev_hora_fmt = !empty($proximo_evento['hora']) ? date('H:i', strtotime($proximo_evento['hora'])) : '18:30';
        ?>
        <div class="evento-banner-destacado">
            <div class="evento-info-side">
                <div style="background:#F3D84F; color:#181818; display:inline-block; padding:3px 10px; border-radius:6px; font-size:11px; font-weight:bold; text-transform:uppercase; margin-bottom:10px;">📅 Próximo Evento Mensual</div>
                <h3><?= htmlspecialchars($proximo_evento['titulo']) ?></h3>
                <p style="font-size:14px; color:#ccc; margin:0; line-height:1.5;"><?= htmlspecialchars($proximo_evento['descripcion']) ?></p>
                <div class="event-chips">
                    <div class="chip">Fecha: <?= htmlspecialchars($ev_fecha_fmt) ?></div>
                    <div class="chip">Hora: <?= htmlspecialchars($ev_hora_fmt) ?> hs</div>
                    <div class="chip">Lugar: <?= htmlspecialchars($proximo_evento['lugar']) ?></div>
                </div>
            </div>
            <div class="evento-ticket-side">
                <span style="font-size:11px; color:#aaa; font-weight:bold; text-transform:uppercase; display:block; margin-bottom:5px;">Valor del Bono</span>
                <strong style="font-family:'Carter One', cursive; font-size:24px; color:#F3D84F; display:block; margin-bottom:5px;">
                    <?= $ev_precio > 0 ? '$' . number_format($ev_precio, 0, ',', '.') : 'Gratis' ?>
                </strong>
                <p style="font-size:10px; color:#999; margin:0 0 15px 0;">Destinado al banco solidario.</p>
                <a href="eventos.php" class="btn btn-y" style="text-decoration:none; font-size:13px; padding:10px; display:block;">Sacar Entradas Online</a>
            </div>
        </div>

        <!-- TESTIMONIOS -->
        <div style="text-align: center;">
            <span style="font-size:12px; font-weight:bold; color:#24AB58; text-transform:uppercase; letter-spacing:1px;">Testimonios Reales</span>
            <h2 class="seccion-titulo" style="margin-top:4px;">La Música Transforma el Barrio</h2>
            <p class="sub-titulo">Opiniones de nuestros alumnos, familias y egresados de comisiones regulares.</p>
        </div>

        <div class="flex-resenas">
            <?php foreach ($lista_resenas as $res): ?>
            <div class="card-resena">
                <div>
                    <div class="stars"><?= str_repeat('★', (int)($res['calificacion'] ?? 5)) ?></div>
                    <p style="font-size:12.5px; font-style:italic; color:#333; line-height:1.5; margin:0;"><?= htmlspecialchars($res['comentario']) ?></p>
                </div>
                <div>
                    <hr style="border:0; border-top:1px solid #eee; margin:12px 0 8px 0;">
                    <strong style="font-size:13px;"><?= htmlspecialchars($res['autor']) ?></strong><br>
                    <small style="color:#24AB58; font-size:11px;"><?= htmlspecialchars($res['rol']) ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="cta-final-box pattern-dots-dark">
            <h2>¿Listo para empezar tu viaje musical?</h2>
            <p>Completa la inscripción online en 2 minutos. Si no tenés instrumento, marcá la opción de préstamo solidario en la ficha y te lo reservamos para tu primera clase.</p>
            <a href="inscripcion.php" class="btn" style="background:#181818; color:#F3D84F; font-family:'Carter One', cursive; text-decoration:none; display:block; margin:0 auto; width:fit-content;">Completar Ficha de Inscripción 2026</a>
        </div>

    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
