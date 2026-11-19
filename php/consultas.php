<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/auth_admin.php';

$cfg = obtenerConfigPagina($pdo);
$contacto_whatsapp = $cfg['contacto_whatsapp'] ?? '(011) 15-5829-1022 (Lunes a Viernes de 10 a 20 hs)';
$contacto_email    = $cfg['contacto_email']    ?? 'consultas@atodoritmo.edu.ar';

try {
    $faqs_db = $pdo->query("SELECT * FROM preguntas_frecuentes ORDER BY orden ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $faqs_db = [];
}

if (empty($faqs_db)) {
    $faqs_db = [
        ['categoria' => 'Instrumentos y Préstamos', 'pregunta' => '¿Qué pasa si no tengo instrumento propio?', 'respuesta' => 'En "A Todo Ritmo" ningún alumno se queda afuera por no tener instrumento. Disponemos de un Banco Solidario de Instrumentos que te presta guitarras, violines, flautas y teclados para su uso en clase y para practicar en nuestras salas de estudio sin costo adicional.'],
        ['categoria' => 'Inscripciones', 'pregunta' => '¿Hay examen de ingreso o audición eliminatoria?', 'respuesta' => '¡No! Rechazamos los filtros excluyentes. La escuela está abierta a personas desde los 7 años en adelante, sin importar si nunca antes tocaste un instrumento. El único requisito es tener curiosidad y ganas de aprender.'],
        ['categoria' => 'Cuotas y Becas', 'pregunta' => '¿Qué facilidades de pago o becas ofrecen?', 'respuesta' => 'Mantenemos aranceles comunitarios y accesibles. Si tu familia atraviesa dificultades económicas, contamos con un programa de becas parciales y totales sustentadas por la cooperadora y eventos solidarios. Consultá en secretaría de manera confidencial.'],
        ['categoria' => 'Niveles y Edades', 'pregunta' => '¿A partir de qué edad se puede ingresar? ¿Hay límite de edad?', 'respuesta' => 'Recibimos chicos y chicas a partir de los 7 años. No hay límite superior de edad: tenemos cursos con alumnos de 15, 30, 50 y más de 75 años compartiendo proyectos sonoros.'],
        ['categoria' => 'Inscripciones', 'pregunta' => '¿Qué documentación necesito para inscribirme?', 'respuesta' => 'Para completar la inscripción solo necesitás DNI (frente y dorso) del alumno (y del adulto responsable si es menor de 18 años), ficha médica simple de aptitud física y completar el formulario online.'],
        ['categoria' => 'Instrumentos y Préstamos', 'pregunta' => '¿Puedo llevarme el instrumento a mi casa?', 'respuesta' => 'Alumnos regulares con más de 2 meses de cursada pueden solicitar el préstamo rotativo a domicilio de instrumentos de menor porte (como violines infantiles y guitarras) mediante el convenio de comodato solidario.']
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A Todo Ritmo | Consultas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Carter+One&family=Mukta:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">

    <style>
        body {
            background-color: #FFFAE6;
            font-family: 'Mukta', Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #181818;
        }

        .contenedor-consultas-full {
            max-width: 1000px;
            margin: 40px auto 80px auto;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .encabezado-centro {
            text-align: center;
            margin-bottom: 40px;
        }
        .tag-atencion {
            font-size: 11px;
            font-weight: 800;
            color: #24AB58;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 5px;
            display: block;
        }
        .titulo-principal {
            font-family: 'Carter One', cursive, sans-serif;
            font-size: 38px;
            margin: 0 0 15px 0;
            line-height: 1.2;
        }
        .bajada-texto {
            font-size: 15px;
            color: #6c757d;
            max-width: 650px;
            margin: 0 auto;
        }

        .seccion-subtitulo {
            font-family: 'Carter One', cursive, sans-serif;
            font-size: 22px;
            margin-bottom: 5px;
        }
        .seccion-bajada {
            font-size: 13px;
            color: #6c757d;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .contenedor-filtros-faq {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 30px;
        }

        .btn-filtro-faq {
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-filtro-faq:hover {
            background-color: #f3f4f6;
            border-color: #181818;
            color: #181818;
        }
        .btn-filtro-faq.activo {
            background-color: #111111 !important;
            color: #ffcc00 !important;
            border-color: #111111 !important;
        }
        .btn-filtro-faq.activo:hover {
            background-color: #111111 !important;
            color: #ffcc00 !important;
        }

        .lista-acordeones {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin-bottom: 50px;
        }

        .faq-item {
            background-color: #ffffff;
            border: 2px solid #181818;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            box-shadow: 5px 5px 0px #181818;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .faq-header {
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            user-select: none;
            gap: 12px;
        }
        .faq-tag {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
            color: #24AB58;
        }

        .faq-pregunta {
            font-family: 'Carter One', cursive, sans-serif;
            font-size: 16px;
            font-weight: 400;
            margin: 0;
            color: #181818;
        }
        .faq-icon-arrow {
            width: 28px;
            height: 28px;
            min-width: 28px;
            background-color: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            transition: transform 0.2s, background-color 0.2s, color 0.2s;
        }
        .faq-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.2s ease-out;
            background-color: #fafafa;
        }

        .faq-item.abierto {
            border-color: #24AB58 !important;
            box-shadow: 5px 5px 0px #24AB58 !important;
            background-color: #f6fdf9;
        }

        .faq-text-inner {
            padding: 20px 24px;
            font-size: 15px;
            line-height: 1.6;
            color: #4b5563;
            border-top: 1px dashed #e5e7eb;
            background-color: #ffffff;
        }
        .faq-item.abierto .faq-icon-arrow {
            transform: rotate(180deg);
            background-color: #24AB58;
            color: #ffffff;
        }

        .grid-contacto-inferior {
            display: grid;
            grid-template-columns: 42% 55%;
            gap: 3%;
            align-items: start;
        }

        .tarjeta-contacto-negra {
            background-color: #111111;
            color: #ffffff;
            border: 2px solid #181818;
            border-radius: 24px;
            padding: 35px;
            box-shadow: 6px 6px 0px #F3D84F;
        }
        .tag-secretaria {
            color: #F3D84F;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 5px;
            display: block;
        }
        .titulo-contacto-izq {
            font-family: 'Carter One', cursive, sans-serif;
            font-size: 24px;
            margin: 0 0 15px 0;
        }
        .bajada-contacto-izq {
            font-size: 13px;
            color: #babcbe;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        .bloque-item-info {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            font-size: 13px;
            line-height: 1.5;
        }
        .bloque-item-info .icono-svg {
            font-size: 18px;
            margin-top: 2px;
        }
        .bloque-item-info strong {
            display: block;
            color: #ffffff;
            font-size: 14px;
            margin-bottom: 2px;
        }
        .bloque-item-info span {
            color: #babcbe;
        }

        .tarjeta-form-blanca {
            background-color: #ffffff;
            border: 2px solid #181818;
            border-radius: 24px;
            padding: 35px;
            box-shadow: 6px 6px 0px #181818;
        }
        .titulo-form-der {
            font-family: 'Carter One', cursive, sans-serif;
            font-size: 24px;
            margin: 0 0 5px 0;
        }
        .bajada-form-der {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 25px;
        }
        .fila-doble-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .grupo-campo {
            margin-bottom: 18px;
        }
        .grupo-campo label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #181818;
            margin-bottom: 6px;
        }
        .input-atr {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #181818;
            border-radius: 12px;
            background-color: #fffdf1;
            font-family: 'Mukta', sans-serif;
            font-size: 14px;
            box-sizing: border-box;
            transition: all 0.2s;
        }
        .input-atr:focus {
            border-color: #24AB58;
            outline: none;
            box-shadow: 0 0 0 3px rgba(36, 171, 88, 0.15);
        }
        select.input-atr {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23181818' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
            padding-right: 40px;
        }
        .btn-enviar-atr {
            width: 100%;
            background-color: #24AB58;
            color: #ffffff;
            border: 2px solid #181818;
            padding: 14px;
            font-size: 15px;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 3px 3px 0px #181818;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            font-family: 'Mukta', sans-serif;
            margin-top: 10px;
            transition: transform 0.1s, box-shadow 0.1s;
        }
        .btn-enviar-atr:active {
            transform: translate(2px, 2px);
            box-shadow: 1px 1px 0px #181818;
        }

        @media (max-width: 850px) {
            .grid-contacto-inferior {
                grid-template-columns: 1fr;
                gap: 24px;
            }
            .fila-doble-inputs {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="contenedor-consultas-full">

        <div class="encabezado-centro">
            <span class="tag-atencion">Atención a la comunidad</span>
            <h1 class="titulo-principal">Espacio de Consultas &<br>Preguntas Frecuentes</h1>
            <p class="bajada-texto">¿Tenés dudas sobre las clases, el banco de instrumentos, las cuotas o las sedes? Estamos para ayudarte.</p>
        </div>

        <section class="bloque-faqs">
            <h2 class="seccion-subtitulo">Preguntas Frecuentes</h2>
            <p class="seccion-bajada">Respuestas rápidas a las consultas más habituales de ingresantes y familias.</p>

            <div class="contenedor-filtros-faq">
                <button type="button" class="btn-filtro-faq activo">Todas las Categorías</button>
                <button type="button" class="btn-filtro-faq">Instrumentos y Préstamos</button>
                <button type="button" class="btn-filtro-faq">Inscripciones</button>
                <button type="button" class="btn-filtro-faq">Cuotas y Becas</button>
                <button type="button" class="btn-filtro-faq">Niveles y Edades</button>
            </div>

            <div class="lista-acordeones">
                <?php foreach ($faqs_db as $faq): ?>
                    <div class="faq-item" data-categoria="<?= htmlspecialchars($faq['categoria']) ?>">
                        <div class="faq-header">
                            <div>
                                <span class="faq-tag"><?= htmlspecialchars($faq['categoria']) ?></span>
                                <h3 class="faq-pregunta"><?= htmlspecialchars($faq['pregunta']) ?></h3>
                            </div>
                            <div class="faq-icon-arrow">▼</div>
                        </div>
                        <div class="faq-content">
                            <div class="faq-text-inner">
                                <?= htmlspecialchars($faq['respuesta']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="grid-contacto-inferior">
            <aside class="tarjeta-contacto-negra">
                <span class="tag-secretaria">Secretaría & Atención</span>
                <h2 class="titulo-contacto-izq">Contactate Directamente</h2>
                <p class="bajada-contacto-izq">Podés acercarte personalmente a nuestras sedes o comunicarte por nuestros canales virtuales:</p>

                <div class="bloque-item-info">
                    <div class="icono-svg">🟢</div>
                    <div>
                        <strong>WhatsApp de Admisiones</strong>
                        <span><?= htmlspecialchars($contacto_whatsapp) ?></span>
                    </div>
                </div>

                <div class="bloque-item-info">
                    <div class="icono-svg">✉️</div>
                    <div>
                        <strong>Correo Electrónico</strong>
                        <span><?= htmlspecialchars($contacto_email) ?></span>
                    </div>
                </div>

                <div class="bloque-item-info">
                    <div class="icono-svg">📍</div>
                    <div>
                        <strong>Sede San Telmo</strong>
                        <span>Defensa 842, CABA (Lun a Vie 14 a 21 hs | Sáb 09:30 a 13 hs)</span>
                    </div>
                </div>

                <div class="bloque-item-info">
                    <div class="icono-svg">📍</div>
                    <div>
                        <strong>Sede Almagro</strong>
                        <span>Av. Corrientes 4120, CABA (Lun a Vie 15 a 20:30 hs)</span>
                    </div>
                </div>
            </aside>

            <section class="tarjeta-form-blanca">
                <h2 class="titulo-form-der">Dejanos tu Mensaje</h2>
                <p class="bajada-form-der">Respondemos habitualmente en menos de 24 horas hábiles.</p>

                <form action="procesar_consulta.php" method="POST">
                    <div class="fila-doble-inputs">
                        <div class="grupo-campo">
                            <label>Nombre Completo *</label>
                            <input type="text" name="nombre" class="input-atr" placeholder="Tu nombre y apellido" required>
                        </div>
                        <div class="grupo-campo">
                            <label>Correo Electrónico *</label>
                            <input type="email" name="email" class="input-atr" placeholder="tu@email.com" required>
                        </div>
                    </div>

                    <div class="fila-doble-inputs">
                        <div class="grupo-campo">
                            <label>Teléfono (Opcional)</label>
                            <input type="text" name="telefono" class="input-atr" placeholder="11 5544-3322">
                        </div>
                        <div class="grupo-campo">
                            <label>Motivo de Consulta *</label>
                            <select name="motivo" class="input-atr" required>
                                <option value="talleres">Consulta sobre Talleres y Horarios</option>
                                <option value="instrumentos">Préstamo de Instrumentos</option>
                                <option value="cuotas">Cuotas, Pagos y Becas</option>
                                <option value="otro">Otro Motivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="grupo-campo">
                        <label>Tu Mensaje *</label>
                        <textarea name="mensaje" class="input-atr" style="height: 110px; resize: vertical;" placeholder="Escribinos tus dudas o comentarios..." required></textarea>
                    </div>

                    <button type="submit" class="btn-enviar-atr">
                        🚀 Enviar Consulta
                    </button>
                </form>
            </section>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const headers = document.querySelectorAll('.faq-header');
        headers.forEach(header => {
            header.addEventListener('click', function() {
                const item = this.parentElement;
                const content = this.nextElementSibling;

                if (item.classList.contains('abierto')) {
                    item.classList.remove('abierto');
                    if (content) content.style.maxHeight = null;
                } else {
                    document.querySelectorAll('.faq-item').forEach(el => {
                        el.classList.remove('abierto');
                        const innerContent = el.querySelector('.faq-content');
                        if (innerContent) innerContent.style.maxHeight = null;
                    });
                    item.classList.add('abierto');
                    if (content) content.style.maxHeight = content.scrollHeight + "px";
                }
            });
        });

        const botonesFiltro = document.querySelectorAll('.btn-filtro-faq');
        const faqItems = document.querySelectorAll('.faq-item');

        const normalizarTexto = (texto) => {
            return texto.trim()
                        .toLowerCase()
                        .normalize("NFD")
                        .replace(/[\u0300-\u036f]/g, "");
        };

        botonesFiltro.forEach(boton => {
            boton.addEventListener('click', function() {
                const botonActivoActual = document.querySelector('.btn-filtro-faq.activo');
                if (botonActivoActual) {
                    botonActivoActual.classList.remove('activo');
                }
                this.classList.add('activo');

                const categoriaSeleccionada = normalizarTexto(this.textContent);

                faqItems.forEach(item => {
                    item.classList.remove('abierto');
                    const content = item.querySelector('.faq-content');
                    if (content) content.style.maxHeight = null;

                    const categoriaAttr = item.getAttribute('data-categoria');
                    const categoriaItem = normalizarTexto(categoriaAttr || '');

                    if (categoriaSeleccionada === "todas las categorias" || categoriaItem === categoriaSeleccionada) {
                        item.style.display = "block";
                    } else {
                        item.style.display = "none";
                    }
                });
            });
        });
    });
    </script>
</body>
</html>
