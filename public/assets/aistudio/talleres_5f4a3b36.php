<?php
require_once __DIR__ . '/conexion.php';

// Filtros relacionales por formulario GET para que funcionen al 100%
$buscar            = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$filtroInstrumento = isset($_GET['instrumento']) ? trim($_GET['instrumento']) : '';
$filtroNivel       = isset($_GET['nivel']) ? trim($_GET['nivel']) : '';

try {
    $sql = "SELECT t.*, CONCAT(d.nombre, ' ', d.apellido) AS docente_nombre 
            FROM talleres t
            INNER JOIN docentes d ON t.docente_id = d.id
            WHERE 1=1";

    $params = [];
    if (!empty($buscar)) {
        $sql .= " AND (t.nombre LIKE :buscar OR t.descripcion LIKE :buscar)";
        $params[':buscar'] = '%' . $buscar . '%';
    }
    if (!empty($filtroInstrumento)) {
        $sql .= " AND t.instrumento = :instrumento";
        $params[':instrumento'] = $filtroInstrumento;
    }
    if (!empty($filtroNivel)) {
        $sql .= " AND t.nivel = :nivel";
        $params[':nivel'] = $filtroNivel;
    }

    $sql .= " ORDER BY t.destacado DESC, t.id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $talleres = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $talleres = [];
}

