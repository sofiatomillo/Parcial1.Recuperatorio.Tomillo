-- =========================================================================
-- PARCIAL 2 PROGRAMACIÓN/DESARROLLO WEB - ISEC
-- Caso 3: Escuela de Música "A Todo Ritmo"
-- Base de Datos Relacional Completa + Datos Iniciales (MySQL / phpMyAdmin)
-- =========================================================================

CREATE DATABASE IF NOT EXISTS a_todo_ritmo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE a_todo_ritmo;

-- 1. TABLA: SEDES
CREATE TABLE IF NOT EXISTS sedes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    barrio VARCHAR(100) NOT NULL,
    telefono VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    horarios_atencion VARCHAR(150) NOT NULL,
    colectivos VARCHAR(150) DEFAULT '',
    subte VARCHAR(150) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. TABLA: DOCENTES
CREATE TABLE IF NOT EXISTS docentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    especialidad VARCHAR(150) NOT NULL,
    instrumento_principal VARCHAR(100) NOT NULL,
    biografia TEXT NOT NULL,
    trayectoria_cv TEXT NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(50),
    imagen VARCHAR(255),
    foto_url VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. TABLA: TALLERES
CREATE TABLE IF NOT EXISTS talleres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    instrumento VARCHAR(100) NOT NULL,
    docente_id INT NOT NULL,
    nivel ENUM('Inicial / Sin experiencia', 'Intermedio', 'Avanzado', 'Ensamble') NOT NULL,
    edad_minima INT DEFAULT 7,
    dia_semana VARCHAR(50) NOT NULL,
    horario VARCHAR(50) NOT NULL,
    vacantes_totales INT NOT NULL,
    vacantes_disponibles INT NOT NULL,
    arancel_mensual DECIMAL(10, 2) NOT NULL,
    modalidad ENUM('Presencial', 'Online', 'Híbrida') DEFAULT 'Presencial',
    sede_id INT DEFAULT 1,
    descripcion TEXT NOT NULL,
    requisitos TEXT,
    instrumentos_prestamo_disponibles INT DEFAULT 5,
    destacado BOOLEAN DEFAULT FALSE,
    imagen_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (docente_id) REFERENCES docentes(id) ON DELETE RESTRICT,
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE SET NULL
);

-- 4. TABLA: EVENTOS
CREATE TABLE IF NOT EXISTS eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    tipo ENUM('Muestra de Alumnos', 'Concierto Abierto', 'Masterclass', 'Jam Session') NOT NULL,
    badge_color VARCHAR(20) DEFAULT 'yellow',
    fecha DATE NOT NULL,
    hora VARCHAR(20) NOT NULL,
    lugar VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    repertorio TEXT NOT NULL,
    precio_entrada DECIMAL(10, 2) DEFAULT 0.00,
    capacidad_total INT NOT NULL,
    entradas_disponibles INT NOT NULL,
    estado ENUM('proximo', 'previo', 'agotado') DEFAULT 'proximo',
    imagen_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. TABLA: INSCRIPCIONES
CREATE TABLE IF NOT EXISTS inscripciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_inscripcion VARCHAR(50) UNIQUE NOT NULL,
    taller_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefono VARCHAR(50) NOT NULL,
    edad INT NOT NULL,
    genero VARCHAR(50) DEFAULT 'NB',
    modalidad VARCHAR(50) NOT NULL,
    necesita_prestamo_instrumento BOOLEAN DEFAULT FALSE,
    posee_instrumento_propio BOOLEAN DEFAULT FALSE,
    sede_id INT DEFAULT 1,
    horario_seleccionado VARCHAR(100) NOT NULL,
    comentarios TEXT,
    estado ENUM('Pendiente de Confirmación', 'Confirmada', 'En Lista de Espera') DEFAULT 'Confirmada',
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (taller_id) REFERENCES talleres(id) ON DELETE CASCADE,
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE SET NULL
);

