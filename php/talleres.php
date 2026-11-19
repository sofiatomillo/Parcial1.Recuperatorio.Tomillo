<?php
/**
 * Proyecto: Escuela de Música "A Todo Ritmo"
 * Archivo: talleres.php
 * Sección: Talleres Instrumentales y Vocales
 * Requisitos: HTML5 Clásico, PHP Nativo con PDO, Contenedor max-width 1000px, Tarjetas Horizontales Compactas.
 *             Sin formulario de matrícula rápida (el botón redirige a la sección de inscripción).
 */

require_once __DIR__ . '/conexion.php';

// Filtro opcional por instrumento o nivel (si se envía por GET)
$filtroInstrumento = isset($_GET['instrumento']) ? trim($_GET['instrumento']) : '';
$filtroNivel       = isset($_GET['nivel']) ? trim($_GET['nivel']) : '';

// Consulta SQL con PDO uniendo las tablas talleres, docentes y sedes
try {
    $sql = "SELECT 
                t.id, 
                t.nombre, 
                t.instrumento, 
                t.docente_id, 
                t.nivel, 
                t.edad_minima, 
                t.dia_semana, 
                t.horario, 
                t.vacantes_totales, 
                t.vacantes_disponibles, 
                t.arancel_mensual, 
                t.modalidad, 
                t.sede_id, 
                t.descripcion, 
                t.requisitos, 
                t.instrumentos_prestamo_disponibles, 
                t.destacado, 
                t.imagen_url,
                CONCAT(d.nombre, ' ', d.apellido) AS docente_nombre,
                d.especialidad AS docente_especialidad,
                s.nombre AS sede_nombre,
                s.barrio AS sede_barrio
            FROM talleres t
            INNER JOIN docentes d ON t.docente_id = d.id
            LEFT JOIN sedes s ON t.sede_id = s.id
            WHERE 1=1";

    $params = [];

    if (!empty($filtroInstrumento)) {
        $sql .= " AND t.instrumento = :instrumento";
        $params[':instrumento'] = $filtroInstrumento;
    }

    if (!empty($filtroNivel)) {
        $sql .= " AND t.nivel = :nivel";
        $params[':nivel'] = $filtroNivel;
    }

    $sql .= " ORDER BY t.destacado DESC, t.instrumento ASC, t.nombre ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $talleres = $stmt->fetchAll();

    // Consulta para obtener la lista de instrumentos únicos para el filtro rápido
    $stmtInst = $pdo->query("SELECT DISTINCT instrumento FROM talleres ORDER BY instrumento ASC");
    $instrumentosDisponibles = $stmtInst->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    $errorBD = "No se pudieron recuperar los talleres: " . $e->getMessage();
    $talleres = [];
    $instrumentosDisponibles = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talleres y Cursos | Escuela de Música "A Todo Ritmo"</title>
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

        /* Banner de Sección */
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

        /* Barra de Filtros Compacta */
        .filtros-box {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filtros-form {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filtros-form label {
            font-size: 0.88rem;
            font-weight: 600;
            color: #475569;
        }

        .filtros-form select {
            padding: 6px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 0.88rem;
            color: #1e293b;
            background-color: #fff;
        }

        .btn-filtrar {
            background-color: #b93b2a;
            color: #ffffff;
            border: none;
            padding: 7px 16px;
            font-size: 0.88rem;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-filtrar:hover {
            background-color: #991b1b;
        }

        .btn-limpiar {
            color: #64748b;
            font-size: 0.85rem;
            text-decoration: underline;
            margin-left: 5px;
        }

        .conteo-resultados {
            font-size: 0.88rem;
            color: #64748b;
        }

        /* ==========================================================================
           TARJETAS HORIZONTALES COMPACTAS DE TALLERES
           ========================================================================== */
        .lista-talleres {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin-bottom: 40px;
        }

        .tarjeta-taller-horizontal {
            display: flex;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .tarjeta-taller-horizontal:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.07);
            border-color: #cbd5e1;
        }

        /* Columna Izquierda: Imagen fija compacta */
        .tarjeta-taller-foto {
            width: 200px;
            min-width: 200px;
            position: relative;
            background-color: #1e293b;
        }

        .tarjeta-taller-foto img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .badge-instrumento {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: rgba(15, 23, 42, 0.88);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 3px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .badge-destacado {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background-color: #ea580c;
            color: #ffffff;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 3px;
        }

        /* Columna Derecha: Contenido y Acciones */
        .tarjeta-taller-contenido {
            padding: 18px 22px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .taller-cabecera {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 8px;
        }

        .taller-cabecera h3 {
            font-size: 1.2rem;
            color: #0f172a;
            font-weight: 700;
        }

        .taller-arancel {
            text-align: right;
            white-space: nowrap;
        }

        .arancel-valor {
            font-size: 1.25rem;
            font-weight: 800;
            color: #b93b2a;
        }

        .arancel-periodo {
            font-size: 0.75rem;
            color: #64748b;
            display: block;
        }

        .taller-docente {
            font-size: 0.9rem;
            color: #475569;
            margin-bottom: 8px;
        }

        .taller-docente strong {
            color: #1e293b;
        }

        .taller-docente a {
            color: #b93b2a;
            text-decoration: none;
        }

        .taller-docente a:hover {
            text-decoration: underline;
        }

        .taller-descripcion {
            font-size: 0.9rem;
            color: #475569;
            margin-bottom: 14px;
            line-height: 1.5;
        }

        /* Fila de Metadatos Compacta */
        .taller-detalles-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 16px;
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            padding: 10px 14px;
            border-radius: 5px;
            margin-bottom: 14px;
            font-size: 0.84rem;
        }

        .detalle-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #334155;
        }

        .detalle-item strong {
            color: #0f172a;
        }

        .badge-nivel {
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 2px 7px;
            border-radius: 3px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .badge-vacantes {
            background-color: #fef2f2;
            color: #991b1b;
            padding: 2px 7px;
            border-radius: 3px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .badge-prestamo {
            background-color: #ecfdf5;
            color: #047857;
            padding: 2px 7px;
            border-radius: 3px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* Fila Inferior con Botón de Acción */
        .taller-acciones {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 12px;
        }

        .taller-aviso {
            font-size: 0.82rem;
            color: #64748b;
        }

        .btn-inscribirse {
            display: inline-block;
            background-color: #b93b2a;
            color: #ffffff;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .btn-inscribirse:hover {
            background-color: #991b1b;
        }

        /* Estado Vacío o Error */
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
                    <li><a href="talleres.php" class="active">Talleres</a></li>
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
            <h2>Nuestros Talleres de Música</h2>
            <p>Clases semanales teórico-prácticas con enfoque grupal y préstamo gratuito de instrumentos en sede.</p>
        </section>

        <!-- Filtros Rápidos Compactos -->
        <section class="filtros-box">
            <form method="GET" action="talleres.php" class="filtros-form">
                <label for="instrumento">Filtrar por Instrumento:</label>
                <select name="instrumento" id="instrumento">
                    <option value="">-- Todos los instrumentos --</option>
                    <?php foreach ($instrumentosDisponibles as $inst): ?>
                        <option value="<?= htmlspecialchars($inst) ?>" <?= ($filtroInstrumento === $inst) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($inst) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="nivel">Nivel:</label>
                <select name="nivel" id="nivel">
                    <option value="">-- Todos los niveles --</option>
                    <option value="Inicial / Sin experiencia" <?= ($filtroNivel === 'Inicial / Sin experiencia') ? 'selected' : '' ?>>Inicial / Sin experiencia</option>
                    <option value="Intermedio" <?= ($filtroNivel === 'Intermedio') ? 'selected' : '' ?>>Intermedio</option>
                    <option value="Avanzado" <?= ($filtroNivel === 'Avanzado') ? 'selected' : '' ?>>Avanzado</option>
                    <option value="Ensamble" <?= ($filtroNivel === 'Ensamble') ? 'selected' : '' ?>>Ensamble</option>
                </select>

                <button type="submit" class="btn-filtrar">Filtrar</button>
                <?php if (!empty($filtroInstrumento) || !empty($filtroNivel)): ?>
                    <a href="talleres.php" class="btn-limpiar">Restablecer</a>
                <?php endif; ?>
            </form>

            <div class="conteo-resultados">
                Mostrando <strong><?= count($talleres) ?></strong> taller(es) disponible(s)
            </div>
        </section>

        <!-- LISTADO DE TARJETAS HORIZONTALES COMPACTAS -->
        <section class="lista-talleres">
            <?php if (!empty($talleres)): ?>
                <?php foreach ($talleres as $taller): ?>
                    <article class="tarjeta-taller-horizontal">
                        <!-- Foto del Taller / Instrumento -->
                        <div class="tarjeta-taller-foto">
                            <img src="<?= htmlspecialchars($taller['imagen_url'] ?? 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=400&q=80') ?>" alt="<?= htmlspecialchars($taller['nombre']) ?>">
                            <span class="badge-instrumento"><?= htmlspecialchars($taller['instrumento']) ?></span>
                            <?php if (!empty($taller['destacado'])): ?>
                                <span class="badge-destacado">Destacado</span>
                            <?php endif; ?>
                        </div>

                        <!-- Contenido del Taller -->
                        <div class="tarjeta-taller-contenido">
                            <div>
                                <div class="taller-cabecera">
                                    <h3><?= htmlspecialchars($taller['nombre']) ?></h3>
                                    <div class="taller-arancel">
                                        <span class="arancel-valor">$<?= number_format($taller['arancel_mensual'], 0, ',', '.') ?></span>
                                        <span class="arancel-periodo">por mes</span>
                                    </div>
                                </div>

                                <div class="taller-docente">
                                    Profesor/a: <strong><a href="docentes.php#docente-<?= $taller['docente_id'] ?>"><?= htmlspecialchars($taller['docente_nombre']) ?></a></strong> (<?= htmlspecialchars($taller['docente_especialidad']) ?>)
                                </div>

                                <p class="taller-descripcion">
                                    <?= htmlspecialchars($taller['descripcion']) ?>
                                </p>
                            </div>

                            <!-- Fila de Detalles Clave -->
                            <div class="taller-detalles-grid">
                                <div class="detalle-item">
                                    <strong>Horario:</strong> <?= htmlspecialchars($taller['dia_semana']) ?>, <?= htmlspecialchars($taller['horario']) ?>
                                </div>
                                <div class="detalle-item">
                                    <strong>Sede:</strong> <?= htmlspecialchars($taller['sede_nombre'] ?? 'Sede Central') ?>
                                </div>
                                <div class="detalle-item">
                                    <strong>Nivel:</strong> <span class="badge-nivel"><?= htmlspecialchars($taller['nivel']) ?></span>
                                </div>
                                <div class="detalle-item">
                                    <strong>Vacantes:</strong> <span class="badge-vacantes"><?= (int)$taller['vacantes_disponibles'] ?> libres (de <?= (int)$taller['vacantes_totales'] ?>)</span>
                                </div>
                                <div class="detalle-item">
                                    <strong>Préstamo:</strong> <span class="badge-prestamo"><?= (int)$taller['instrumentos_prestamo_disponibles'] ?> instrumentos en aula</span>
                                </div>
                            </div>

                            <!-- Acciones (Sin formulario de matrícula rápida; redirección a inscripcion.php) -->
                            <div class="taller-acciones">
                                <div class="taller-aviso">
                                    Requisito: Edad mínima <?= (int)$taller['edad_minima'] ?> años. <?= htmlspecialchars($taller['modalidad']) ?>.
                                </div>
                                <a href="inscripcion.php?taller_id=<?= (int)$taller['id'] ?>" class="btn-inscribirse">
                                    Inscribirme al Taller &rarr;
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alerta-vacio">
                    <h3>No se encontraron talleres con los filtros seleccionados</h3>
                    <p>Por favor intenta restablecer los filtros para ver todos los cursos disponibles.</p>
                    <p style="margin-top: 15px;"><a href="talleres.php" class="btn-filtrar" style="text-decoration:none; display:inline-block;">Ver Todos</a></p>
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
                <a href="docentes.php">Cuerpo Docente</a> &nbsp;|&nbsp;
                <a href="inscripcion.php">Sección de Inscripción</a>
            </div>
        </div>
    </footer>

</body>
</html>
