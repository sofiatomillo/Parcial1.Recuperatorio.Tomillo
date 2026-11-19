<?php
/**
 * Proyecto: Escuela de Música "A Todo Ritmo"
 * Archivo: nosotros.php
 * Sección: Nosotros - Historia, Sedes Físicas y Trayectos Formativos
 * Requisitos: HTML5 Clásico, PHP Nativo con PDO, Contenedor max-width 1000px, Tarjetas Horizontales Compactas.
 */

// Inclusión de la conexión a la base de datos (opcional si se consultan datos de sedes en BD o fallback)
require_once __DIR__ . '/conexion.php';

// Intentar consultar sedes desde MySQL, o fallback si la BD aún no está poblada
try {
    $stmtSedes = $pdo->query("SELECT * FROM sedes ORDER BY id ASC");
    $sedesBD = $stmtSedes->fetchAll();
} catch (Exception $e) {
    $sedesBD = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros | Escuela de Música "A Todo Ritmo"</title>
    <style>
        /* ==========================================================================
           ESTILOS GENERALES Y LAYOUT CENTRADO (MAX-WIDTH 1000PX)
           ========================================================================== */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }

        .contenedor {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Encabezado y Navegación */
        header {
            background-color: #ffffff;
            border-bottom: 2px solid #e2e8f0;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-box h1 {
            font-size: 1.4rem;
            color: #b93b2a;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .logo-box span {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
            display: block;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 15px;
        }

        nav a {
            text-decoration: none;
            color: #334155;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 6px 12px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        nav a:hover, nav a.active {
            color: #b93b2a;
            background-color: #fef2f2;
        }

        /* Banner Hero de Sección */
        .hero-seccion {
            background: linear-gradient(135deg, #7f1d1d, #b93b2a);
            color: #ffffff;
            padding: 40px 20px;
            text-align: center;
            border-radius: 8px;
            margin: 30px auto 35px auto;
        }

        .hero-seccion h2 {
            font-size: 2rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .hero-seccion p {
            font-size: 1.05rem;
            max-width: 750px;
            margin: 0 auto;
            opacity: 0.95;
        }

        /* Bloques de Contenido */
        .seccion-bloque {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .titulo-bloque {
            font-size: 1.35rem;
            color: #0f172a;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid #b93b2a;
            display: inline-block;
        }

        /* Texto Institucional e Historia */
        .historia-texto p {
            margin-bottom: 15px;
            color: #334155;
            font-size: 0.98rem;
            text-align: justify;
        }

        .valores-grid {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .valor-item {
            flex: 1;
            background-color: #fdfbf7;
            border: 1px solid #fed7aa;
            border-left: 4px solid #ea580c;
            padding: 15px;
            border-radius: 4px;
        }

        .valor-item h4 {
            color: #9a3412;
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .valor-item p {
            font-size: 0.88rem;
            color: #431407;
            margin-bottom: 0;
        }

        /* ==========================================================================
           TARJETAS HORIZONTALES COMPACTAS (SEDÉS Y TRAYECTOS)
           ========================================================================== */
        .tarjetas-horizontales {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .tarjeta-horizontal {
            display: flex;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .tarjeta-horizontal:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            border-color: #cbd5e1;
        }

        /* Columna Izquierda Destacada */
        .tarjeta-lateral-badge {
            width: 140px;
            min-width: 140px;
            background: #1e293b;
            color: #ffffff;
            padding: 20px 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .tarjeta-lateral-badge.sede-telmo {
            background: #991b1b;
        }

        .tarjeta-lateral-badge.sede-almagro {
            background: #0f766e;
        }

        .tarjeta-lateral-badge.edad-ninos {
            background: #b45309;
        }

        .tarjeta-lateral-badge.edad-jovenes {
            background: #1d4ed8;
        }

        .tarjeta-lateral-badge.edad-ensambles {
            background: #475569;
        }

        .tarjeta-lateral-badge .icono {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .tarjeta-lateral-badge h3 {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .tarjeta-lateral-badge span {
            font-size: 0.75rem;
            opacity: 0.9;
            margin-top: 4px;
        }

        /* Cuerpo de la Tarjeta */
        .tarjeta-cuerpo {
            padding: 18px 22px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .tarjeta-cuerpo h3 {
            font-size: 1.15rem;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .tarjeta-cuerpo p {
            font-size: 0.92rem;
            color: #475569;
            margin-bottom: 10px;
        }

        .tarjeta-datos {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            background-color: #f8fafc;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 0.85rem;
            border: 1px solid #f1f5f9;
        }

        .tarjeta-datos-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #334155;
        }

        .tarjeta-datos-item strong {
            color: #0f172a;
        }

        .transporte-tag {
            background: #e2e8f0;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 0.8rem;
            color: #1e293b;
            font-weight: 600;
            margin-right: 4px;
        }

        /* Footer Clásico */
        footer {
            background-color: #0f172a;
            color: #94a3b8;
            padding: 30px 0;
            margin-top: 50px;
            font-size: 0.88rem;
            border-top: 3px solid #b93b2a;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .footer-content a {
            color: #cbd5e1;
            text-decoration: none;
        }

        .footer-content a:hover {
            color: #ffffff;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- ENCABEZADO Y NAVEGACIÓN -->
    <header>
        <div class="contenedor header-content">
            <div class="logo-box">
                <h1>A TODO RITMO</h1>
                <span>Escuela Popular de Música Comunitaria</span>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="nosotros.php" class="active">Nosotros</a></li>
                    <li><a href="talleres.php">Talleres</a></li>
                    <li><a href="docentes.php">Docentes</a></li>
                    <li><a href="inscripcion.php">Inscripción</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL (MAX-WIDTH: 1000PX) -->
    <main class="contenedor">

        <!-- Banner de Encabezado -->
        <section class="hero-seccion">
            <h2>Nuestra Escuela y Trayectoria</h2>
            <p>Construyendo puentes culturales, educación musical inclusiva y espacios de encuentro comunitario a través del arte sonoro desde 2011.</p>
        </section>

        <!-- 1. HISTORIA INSTITUCIONAL -->
        <section class="seccion-bloque">
            <h2 class="titulo-bloque">Historia Institucional</h2>
            <div class="historia-texto">
                <p>
                    <strong>A Todo Ritmo</strong> nació en el año 2011 por iniciativa de un colectivo de músicos, pedagogos y vecinos comprometidos de la Ciudad de Buenos Aires. Nuestro propósito fundacional fue derribar la creencia de que el estudio de un instrumento musical es un privilegio reservado para unos pocos: sostenemos que la música es un lenguaje vivo, un derecho cultural y una poderosa herramienta de transformación social.
                </p>
                <p>
                    Comenzamos con un pequeño ensamble acústico en una casona de San Telmo prestando guitarras y violines recuperados. Con el correr de los años, el proyecto creció exponencialmente gracias al boca en boca y a la convicción de nuestro equipo docente, logrando abrir nuestra segunda sede en el emblemático barrio de Almagro.
                </p>
                <p>
                    Hoy en día, más de 450 estudiantes activos de todas las edades participan semana a semana en talleres de formación inicial, técnica instrumental, ensambles orquestales de jazz, tango, rock y música latinoamericana, complementados por muestras públicas y conciertos barriales mensuales con acceso libre.
                </p>
            </div>

            <!-- Valores / Pilares -->
            <div class="valores-grid">
                <div class="valor-item">
                    <h4>Sin Exámenes Excluyentes</h4>
                    <p>Enseñanza basada en el disfrute, la escucha y el pulso colectivo, sin audiciones de ingreso.</p>
                </div>
                <div class="valor-item">
                    <h4>Préstamo Gratuito de Instrumentos</h4>
                    <p>Contamos con bancos de instrumentos para que la falta de equipamiento propio no sea un obstáculo.</p>
                </div>
                <div class="valor-item">
                    <h4>Práctica en Ensamble Real</h4>
                    <p>Desde las primeras semanas, cada alumno vivencia el ensamble con compañeros y otros instrumentos.</p>
                </div>
            </div>
        </section>

        <!-- 2. SEDES FÍSICAS Y CÓMO LLEGAR -->
        <section class="seccion-bloque">
            <h2 class="titulo-bloque">Nuestras Sedes Físicas y Accesos</h2>
            <p style="margin-bottom: 20px; color: #64748b; font-size: 0.95rem;">
                Espacios especialmente acustizados, con aulas equipadas con amplificadores, pianos y salas de ensayo para ensambles.
            </p>

            <div class="tarjetas-horizontales">
                <!-- Sede San Telmo -->
                <article class="tarjeta-horizontal">
                    <div class="tarjeta-lateral-badge sede-telmo">
                        <div class="icono">🏛️</div>
                        <h3>San Telmo</h3>
                        <span>Sede Central</span>
                    </div>
                    <div class="tarjeta-cuerpo">
                        <h3>Sede Central - San Telmo</h3>
                        <p><strong>Dirección:</strong> Defensa 842, San Telmo (CABA) &nbsp;|&nbsp; <strong>Teléfono:</strong> (011) 4361-9820 &nbsp;|&nbsp; <strong>Email:</strong> santelmo@atodoritmo.edu.ar</p>
                        <p><strong>Horarios de Atención y Clases:</strong> Lunes a Viernes de 14:00 a 21:00 hs | Sábados de 09:30 a 19:00 hs.</p>
                        <div class="tarjeta-datos">
                            <div class="tarjeta-datos-item">
                                <strong>Colectivos:</strong>
                                <span>
                                    <span class="transporte-tag">24</span>
                                    <span class="transporte-tag">29</span>
                                    <span class="transporte-tag">64</span>
                                    <span class="transporte-tag">86</span>
                                    <span class="transporte-tag">152</span>
                                </span>
                            </div>
                            <div class="tarjeta-datos-item">
                                <strong>Subtes:</strong>
                                <span>Línea C (Estación San Juan) &nbsp;•&nbsp; Línea E (Estación Bolívar)</span>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Sede Almagro -->
                <article class="tarjeta-horizontal">
                    <div class="tarjeta-lateral-badge sede-almagro">
                        <div class="icono">🎶</div>
                        <h3>Almagro</h3>
                        <span>Anexo Comunitario</span>
                    </div>
                    <div class="tarjeta-cuerpo">
                        <h3>Sede Anexo Comunitario - Almagro</h3>
                        <p><strong>Dirección:</strong> Av. Corrientes 4120, Almagro (CABA) &nbsp;|&nbsp; <strong>Teléfono:</strong> (011) 4862-5510 &nbsp;|&nbsp; <strong>Email:</strong> almagro@atodoritmo.edu.ar</p>
                        <p><strong>Horarios de Atención y Clases:</strong> Lunes a Viernes de 15:00 a 20:30 hs | Sábados de 10:00 a 16:30 hs.</p>
                        <div class="tarjeta-datos">
                            <div class="tarjeta-datos-item">
                                <strong>Colectivos:</strong>
                                <span>
                                    <span class="transporte-tag">19</span>
                                    <span class="transporte-tag">26</span>
                                    <span class="transporte-tag">71</span>
                                    <span class="transporte-tag">92</span>
                                    <span class="transporte-tag">127</span>
                                    <span class="transporte-tag">168</span>
                                </span>
                            </div>
                            <div class="tarjeta-datos-item">
                                <strong>Subtes:</strong>
                                <span>Línea B (Estación Medrano a 50 metros)</span>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <!-- 3. TRAYECTOS FORMATIVOS POR EDADES -->
        <section class="seccion-bloque">
            <h2 class="titulo-bloque">Trayectos Formativos por Edades</h2>
            <p style="margin-bottom: 20px; color: #64748b; font-size: 0.95rem;">
                Diseñamos planes pedagógicos adaptados al desarrollo evolutivo, intereses musicales y ritmo de aprendizaje de cada grupo etario.
            </p>

            <div class="tarjetas-horizontales">
                <!-- Trayecto 1: Niñeces (7 a 11 años) -->
                <article class="tarjeta-horizontal">
                    <div class="tarjeta-lateral-badge edad-ninos">
                        <div class="icono">🌱</div>
                        <h3>7 a 11</h3>
                        <span>Años</span>
                    </div>
                    <div class="tarjeta-cuerpo">
                        <h3>Iniciación Musical y Primeras Cuerdas (7 a 11 Años)</h3>
                        <p>
                            Enfoque lúdico vivencial donde los niños exploran el lenguaje sonoro a través del cuerpo, la rítmica y la experimentación con instrumentos reales a escala (violines 1/2 y 3/4, guitarras infantiles y pequeña percusión). Se potencia la capacidad auditiva, el pulso y la expresión sin la presión de la lectura académica abstracta previa.
                        </p>
                        <div class="tarjeta-datos">
                            <div class="tarjeta-datos-item">
                                <strong>Metodología:</strong> Juegos sonoros, canto colectivo y método Suzuki adaptado.
                            </div>
                            <div class="tarjeta-datos-item">
                                <strong>Instrumentos iniciales:</strong> Violín, Guitarra Criolla, Teclado, Batería Infantil.
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Trayecto 2: Adolescentes y Adultos (12 en adelante) -->
                <article class="tarjeta-horizontal">
                    <div class="tarjeta-lateral-badge edad-jovenes">
                        <div class="icono">🎸</div>
                        <h3>12+</h3>
                        <span>En Adelante</span>
                    </div>
                    <div class="tarjeta-cuerpo">
                        <h3>Desarrollo Técnico e Instrumental (12 Años en Adelante y Adultos)</h3>
                        <p>
                            Diseñado tanto para jóvenes que desean iniciar su camino en la música como para adultos que siempre soñaron con tocar un instrumento. Se trabaja sobre repertorio popular de interés directo del alumno (rock nacional, tango, jazz, folklore o pop), combinando técnica postural, armonía práctica funcional y soltura interpretativa.
                        </p>
                        <div class="tarjeta-datos">
                            <div class="tarjeta-datos-item">
                                <strong>Niveles:</strong> Inicial (cero experiencia), Intermedio y Avanzado.
                            </div>
                            <div class="tarjeta-datos-item">
                                <strong>Enfoque:</strong> Cancionero real, acordes, cifrado americano y audio-percepción.
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Trayecto 3: Ensambles Abiertos -->
                <article class="tarjeta-horizontal">
                    <div class="tarjeta-lateral-badge edad-ensambles">
                        <div class="icono">🥁</div>
                        <h3>Ensambles</h3>
                        <span>Abiertos</span>
                    </div>
                    <div class="tarjeta-cuerpo">
                        <h3>Ensambles de Práctica Compartida y Agrupaciones</h3>
                        <p>
                            El corazón de nuestra propuesta comunitaria. Espacio horizontal donde se cruzan estudiantes de diferentes talleres e instrumentos para armar bandas de rock, orquestas de tango, grupos de percusión rioplatense y coros vocales. Aquí se aprende a escuchar al compañero, a amalgamar timbres y a dominar el escenario en los conciertos mensuales.
                        </p>
                        <div class="tarjeta-datos">
                            <div class="tarjeta-datos-item">
                                <strong>Modalidad:</strong> Grupal semanal con presentaciones en vivo en salas y festivales.
                            </div>
                            <div class="tarjeta-datos-item">
                                <strong>Requisito:</strong> Conocimientos básicos del instrumento o estar cursando un taller individual.
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>

    </main>

    <!-- PIE DE PÁGINA -->
    <footer>
        <div class="contenedor footer-content">
            <div>
                <strong>Escuela de Música "A Todo Ritmo"</strong> &copy; <?= date('Y'); ?>. Asociación Civil Sin Fines de Lucro.
            </div>
            <div>
                <a href="talleres.php">Ver Talleres</a> &nbsp;|&nbsp;
                <a href="docentes.php">Cuerpo Docente</a> &nbsp;|&nbsp;
                <a href="inscripcion.php">Inscripciones Abiertas</a>
            </div>
        </div>
    </footer>

</body>
</html>
