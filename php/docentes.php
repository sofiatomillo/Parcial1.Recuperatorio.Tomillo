<?php
/**
 * Proyecto: Escuela de Música "A Todo Ritmo"
 * Archivo: docentes.php
 * Sección: Docentes - Equipo Pedagógico
 * Requisitos: HTML5 Clásico, PHP Nativo con PDO, Contenedor max-width 1000px, Tarjetas Horizontales Compactas.
 *             Muestra nombre, especialidad y trayectoria pedagógica.
 *             Sin formulario de matrícula rápida.
 */

require_once __DIR__ . '/conexion.php';

// Consulta SQL con PDO para obtener todos los docentes activos de la escuela
try {
    $sqlDocentes = "SELECT 
                        d.id, 
                        d.nombre, 
                        d.apellido, 
                        d.especialidad, 
                        d.instrumento_principal, 
                        d.biografia, 
                        d.trayectoria_cv, 
                        d.email, 
                        d.telefono, 
                        d.foto_url
                    FROM docentes d
                    WHERE d.activo = 1
                    ORDER BY d.apellido ASC, d.nombre ASC";

    $stmtDocentes = $pdo->prepare($sqlDocentes);
    $stmtDocentes->execute();
    $docentes = $stmtDocentes->fetchAll();

    // Consulta complementaria para listar los talleres a cargo de cada docente
    $sqlTalleresDoc = "SELECT id, nombre, instrumento, nivel, docente_id FROM talleres ORDER BY nombre ASC";
    $stmtTalleresDoc = $pdo->query($sqlTalleresDoc);
    $todosTalleres = $stmtTalleresDoc->fetchAll();

    // Agrupar talleres por docente_id
    $talleresPorDocente = [];
    foreach ($todosTalleres as $t) {
        $talleresPorDocente[$t['docente_id']][] = $t;
    }

} catch (PDOException $e) {
    $errorBD = "Error al consultar los profesores: " . $e->getMessage();
    $docentes = [];
    $talleresPorDocente = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuerpo Docente | Escuela de Música "A Todo Ritmo"</title>
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
            padding: 35px 20px;
            text-align: center;
            border-radius: 8px;
            margin: 25px auto 30px auto;
        }

        .hero-seccion h2 {
            font-size: 2rem;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .hero-seccion p {
            font-size: 1.05rem;
            max-width: 750px;
            margin: 0 auto;
            opacity: 0.95;
        }

        /* Declaración Institucional Pedagógica */
        .bloque-intro {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #b93b2a;
            padding: 20px 24px;
            border-radius: 6px;
            margin-bottom: 30px;
            font-size: 0.95rem;
            color: #334155;
        }

        /* ==========================================================================
           TARJETAS HORIZONTALES COMPACTAS DE DOCENTES
           ========================================================================== */
        .lista-docentes {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 45px;
        }

        .tarjeta-docente-horizontal {
            display: flex;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .tarjeta-docente-horizontal:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.07);
            border-color: #cbd5e1;
        }

        /* Columna Izquierda: Retrato del Docente */
        .tarjeta-docente-foto {
            width: 190px;
            min-width: 190px;
            position: relative;
            background-color: #334155;
        }

        .tarjeta-docente-foto img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .badge-instrumento-doc {
            position: absolute;
            bottom: 10px;
            left: 10px;
            right: 10px;
            background-color: rgba(15, 23, 42, 0.9);
            color: #f8fafc;
            text-align: center;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
            letter-spacing: 0.3px;
        }

        /* Columna Derecha: Información Pedagógica */
        .tarjeta-docente-contenido {
            padding: 20px 24px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .docente-cabecera {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            flex-wrap: wrap;
            margin-bottom: 6px;
            gap: 10px;
        }

        .docente-cabecera h3 {
            font-size: 1.3rem;
            color: #0f172a;
            font-weight: 700;
        }

        .docente-email {
            font-size: 0.85rem;
            color: #64748b;
        }

        .docente-especialidad {
            font-size: 0.95rem;
            font-weight: 600;
            color: #b93b2a;
            margin-bottom: 12px;
        }

        /* Bloque de Trayectoria Pedagógica */
        .trayectoria-box {
            background-color: #fdfbf7;
            border-left: 3px solid #d97706;
            padding: 10px 14px;
            margin-bottom: 12px;
            border-radius: 0 4px 4px 0;
        }

        .trayectoria-box strong {
            display: block;
            font-size: 0.84rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #92400e;
            margin-bottom: 4px;
        }

        .trayectoria-box p {
            font-size: 0.9rem;
            color: #451a03;
            margin-bottom: 0;
            line-height: 1.5;
        }

        .docente-bio {
            font-size: 0.9rem;
            color: #475569;
            margin-bottom: 14px;
            line-height: 1.55;
        }

        /* Talleres a Cargo */
        .docente-talleres-vinculo {
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .docente-talleres-vinculo strong {
            color: #1e293b;
        }

        .taller-chip {
            background-color: #e2e8f0;
            color: #1e293b;
            text-decoration: none;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .taller-chip:hover {
            background-color: #cbd5e1;
            color: #0f172a;
        }

        /* Estado Vacío */
        .alerta-vacio {
            background-color: #fff;
            border: 1px dashed #cbd5e1;
            padding: 40px;
            text-align: center;
            border-radius: 6px;
            color: #64748b;
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
                    <li><a href="nosotros.php">Nosotros</a></li>
                    <li><a href="talleres.php">Talleres</a></li>
                    <li><a href="docentes.php" class="active">Docentes</a></li>
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
            <h2>Equipo Docente</h2>
            <p>Músicos profesionales, arregladores y pedagogos con vasta experiencia en orquestas, escenarios y formación comunitaria.</p>
        </section>

        <!-- Bloque Institucional -->
        <section class="bloque-intro">
            <strong>Nuestra Vocación Pedagógica:</strong> En la Escuela de Música "A Todo Ritmo", los docentes no son meros instructores técnicos; son guías artísticos que acompañan la sensibilidad individual de cada estudiante, fomentando el aprendizaje libre de frustraciones y la integración en ensambles grupales.
        </section>

        <!-- LISTADO DE PROFESORES DESDE MYSQL -->
        <section class="lista-docentes">
            <?php if (!empty($docentes)): ?>
                <?php foreach ($docentes as $prof): ?>
                    <article class="tarjeta-docente-horizontal" id="docente-<?= $prof['id'] ?>">
                        <!-- Retrato del Docente -->
                        <div class="tarjeta-docente-foto">
                            <img src="<?= htmlspecialchars($prof['foto_url'] ?? 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=400&q=80') ?>" alt="<?= htmlspecialchars($prof['nombre'] . ' ' . $prof['apellido']) ?>">
                            <span class="badge-instrumento-doc"><?= htmlspecialchars($prof['instrumento_principal']) ?></span>
                        </div>

                        <!-- Información y Trayectoria Pedagógica -->
                        <div class="tarjeta-docente-contenido">
                            <div>
                                <div class="docente-cabecera">
                                    <h3><?= htmlspecialchars($prof['nombre'] . ' ' . $prof['apellido']) ?></h3>
                                    <span class="docente-email"><?= htmlspecialchars($prof['email']) ?></span>
                                </div>

                                <div class="docente-especialidad">
                                    <?= htmlspecialchars($prof['especialidad']) ?>
                                </div>

                                <!-- Trayectoria Pedagógica y Currículum (Requisito estricto) -->
                                <div class="trayectoria-box">
                                    <strong>Trayectoria Pedagógica y Formación:</strong>
                                    <p><?= htmlspecialchars($prof['trayectoria_cv']) ?></p>
                                </div>

                                <!-- Reseña biográfica -->
                                <p class="docente-bio">
                                    <?= htmlspecialchars($prof['biografia']) ?>
                                </p>
                            </div>

                            <!-- Talleres a cargo del docente -->
                            <?php if (!empty($talleresPorDocente[$prof['id']])): ?>
                                <div class="docente-talleres-vinculo">
                                    <strong>Talleres que coordina:</strong>
                                    <?php foreach ($talleresPorDocente[$prof['id']] as $tallerDictado): ?>
                                        <a href="talleres.php#taller-<?= $tallerDictado['id'] ?>" class="taller-chip">
                                            <?= htmlspecialchars($tallerDictado['nombre']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alerta-vacio">
                    <h3>No se encontraron profesores registrados en la base de datos</h3>
                    <p>Verifique que la tabla <code>docentes</code> esté creada y contenga registros activos.</p>
                </div>
            <?php endif; ?>
        </section>

    </main>

    <!-- PIE DE PÁGINA -->
    <footer>
        <div class="contenedor footer-content">
            <div>
                <strong>Escuela de Música "A Todo Ritmo"</strong> &copy; <?= date('Y'); ?>. Asociación Civil Sin Fines de Lucro.
            </div>
            <div>
                <a href="nosotros.php">Nuestra Historia</a> &nbsp;|&nbsp;
                <a href="talleres.php">Ver Talleres</a> &nbsp;|&nbsp;
                <a href="inscripcion.php">Inscripciones Abiertas</a>
            </div>
        </div>
    </footer>

</body>
</html>
