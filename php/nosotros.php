<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/auth_admin.php';

$cfg = obtenerConfigPagina($pdo);
$historia_titulo = $cfg['nosotros_historia_titulo'] ?? 'De un Taller Barrial a una Red de Formación Musical Abierta';
$historia_p1     = $cfg['nosotros_historia_p1'] ?? '"A Todo Ritmo" nació en 2018 impulsada por un grupo de músicos y docentes egresados de conservatorios públicos que veían cómo cientos de chicos, jóvenes y adultos quedaban afuera de la formación musical por no conseguir vacantes en instituciones saturadas o por no poder costear un instrumento propio.';
$historia_p2     = $cfg['nosotros_historia_p2'] ?? 'Comenzamos con una sala prestada en San Telmo y guitarras donadas. Hoy contamos con dos sedes activas, más de 200 alumnos regulares, un Banco Solidario con más de 60 instrumentos en comodato y un cuerpo docente apasionado por la enseñanza popular.';

try {
    $sedes_db = $pdo->query("SELECT * FROM sedes ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $sedes_db = [];
}

if (empty($sedes_db)) {
    $sedes_db = [
        [
            'nombre' => 'Sede Central - San Telmo', 'direccion' => 'Defensa 842', 'barrio' => 'San Telmo, CABA',
            'horarios_atencion' => 'Lun a Vie 14:00 - 21:00 hs | Sáb 09:30 - 19:00 hs', 'telefono' => '(011) 4361-9820',
            'email' => 'santelmo@atodoritmo.edu.ar', 'colectivos' => 'Líneas 24, 29, 64, 86, 152',
            'subte' => 'Línea C (Estación San Juan) y Línea E (Estación Bolívar)'
        ],
        [
            'nombre' => 'Anexo Comunitario - Almagro', 'direccion' => 'Av. Corrientes 4120', 'barrio' => 'Almagro, CABA',
            'horarios_atencion' => 'Lun a Vie 15:00 - 20:30 hs | Sáb 10:00 - 16:30 hs', 'telefono' => '(011) 4862-5510',
            'email' => 'almagro@atodoritmo.edu.ar', 'colectivos' => 'Líneas 19, 26, 71, 92, 127, 168',
            'subte' => 'Línea B (Estación Medrano)'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros - Escuela de Música A Todo Ritmo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Carter+One&family=Mukta:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <style>
        h1, h2, h3, .font-carter { font-family: 'Carter One', cursive, system-ui, sans-serif !important; }
        body { font-family: 'Mukta', system-ui, sans-serif !important; background-color: #FFFAE6; color: #181818; }

        .grid-2-nosotros { display: flex; gap: 40px; align-items: center; margin-top: 20px; }
        .grid-2-nosotros > div { flex: 1; }

        .stats-box-nosotros { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 25px; }

        .stat-card { background: #FFFFFF; border: 2px solid #181818; padding: 15px; text-align: center; border-radius: 16px; box-shadow: 2px 2px 0px #181818; }
        .stat-card span { font-family: 'Carter One', cursive; font-size: 24px; color: #24AB58; display: block; }

        .trayecto-badge { padding: 4px 10px; border-radius: 4px; font-family: 'Carter One', cursive; font-size: 12px; display: inline-block; margin-bottom: 10px; border: 1px solid #181818; }
        .badge-green { background: #24AB58; color: #FFFAE6; }
        .badge-yellow { background: #F3D84F; color: #181818; }
        .badge-dark { background: #181818; color: #F3D84F; }

        .grid-trayectos-horizontal {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 25px;
        }
        .card-comic-horizontal {
            background: #FFFAE6;
            border: 2px solid #181818;
            border-radius: 20px;
            padding: 20px 25px;
            box-shadow: 4px 4px 0px #181818;
            box-sizing: border-box;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }
        .card-comic-horizontal .trayecto-info-left {
            min-width: 250px;
            max-width: 250px;
        }
        .card-comic-horizontal h3 {
            font-family: 'Carter One', cursive;
            margin: 5px 0 0 0;
            font-size: 18px;
            color: #181818;
        }
        .card-comic-horizontal p {
            margin: 0;
            font-size: 14px;
            color: #444;
            line-height: 1.5;
            flex: 1;
        }

        .btn-y, .btn-outline {
            font-family: 'Carter One', cursive !important;
            font-size: 15px !important;
        }

        @media (max-width: 768px) {
            .grid-2-nosotros { flex-direction: column; gap: 20px; }
            .card-comic-horizontal { flex-direction: column; align-items: flex-start; gap: 10px; }
            .card-comic-horizontal .trayecto-info-left { min-width: 100%; }
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container">
        <div style="text-align: center; margin-bottom: 40px;">
            <span style="font-weight: bold; color: #24AB58; text-transform: uppercase; font-size: 13px;">Quiénes Somos</span>
            <h2 class="seccion-titulo" style="margin-top: 4px; font-size: 36px;">Escuela de Música "A Todo Ritmo"</h2>
            <p class="sub-titulo" style="font-size: 16px; color: #444; max-width: 700px; margin: 10px auto 0 auto;">Nacimos con una convicción innegociable: la música no es un privilegio de pocos, sino un lenguaje colectivo, transformador y comunitario.</p>
        </div>

        <!-- HISTORIA INSTITUCIONAL -->
        <section style="margin-bottom:60px; padding: 0 20px;">
            <div class="grid-2-nosotros">
                <div>
                    <div style="display: inline-flex; align-items: center; gap: 6px; background-color: rgba(243, 216, 79, 0.3); border: 1px solid #F3D84F; color: #181818; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; margin-bottom: 15px; text-transform: uppercase;">
                        ❤️ Nuestra Historia
                    </div>
                    <h3 style="font-family: 'Carter One', cursive; font-size: 22px; margin: 0 0 15px 0; line-height:1.3;"><?= htmlspecialchars($historia_titulo) ?></h3>
                    <p style="font-size: 15px; margin: 0 0 15px 0; line-height:1.6; text-align: justify; color: #444;">
                        <?= htmlspecialchars($historia_p1) ?>
                    </p>
                    <p style="font-size: 15px; margin: 0 0 20px 0; line-height:1.6; text-align: justify; color: #444;">
                        <?= htmlspecialchars($historia_p2) ?>
                    </p>
                    <div class="stats-box-nosotros">
                        <div class="stat-card"><span>+200</span><small style="font-weight:700; color:#555;">Alumnos Activos</small></div>
                        <div class="stat-card"><span>+60</span><small style="font-weight:700; color:#555;">Instrumentos</small></div>
                        <div class="stat-card"><span>100%</span><small style="font-weight:700; color:#555;">Inclusiva</small></div>
                    </div>
                </div>

                <div style="position: relative; box-sizing: border-box; width: 100%;">
                    <div style="width: 100%; height: 340px; border-radius: 24px; overflow: hidden; border: 4px solid #181818; box-shadow: 4px 4px 0px #181818;">
                        <img src="img/microfono.jpg" alt="Micrófono original de la Escuela de Música" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="position: absolute; bottom: -20px; left: -20px; background-color: #F3D84F; border: 3px solid #181818; border-radius: 16px; padding: 15px; box-shadow: 4px 4px 0px #181818; max-width: 280px; box-sizing: border-box;">
                        <p style="font-family: 'Carter One', cursive; font-size: 13px; color: #181818; margin: 0; line-height: 1.4;">
                            "Aprender un instrumento te da una voz que las palabras no alcanzan."
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- TRAYECTOS PEDAGÓGICOS EN FORMATO HORIZONTAL -->
        <section class="hero" style="background:#FFFDF5; border-color:#181818; text-align:left; color:#181818; padding:45px; box-shadow: 4px 4px 0px #181818; margin-bottom:60px;">
            <div style="text-align: center;">
                <span style="font-weight: bold; color: #24AB58; text-transform: uppercase; font-size: 13px;">Propuesta Pedagógica</span>
                <h2 class="seccion-titulo" style="margin-top: 4px; margin-bottom: 10px;">Trayectos Formativos y Niveles</h2>
                <p style="text-align:center; color:#555; font-size:15px; margin-bottom:35px;">Nuestros programas se adaptan al ritmo de aprendizaje de cada etapa de la vida.</p>
            </div>

            <div class="grid-trayectos-horizontal">
                <div class="card-comic-horizontal">
                    <div class="trayecto-info-left">
                        <span class="trayecto-badge badge-green">7 a 11 Años</span>
                        <h3>Iniciación Musical Infantil</h3>
                    </div>
                    <p>Juego, exploración rítmica, canciones infantiles y primeras notas en violín, teclado o percusión mediante métodos sensoriales y dinámicas de grupo adaptadas.</p>
                </div>

                <div class="card-comic-horizontal">
                    <div class="trayecto-info-left">
                        <span class="trayecto-badge badge-yellow">12 Años en Adelante</span>
                        <h3>Trayecto Joven y Adultos</h3>
                    </div>
                    <p>Formación instrumental intensiva (Guitarra, Batería, Flauta, Canto, Piano). Práctica directa sobre el instrumento con lectura aplicada, repertorio popular, armonía práctica e improvisación progresiva.</p>
                </div>

                <div class="card-comic-horizontal">
                    <div class="trayecto-info-left">
                        <span class="trayecto-badge badge-dark">Abierto a la Comunidad</span>
                        <h3>Ensambles Comunitarios</h3>
                    </div>
                    <p>El espacio de práctica orquestal donde confluyen todos los talleres para montar repertorios conjuntos de rock nacional, cumbia, tango y folklore, preparando muestras abiertas al público.</p>
                </div>
            </div>
        </section>

        <!-- SEDES Y ACCESOS (DINÁMICAS DESDE MYSQL) -->
        <div style="text-align: center;">
            <span style="font-weight: bold; color: #24AB58; text-transform: uppercase; font-size: 13px;">Ubicaciones</span>
            <h2 class="seccion-titulo" style="margin-top: 4px;">Conocé Nuestras Sedes Físicas</h2>
            <p class="sub-titulo">Salas equipadas, insonorizadas y con instrumentos disponibles para estudiar.</p>
        </div>

        <div class="flex-resenas" style="margin-bottom:40px;">
            <?php foreach ($sedes_db as $sede): ?>
                <div class="card-resena" style="width:470px; max-width:470px; min-height:200px;">
                    <div>
                        <h3 style="font-size:20px; color:#181818; margin-bottom:6px;"><?= htmlspecialchars($sede['nombre']) ?></h3>
                        <p style="font-size:13.5px; margin:0 0 10px 0; color:#24AB58; font-weight:700;">📍 <?= htmlspecialchars($sede['direccion']) ?> (<?= htmlspecialchars($sede['barrio']) ?>)</p>
                        <p style="font-size:13px; color:#555;"><strong>Horarios:</strong> <?= htmlspecialchars($sede['horarios_atencion']) ?></p>
                        <p style="font-size:13px; color:#555;"><strong>Teléfono:</strong> <?= htmlspecialchars($sede['telefono']) ?> &nbsp;|&nbsp; <strong>Email:</strong> <?= htmlspecialchars($sede['email']) ?></p>
                    </div>
                    <?php if (!empty($sede['colectivos']) || !empty($sede['subte'])): ?>
                        <div class="tarjeta-datos" style="margin-top:15px; font-size:13px; color:#555; background:#fffdf5; border-top:1px solid #eee; padding-top:10px;">
                            <?php if (!empty($sede['colectivos'])): ?>
                                <div><strong>Líneas de colectivo:</strong> <?= htmlspecialchars($sede['colectivos']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($sede['subte'])): ?>
                                <div style="margin-top:3px;"><strong>Estaciones de Subte:</strong> <?= htmlspecialchars($sede['subte']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cta-final-box" style="margin-top:60px; background-color:#24AB58; border:4px solid #181818; box-shadow:6px 6px 0px #181818;">
            <h2 style="color:#FFFAE6;">¿Querés sumarte a nuestra comunidad?</h2>
            <p style="color:#FFFAE6;">Podés conocer más sobre los profesionales que dictan nuestras clases o registrarte directamente para reservar tu vacante en el ciclo lectivo vigente.</p>
            <div class="flex-center" style="margin-top: 25px;">
                <a href="docentes.php" class="btn btn-outline" style="border-color:#FFFAE6; color:#FFFAE6; background:#181818;">Conocer a los docentes</a>
                <a href="inscripcion.php" class="btn btn-y" style="background-color:#F3D84F; color:#181818; box-shadow: 3px 3px 0px #181818;">Ir a la ficha de inscripción</a>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
