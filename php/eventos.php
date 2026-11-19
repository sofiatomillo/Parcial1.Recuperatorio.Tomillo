<?php
require_once __DIR__ . '/conexion.php';

// Captura cuál pestaña de filtrado clickeó el usuario
$filtro_mes = isset($_GET['mes']) ? trim($_GET['mes']) : 'proximos';

try {
    $sql = "SELECT * FROM eventos WHERE 1=1";
    $params = [];

    if ($filtro_mes === 'previos') {
        $sql .= " AND fecha < CURDATE() ORDER BY fecha DESC";
    } else {
        $sql .= " AND fecha >= CURDATE() ORDER BY fecha ASC";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $eventos_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $eventos = [];
    foreach ($eventos_raw as $ev) {
        $categoria = $ev['categoria'] ?? ($ev['tipo'] ?? 'Evento');
        $badge_color = $ev['badge_color'] ?? 'yellow';
        if (empty($ev['badge_color'])) {
            if (stripos($categoria, 'Concierto') !== false) $badge_color = 'orange';
            elseif (stripos($categoria, 'Masterclass') !== false) $badge_color = 'blue';
        }
        $eventos[] = [
            'id'                  => $ev['id'],
            'titulo'              => $ev['titulo'],
            'categoria'           => $categoria,
            'badge_color'         => $badge_color,
            'descripcion'         => $ev['descripcion'],
            'repertorio'          => $ev['repertorio'] ?? '',
            'fecha'               => $ev['fecha'],
            'hora'                => $ev['hora'],
            'lugar'               => $ev['lugar'],
            'precio'              => isset($ev['precio']) ? (float)$ev['precio'] : (float)($ev['precio_entrada'] ?? 0),
            'butacas_disponibles' => isset($ev['butacas_disponibles']) ? (int)$ev['butacas_disponibles'] : (int)($ev['entradas_disponibles'] ?? 0),
            'imagen'              => $ev['imagen'] ?? ($ev['imagen_url'] ?? 'muestra-primavera.jpg')
        ];
    }
} catch (PDOException $e) {
    $eventos = [];
}

// Respaldo estático obligatorio por si tu base de datos local está vacía
if (empty($eventos) && $filtro_mes !== 'previos') {
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Carter+One&family=Mukta:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">

    <style>
        body {
            font-family: 'Mukta', Arial, sans-serif !important;
            background-color: #FFFAE6;
            color: #181818;
        }
        h2, h3 {
            font-family: 'Carter One', cursive !important;
            font-weight: 400 !important;
        }
        .events-section { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .section-header { text-align: center; margin-bottom: 40px; }
        .section-header .subtitle { color: #24AB58; font-weight: 800; font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 8px; }
        .section-header .main-title { font-size: 2.6rem; font-weight: 800; margin-bottom: 12px; letter-spacing: -0.5px; }
        .section-header .description { color: #555555; font-size: 0.95rem; max-width: 650px; margin: 0 auto 30px auto; line-height: 1.5; }

        .filter-buttons { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
        .btn-filter { background: #ffffff; border: 2px solid #181818; padding: 10px 22px; border-radius: 30px; font-weight: 700; font-size: 0.85rem; text-decoration: none; color: #181818; display: inline-flex; align-items: center; }
        .btn-filter.active { background-color: #24AB58; color: #ffffff; border-color: #181818; }

        .events-container { display: flex; flex-direction: column; gap: 30px; margin-top: 40px; }
        .event-card { background: #ffffff; border: 2px solid #181818; border-radius: 20px; display: flex; overflow: hidden; box-shadow: 4px 4px 0px #181818; }
        .event-image-container { position: relative; width: 320px; min-width: 320px; background: #181818; }
        .event-img { width: 100%; height: 100%; object-fit: cover; display: block; }

        .badge { position: absolute; top: 15px; left: 15px; font-size: 0.75rem; font-weight: 800; padding: 5px 14px; border-radius: 20px; color: #181818; border: 1px solid #181818; }
        .badge.yellow { background-color: #f4c430; }
        .badge.orange { background-color: #f77f00; color: #ffffff; }
        .badge.blue { background-color: #00b4d8; color: #ffffff; }

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
            font-size: 0.85rem;
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
            font-size: 0.95rem;
            color: #555555;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .repertoire-box {
            background-color: #fffef2;
            border: 1px solid #fae8a4;
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 0.88rem;
            margin-bottom: 20px;
        }

        .event-footer {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            border-top: 1px solid #f4f2eb !important;
            padding-top: 18px !important;
            flex-wrap: wrap !important;
            gap: 12px;
            width: 100% !important;
        }
        .price-label {
            font-size: 0.68rem;
            color: #999999;
            font-weight: 800;
            display: block;
        }
        .price-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: #181818;
        }
        .price-value.free {
            color: #24AB58;
            font-size: 1.1rem;
        }

        .booking-actions {
            display: flex !important;
            align-items: center !important;
            gap: 15px !important;
            flex-shrink: 0 !important;
        }
        .seats-available {
            font-size: 0.85rem;
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
            font-size: 0.88rem;
            cursor: pointer;
            box-shadow: 2px 2px 0px #181818;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Modal Reserva de Entradas */
        .modal-ticket-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); z-index: 9999; align-items: center; justify-content: center; padding: 20px; }
        .modal-ticket-overlay.abierto { display: flex; }
        .modal-ticket-caja { background: #ffffff; border: 3px solid #181818; border-radius: 24px; max-width: 500px; width: 100%; padding: 30px; box-shadow: 6px 6px 0px #181818; position: relative; }
        .modal-ticket-cerrar { position: absolute; top: 15px; right: 15px; background: #F3D84F; border: 2px solid #181818; border-radius: 8px; padding: 4px 10px; font-weight: 800; cursor: pointer; }
        .input-ticket { width: 100%; padding: 10px 14px; border: 2px solid #181818; border-radius: 10px; background: #fffdf1; font-family: 'Mukta', sans-serif; font-size: 14px; margin-top: 4px; margin-bottom: 12px; }

        @media (max-width: 820px) {
            .event-card { flex-direction: column; }
            .event-image-container { width: 100%; min-width: 100%; height: 220px; }
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="events-section">
        <div class="section-header">
            <p class="subtitle">MUESTRAS & CONCIERTOS</p>
            <h2 class="main-title">Agenda Cultural y de Alumnos</h2>
            <p class="description">
                Cada mes la escuela abre sus puertas para compartir en vivo el trabajo de los talleres, ensambles y artistas invitados.
            </p>

            <div class="filter-buttons">
                <a href="eventos.php?mes=proximos" class="btn-filter <?= $filtro_mes === 'proximos' ? 'active' : '' ?>">Próximos Eventos Mensuales</a>
                <a href="eventos.php?mes=previos" class="btn-filter <?= $filtro_mes === 'previos' ? 'active' : '' ?>">Eventos Previos / Archivo</a>
            </div>
        </div>

        <div class="events-container">
            <?php if (!empty($eventos)): ?>
                <?php foreach ($eventos as $evento):
                    $img_src = (strpos($evento['imagen'], 'http') === 0 || strpos($evento['imagen'], 'img/') === 0)
                        ? $evento['imagen']
                        : 'img/' . $evento['imagen'];
                ?>
                    <article class="event-card">
                        <div class="event-image-container">
                            <span class="badge <?= htmlspecialchars($evento['badge_color']) ?>">
                                <?= htmlspecialchars($evento['categoria']) ?>
                            </span>
                            <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($evento['titulo']) ?>" class="event-img">
                        </div>

                        <div class="event-content">
                            <div>
                                <div class="event-meta">
                                    <span>📅 <?= htmlspecialchars(date('d/m/Y', strtotime($evento['fecha']))) ?></span>
                                    <span>🕒 <?= htmlspecialchars(date('H:i', strtotime($evento['hora']))) ?> hs</span>
                                    <span>📍 <?= htmlspecialchars($evento['lugar']) ?></span>
                                </div>
                                <h3 class="event-card-title"><?= htmlspecialchars($evento['titulo']) ?></h3>
                                <p class="event-card-text"><?= htmlspecialchars($evento['descripcion']) ?></p>

                                <?php if (!empty($evento['repertorio'])): ?>
                                    <div class="repertoire-box">
                                        <strong>🎵 Repertorio Destacado:</strong>
                                        <p style="margin-top:4px;"><?= htmlspecialchars($evento['repertorio']) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="event-footer">
                                <div class="ticket-info">
                                    <span class="price-label">VALOR DE BONO / ENTRADA</span>
                                    <?php if ($evento['precio'] > 0): ?>
                                        <span class="price-value">$<?= number_format($evento['precio'], 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span class="price-value free">Entrada Libre y Gratuita</span>
                                    <?php endif; ?>
                                </div>
                                <div class="booking-actions">
                                    <span class="seats-available"><?= (int)$evento['butacas_disponibles'] ?> butacas disponibles</span>
                                    <?php if ($filtro_mes !== 'previos' && (int)$evento['butacas_disponibles'] > 0): ?>
                                        <button type="button"
                                                class="btn-ticket"
                                                onclick="abrirModalEntrada(this)"
                                                data-id="<?= (int)$evento['id'] ?>"
                                                data-titulo="<?= htmlspecialchars($evento['titulo']) ?>"
                                                data-precio="<?= (float)$evento['precio'] ?>"
                                                data-max="<?= (int)$evento['butacas_disponibles'] ?>">📋 Sacar Entradas</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 50px 0; background:#fff; border:2px dashed #181818; border-radius:20px;">No se encontraron eventos programados en esta sección.</p>
            <?php endif; ?>
        </div>
    </main>

    <!-- MODAL RESERVA DE ENTRADAS -->
    <div id="modal-entrada" class="modal-ticket-overlay" onclick="if(event.target===this)cerrarModalEntrada()">
        <div class="modal-ticket-caja">
            <button type="button" class="modal-ticket-cerrar" onclick="cerrarModalEntrada()">✕</button>
            <h3 style="font-size:22px; margin-bottom:6px;">Reservar Entradas</h3>
            <p id="me-titulo" style="font-size:14px; font-weight:700; color:#24AB58; margin-bottom:16px;"></p>
            <form action="procesar_entrada.php" method="POST">
                <input type="hidden" name="evento_id" id="me-evento-id">
                <label style="font-size:13px; font-weight:700;">Nombre y Apellido *</label>
                <input type="text" name="nombre_comprador" class="input-ticket" required placeholder="Ej: Juan Pérez">

                <label style="font-size:13px; font-weight:700;">Correo Electrónico *</label>
                <input type="email" name="email_comprador" class="input-ticket" required placeholder="tu@correo.com">

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div>
                        <label style="font-size:13px; font-weight:700;">Cantidad *</label>
                        <input type="number" name="cantidad" id="me-cantidad" class="input-ticket" min="1" max="6" value="1" required>
                    </div>
                    <div>
                        <label style="font-size:13px; font-weight:700;">Medio de Pago *</label>
                        <select name="metodo_pago" class="input-ticket">
                            <option value="Transferencia">Transferencia</option>
                            <option value="Efectivo en Puerta">Efectivo en Puerta</option>
                            <option value="Beca Comunitaria">Beca Comunitaria</option>
                        </select>
                    </div>
                </div>

                <button type="submit" style="width:100%; background:#24AB58; color:#fff; border:2px solid #181818; padding:12px; border-radius:12px; font-weight:800; font-size:15px; cursor:pointer; box-shadow:3px 3px 0px #181818;">
                    Confirmar Reserva
                </button>
            </form>
        </div>
    </div>

    <script>
        function abrirModalEntrada(btn) {
            document.getElementById('me-evento-id').value = btn.getAttribute('data-id');
            document.getElementById('me-titulo').textContent = btn.getAttribute('data-titulo');
            const max = Math.min(6, parseInt(btn.getAttribute('data-max') || '6', 10));
            document.getElementById('me-cantidad').max = max;
            document.getElementById('modal-entrada').classList.add('abierto');
        }
        function cerrarModalEntrada() {
            document.getElementById('modal-entrada').classList.remove('abierto');
        }
    </script>

    <?php include 'footer.php'; ?>

</body>
</html>
