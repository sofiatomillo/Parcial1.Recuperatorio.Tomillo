<?php
// Incluimos tu archivo de conexión existente
require_once __DIR__ . '/conexion.php';

try {
    // Consultamos los eventos ordenados por fecha de forma ascendente
    $sql_eventos = "SELECT * FROM eventos ORDER BY fecha ASC";
    $stmt_eventos = $pdo->query($sql_eventos);
    $lista_eventos = $stmt_eventos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si la conexión falla, creamos un array vacío de contingencia para que la página no tire error
    $lista_eventos = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escuela de Música A Todo Ritmo</title>
    <link href="https://googleapis.com" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>

<body>

<!-- HEADER -->
<?php include 'header.php'; ?>

    <div class="container">

        <!-- ================= HERO / BANNER PRINCIPAL ================= -->
        <section class="hero pattern-dots-yellow">
            <div style="display:inline-flex; align-items:center; gap:8px; background-color:rgba(36, 171, 88, 0.3); border:1px solid #24AB58; color:#F3D84F; padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700; margin-bottom:20px; text-transform:uppercase;">
                ✨ Inscripciones Ciclo Lectivo 2026
            </div>
            <h2>Sentí la música,<br><span style="color: #F3D84F;">descubrí tu ritmo.</span></h2>
            <p>Una escuela de música inclusiva en el corazón del barrio. Clases de guitarra, violín, flauta, batería, piano y canto para todas las edades. <span style="color:#F3D84F; font-weight:700;">¿No tenés instrumento? Te lo prestamos nosotros.</span></p>
            <div class="flex-center">
                <a href="inscripcion.php" class="btn btn-y" style="text-decoration:none;">¡Quiero Inscribirme!</a>
                <a href="talleres.php" class="btn btn-outline" style="text-decoration:none;">Explorar Talleres 2026</a>
            </div>
        </section>
        
        <!-- SECCIÓN FILOSOFÍA ORIGINAL -->
        <span style="text-align:center; display:block; font-size:12px; font-weight:bold; color:#24AB58; text-transform:uppercase; tracking-wide:1px;">Nuestra Filosofía</span>
        <h2 class="seccion-titulo" style="margin-top:4px;">¿Por qué estudiar en "A Todo Ritmo"?</h2>
        <p class="sub-titulo">Rompemos las barreras de los conservatorios tradicionales para que el arte sea un espacio de encuentro y disfrute.</p>
        
        <div class="grid-4-horizontal">
            <div class="card-comic"><div class="icon-box">🤝</div><h3>Banco de Instrumentos</h3><p>No dejes de aprender por no poder comprar un instrumento caro. La escuela cuenta con stock para uso gratuito.</p></div>
            <div class="card-comic"><div class="icon-box yellow">✓</div><h3>Vacantes Reales</h3><p>Sin audiciones elitistas ni filtros frustrantes. Todos tenemos derecho a la música; organizamos grupos reducidos por edades.</p></div>
            <div class="card-comic"><div class="icon-box">👥</div><h3>Docentes de Vocación</h3><p>Profesores graduados de conservatorios nacionales con una pedagogía cercana, alegre y respetuosa de cada tiempo.</p></div>
            <div class="card-comic"><div class="icon-box yellow">🎵</div><h3>Ensambles y Muestras</h3><p>No tocás solo: desde los primeros meses participás en ensambles colectivos y muestras en vivo en el escenario.</p></div>
        </div>

        <!-- TALLERES DESTACADOS -->
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:25px; margin-top: 50px;">
            <div>
                <span style="font-size:12px; font-weight:bold; color:#24AB58; text-transform:uppercase;">Nuestra Oferta Educativa</span>
                <h3 style="font-family:'Carter One', cursive; font-size:24px; margin:4px 0 0 0;">Talleres Destacados</h3>
                <p style="margin:4px 0 0 0; font-size:14px; color:#555;">Conocé las disciplinas más elegidas por nuestros alumnos.</p>
            </div>
            <a href="talleres.php" style="color:#24AB58; font-weight:bold; text-decoration:underline; font-size:14px;">Ver todos los talleres →</a>
        </div>

        <div class="grid-4-horizontal">
            <!-- Tarjeta 1: Guitarra -->
            <div class="taller-mini-card">
                <div class="taller-img-container"><img src="img/guitarra.jpg" alt="Guitarra"></div>
                <div class="taller-mini-body"><div class="taller-badge">Guitarra</div><h4>Taller de Guitarra Criolla</h4><p>Acordes, rasgueos folclóricos y ritmos de rock desde el primer día en clase grupal.</p></div>
                <div class="taller-mini-footer"><span style="color:#24AB58; font-weight:bold;">4 libres</span><strong>$14.500</strong></div>
            </div>
            <!-- Tarjeta 2: Violín -->
            <div class="taller-mini-card">
                <div class="taller-img-container"><img src="img/violin.jpg" alt="Violín"></div>
                <div class="taller-mini-body"><div class="taller-badge">Violín</div><h4>Taller de Violín</h4><p>Postura corporal relajada, arco, afinación y melodías sencillas adaptadas.</p></div>
                <div class="taller-mini-footer"><span style="color:#24AB58; font-weight:bold;">3 libres</span><strong>$15.500</strong></div>
            </div>
            <!-- Tarjeta 3: Batería -->
            <div class="taller-mini-card">
                <div class="taller-img-container"><img src="img/bateria.jpg" alt="Batería"></div>
                <div class="taller-mini-body"><div class="taller-badge">Batería</div><h4>Taller de Batería</h4><p>Coordinación motriz, rock, funk y cumbia en sets acústicos reales.</p></div>
                <div class="taller-mini-footer"><span style="color:#24AB58; font-weight:bold;">2 libres</span><strong>$16.000</strong></div>
            </div>
            <!-- Tarjeta 4: Piano -->
            <div class="taller-mini-card">
                <div class="taller-img-container"><img src="img/piano.jpg" alt="Piano"></div>
                <div class="taller-mini-body"><div class="taller-badge">Piano</div><h4>Taller de Piano</h4><p>Lectura fluida en clave de sol y fa, armonía práctica y acompañamiento.</p></div>
                <div class="taller-mini-footer"><span style="color:#24AB58; font-weight:bold;">5 libres</span><strong>$15.500</strong></div>
            </div>
        </div>
        <!-- PRÓXIMO EVENTO -->
        <div class="evento-banner-destacado">
            <div class="evento-info-side">
                <div style="background:#F3D84F; color:#181818; display:inline-block; padding:3px 10px; border-radius:6px; font-size:11px; font-weight:bold; text-transform:uppercase; margin-bottom:10px;">📅 Próximo Evento Mensual</div>
                <h3>Gran Muestra de Primavera: Todos al Escenario</h3>
                <p style="font-size:14px; color:#ccc; margin:0; line-height:1.5;">Presentación en vivo de los ensambles de iniciales y avanzados. Los alumnos de guitarra, batería, violín, flauta y coro mostrarán los proyectos trabajados.</p>
                <div class="event-chips"><div class="chip">Fecha: 17 de Octubre de 2026</div><div class="chip">Hora: 18:30 hs</div><div class="chip">Lugar: Auditorio Sala Mayor - San Telmo</div></div>
            </div>
            <div class="evento-ticket-side">
                <span style="font-size:11px; color:#aaa; font-weight:bold; text-transform:uppercase; display:block; margin-bottom:5px;">Valor del Bono</span>
                <strong style="font-family:'Carter One', cursive; font-size:24px; color:#F3D84F; display:block; margin-bottom:5px;">$1.500</strong>
                <p style="font-size:10px; color:#999; margin:0 0 15px 0;">Destinado al banco solidario.</p>
                <a href="eventos.php" class="btn btn-y" style="text-decoration:none; font-size:13px; padding:10px; display:block;">Sacar Entradas Online</a>
            </div>
        </div>

        <!-- TESTIMONIOS -->
        <div style="text-align: center;">
            <span style="font-size:12px; font-weight:bold; color:#24AB58; text-transform:uppercase; tracking-wide:1px;">Testimonios Reales</span>
            <h2 class="seccion-titulo" style="margin-top:4px;">La Música Transforma el Barrio</h2>
            <p class="sub-titulo">Opiniones de nuestros alumnos, familias y egresados de comisiones regulares.</p>
        </div>
        
        <div class="flex-resenas">
            <div class="card-resena">
                <div><div class="stars">★★★★★</div><p style="font-size:12.5px; font-style:italic; color:#333; line-height:1.5; margin:0;">"Empecé de cero a los 42 años sin tener guitarra. Julián y la escuela me prestaron una y en 3 meses ya estaba tocando."</p></div>
                <div><hr style="border:0; border-top:1px solid #eee; margin:12px 0 8px 0;"><strong style="font-size:13px;">Esteban Méndez</strong><br><small style="color:#24AB58; font-size:11px;">Alumno — Guitarra</small></div>
            </div>
            <div class="card-resena">
                <div><div class="stars">★★★★★</div><p style="font-size:12.5px; font-style:italic; color:#333; line-height:1.5; margin:0;">"En otras escuelas nos pedían comprar un violín carísimo antes de empezar. Acá le adaptaron un violín 1/2 con paciencia admirable."</p></div>
                <div><hr style="border:0; border-top:1px solid #eee; margin:12px 0 8px 0;"><strong style="font-size:13px;">Marcela V.</strong><br><small style="color:#24AB58; font-size:11px;">Familiar — Violín</small></div>
            </div>
            <div class="card-resena">
                <div><div class="stars">★★★★★</div><p style="font-size:12.5px; font-style:italic; color:#333; line-height:1.5; margin:0;">"La cuota es accesible y no sentís esa competencia de otros lados. Acá aprendés a escuchar al de al lado y disfrutar en grupo."</p></div>
                <div><hr style="border:0; border-top:1px solid #eee; margin:12px 0 8px 0;"><strong style="font-size:13px;">Lucas Benítez</strong><br><small style="color:#24AB58; font-size:11px;">Egresado — Batería</small></div>
            </div>
        </div>

        <div class="cta-final-box pattern-dots-dark">
            <h2>¿Listo para empezar tu viaje musical?</h2>
            <p>Completa la inscripción online en 2 minutos. Si no tenés instrumento, marcá la opción de préstamo solidario en la ficha y te lo reservamos para tu primera clase.</p>
            <a href="inscripcion.php" class="btn" style="background:#181818; color:#F3D84F; font-family:'Carter One', cursive; text-decoration:none; display:block; margin:0 auto; width:fit-content;">Completar Ficha de Inscripción 2026</a>
        </div>

    </div> <!-- Fin del .container -->

    <?php include 'footer.php'; ?>
</body>
</html>
