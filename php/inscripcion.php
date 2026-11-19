<?php
require_once __DIR__ . '/conexion.php';

// Aceptamos tanto ?taller_id=X (desde talleres.php) como ?taller=X (desde docentes.php)
$taller_seleccionado_id = 0;
if (isset($_GET['taller_id'])) {
    $taller_seleccionado_id = intval($_GET['taller_id']);
} elseif (isset($_GET['taller'])) {
    $taller_seleccionado_id = intval($_GET['taller']);
}

try {
    $sqlTalleres = "SELECT * FROM talleres ORDER BY nombre ASC";
    $stmt = $pdo->prepare($sqlTalleres);
    $stmt->execute();
    $talleres_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $talleres_db = [];
}

if (empty($talleres_db)) {
    $talleres_db = [
        ['id' => 1, 'nombre' => 'Taller de Guitarra Criolla y Popular', 'instrumento' => 'Guitarra', 'dia_semana' => 'Martes y Jueves', 'horario' => '17:30 a 19:00 hs', 'vacantes_disponibles' => 4, 'arancel_mensual' => 14500],
        ['id' => 2, 'nombre' => 'Taller de Violín: Primeras Cuerdas', 'instrumento' => 'Violín', 'dia_semana' => 'Lunes y Miércoles', 'horario' => '18:00 a 19:30 hs', 'vacantes_disponibles' => 3, 'arancel_mensual' => 15500],
        ['id' => 3, 'nombre' => 'Taller de Batería y Pulso Rítmico', 'instrumento' => 'Batería', 'dia_semana' => 'Viernes', 'horario' => '16:00 a 18:00 hs', 'vacantes_disponibles' => 2, 'arancel_mensual' => 16000],
        ['id' => 4, 'nombre' => 'Taller de Canto Popular y Expresión Vocal', 'instrumento' => 'Canto Popular', 'dia_semana' => 'Lunes y Jueves', 'horario' => '19:00 a 20:30 hs', 'vacantes_disponibles' => 5, 'arancel_mensual' => 14000],
        ['id' => 5, 'nombre' => 'Gran Ensamble Comunitario A Todo Ritmo', 'instrumento' => 'Ensamble', 'dia_semana' => 'Sábados', 'horario' => '15:00 a 18:00 hs', 'vacantes_disponibles' => 8, 'arancel_mensual' => 9500],
        ['id' => 6, 'nombre' => 'Taller de Piano y Teclado Creativo', 'instrumento' => 'Piano', 'dia_semana' => 'Miércoles y Viernes', 'horario' => '18:00 a 20:00 hs', 'vacantes_disponibles' => 5, 'arancel_mensual' => 14500],
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escuela ATR | Inscripción</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Carter+One&family=Mukta:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">

    <style>
        body {
            background-color: #FFFAE6;
            color: #181818;
            font-family: 'Mukta', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        h2, h3, .titulo-retro {
            font-family: 'Carter One', cursive !important;
            font-weight: 400 !important;
        }

        .contenedor-inscripcion {
            max-width: 1250px !important;
            margin: 40px auto 80px auto !important;
            padding: 0 20px !important;
            display: grid !important;
            grid-template-columns: 35% 65% !important;
            gap: 40px !important;
            box-sizing: border-box !important;
        }

        .tarjeta-requisitos {
            background-color: #fffdf1 !important;
            border: 3px solid #181818 !important;
            border-radius: 20px !important;
            padding: 30px !important;
            box-shadow: 6px 6px 0px #181818 !important;
            height: fit-content !important;
            box-sizing: border-box !important;
            width: 100% !important;
        }

        .tarjeta-formulario-pasos {
            background-color: #ffffff;
            border: 3px solid #181818;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 6px 6px 0px #181818;
            box-sizing: border-box;
        }

        .input-formulario-atr {
            width: 100%;
            padding: 12px 15px !important;
            border: 2px solid #181818 !important;
            border-radius: 12px !important;
            background-color: #fffdf1 !important;
            font-family: 'Mukta', sans-serif !important;
            font-size: 15px !important;
            box-sizing: border-box !important;
            margin-top: 5px !important;
            color: #181818 !important;
            outline: none !important;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-formulario-atr:focus {
            border-color: #24AB58 !important;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15) !important;
        }

        .btn-confirmar-inscripcion {
            width: 100%;
            background-color: #24AB58;
            color: #ffffff;
            border: 2px solid #181818;
            padding: 15px;
            font-size: 16px;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 4px 4px 0px #181818;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-family: 'Mukta', sans-serif;
            margin-top: 25px;
        }

        .taller-seleccionado-container {
            background-color: #fffbeb;
            border: 2px solid #fde047;
            border-radius: 16px;
            padding: 24px;
            box-sizing: border-box;
        }

        .taller-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #4b5563;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .taller-titulo {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
            margin: 0 0 12px 0;
        }

        .taller-info-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            font-size: 14px;
            color: #4b5563;
            margin-bottom: 12px;
        }

        .taller-cupos {
            font-size: 14px;
            font-weight: 700;
            color: #16a34a;
            margin: 0 0 8px 0;
        }

        .taller-arancel {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        @media (max-width: 900px) {
            .contenedor-inscripcion {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <?php include 'header.php'; ?>

    <div style="text-align: center; margin: 50px auto 20px auto; padding: 0 20px;">
        <span style="color: #24AB58; text-transform: uppercase; font-weight: 700; font-size: 13px; letter-spacing: 1px;">
            Ciclo Lectivo 2026
        </span>
        <h2 style="font-size: 42px; margin: 5px 0 15px 0; color: #181818;">
            Ficha de Inscripción Online
        </h2>
        <p style="font-size: 16px; color: #6c757d; max-width: 600px; margin: 0 auto; line-height: 1.5;">
            Completá el formulario para matricularte. Si no disponés de instrumento propio, podés solicitar uno de nuestro Banco Solidario sin costo.
        </p>
    </div>

    <div class="contenedor-inscripcion">

        <!-- COLUMNA IZQUIERDA: Requisitos e Información del Taller -->
        <aside class="tarjeta-requisitos">
            <h3 style="font-size: 18px; color: #24AB58; margin-top: 0; display: flex; align-items: center; gap: 8px;">
                🛡️ REQUISITOS DE INSCRIPCIÓN
            </h3>

            <ol style="padding-left: 0; list-style: none; margin-top: 20px;">
                <li style="margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-start;">
                    <span style="background: #ffcc00; border: 2px solid #181818; border-radius: 50%; min-width: 28px; height: 28px; display: flex; justify-content: center; align-items: center; font-weight: 800; font-size: 14px; box-shadow: 1px 1px 0px #181818;">1</span>
                    <div>
                        <strong style="display: block; font-size: 15px; color: #181818;">Edad Mínima:</strong>
                        <span style="font-size: 13.5px; color: #555555; line-height: 1.4;">A partir de los 7 años cumplidos. No hay límite superior de edad: jóvenes, adultos y personas mayores son siempre bienvenidos.</span>
                    </div>
                </li>

                <li style="margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-start;">
                    <span style="background: #ffcc00; border: 2px solid #181818; border-radius: 50%; min-width: 28px; height: 28px; display: flex; justify-content: center; align-items: center; font-weight: 800; font-size: 14px; box-shadow: 1px 1px 0px #181818;">2</span>
                    <div>
                        <strong style="display: block; font-size: 15px; color: #181818;">Nivel Musical:</strong>
                        <span style="font-size: 13.5px; color: #555555; line-height: 1.4;">¡No se requieren conocimientos previos! Los talleres iniciales comienzan desde cero con una pedagogía integradora.</span>
                    </div>
                </li>

                <li style="margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-start;">
                    <span style="background: #ffcc00; border: 2px solid #181818; border-radius: 50%; min-width: 28px; height: 28px; display: flex; justify-content: center; align-items: center; font-weight: 800; font-size: 14px; box-shadow: 1px 1px 0px #181818;">3</span>
                    <div>
                        <strong style="display: block; font-size: 15px; color: #181818;">Documentación Necesaria:</strong>
                        <span style="font-size: 13.5px; color: #555555; line-height: 1.4;">DNI original y copia (frente y dorso) y certificado médico básico de aptitud física (para menores de edad, firma del tutor).</span>
                    </div>
                </li>

                <li style="margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-start;">
                    <span style="background: #24AB58; border: 2px solid #181818; color: #ffffff; border-radius: 50%; min-width: 28px; height: 28px; display: flex; justify-content: center; align-items: center; font-weight: 800; font-size: 12px; box-shadow: 1px 1px 0px #181818;">★</span>
                    <div>
                        <strong style="display: block; font-size: 15px; color: #181818;">Banco Solidario de Instrumentos:</strong>
                        <span style="font-size: 13.5px; color: #555555; line-height: 1.4;">Si no tenés instrumento en casa, la escuela te presta uno en comodato durante las clases para que nunca te quedes sin tocar.</span>
                    </div>
                </li>
            </ol>

            <!-- Componente de Taller Seleccionado -->
            <div class="taller-seleccionado-container">
                <span class="taller-label">TALLER SELECCIONADO:</span>
                <h2 class="taller-titulo" id="tarjeta-titulo">Seleccioná un taller</h2>

                <div class="taller-info-row">
                    <span class="info-item" id="tarjeta-dias">📅 —</span>
                </div>

                <p class="taller-cupos" id="tarjeta-cupos">Cupos disponibles: —</p>
                <p class="taller-arancel" id="tarjeta-arancel">Arancel: —</p>
            </div>
        </aside>

        <!-- COLUMNA DERECHA: El Formulario por Pasos -->
        <main class="tarjeta-formulario-pasos">
            <form action="procesar_inscripcion.php" method="POST">

                <!-- PASO 1 -->
                <h3 style="font-size: 24px; margin-top: 0; margin-bottom: 5px;">Paso 1: Datos Personales del Postulante</h3>
                <p style="font-size: 14px; color: #6c757d; margin-top: 0; margin-bottom: 25px; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">Ingresá los datos del alumno que tomará las clases.</p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label style="font-size: 14px; font-weight: 700; color: #181818;">Nombre *</label>
                        <input type="text" name="nombre" class="input-formulario-atr" placeholder="Ej: Sofía" required>
                    </div>
                    <div>
                        <label style="font-size: 14px; font-weight: 700; color: #181818;">Apellido *</label>
                        <input type="text" name="apellido" class="input-formulario-atr" placeholder="Ej: Tomillo" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label style="font-size: 14px; font-weight: 700; color: #181818;">Correo Electrónico *</label>
                        <input type="email" name="email" class="input-formulario-atr" placeholder="ejemplo@correo.com" required>
                    </div>
                    <div>
                        <label style="font-size: 14px; font-weight: 700; color: #181818;">Teléfono / WhatsApp *</label>
                        <input type="text" name="telefono" class="input-formulario-atr" placeholder="Ej: 11 5544-3322" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label style="font-size: 14px; font-weight: 700; color: #181818;">Edad (Mínimo 7 años) *</label>
                        <input type="number" name="edad" min="7" class="input-formulario-atr" placeholder="14" required>
                    </div>
                    <div>
                        <label style="font-size: 14px; font-weight: 700; color: #181818;">Género *</label>
                        <select name="genero" class="input-formulario-atr">
                            <option value="Fem">Femenino</option>
                            <option value="Masc">Masculino</option>
                            <option value="NB">No Binario (NB)</option>
                            <option value="Prefiero no decir">Prefiero no decirlo</option>
                        </select>
                    </div>
                </div>

                <!-- PASO 2 -->
                <h3 style="font-size: 24px; margin-top: 40px; margin-bottom: 5px;">Paso 2: Elección de Taller, Modalidad y Sede</h3>
                <p style="font-size: 14px; color: #6c757d; margin-top: 0; margin-bottom: 25px; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">Seleccioná tu comisión y sede de cursada.</p>

                <div style="margin-bottom: 20px;">
                    <label style="font-size: 14px; font-weight: 700; color: #181818;">Taller de Instrumento *</label>
                    <select name="taller_id" class="input-formulario-atr" required>
                        <option value="">-- Elegí un taller disponible --</option>
                        <?php foreach ($talleres_db as $taller):
                            $dias_texto = trim(($taller['dia_semana'] ?? '') . ' • ' . ($taller['horario'] ?? ''), ' •');
                            $arancel_fmt = number_format((float)($taller['arancel_mensual'] ?? 15500), 0, ',', '.');
                        ?>
                            <option value="<?= (int)$taller['id'] ?>"
                                    <?= ((int)$taller['id'] === $taller_seleccionado_id) ? 'selected' : '' ?>
                                    data-titulo="<?= htmlspecialchars($taller['nombre']) ?>"
                                    data-dias="<?= htmlspecialchars($dias_texto) ?>"
                                    data-cupos="<?= (int)($taller['vacantes_disponibles'] ?? 0) ?> vacantes"
                                    data-arancel="$<?= $arancel_fmt ?>">
                                <?= htmlspecialchars($taller['nombre']) ?>
                                <?= !empty($taller['instrumento']) ? ' (' . htmlspecialchars($taller['instrumento']) . ')' : '' ?>
                                — <?= htmlspecialchars($dias_texto) ?>
                                — <?= (int)($taller['vacantes_disponibles'] ?? 0) ?> vacantes
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label style="font-size: 14px; font-weight: 700; color: #181818;">Modalidad de Cursada *</label>
                        <select name="modalidad" class="input-formulario-atr">
                            <option value="Presencial">Presencial en Aula</option>
                            <option value="Online">Virtual / Online</option>
                            <option value="Híbrida">Híbrida</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 14px; font-weight: 700; color: #181818;">Sede Preferida *</label>
                        <select name="sede_id" class="input-formulario-atr">
                            <option value="1">Sede Central - San Telmo (Defensa 842)</option>
                            <option value="2">Anexo Comunitario - Almagro (Av. Corrientes 4120)</option>
                        </select>
                    </div>
                </div>

                <!-- BANCO SOLIDARIO -->
                <div style="background-color: #fffdf1 !important; border: 2px solid #181818 !important; padding: 20px !important; border-radius: 16px !important; margin-top: 30px !important; box-shadow: 3px 3px 0px #181818 !important; box-sizing: border-box !important;">
                    <strong style="display: block; font-size: 16px; margin-bottom: 12px; color: #181818;">💚 Banco Solidario de Instrumentos</strong>

                    <label style="display: flex; gap: 10px; font-size: 14px; align-items: flex-start; cursor: pointer; margin-bottom: 10px; color: #181818;">
                        <input type="checkbox" name="solicita_instrumento" value="1" style="margin-top: 4px;" checked>
                        <span>Solicito que la escuela me preste un instrumento durante las clases (Sin costo adicional).</span>
                    </label>

                    <label style="display: flex; gap: 10px; font-size: 14px; align-items: flex-start; cursor: pointer; color: #181818;">
                        <input type="checkbox" name="tiene_instrumento" value="1" style="margin-top: 4px;">
                        <span>Cuento con instrumento propio en mi hogar para practicar.</span>
                    </label>
                </div>

                <div style="margin-top: 25px; margin-bottom: 15px;">
                    <label style="font-size: 14px; font-weight: 700; color: #181818;">Comentarios o Experiencia Previa (Opcional)</label>
                    <textarea name="comentarios" class="input-formulario-atr" style="height: 100px; resize: vertical;" placeholder="Contanos si tocás algún otro instrumento o si tenés alguna necesidad específica..."></textarea>
                </div>

                <button type="submit" class="btn-confirmar-inscripcion">
                    👤 Confirmar e Iniciar Cursada
                </button>

                <span style="font-size: 12px; color: #6c757d; display: block; text-align: center; margin-top: 12px;">Al enviar la ficha, se reserva la vacante y se genera el comprobante digital al instante.</span>
            </form>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuTaller = document.querySelector('select[name="taller_id"]');

        function actualizarTarjetaTaller() {
            if (!menuTaller) return;
            const opcion = menuTaller.options[menuTaller.selectedIndex];

            if (!opcion || !opcion.value) {
                document.getElementById('tarjeta-titulo').textContent = "Seleccioná un taller";
                document.getElementById('tarjeta-dias').textContent = "📅 —";
                document.getElementById('tarjeta-cupos').textContent = "Cupos disponibles: —";
                document.getElementById('tarjeta-arancel').textContent = "Arancel: —";
                return;
            }

            document.getElementById('tarjeta-titulo').textContent = opcion.getAttribute('data-titulo');
            document.getElementById('tarjeta-dias').textContent = "📅 " + opcion.getAttribute('data-dias');
            document.getElementById('tarjeta-cupos').textContent = "Cupos disponibles: " + opcion.getAttribute('data-cupos');
            document.getElementById('tarjeta-arancel').textContent = "Arancel: " + opcion.getAttribute('data-arancel') + " / mes";
        }

        if (menuTaller) {
            menuTaller.addEventListener('change', actualizarTarjetaTaller);
            actualizarTarjetaTaller();
        }
    });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>
