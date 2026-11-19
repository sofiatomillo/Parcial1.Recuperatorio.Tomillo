<?php
require_once __DIR__ . '/conexion.php';

// Capturamos si viene un taller seleccionado por la URL (ej: inscripcion.php?taller=1)
$taller_seleccionado_id = isset($_GET['taller']) ? intval($_GET['taller']) : 0;
$info_taller_activo = null;

try {
    // 1. Traemos todos los talleres disponibles para el menú desplegable derecho
    $sqlTalleres = "SELECT * FROM talleres ORDER BY nombre ASC";
    $stmt = $pdo->prepare($sqlTalleres);
    $stmt->execute();
    $talleres_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 2. 🟢 NUEVO: Si hay un taller seleccionado, buscamos su información específica para la barra izquierda
    if ($taller_seleccionado_id > 0) {
        $sqlInfoTaller = "SELECT * FROM talleres WHERE id = :id LIMIT 1";
        $stmtInfo = $pdo->prepare($sqlInfoTaller);
        $stmtInfo->execute(['id' => $taller_seleccionado_id]);
        $info_taller_activo = $stmtInfo->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $talleres_db = [];
    $info_taller_activo = null;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escuela ATR | Inscripción</title>
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
            grid-template-columns: 35% 65% !important; /* Modificado para dar más ancho horizontal */
            gap: 40px !important;
            box-sizing: border-box !important;
        }

        /* 🟡 CUADRO IZQUIERDO DE REQUISITOS (Neo-brutalista) */
        .tarjeta-requisitos {
            background-color: #fffdf1 !important;
            border: 3px solid #181818 !important; 
            border-radius: 20px !important;
            padding: 30px !important;
            box-shadow: 6px 6px 0px #181818 !important; 
            height: fit-content !important;
            box-sizing: border-box !important;
        }

        /* ⚪ CUADRO DERECHO DEL FORMULARIO */
        .tarjeta-formulario-pasos {
            background-color: #ffffff;
            border: 2px solid #181818;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 6px 6px 0px #181818;
        }

        /* Campos de Entrada (Inputs y Selects) */
        .input-formulario-atr {
            width: 100%;
            padding: 12px 15px !important;
            border: 2px solid #181818 !important;
            border-radius: 12px !important;
            background-color: #fffdf1 !important; /* Forzamos el color crema suave de fondo */
            font-family: 'Mukta', sans-serif !important;
            font-size: 15px !important;
            box-sizing: border-box !important;
            margin-top: 5px !important;
            color: #181818 !important;
            outline: none !important;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        /* Efecto de foco verde idéntico al seleccionar */
        .input-formulario-atr:focus {
            border-color: #24AB58 !important;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15) !important;
        }

        /* Botón de Confirmación Verde Vibrante */
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
            box-shadow: 3px 3px 0px #181818;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-family: 'Mukta', sans-serif;
            margin-top: 25px;
        }

        /* Contenedor principal con fondo crema y borde amarillo/dorado redondeado */
  .taller-seleccionado-container {
    background-color: #fffbeb; /* Fondo crema suave */
    border: 2px solid #fde047; /* Borde amarillo/dorado fino */
    border-radius: 16px;       /* Bordes bien redondeados */
    padding: 24px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 450px;          /* Ajustable según tu layout */
    box-sizing: border-box;
  }

  /* Etiqueta superior gris en mayúsculas */
  .taller-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #4b5563;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
  }

  /* Título principal en negrita y color oscuro */
  .taller-titulo {
    font-size: 18px;
    font-weight: 800;
    color: #111827;
    margin: 0 0 12px 0;
  }

  /* Fila de horarios y días */
  .taller-info-row {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: #4b5563;
    margin-bottom: 12px;
  }

  .taller-info-row .icon {
    font-size: 14px;
    margin-right: 2px;
  }

  .taller-info-row .separator {
    color: #9ca3af;
    font-weight: bold;
  }

  /* Cupos en color verde característico */
  .taller-cupos {
    font-size: 14px;
    font-weight: 700;
    color: #16a34a; /* Verde para vacantes */
    margin: 0 0 8px 0;
  }

  /* Arancel final en negrita */
  .taller-arancel {
    font-size: 14px;
    font-weight: 700;
    color: #111827;
    margin: 0;
  }
    </style>