-- 6. TABLA: ENTRADAS_EVENTOS
CREATE TABLE IF NOT EXISTS entradas_eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_ticket VARCHAR(50) UNIQUE NOT NULL,
    evento_id INT NOT NULL,
    nombre_comprador VARCHAR(150) NOT NULL,
    email_comprador VARCHAR(150) NOT NULL,
    telefono_comprador VARCHAR(50),
    cantidad INT NOT NULL DEFAULT 1,
    total_abonado DECIMAL(10, 2) NOT NULL,
    metodo_pago VARCHAR(50) DEFAULT 'Transferencia',
    estado ENUM('Reservada', 'Abonada', 'Cancelada') DEFAULT 'Reservada',
    fecha_reserva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE
);

-- 7. TABLA: CONSULTAS
CREATE TABLE IF NOT EXISTS consultas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefono VARCHAR(50),
    motivo VARCHAR(150) NOT NULL,
    mensaje TEXT NOT NULL,
    leido BOOLEAN DEFAULT FALSE,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 8. TABLA: PREGUNTAS_FRECUENTES
CREATE TABLE IF NOT EXISTS preguntas_frecuentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria VARCHAR(100) NOT NULL,
    pregunta VARCHAR(255) NOT NULL,
    respuesta TEXT NOT NULL,
    orden INT DEFAULT 1
);

-- 9. TABLA: RESEÑAS
CREATE TABLE IF NOT EXISTS resenas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    autor VARCHAR(100) NOT NULL,
    rol VARCHAR(100) NOT NULL,
    calificacion INT CHECK(calificacion BETWEEN 1 AND 5),
    comentario TEXT NOT NULL,
    taller_nombre VARCHAR(100),
    fecha VARCHAR(50)
);

-- 10. TABLA: ADMINISTRADORES (Usuarios del Panel Admin)
CREATE TABLE IF NOT EXISTS administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(80) UNIQUE NOT NULL,
    nombre_completo VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('superadmin', 'admin') DEFAULT 'admin',
    activo BOOLEAN DEFAULT TRUE,
    ultimo_acceso TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 11. TABLA: CONFIGURACION_PAGINA (Contenido editable de la web)
