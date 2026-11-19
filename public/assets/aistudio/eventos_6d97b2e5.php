<?php
require_once __DIR__ . '/conexion.php';

// Captura cuál pestaña de filtrado clickeó el usuario
$filtro_mes = isset($_GET['mes']) ? trim($_GET['mes']) : 'proximos';

try {
    $sql = "SELECT * FROM eventos WHERE 1=1";
    $params = [];

    // Lógica relacional: eventos futuros o archivo histórico
    if ($filtro_mes === 'previos') {
        $sql .= " AND fecha < CURDATE() ORDER BY fecha DESC";
    } else {
        $sql .= " AND fecha >= CURDATE() ORDER BY fecha ASC";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $eventos = [];
}

// Respaldo estático obligatorio por si tu base de datos local está vacía
if (empty($eventos)) {
    $eventos = [
        [
            'id' => 1, 'titulo' => 'Gran Muestra de Primavera: Todos al Escenario', 'categoria' => 'Muestra de Alumnos', 'badge_color' => 'yellow',
            'descripcion' => 'Presentación en vivo de los ensambles de iniciales y avanzados. Los alumnos de guitarra, batería, violín, flauta y coro mostrarán los proyectos trabajados durante el cuatrimestre.',
            'repertorio' => 'Rock nacional (Charly García, Spinetta, Los Redondos), candombe rioplatense, piezas clásicas adaptadas y composiciones colectivas de los alumnos.',
            'fecha' => '2026-10-17', 'hora' => '18:30:00', 'lugar' => 'Auditorio Sala Mayor - Sede San Telmo (Defensa 842)', 'precio' => 1500, 'butacas_disponibles' => 38, 'imagen' => 'muestra-primavera.jpg'
        ],
        [
            'id' => 2, 'titulo' => 'Concierto Solidario: Instrumentos para Todos', 'categoria' => 'Concierto Abierto', 'badge_color' => 'orange',
            'descripcion' => 'Concierto benéfico abierto a todo el barrio a cargo de los docentes de la escuela e invitados especiales para recaudar fondos para el banco de instrumentos gratuitos.',
            'repertorio' => 'Jazz fusión, bossa nova, folklore argentino / canciones tradicionales en formato acústico.',
            'fecha' => '2026-11-07', 'hora' => '20:00:00', 'lugar' => 'Teatro Comunitario Almagro (Av. Corrientes 4120)', 'precio' => 2000, 'butacas_disponibles' => 64, 'imagen' => 'concierto-solidario.jpg'
        ],
        [
            'id' => 3, 'titulo' => 'Masterclass Abierta: "El Ritmo en el Cuerpo"', 'categoria' => 'Masterclass', 'badge_color' => 'blue',
            'descripcion' => 'Taller participativo de percusión corporal y polirritmias dictado por Facundo Pérez. Para alumnos, familiares y vecinos de todas las edades.',
            'repertorio' => 'Claves afroamericanas, chacarera, candombe y juegos de coordinación colectiva.',
            'fecha' => '2026-11-21', 'hora' => '11:00:00', 'lugar' => 'Patio Abierto - Sede San Telmo', 'precio' => 0, 'butacas_disponibles' => 19, 'imagen' => 'masterclass-ritmo.jpg'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Cultural - A Todo Ritmo</title>
    <link rel="stylesheet" href="index.css">
    
    <style>
        body { 
        font-family: 'Mukta', Arial, sans-serif !important; 
        }

        h2, h3, .malla-docentes-propia h3 {
        font-family: 'Carter One', cursive !important;
        font-weight: 400 !important;
        }
        body { background-color: #FFFAE6; color: #181818; }
        .events-section { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .section-header { text-align: center; margin-bottom: 40px; }
        .section-header .subtitle { color: #24AB58; font-weight: 800; font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 8px; }
        .section-header .main-title { font-size: 2.6rem; font-weight: 800; margin-bottom: 12px; letter-spacing: -0.5px; }
        .section-header .description { color: #555555; font-size: 0.95rem; max-width: 650px; margin: 0 auto 30px auto; line-height: 1.5; }
        
        /* Botones Solapa Estilo Cápsula */
        .filter-buttons { display: flex; justify-content: center; gap: 12px; }
        .btn-filter { background: #ffffff; border: 2px solid #181818; padding: 10px 22px; border-radius: 30px; font-weight: 700; font-size: 0.85rem; text-decoration: none; color: #181818; display: inline-flex; align-items: center; }
        .btn-filter.active { background-color: #24AB58; color: #ffffff; border-color: #24AB58; }

        /* Contenedor Grilla de Tarjetas */
        .events-container { display: flex; flex-direction: column; gap: 30px; margin-top: 40px; }
        .event-card { background: #ffffff; border: 2px solid #e3e1da; border-radius: 20px; display: flex; overflow: hidden; }
        .event-image-container { position: relative; width: 320px; min-width: 320px; }
        .event-img { width: 100%; height: 100%; object-fit: cover; display: block; }
        
        /* Badges de Categoría */
        .badge { position: absolute; top: 15px; left: 15px; font-size: 0.75rem; font-weight: 800; padding: 5px 14px; border-radius: 20px; color: #181818; border: 1px solid #181818; }
        .badge.yellow { background-color: #f4c430; }
        .badge.orange { background-color: #f77f00; color: #ffffff; }
        .badge.blue { background-color: #00b4d8; color: #ffffff; }

        /* Estructura Interna del Contenido */
        .event-content { 
            padding: 30px; 
            flex-grow: 1; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
        }
        .event-meta { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 15px; 
            font-size: 0.8rem; 
            font-weight: 600; 
            color: #666666; 
            margin-bottom: 12px; 
        }
        .event-card-title {
             font-size: 1.6rem; 
             font-weight: 800; 
             color: #181818; 
             margin-bottom: 14px; 
             line-height: 1.2; 
            }
        .event-card-text { 
            font-size: 0.9rem; 
            color: #555555; 
            line-height: 1.5; 
            margin-bottom: 20px; 
        }
        .repertoire-box {
             background-color: #fffef2; 
             border: 1px solid #fae8a4; 
             border-radius: 12px; 
             padding: 12px 18px; 
             font-size: 0.85rem; 
             margin-bottom: 20px; 
            }
        
        /* Compilado Pie de Tarjeta */
        .event-footer { 
            display: flex !important;
            justify-content: space-between !important; 
            align-items: center !important; 
            border-top: 1px solid #f4f2eb !important; 
            padding-top: 18px !important;
            flex-wrap: nowrap !important;
            width: 100% !important;
        }
        .price-label { 
            font-size: 0.65rem; 
            color: #999999; 
            font-weight: 800; 
        }
        .price-value { 
            font-size: 1.4rem; 
            font-weight: 800; 
            color: #181818; 
        }
        
        .booking-actions { 
            display: flex !important; 
            align-items: center !important; 
            gap: 15px !important;
            flex-shrink: 0 !important;
        }
        .seats-available { 
            font-size: 0.8rem; 
            color: #24AB58; 
            font-weight: 700; 
        }
        .btn-ticket { 
            background-color: #f4c430; 
            color: #181818; 
            border: 2px solid #181818; 
            padding: 10px 20px; 
            border-radius: 12px; 
            font-weight: 700; 
            font-size: 0.85rem; 
            cursor: pointer; 
            box-shadow: 2px 2px 0px #181818; 
            display: flex; 
            align-items: center; 
            gap: 6px; 
        }
    </style>
</head>
<body>

    <!-- Incluimos la barra unificada superior -->
    <?php include 'header.php'; ?>
    <!-- 3. CUERPO PRINCIPAL DE LA AGENDA CULTURAL -->
    <main class="events-section">
        <div class="section-header">
            <p class="subtitle">MUESTRAS & CONCIERTOS</p>
            <h2 class="main-title">Agenda Cultural y de Alumnos</h2>
            <p class="description">
                Cada mes la escuela abre sus puertas para compartir en vivo el trabajo de los talleres, ensambles y artistas invitados.
            </p>
            
            <!-- Cápsulas de filtrado dinámico que cambian por URL -->
            <div class="filter-buttons">
                <a href="eventos.php?mes=proximos" class="btn-filter <?php echo $filtro_mes === 'proximos' ? 'active' : ''; ?>">Próximos Eventos Mensuales</a>
                <a href="eventos.php?mes=previos" class="btn-filter <?php echo $filtro_mes === 'previos' ? 'active' : ''; ?>">Eventos Previos / Archivo</a>
            </div>
        </div>

        <div class="events-container">
            <?php if (!empty($eventos)): ?>
                <?php foreach ($eventos as $evento): ?>
                    <!-- Tarjeta de Evento Horizontal -->
                    <article class="event-card">
                        <!-- Contenedor de Imagen y Badge -->
                        <div class="event-image-container">
                            <span class="badge <?php echo htmlspecialchars($evento['badge_color']); ?>">
                                <?php echo htmlspecialchars($evento['categoria']); ?>
                            </span>
                            <img src="img/<?php echo htmlspecialchars($evento['imagen']); ?>" alt="<?php echo htmlspecialchars($evento['titulo']); ?>" class="event-img">
                        </div>

                        <!-- Bloque de Contenido Informativo -->
                        <div class="event-content">
                            <div>
                                <div class="event-meta">
                                    <span>📅 <?php echo htmlspecialchars($evento['fecha']); ?></span>
                                    <span>🕒 <?php echo date("H:i", strtotime($evento['hora'])); ?> hs</span>
                                    <span>📍 <?php echo htmlspecialchars($evento['lugar']); ?></span>
                                </div>
                                <h3 class="event-card-title"><?php echo htmlspecialchars($evento['titulo']); ?></h3>
                                <p class="event-card-text"><?php echo htmlspecialchars($evento['descripcion']); ?></p>
                                
                                <!-- Renderizado condicional de la caja amarilla de repertorio -->
                                <?php if (!empty($evento['repertorio'])): ?>
                                    <div class="repertoire-box">
                                        <strong>🎵 Repertorio Destacado:</strong>
                                        <p><?php echo htmlspecialchars($evento['repertorio']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Pie de tarjeta con precios, vacantes y botón amarillo -->
                            <div class="event-footer">
                                <div class="ticket-info">
                                    <span class="price-label">VALOR DE BONO / ENTRADA</span>
                                    <?php if ($evento['precio'] > 0): ?>
                                        <span class="price-value">$<?php echo number_format($evento['precio'], 0, ',', '.'); ?></span>
                                    <?php else: ?>
                                        <span class="price-value free">Entrada Libre y Gratuita</span>
                                    <?php endif; ?>
                                </div>
                                <div class="booking-actions">
                                    <span class="seats-available"><?php echo htmlspecialchars($evento['butacas_disponibles']); ?> butacas disponibles</span>
                                    <button class="btn-ticket">📋 Sacar Entradas</button>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Mensaje alternativo por si no hay registros en la base de datos -->
                <p style="text-align: center; color: #666; padding: 50px 0;">No se encontraron eventos programados en esta sección.</p>
            <?php endif; ?>
        </div>
    </main>
    <!-- Incluimos el pie de página unificado de la escuela -->
    <?php include 'footer.php'; ?>

</body>
</html>