</head>
<body>
    <!-- HEADER -->
     <?php include 'header.php'; ?>

     <!-- Títulos Principales de la Sección -->
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
        
        <!-- COLUMNA IZQUIERDA: Requisitos -->
                <!-- COLUMNA IZQUIERDA: Requisitos e Información del Taller -->
                <!-- 🟢 Forzamos a que use todo el nuevo ancho disponible de la columna para ensancharse -->
        <aside class="tarjeta-requisitos" style="background-color: #fffdf1 !important; border: 3px solid #181818 !important; border-radius: 12px !important; padding: 30px !important; box-shadow: 6px 6px 0px #181818 !important; height: fit-content !important; box-sizing: border-box !important; width: 100% !important; max-width: 100% !important;">
            <!-- 🟢 TÍTULO CON SU ÍCONO ORIGINAL -->
            <h3 style="font-size: 18px; color: #24AB58; margin-top: 0; display: flex; align-items: center; gap: 8px; font-family: 'Carter One', cursive !important;">
                🛡️ REQUISITOS DE INSCRIPCIÓN
            </h3>
            
            <ol style="padding-left: 0; list-style: none; margin-top: 20px;">
                <!-- PASO 1 -->
                <li style="margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-start;">
                    <span style="background: #ffcc00; border: 2px solid #181818; border-radius: 50%; width: 55px; height: 26px; display: flex; justify-content: center; align-items: center; font-weight: 800; font-size: 14px; shrink: 0; box-shadow: 1px 1px 0px #181818;">1</span>
                    <div>
                        <strong style="display: block; font-size: 15px; color: #181818;">Edad Mínima:</strong>
                        <span style="font-size: 13.5px; color: #555555; line-height: 1.4;">A partir de los 7 años cumplidos. No hay límite superior de edad: jóvenes, adultos y personas mayores son siempre bienvenidos.</span>
                    </div>
                </li>
                
                <!-- PASO 2 -->
                <li style="margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-start;">
                    <span style="background: #ffcc00; border: 2px solid #181818; border-radius: 50%; width: 55px; height: 26px; display: flex; justify-content: center; align-items: center; font-weight: 800; font-size: 14px; shrink: 0; box-shadow: 1px 1px 0px #181818;">2</span>
                    <div>
                        <strong style="display: block; font-size: 15px; color: #181818;">Nivel Musical:</strong>
                        <span style="font-size: 13.5px; color: #555555; line-height: 1.4;">¡No se requieren conocimientos previos! Los talleres iniciales comienzan desde cero con una pedagogía integradora.</span>
                    </div>
                </li>

                <!-- 🟢 PASO 3 AGREGADO -->
                <li style="margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-start;">
                    <span style="background: #ffcc00; border: 2px solid #181818; border-radius: 50%; width: 55px; height: 26px; display: flex; justify-content: center; align-items: center; font-weight: 800; font-size: 14px; shrink: 0; box-shadow: 1px 1px 0px #181818;">3</span>
                    <div>
                        <strong style="display: block; font-size: 15px; color: #181818;">Documentación Necesaria:</strong>
                        <span style="font-size: 13.5px; color: #555555; line-height: 1.4;">DNI original y copia (frente y dorso) y certificado médico básico de aptitud física (para menores de edad, firma del tutor).</span>
                    </div>
                </li>

                <!-- 🟢 BLOQUE BANCO SOLIDARIO AGREGADO -->
                <li style="margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-start;">
                    <span style="background: #24AB58; border: 2px solid #181818; color: #ffffff; border-radius: 50%; width: 55px; height: 26px; display: flex; justify-content: center; align-items: center; font-weight: 800; font-size: 12px; shrink: 0; box-shadow: 1px 1px 0px #181818;">★</span>
                    <div>
                        <strong style="display: block; font-size: 15px; color: #181818;">Banco Solidario de Instrumentos:</strong>
                        <span style="font-size: 13.5px; color: #555555; line-height: 1.4;">Si no tenés instrumento en casa, la escuela te presta uno en comodato durante las clases para que nunca te quedes sin tocar.</span>
                    </div>
                </li>
            </ol>
            
          <!-- Componente de Taller Seleccionado -->
            <div class="taller-seleccionado-container">
                <span class="taller-label">TALLER SELECCIONADO:</span>
                <!-- Le ponemos IDs para que JavaScript pueda cambiar los textos -->
                <h2 class="taller-titulo" id="tarjeta-titulo">Seleccioná un taller</h2>
                
                <div class="taller-info-row">
                    <span class="info-item" id="tarjeta-dias">📅 —</span>
                    <span class="separator">•</span>    
                </div>
                
                <p class="taller-cupos" id="tarjeta-cupos">Cupos disponibles: —</p>
                <p class="taller-arancel" id="tarjeta-arancel">Arancel: —</p>
            </div>
        </aside>


        <!-- COLUMNA DERECHA: El Formulario por Pasos -->
        <main class="tarjeta-formulario-pasos" style="background-color: #ffffff !important; border: 3px solid #181818 !important; border-radius: 24px !important; padding: 40px !important; box-shadow: 6px 6px 0px #181818 !important; box-sizing: border-box !important;">
            <form action="procesar_inscripcion.php" method="POST">
                
                <!-- PASO 1 -->
                <h3 style="font-size: 24px; margin-top: 0; margin-bottom: 5px; font-family: 'Carter One', cursive !important;">Paso 1: Datos Personales del Postulante</h3>
                <p style="font-size: 14px; color: #6c757d; margin-top: 0; margin-bottom: 25px; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">Ingresá los datos del alumno que tomará las clases.</p>
                
                <!-- Fila 1: Nombre y Apellido -->
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

                <!-- 🟢 Fila 2: Correo y Teléfono -->
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

                <!-- 🟢 Fila 3: Edad y Género -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label style="font-size: 14px; font-weight: 700; color: #181818;">Edad (Mínimo 7 años) *</label>
                        <input type="number" name="edad" min="7" class="input-formulario-atr" placeholder="14" required>
                    </div>
                    <div>
                        <label style="font-size: 14px; font-weight: 700; color: #181818;">Género *</label>
                        <select name="genero" class="input-formulario-atr">
                            <option value="Femenino">Femenino</option>
                            <option value="Masculino">Masculino</option>
                            <option value="No Binario">No Binario (NB)</option>
                            <option value="Otro">Prefiero no decirlo</option>
                        </select>
                    </div>
                </div>

                <!-- PASO 2 -->
                <h3 style="font-size: 24px; margin-top: 40px; margin-bottom: 5px; font-family: 'Carter One', cursive !important;">Paso 2: Elección de Taller, Modalidad y Sede</h3>
                <p style="font-size: 14px; color: #6c757d; margin-top: 0; margin-bottom: 25px; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;"></p>
                
                <!-- Fila Selector Taller -->
               <div style="margin-bottom: 20px;">
                    <label style="font-size: 14px; font-weight: 700; color: #181818;">Taller de Instrumento *</label>
                    <select name="taller_id" class="input-formulario-atr" style="background-color: #fffdf1 !important; border: 2px solid #181818 !important;">
                        <?php if (!empty($talleres_db)): ?>
                            <?php foreach ($talleres_db as $taller): ?>
                                <option value="<?php echo $taller['id']; ?>" 
                                            <?php echo $taller['id'] == $taller_seleccionado_id ? 'selected' : ''; ?>
                                            data-titulo="Taller de <?php echo htmlspecialchars($taller['nombre']); ?>"
                                            data-dias="<?php echo htmlspecialchars($taller['horario']); /* Si guardas los días acá */ ?>"
                                            data-horario="Horarios a consultar" 
                                            data-cupos="<?php echo htmlspecialchars($taller['vacantes_disponibles'] ?? '0'); ?> vacantes"
                                            data-arancel="$15.500"> <!-- Podes cambiar este valor fijo por una variable si tenés el precio en la BD -->
                                    Taller de <?php echo htmlspecialchars($taller['nombre']); ?> 
                                    <?php echo !empty($taller['instrumento']) ? '('.htmlspecialchars($taller['instrumento']).')' : ''; ?> 
                                    — <?php echo htmlspecialchars($taller['horario']); ?> 
                                    — <?php echo htmlspecialchars($taller['vacantes_disponibles'] ?? '0'); ?> vacantes
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No hay talleres disponibles en este momento</option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- 🟢 Fila Modalidad y Sede -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label style="font-size: 14px; font-weight: 700; color: #181818;">Modalidad de Cursada *</label>
                        <select name="modalidad" class="input-formulario-atr">
                            <option value="Presencial">Presencial en Aula</option>
                            <option value="Virtual">Virtual / Online</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 14px; font-weight: 700; color: #181818;">Sede Preferida *</label>
                        <select name="sede" class="input-formulario-atr">
                            <option value="San Telmo">Sede Central - San Telmo (San Telmo, CABA)</option>
                            <option value="Almagro">Sede Almagro (Almagro, CABA)</option>
                        </select>
                    </div>
                </div>

                <!-- 🟢 BANCO SOLIDARIO -->
                <div style="background-color: #fffdf1 !important; border: 2px solid #181818 !important; padding: 20px !important; border-radius: 16px !important; margin-top: 30px !important; box-shadow: 3px 3px 0px #181818 !important; box-sizing: border-box !important;">
                    <strong style="display: block; font-size: 16px; margin-bottom: 12px; color: #181818;">💚 Banco Solidario de Instrumentos</strong>
                    
                    <label style="display: flex; gap: 10px; font-size: 14px; align-items: flex-start; cursor: pointer; margin-bottom: 10px; color: #181818;">
                        <input type="checkbox" name="solicita_instrumento" style="margin-top: 4px;" checked>
                        <span>Solicito que la escuela me preste un instrumento durante las clases (Sin costo adicional).</span>
                    </label>
                    
                    <label style="display: flex; gap: 10px; font-size: 14px; align-items: flex-start; cursor: pointer; color: #181818;">
                        <input type="checkbox" name="tiene_instrumento" style="margin-top: 4px;">
                        <span>Cuento con instrumento propio en mi hogar para practicar.</span>
                    </label>
                </div>

                <!-- 🟢 COMENTARIOS AGREGADOS -->
                <div style="margin-top: 25px; margin-bottom: 15px;">
                    <label style="font-size: 14px; font-weight: 700; color: #181818;">Comentarios o Experiencia Previa (Opcional)</label>
                    <textarea name="comentarios" class="input-formulario-atr" style="height: 100px; resize: vertical;" placeholder="Contanos si tocás algún otro instrumento o si tenés alguna necesidad específica..."></textarea>
                </div>

                <!-- 🟢 BOTÓN VERDE REPLICA FIEL -->
                <button type="submit" class="btn-confirmar-inscripcion" style="background-color: #24AB58 !important; color: #ffffff !important; border: 2px solid #181818 !important; padding: 15px !important; font-size: 16px !important; font-weight: 700 !important; border-radius: 12px !important; box-shadow: 4px 4px 0px #181818 !important; width: 100% !important; cursor: pointer !important;">
                    👤 Confirmar e Iniciar Cursada
                </button>
                
                <span style="font-size: 12px; color: #6c757d; display: block; text-align: center; margin-top: 12px;">Al enviar la ficha, se reserva la vacante y se genera el comprobante digital al instante.</span>
            </form>
        </main>
    </div>

    <!-- SCRIPT PARA CONECTAR EL MENÚ CON LA TARJETA EN TIEMPO REAL -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Buscamos tu select mediante su name ya que no tiene un ID único asignado
        const menuTaller = document.querySelector('select[name="taller_id"]');

        if (menuTaller) {
        menuTaller.addEventListener('change', function() {
            const opcion = this.options[this.selectedIndex];
            
            // Si el usuario vuelve a elegir la opción vacía inicial
            if (!opcion.value) {
            document.getElementById('tarjeta-titulo').textContent = "Seleccioná un taller";
            document.getElementById('tarjeta-dias').textContent = "📅 —";
            document.getElementById('tarjeta-cupos').textContent = "Cupos disponibles: —";
            document.getElementById('tarjeta-arancel').textContent = "Arancel: —";
            return;
            }

            // Extraemos los atributos data-* que cargamos con PHP y los metemos en la tarjeta
            document.getElementById('tarjeta-titulo').textContent = opcion.getAttribute('data-titulo');
            document.getElementById('tarjeta-dias').textContent = `📅 ${opcion.getAttribute('data-dias')}`;
            document.getElementById('tarjeta-cupos').textContent = `Cupos disponibles: ${opcion.getAttribute('data-cupos')}`;
            document.getElementById('tarjeta-arancel').textContent = `Arancel: ${opcion.getAttribute('data-arancel')} / mes`;
        });
        }
    });
    </script>

     <!-- FOOTER -->
      <?php include 'footer.php'; ?>

</body>
</html>