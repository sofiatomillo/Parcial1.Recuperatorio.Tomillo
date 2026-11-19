<?php 
require_once __DIR__ . '/conexion.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A Todo Ritmo | Consultas</title>
    <link rel="stylesheet" href="index.css">
    
    <style>
        /* --- ESTILOS GENERALES Y FUENTES --- */
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

        /* --- ENCABEZADOS PRINCIPALES --- */
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

        /* --- FILTROS DE CATEGORÍAS --- */
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
            border: 1px solid #d1d5db; /* Borde sutil gris */
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            cursor: pointer;
            transition: all 0.2s ease; /* Suaviza el cambio de color */
        }

        /* Efecto al pasar el mouse por encima (Hover) */
        .btn-filtro-faq:hover {
            background-color: #f3f4f6; /* Gris muy claro al posarse */
            border-color: #181818;     /* El borde se vuelve oscuro */
            color: #181818;            /* El texto se oscurece */
        }
        /* Estado Activo (Botón Negro) */
        .btn-filtro-faq.activo {
            background-color: #111111 !important;
            color: #ffcc00 !important; /* Texto amarillo idéntico a tu referencia */
            border-color: #111111 !important;
        }
        /* Evita que el hover altere el botón que ya está seleccionado */
        .btn-filtro-faq.activo:hover {
            background-color: #111111 !important;
            color: #ffcc00 !important;
        }

        /* --- ACORDEONES NEO-BRUTALISTAS (FAQ) --- */
        
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
        }
        .faq-tag {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
        }

        .tag-instrumentos { color: #24AB58; }
        .tag-inscripciones { color: #24AB58; }
        .tag-cuotas { color: #24AB58; }
        .tag-niveles { color: #24AB58; }

        .faq-pregunta {
            font-family: 'Carter One';
            font-size: 16px;
            font-weight: 800;
            margin: 0;
            color: #181818;
        }
        .faq-icon-arrow {
            width: 28px;
            height: 28px;
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
            background-color: #f6fdf9; /* Fondo sutilmente verdoso para la cabecera */
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

        
        /* --- SECCIÓN CONTACTO (DOS COLUMNAS) --- */
        .grid-contacto-inferior {
            display: grid;
            grid-template-columns: 42% 55%;
            gap: 3%;
            align-items: start;
        }

        /* Tarjeta Izquierda Oscura */
        .tarjeta-contacto-negra {
            background-color: #111111;
            color: #ffffff;
            border: 2px solid #181818;
            border-radius: 24px;
            padding: 35px;
            box-shadow: 6px 6px 0px #F3D84F; /* Sombra amarilla del diseño */
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

        /* Tarjeta Derecha Formulario Blanca */
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
            background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://w3.org' viewBox='0 0 24 24' fill='none' stroke='%23181818' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
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
    </style>
</head>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!-- HEADER -->
    <?php include 'header.php'; ?>
    
    <div class="contenedor-consultas-full">
    
        <!-- SECCIÓN SUPERIOR: TÍTULOS -->
        <div class="encabezado-centro">
            <span class="tag-atencion">Atención a la comunidad</span>
            <h1 class="titulo-principal">Espacio de Consultas &<br>Preguntas Frecuentes</h1>
            <p class="bajada-texto">¿Tenés dudas sobre las clases, el banco de instrumentos, las cuotas o las sedes? Estamos para ayudarte.</p>
        </div>
    
        <!-- SECCIÓN INTERMEDIA: PREGUNTAS FRECUENTES (FAQ) -->
        <section class="bloque-faqs">
            <h2 class="seccion-subtitulo">Preguntas Frecuentes</h2>
            <p class="seccion-bajada">Respuestas rápidas a las consultas más habituales de ingresantes y familias.</p>
    
            <!-- Filtros -->
            <div class="contenedor-filtros-faq">
                <button class="btn-filtro-faq activo">Todas las Categorías</button>
                <button class="btn-filtro-faq">Instrumentos y Préstamos</button>
                <button class="btn-filtro-faq">Inscripciones</button>
                <button class="btn-filtro-faq">Cuotas y Becas</button>
                <button class="btn-filtro-faq">Niveles y Edades</button>
            </div>
    
            <!-- Lista de preguntas -->
            <div class="lista-acordeones">
                <!-- PRIMERA CONSULTA: INSTRUMENTOS -->
                <div class="faq-item" data-categoria="Instrumentos y Préstamos"> 
                    <div class="faq-item">
                        <div class="faq-header">
                            <div>
                                <span class="faq-tag tag-instrumentos">Instrumentos y Préstamos</span>
                                <h3 class="faq-pregunta">¿Qué pasa si no tengo instrumento propio?</h3>
                            </div>
                            <div class="faq-icon-arrow">▼</div>
                        </div>
                        <div class="faq-content">
                            <div class="faq-text-inner">
                                En "A Todo Ritmo" ningún alumno se queda afuera por no tener instrumento. Disponemos de un Banco Solidario de Instrumentos que te presta guitarras, violines, flautas y teclados para su uso en clase y para practicar en nuestras salas de estudio sin costo adicional.
                            </div>
                        </div>
                    </div>
                </div>
                <!-- SEGUNDA CONSULTA: EXAMEN DE INGRESO -->
                 <div class="faq-item" data-categoria="Inscripciones">
                     <div class="faq-item">
                         <div class="faq-header">
                             <div>
                                 <span class="faq-tag tag-inscripciones">Inscripciones</span>
                                 <h3 class="faq-pregunta">¿Hay examen de ingreso o audición eliminatoria?</h3>
                             </div>
                             <div class="faq-icon-arrow">▼</div>
                         </div>
                         <div class="faq-content">
                             <div class="faq-text-inner">
                                 ¡No! Rechazamos los filtros excluyentes. La escuela está abierta a personas desde los 7 años en adelante, sin importar si nunca antes tocaste un instrumento. El único requisito es tener curiosidad y ganas de aprender.
                             </div>
                         </div>
                     </div>
                </div>
                <!-- TERCERA CONSULTA: BECAS -->
                 <div class="faq-item" data-categoria="Cuotas y Becas"> 
                     <div class="faq-item">
                         <div class="faq-header">
                             <div>
                                 <span class="faq-tag tag-cuotas">Cuotas y Becas</span>
                                 <h3 class="faq-pregunta">¿Qué facilidades de pago o becas ofrecen?</h3>
                             </div>
                             <div class="faq-icon-arrow">▼</div>
                         </div>
                         <div class="faq-content">
                             <div class="faq-text-inner">
                                 Mantenemos aranceles comunitarios y accesibles. Si tu familia atraviesa dificultades económicas, contamos con un programa de becas parciales y totales sustentadas por la cooperadora y eventos solidarios. Consulta en secretaría de manera confidencial.
                             </div>
                         </div>
                     </div>
                </div>
                 <!-- CUARTA CONSULTA: EDADES -->
                <div class="faq-item" data-categoria="Niveles y Edades"> 
                    <div class="faq-item">
                        <div class="faq-header">
                            <div>
                                <span class="faq-tag tag-niveles">Niveles y Edades</span>
                                <h3 class="faq-pregunta">¿A partir de qué edad se puede ingresar? ¿Hay límite de edad?</h3>
                            </div>
                            <div class="faq-icon-arrow">▼</div>
                        </div>
                        <div class="faq-content">
                            <div class="faq-text-inner">
                                Recibimos chicos y chicas a partir de los 7 años. No hay límite superior de edad: tenemos cursos con alumnos de 15, 30, 50 y más de 75 años compartiendo proyectos sonoros.
                            </div>
                        </div>
                    </div>
                </div>
                 <!-- QUINTA CONSULTA: INSCRIPCIONES -->
                  <div class="faq-item" data-categoria="Inscripciones"> 
                      <div class="faq-item">
                          <div class="faq-header">
                              <div>
                                  <span class="faq-tag tag-inscripciones">Inscripciones</span>
                                  <h3 class="faq-pregunta">¿Qué documentación necesito para inscribirme?</h3>
                              </div>
                              <div class="faq-icon-arrow">▼</div>
                          </div>
                          <div class="faq-content">
                              <div class="faq-text-inner">
                                  Para completar la inscripción solo necesitas DNI (frente y dorso) del alumno (y del adulto responsable si es menor de 18 años), ficha médica simple de aptitud física y completar el formulario online.
                              </div>
                          </div>
                      </div>
                </div>

            <!-- SEXTA CONSULTA: INSTRUMENTOS Y PRÉSTAMOS -->
             <div class="faq-item" data-categoria="Instrumentos y Préstamos"> 
                 <div class="faq-item">
                     <div class="faq-header">
                         <div>
                             <span class="faq-tag tag-instrumentos">Instrumentos y Préstamos</span>
                             <h3 class="faq-pregunta">¿Puedo llevarme el instrumento a mi casa?</h3>
                         </div>
                         <div class="faq-icon-arrow">▼</div>
                     </div>
                     <div class="faq-content">
                         <div class="faq-text-inner">
                             Alumnos regulares con más de 2 meses de cursada pueden solicitar el préstamo rotativo a domicilio de instrumentos de menor porte (como violines infantiles y guitarras) mediante el convenio de comodato solidario.
                         </div>
                     </div>
                 </div>
            </div>
        </section>
    
        <!-- SECCIÓN CONTACTO (DOS COLUMNAS) -->
        <div class="grid-contacto-inferior">
            
            <!-- Tarjeta Izquierda Oscura -->
            <aside class="tarjeta-contacto-negra">
                <span class="tag-secretaria">Secretaría & Atención</span>
                <h2 class="titulo-contacto-izq">Contactate Directamente</h2>
                <p class="bajada-contacto-izq">Podés acercarte personalmente a nuestras sedes o comunicarte por nuestros canales virtuales:</p>
    
                <div class="bloque-item-info">
                    <div class="icono-svg">🟢</div>
                    <div>
                        <strong>WhatsApp de Admisiones</strong>
                        <span>(011) 15-5829-1022 (Lunes a Viernes de 10 a 20 hs)</span>
                    </div>
                </div>
    
                <div class="bloque-item-info">
                    <div class="icono-svg">✉️</div>
                    <div>
                        <strong>Correo Electrónico</strong>
                        <span>consultas@atodoritmo.edu.ar</span>
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
    
            <!-- Tarjeta Derecha Formulario Blanca -->
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
    </div> <!-- Cierre definitivo del contenedor-consultas-full -->
    
    <!-- FOOTER -->
    <?php include 'footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
    // ==========================================
    // 1. LÓGICA DE LOS ACORDEONES (DESPLIEGUE)
    // ==========================================
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

    // ==========================================
    // 2. LÓGICA DE FILTROS DE CATEGORÍAS
    // ==========================================
     const botonesFiltro = document.querySelectorAll('.btn-filtro-faq');
    const faqItems = document.querySelectorAll('.faq-item');

    // Función interna para remover acentos y dejar el texto plano
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
                // Forzamos el cierre del acordeón para evitar desbordes visuales
                item.classList.remove('abierto');
                const content = item.querySelector('.faq-content');
                if (content) content.style.maxHeight = null;

                const categoriaAttr = item.getAttribute('data-categoria');
                const categoriaItem = normalizarTexto(categoriaAttr || '');

                // Buscamos de forma ascendente cualquier contenedor que envuelva la tarjeta (el div exterior)
                const contenedorBloqueCompleto = item.closest('div:not(.faq-item):not(.faq-header)');

                if (categoriaSeleccionada === "todas las categorias" || categoriaItem === categoriaSeleccionada) {
                    item.classList.remove('oculto-filtrado');
                    item.style.display = "block"; // Fuerza el renderizado normal
                } 
                
                else {
                    item.classList.add('oculto-filtrado');
                    item.style.display = "none";  // Apaga el elemento por completo
                }
            });
        });
    });

});
</script>



</body>
</html>