// Array de respaldo oficial si tu base de datos local está vacía
if (empty($talleres)) {
    $talleres_respaldo = [
        [
            'id' => 1, 'nombre' => 'Taller de Guitarra Criolla y Popular', 'instrumento' => 'Guitarra', 'nivel' => 'INICIAL / SIN EXPERIENCIA', 'edad_minima' => 7,
            'descripcion' => 'Aprende tus primeros acordes, rasgueos folclóricos, ritmos de rock y canciones populares desde la primera clase sin necesidad de saber teoría previa.',
            'dia_semana' => 'Martes y Jueves', 'horario' => '17:30 a 19:00 hs', 'docente_nombre' => 'Julián Morales', 'vacantes_disponibles' => 4, 'arancel_mensual' => 14500
        ],
        [
            'id' => 2, 'nombre' => 'Taller de Violín: Primeras Cuerdas', 'instrumento' => 'Violín', 'nivel' => 'INICIAL / SIN EXPERIENCIA', 'edad_minima' => 7,
            'descripcion' => 'Postura corporal relajada, agarre del arco, afinación y melodías sencillas. Clases dinámicas y grupales donde el juego y el oído son protagonistas.',
            'dia_semana' => 'Lunes y Miércoles', 'horario' => '18:00 a 19:30 hs', 'docente_nombre' => 'Valeria Corvalán', 'vacantes_disponibles' => 3, 'arancel_mensual' => 15500
        ],
        [
            'id' => 3, 'nombre' => 'Taller de Batería y Pulso Rítmico', 'instrumento' => 'Batería', 'nivel' => 'INICIAL / SIN EXPERIENCIA', 'edad_minima' => 8,
            'descripcion' => 'Coordinación motriz, rudimentos básicos, ritmos de rock, funk y cumbia. Práctica directa en sets acústicos y pads de práctica insonorizados.',
            'dia_semana' => 'Viernes', 'horario' => '16:00 a 18:00 hs', 'docente_nombre' => 'Facundo Pérez', 'vacantes_disponibles' => 2, 'arancel_mensual' => 16000
        ],
        [
            'id' => 4, 'nombre' => 'Taller de Canto Popular y Expresión Vocal', 'instrumento' => 'Canto', 'nivel' => 'INICIAL / SIN EXPERIENCIA', 'edad_minima' => 10,
            'descripcion' => 'Descubre tu voz natural sin forzar la garganta. Respiración, afinación, soltura escénica y ensamble a varias voces sobre repertorio popular.',
            'dia_semana' => 'Lunes y Jueves', 'horario' => '19:00 a 20:30 hs', 'docente_nombre' => 'Valeria Corvalán', 'vacantes_disponibles' => 5, 'arancel_mensual' => 14000
        ],
        [
            'id' => 5, 'nombre' => 'Gran Ensamble Comunitario A Todo Ritmo', 'instrumento' => 'Ensamble', 'nivel' => 'ENSAMBLE ABIERTO', 'edad_minima' => 10,
            'descripcion' => 'El corazón de la escuela. Guitarras, vientos, teclados, percusión y voces unidas en arreglos orquestales colectivos de rock nacional y folklore.',
            'dia_semana' => 'Sábados', 'horario' => '15:00 a 18:00 hs', 'docente_nombre' => 'Julián Morales', 'vacantes_disponibles' => 8, 'arancel_mensual' => 9500
        ],
        [
            'id' => 6, 'nombre' => 'Taller de Piano y Teclado Creativo', 'instrumento' => 'Piano', 'nivel' => 'INICIAL / SIN EXPERIENCIA', 'edad_minima' => 8,
            'descripcion' => 'Conoce el teclado, lectura fluida en clave de sol y fa sin presiones, armonía de canciones y acompañamiento.',
            'dia_semana' => 'Miércoles y Viernes', 'horario' => '18:00 a 20:00 hs', 'docente_nombre' => 'Lucía Herrera', 'vacantes_disponibles' => 5, 'arancel_mensual' => 14500
        ],
        [
            'id' => 7, 'nombre' => 'Taller de Flauta Traversa y Melodías', 'instrumento' => 'Flauta', 'nivel' => 'INICIAL / SIN EXPERIENCIA', 'edad_minima' => 9,
            'descripcion' => 'Emisión del sonido, respiración diafragmática, digitación ágil y repertorio de música de películas y folklore.',
            'dia_semana' => 'Sábados', 'horario' => '10:30 a 12:30 hs', 'docente_nombre' => 'Mariano Díaz', 'vacantes_disponibles' => 4, 'arancel_mensual' => 15500
        ],
        [
            'id' => 8, 'nombre' => 'Taller de Teclado y Sintetizadores', 'instrumento' => 'Teclado', 'nivel' => 'INTERMEDIO', 'edad_minima' => 12,
            'descripcion' => 'Sonidos modernos, síntesis analógica básica, arreglos pop y pads ambientales para tocar en bandas.',
            'dia_semana' => 'Jueves', 'horario' => '19:00 a 20:30 hs', 'docente_nombre' => 'Lucía Herrera', 'vacantes_disponibles' => 3, 'arancel_mensual' => 16000
        ]
    ];

    // Lógica de filtrado dinámico sobre el array local
    $talleres = array_filter($talleres_respaldo, function($t) use ($buscar, $filtroInstrumento, $filtroNivel) {
        $cumpleBusc = empty($buscar) || stripos($t['nombre'], $buscar) !== false || stripos($t['descripcion'], $buscar) !== false;
        $cumpleIns  = empty($filtroInstrumento) || (stripos($t['instrumento'], $filtroInstrumento) !== false);
        $cumpleNiv  = empty($filtroNivel) || stripos($t['nivel'], $filtroNivel) !== false || (stripos($t['nivel'], 'INICIAL') !== false && $filtroNivel == 'Inicial / Sin experiencia');
        return $cumpleBusc && $cumpleIns && $cumpleNiv;
    });
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talleres - Escuela de Música A Todo Ritmo</title>
    
    <!-- 1. Primero cargamos tus estilos CSS de la página de talleres -->
    <style>
        body * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Mukta', sans-serif; }
        body { background-color: #FFFAE6; color: #181818; }
        nav a { color: #FFFAE6; text-decoration: none; font-weight: 600; font-size: 14px; margin-left: 10px; padding: 8px 14px; border-radius: 10px; }
        nav a:hover, nav a.active { color: #181818; background-color: #F3D84F; font-weight: 700; box-shadow: 2px 2px 0px #181818; }
        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        .filtros-box { background-color: #FFFDF5; border: 2px solid #181818; border-radius: 24px; padding: 25px; margin-bottom: 40px; box-shadow: 4px 4px 0px #181818; }
        .filtros-form { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
        .search-input { flex: 1; min-width: 260px; padding: 10px 15px; border: 2px solid #181818; border-radius: 12px; font-size: 14px; }
        .filtros-form select { padding: 10px 15px; border: 2px solid #181818; border-radius: 12px; background: white; font-weight: 600; font-size: 14px; }
        .btn-filtrar { background-color: #F3D84F; color: #181818; font-family: 'Carter One', cursive; border: 2px solid #181818; padding: 10px 20px; font-size: 14px; border-radius: 12px; cursor: pointer; box-shadow: 2px 2px 0px #181818; }
        .grid-talleres { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 50px; }
        .card-taller { background: #FFFDF5; border: 2px solid #181818; border-radius: 24px; overflow: hidden; box-shadow: 4px 4px 0px #181818; display: flex; flex-direction: column; justify-content: space-between; }
        .img-container { height: 190px; position: relative; border-bottom: 2px solid #181818; background: #181818; }
        .img-container img { width: 100%; height: 100%; object-fit: cover; }
        .badge-cat { position: absolute; top: 12px; left: 12px; background: #181818; color: #FFFDF5; padding: 4px 12px; border-radius: 20px; font-family: 'Carter One', cursive !important; font-size: 11px; }
        .badge-vacantes { position: absolute; top: 12px; right: 12px; background: #24AB58; color: #FFF; padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 11px; border: 1px solid #181818; }
        .card-body { padding: 20px; flex-grow: 1; }
        .meta-sup { font-size: 11px; font-weight: 800; color: #777; letter-spacing: 0.5px; margin-bottom: 8px; text-transform: uppercase; }
        .card-body h3 { font-family: 'Carter One', cursive !important; font-size: 19px; color: #181818; margin-bottom: 10px; line-height: 1.3; }
        .card-body p { font-size: 13.5px; color: #444; line-height: 1.5; margin-bottom: 18px; }
        .info-line { font-size: 13.5px; color: #181818; font-weight: 600; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
        .card-footer-price { border-top: 2px solid #181818; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; background: #FFFDF5; }
        .price-box span { font-size: 11px; color: #555; display: block; font-weight: 700; text-transform: uppercase; }
        .price-box strong { font-family: 'Carter One', cursive !important; font-size: 22px; color: #181818; }
        .btn-det { background: white; border: 2px solid #181818; padding: 8px 16px; border-radius: 12px; font-weight: 700; font-size: 13px; text-decoration: none; color: #181818; box-shadow: 2px 2px 0px #181818; }
        .btn-ins { background: #24AB58; color: #FFF; border: 2px solid #181818; padding: 8px 16px; border-radius: 12px; font-weight: 700; font-size: 13px; text-decoration: none; box-shadow: 2px 2px 0px #181818; }
        @media (max-width: 992px) { .grid-talleres { grid-template-columns: repeat(2, 1fr); } .footer-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 650px) { .grid-talleres { grid-template-columns: 1fr; } .footer-grid { grid-template-columns: 1fr; } }
        /* --- CONGELAR TAMAÑOS INTERNOS DEL HEADER (Evita diferencias en Talleres) --- */


    </style>

    <!-- 2. Al final de todo cargamos el archivo CSS general del Header unificado para que tenga la última palabra en el diseño -->
    <link rel="stylesheet" href="index.css">
</head>
<body>


  <!-- HEADER -->
<?php include 'header.php'; ?>



    <main class="container" style="margin-top: 0 !important; padding-top: 20px !important;">
        <!-- Buscá tu contenedor y forzá el margen superior en 0 así: -->
        <div class="container" style="margin-top: 0 !important; padding-top: 20px !important;">

            <p style="font-size: 12px; font-weight: bold; color: #24AB58; text-transform: uppercase; letter-spacing: 1px;">PROPUESTA ACADÉMICA 2026</p>
            <h2 style="font-family: 'Carter One', cursive; font-size: 36px; margin-top: 4px;">Nuestros Talleres Instrumentales</h2>
            <p style="font-size: 15px; color: #555; margin-top: 4px;">Clases grupales reducidas, método práctico desde el primer día y acceso al Banco Solidario.</p>
        </div>

        <section class="filtros-box" style="margin-top: 0 !important;">
            <form method="GET" action="talleres.php" class="filtros-form">
                <input type="text" class="search-input" name="buscar" placeholder="Buscar por instrumento, tema o palabra..." value="<?= htmlspecialchars($buscar) ?>">
                
                <select name="instrumento">
                    <option value="">Todos los instrumentos</option>
                    <option value="Guitarra" <?= $filtroInstrumento=='Guitarra'?'selected':'' ?>>Guitarra</option>
                    <option value="Violín" <?= $filtroInstrumento=='Violín'?'selected':'' ?>>Violín</option>
                    <option value="Batería" <?= $filtroInstrumento=='Batería'?'selected':'' ?>>Batería</option>
                    <option value="Piano" <?= $filtroInstrumento=='Piano'?'selected':'' ?>>Piano</option>
                    <option value="Flauta" <?= $filtroInstrumento=='Flauta'?'selected':'' ?>>Flauta</option>
                    <option value="Teclado" <?= $filtroInstrumento=='Teclado'?'selected':'' ?>>Teclado</option>
                    <option value="Canto" <?= $filtroInstrumento=='Canto'?'selected':'' ?>>Canto Popular</option>
                    <option value="Ensamble" <?= $filtroInstrumento=='Ensamble'?'selected':'' ?>>Ensamble</option>
                </select>

                <select name="nivel">
                    <option value="">Todos los niveles</option>
                    <option value="Inicial / Sin experiencia" <?= ($filtroNivel === 'Inicial / Sin experiencia') ? 'selected' : '' ?>>Inicial</option>
                    <option value="Intermedio" <?= ($filtroNivel === 'Intermedio') ? 'selected' : '' ?>>Intermedio</option>
                    <option value="Avanzado" <?= ($filtroNivel === 'Avanzado') ? 'selected' : '' ?>>Avanzado</option>
                </select>

                <button type="submit" class="btn-filtrar">Filtrar</button>
                <?php if (!empty($buscar) || !empty($filtroInstrumento) || !empty($filtroNivel)): ?>
                    <a href="talleres.php" style="color:#24AB58; font-weight:700; text-decoration:underline; font-size:14px; margin-left:5px;">Limpiar</a>
                <?php endif; ?>
            </form>
        </section>
        <div class="grid-talleres">
            <?php if (!empty($talleres)): ?>
                <?php foreach ($talleres as $taller): 
                    $img_src = "img/guitarra.jpg";
                    if (stripos($taller['instrumento'], 'viol') !== false) $img_src = "img/violin.jpg";
                    if (stripos($taller['instrumento'], 'bat') !== false) $img_src = "img/bateria.jpg";
                    
                    if (stripos($taller['instrumento'], 'canto') !== false || $taller['id'] == 4) {
                        $img_src = "img/microfono.jpg";
                    }
                    if (stripos($taller['instrumento'], 'ensam') !== false || $taller['id'] == 5) {
                        $img_src = "img/ensamble.jpg";
                    }
                    if (stripos($taller['instrumento'], 'piano') !== false || $taller['id'] == 6) {
                        $img_src = "img/piano.jpg";
                    }
                    if (stripos($taller['instrumento'], 'flaut') !== false || $taller['id'] == 7) {
                        $img_src = "img/flauta.jpg";
                    }
                    if (stripos($taller['instrumento'], 'tecla') !== false || $taller['id'] == 8) {
                        $img_src = "img/teclado.jpg";
                    }
                ?>
                    <article class="card-taller">
                        <div class="img-container">
                            <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($taller['nombre']) ?>">
                            <span class="badge-cat"><?= htmlspecialchars($taller['instrumento']) ?></span>
                            <span class="badge-vacantes"><?= (int)$taller['vacantes_disponibles'] ?> vacantes libres</span>
                        </div>
                        
                        <div class="card-body">
                            <div class="meta-sup">NIVEL: <?= htmlspecialchars($taller['nivel']) ?> • EDAD: +<?= (int)$taller['edad_minima'] ?> AÑOS</div>
                            <h3><?= htmlspecialchars($taller['nombre']) ?></h3>
                            <p><?= htmlspecialchars($taller['descripcion']) ?></p>
                            
                            <div class="info-line">📅 <?= htmlspecialchars($taller['dia_semana']) ?></div>
                            <div class="info-line">🕒 <?= htmlspecialchars($taller['horario']) ?> (Presencial)</div>
                            <div class="info-line">👤 Prof. <?= htmlspecialchars($taller['docente_nombre']) ?></div>
                            <div class="info-line">🎵 Préstamo de Instrumento: <span style="color:#24AB58; font-weight:700;">Disponible</span></div>
                        </div>
                        
                        <div class="card-footer-price">
                            <div class="price-box">
                                <span>Arancel Mensual</span>
                                <strong>$<?= number_format($taller['arancel_mensual'], 0, ',', '.') ?></strong>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <a href="#" class="btn-det">Detalles</a>
                                <a href="inscripcion.php?taller_id=<?= $taller['id'] ?>" class="btn-ins">Inscribirme →</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: span 3; text-align: center; padding: 40px; background: #FFFDF5; border: 2px dashed #181818; border-radius: 20px;">
                    <h3 style="font-family:'Carter One', cursive;">No se encontraron talleres con los filtros seleccionados</h3>
                    <p style="margin-top: 10px;"><a href="talleres.php" class="btn-filtrar" style="text-decoration:none; display:inline-block;">Ver todos los talleres</a></p>
                </div>
            <?php endif; ?>
        </div>
    </main>


<?php include 'footer.php'; ?>
    
</body>
</html>