CREATE TABLE IF NOT EXISTS configuracion_pagina (
    clave VARCHAR(100) PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    valor TEXT NOT NULL,
    seccion VARCHAR(60) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================================================================
-- DATOS INICIALES (SEED)
-- =========================================================================

INSERT INTO sedes (id, nombre, direccion, barrio, telefono, email, horarios_atencion, colectivos, subte) VALUES
(1, 'Sede Central - San Telmo', 'Defensa 842', 'San Telmo, CABA', '(011) 4361-9820', 'santelmo@atodoritmo.edu.ar', 'Lun a Vie 14:00 - 21:00 hs | Sáb 09:30 - 19:00 hs', 'Líneas 24, 29, 64, 86, 152', 'Línea C (Estación San Juan) y Línea E (Estación Bolívar)'),
(2, 'Anexo Comunitario - Almagro', 'Av. Corrientes 4120', 'Almagro, CABA', '(011) 4862-5510', 'almagro@atodoritmo.edu.ar', 'Lun a Vie 15:00 - 20:30 hs | Sáb 10:00 - 16:30 hs', 'Líneas 19, 26, 71, 92, 127, 168', 'Línea B (Estación Medrano)')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO docentes (id, nombre, apellido, especialidad, instrumento_principal, biografia, trayectoria_cv, email, telefono, imagen, foto_url, activo) VALUES
(1, 'Julián', 'Morales', 'Guitarra Criolla, Eléctrica y Ensamble', 'Guitarra', 'Guitarrista y arreglador con más de 14 años de experiencia en docencia popular. Cree fervientemente que la música es un derecho humano y no un privilegio.', 'Licenciado en Artes Musicales (UNA). Guitarrista sesionista en festivales nacionales como Cosquín y Baradero. Coordinador del programa de orquestas infanto-juveniles de Buenos Aires.', 'jmorales@atodoritmo.edu.ar', '(011) 15-5829-1022', 'img/docente-julian.jpg', 'img/docente-julian.jpg', 1),
(2, 'Camila', 'Santillán', 'Violín Clásico y Popular', 'Violín', 'Violinista formada en el método Suzuki y repertorio latinoamericano. Apasionada por desmitificar el violín como un instrumento inalcanzable.', 'Egresada del Conservatorio Superior de Música Manuel de Falla. Miembro de la Orquesta Típica Sans Souci y docente de violín infantil desde 2017.', 'csantillan@atodoritmo.edu.ar', '(011) 15-4991-3044', 'img/docente-camila.jpg', 'img/docente-camila.jpg', 1),
(3, 'Facundo', 'Pérez', 'Batería, Percusión y Ritmos Afroamericanos', 'Batería', 'Baterista y percusionista apasionado por el pulso, la cadencia y el juego en equipo. Especialista en desbloquear el miedo al tempo.', 'Formado con Pipi Piazzolla y quintetos de jazz porteño. Más de 10 discos grabados como baterista independiente. Docente en A Todo Ritmo desde sus inicios.', 'fperez@atodoritmo.edu.ar', '(011) 15-6677-8899', 'img/docente-facundo.jpg', 'img/docente-facundo.jpg', 1),
(4, 'Lucía', 'Herrera', 'Piano, Teclado y Armonía Funcional', 'Piano', 'Pianista y compositora. Su enfoque pedagógico fusiona la lectura accesible con la improvisación y el acompañamiento de canciones populares.', 'Profesorado Nacional de Música en Piano. Pianista de música de cámara y ensambles de jazz. Especialista en adaptaciones para alumnos principiantes de todas las edades.', 'lherrera@atodoritmo.edu.ar', '(011) 15-3211-9080', 'img/docente-lucia.jpg', 'img/docente-lucia.jpg', 1),
(5, 'Mariano', 'Díaz', 'Flauta Traversa y Vientos Andinos', 'Flauta', 'Flautista versátil que abarca desde la música clásica hasta el folclore norteño y el tango.', 'Conservatorio Juan José Castro. Integrante del ensamble Vientos del Sur. Tallerista comunitario en centros barriales durante 8 años.', 'mdiaz@atodoritmo.edu.ar', '(011) 15-7766-4433', 'img/docente-mariano.jpg', 'img/docente-mariano.jpg', 1),
(6, 'Valeria', 'Corvalán', 'Canto Popular, Técnica Vocal y Coro', 'Canto Popular', 'Cantante, fonoaudióloga y preparadora vocal. Fomenta el canto consciente, la confianza corporal y la expresión auténtica.', 'Lic. en Fonoaudiología (UBA). Cantante de música rioplatense con 3 producciones discográficas. Directora del Coro Comunitario A Todo Ritmo.', 'vcorvalan@atodoritmo.edu.ar', '(011) 15-8833-2211', 'img/docente-valeria.jpg', 'img/docente-valeria.jpg', 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO talleres (id, nombre, instrumento, docente_id, nivel, edad_minima, dia_semana, horario, vacantes_totales, vacantes_disponibles, arancel_mensual, modalidad, sede_id, descripcion, requisitos, instrumentos_prestamo_disponibles, destacado, imagen_url) VALUES
(1, 'Taller de Guitarra Criolla y Popular', 'Guitarra', 1, 'Inicial / Sin experiencia', 7, 'Martes y Jueves', '17:30 a 19:00 hs', 12, 4, 14500.00, 'Presencial', 1, 'Aprende tus primeros acordes, rasgueos folclóricos, ritmos de rock y canciones populares desde la primera clase sin necesidad de saber teoría previa.', 'No requiere conocimientos previos. Si no tenés guitarra, la escuela te presta una para las clases.', 6, 1, 'img/guitarra.jpg'),
(2, 'Taller de Violín: Primeras Cuerdas', 'Violín', 2, 'Inicial / Sin experiencia', 7, 'Lunes y Miércoles', '18:00 a 19:30 hs', 10, 3, 15500.00, 'Presencial', 1, 'Postura corporal relajada, agarre del arco, afinación y melodías sencillas. Clases dinámicas y grupales donde el juego y el oído son protagonistas.', 'Edad mínima 7 años. Contamos con violines de distintos tamaños (1/2, 3/4 y 4/4).', 5, 1, 'img/violin.jpg'),
(3, 'Taller de Batería y Pulso Rítmico', 'Batería', 3, 'Inicial / Sin experiencia', 8, 'Viernes', '16:00 a 18:00 hs', 8, 2, 16000.00, 'Presencial', 1, 'Coordinación motriz, rudimentos básicos, ritmos de rock, funk y cumbia. Práctica directa en sets acústicos y pads de práctica insonorizados.', 'Baquetas provistas para la primera clase.', 4, 1, 'img/bateria.jpg'),
(4, 'Taller de Canto Popular y Expresión Vocal', 'Canto', 6, 'Inicial / Sin experiencia', 10, 'Lunes y Jueves', '19:00 a 20:30 hs', 15, 5, 14000.00, 'Presencial', 2, 'Descubre tu voz natural sin forzar la garganta. Respiración, afinación, soltura escénica y ensamble a varias voces sobre repertorio popular.', 'Traer botella de agua. No se requiere experiencia previa.', 0, 0, 'img/microfono.jpg'),
(5, 'Gran Ensamble Comunitario A Todo Ritmo', 'Ensamble', 1, 'Ensamble', 10, 'Sábados', '15:00 a 18:00 hs', 20, 8, 9500.00, 'Presencial', 1, 'El corazón de la escuela. Guitarras, vientos, teclados, percusión y voces unidas en arreglos orquestales colectivos de rock nacional y folklore.', 'Conocimientos básicos de tu instrumento o cursar un taller inicial.', 6, 0, 'img/ensamble.jpg'),
(6, 'Taller de Piano y Teclado Creativo', 'Piano', 4, 'Inicial / Sin experiencia', 8, 'Miércoles y Viernes', '18:00 a 20:00 hs', 10, 5, 14500.00, 'Presencial', 2, 'Conoce el teclado, lectura fluida en clave de sol y fa sin presiones, armonía de canciones y acompañamiento.', 'Cada alumno dispone de un teclado en el aula.', 8, 1, 'img/piano.jpg'),
(7, 'Taller de Flauta Traversa y Melodías', 'Flauta', 5, 'Inicial / Sin experiencia', 9, 'Sábados', '10:30 a 12:30 hs', 10, 4, 15500.00, 'Presencial', 2, 'Emisión del sonido, respiración diafragmática, digitación ágil y repertorio de música de películas y folklore.', 'Préstamo disponible con boquilla higienizada.', 4, 0, 'img/flauta.jpg'),
(8, 'Taller de Teclado y Sintetizadores', 'Teclado', 4, 'Intermedio', 12, 'Jueves', '19:00 a 20:30 hs', 8, 3, 16000.00, 'Presencial', 2, 'Sonidos modernos, síntesis analógica básica, arreglos pop y pads ambientales para tocar en bandas.', 'Conocimientos básicos de acordes y cifrado americano.', 4, 0, 'img/teclado.jpg')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO eventos (id, titulo, tipo, badge_color, fecha, hora, lugar, descripcion, repertorio, precio_entrada, capacidad_total, entradas_disponibles, estado, imagen_url) VALUES
(1, 'Gran Muestra de Primavera: Todos al Escenario', 'Muestra de Alumnos', 'yellow', '2026-10-17', '18:30:00', 'Auditorio Sala Mayor - Sede San Telmo (Defensa 842)', 'Presentación en vivo de los ensambles de iniciales y avanzados. Los alumnos de guitarra, batería, violín, flauta y coro mostrarán los proyectos trabajados durante el cuatrimestre.', 'Rock nacional (Charly García, Spinetta, Los Redondos), candombe rioplatense, piezas clásicas adaptadas y composiciones colectivas de los alumnos.', 1500.00, 120, 38, 'proximo', 'muestra-primavera.jpg'),
(2, 'Concierto Solidario: Instrumentos para Todos', 'Concierto Abierto', 'orange', '2026-11-07', '20:00:00', 'Teatro Comunitario Almagro (Av. Corrientes 4120)', 'Concierto benéfico abierto a todo el barrio a cargo de los docentes de la escuela e invitados especiales para recaudar fondos para el banco de instrumentos gratuitos.', 'Jazz fusión, bossa nova, folklore argentino y canciones tradicionales en formato acústico.', 2000.00, 150, 64, 'proximo', 'concierto-solidario.jpg'),
(3, 'Masterclass Abierta: "El Ritmo en el Cuerpo"', 'Masterclass', 'blue', '2026-11-21', '11:00:00', 'Patio Abierto - Sede San Telmo', 'Taller participativo de percusión corporal y polirritmias dictado por Facundo Pérez. Para alumnos, familiares y vecinos de todas las edades.', 'Claves afroamericanas, chacarera, candombe y juegos de coordinación colectiva.', 0.00, 60, 19, 'proximo', 'masterclass-ritmo.jpg')
ON DUPLICATE KEY UPDATE titulo = VALUES(titulo);

INSERT INTO resenas (id, autor, rol, calificacion, comentario, taller_nombre, fecha) VALUES
(1, 'Esteban Méndez', 'Alumno — Guitarra', 5, '"Empecé de cero a los 42 años sin tener guitarra. Julián y la escuela me prestaron una y en 3 meses ya estaba tocando."', 'Taller de Guitarra Criolla y Popular', '2026-04-12'),
(2, 'Marcela V.', 'Familiar — Violín', 5, '"En otras escuelas nos pedían comprar un violín carísimo antes de empezar. Acá le adaptaron un violín 1/2 con paciencia admirable."', 'Taller de Violín: Primeras Cuerdas', '2026-05-03'),
(3, 'Lucas Benítez', 'Egresado — Batería', 5, '"La cuota es accesible y no sentís esa competencia de otros lados. Acá aprendés a escuchar al de al lado y disfrutar en grupo."', 'Taller de Batería y Pulso Rítmico', '2026-06-18')
ON DUPLICATE KEY UPDATE autor = VALUES(autor);

INSERT INTO preguntas_frecuentes (id, categoria, pregunta, respuesta, orden) VALUES
(1, 'Instrumentos y Préstamos', '¿Qué pasa si no tengo instrumento propio?', 'En "A Todo Ritmo" ningún alumno se queda afuera por no tener instrumento. Disponemos de un Banco Solidario de Instrumentos que te presta guitarras, violines, flautas y teclados para su uso en clase y para practicar en nuestras salas de estudio sin costo adicional.', 1),
(2, 'Inscripciones', '¿Hay examen de ingreso o audición eliminatoria?', '¡No! Rechazamos los filtros excluyentes. La escuela está abierta a personas desde los 7 años en adelante, sin importar si nunca antes tocaste un instrumento. El único requisito es tener curiosidad y ganas de aprender.', 2),
(3, 'Cuotas y Becas', '¿Qué facilidades de pago o becas ofrecen?', 'Mantenemos aranceles comunitarios y accesibles. Si tu familia atraviesa dificultades económicas, contamos con un programa de becas parciales y totales sustentadas por la cooperadora y eventos solidarios. Consultá en secretaría de manera confidencial.', 3),
(4, 'Niveles y Edades', '¿A partir de qué edad se puede ingresar? ¿Hay límite de edad?', 'Recibimos chicos y chicas a partir de los 7 años. No hay límite superior de edad: tenemos cursos con alumnos de 15, 30, 50 y más de 75 años compartiendo proyectos sonoros.', 4),
(5, 'Inscripciones', '¿Qué documentación necesito para inscribirme?', 'Para completar la inscripción solo necesitás DNI (frente y dorso) del alumno (y del adulto responsable si es menor de 18 años), ficha médica simple de aptitud física y completar el formulario online.', 5),
(6, 'Instrumentos y Préstamos', '¿Puedo llevarme el instrumento a mi casa?', 'Alumnos regulares con más de 2 meses de cursada pueden solicitar el préstamo rotativo a domicilio de instrumentos de menor porte (como violines infantiles y guitarras) mediante el convenio de comodato solidario.', 6)
ON DUPLICATE KEY UPDATE pregunta = VALUES(pregunta);

-- Usuario Administrador inicial (Usuario: admin | Clave por defecto: Admin2026!)
INSERT INTO administradores (id, usuario, nombre_completo, email, password_hash, rol, activo) VALUES
(1, 'admin', 'Administrador General', 'admin@atodoritmo.edu.ar', '$2b$12$twsVvrC.oATXtx.EptmQ9OB/R5Bh8PrB3MrH/vUIilDOakrr3ueVK', 'superadmin', 1)
ON DUPLICATE KEY UPDATE usuario = VALUES(usuario);

-- Textos editables de la página web
INSERT INTO configuracion_pagina (clave, titulo, valor, seccion) VALUES
('banner_superior_izq', 'Barra Superior (Anuncio Izquierdo)', '• Inscripciones Abiertas Ciclo Lectivo 2026 • Préstamo gratuito de instrumentos disponible', 'Header'),
('banner_superior_der', 'Barra Superior (Contacto Derecho)', 'Sedes: San Telmo & Almagro | WhatsApp: (011) 15-5829-1022', 'Header'),
('hero_etiqueta', 'Etiqueta del Hero (Home)', '✨ Inscripciones Ciclo Lectivo 2026', 'Inicio'),
('hero_titulo_1', 'Título Hero Línea 1 (Home)', 'Sentí la música,', 'Inicio'),
('hero_titulo_2', 'Título Hero Línea 2 (Destacado Amarillo)', 'descubrí tu ritmo.', 'Inicio'),
('hero_descripcion', 'Bajada del Hero (Home)', 'Una escuela de música inclusiva en el corazón del barrio. Clases de guitarra, violín, flauta, batería, piano y canto para todas las edades. ¿No tenés instrumento? Te lo prestamos nosotros.', 'Inicio'),
('hero_imagen', 'Imagen Destacada del Hero (Home, opcional)', '', 'Inicio'),
('nosotros_historia_titulo', 'Título Historia Institucional (Nosotros)', 'De un Taller Barrial a una Red de Formación Musical Abierta', 'Nosotros'),
('nosotros_historia_p1', 'Historia Párrafo 1 (Nosotros)', '"A Todo Ritmo" nació en 2018 impulsada por un grupo de músicos y docentes egresados de conservatorios públicos que veían cómo cientos de chicos, jóvenes y adultos quedaban afuera de la formación musical por no conseguir vacantes en instituciones saturadas o por no poder costear un instrumento propio.', 'Nosotros'),
('nosotros_historia_p2', 'Historia Párrafo 2 (Nosotros)', 'Comenzamos con una sala prestada en San Telmo y guitarras donadas. Hoy contamos con dos sedes activas, más de 200 alumnos regulares, un Banco Solidario con más de 60 instrumentos en comodato y un cuerpo docente apasionado por la enseñanza popular.', 'Nosotros'),
('nosotros_historia_imagen', 'Imagen Principal Historia (Nosotros)', 'img/microfono.jpg', 'Nosotros'),
('contacto_whatsapp', 'WhatsApp de Admisiones', '(011) 15-5829-1022 (Lunes a Viernes de 10 a 20 hs)', 'Contacto'),
('contacto_email', 'Email Institucional', 'consultas@atodoritmo.edu.ar', 'Contacto')
ON DUPLICATE KEY UPDATE titulo = VALUES(titulo);
