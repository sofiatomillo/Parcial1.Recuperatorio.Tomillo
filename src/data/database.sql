-- =========================================================================
-- PARCIAL 2 PROGRAMACIÓN/DESARROLLO WEB - ISEC
-- Caso 3: Escuela de Música "A Todo Ritmo"
-- Estructura de Base de Datos Relacional y Consultas
-- =========================================================================

CREATE DATABASE IF NOT EXISTS a_todo_ritmo;
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
    foto_url VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. TABLA: TALLERES (Cursos de instrumentos)
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

-- 4. TABLA: EVENTOS (Muestras, conciertos y recitales mensuales)
CREATE TABLE IF NOT EXISTS eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    tipo ENUM('Muestra de Alumnos', 'Concierto Abierto', 'Masterclass', 'Jam Session') NOT NULL,
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

-- 5. TABLA: INSCRIPCIONES (Matriculaciones de alumnos)
CREATE TABLE IF NOT EXISTS inscripciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_inscripcion VARCHAR(50) UNIQUE NOT NULL,
    taller_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefono VARCHAR(50) NOT NULL,
    edad INT NOT NULL,
    genero ENUM('Masc', 'Fem', 'NB', 'Otro', 'Prefiero no decir') DEFAULT 'NB',
    modalidad ENUM('Presencial', 'Online', 'Híbrida') NOT NULL,
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

-- 6. TABLA: ENTRADAS_EVENTOS (Reservas / Compra de entradas)
CREATE TABLE IF NOT EXISTS entradas_eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_ticket VARCHAR(50) UNIQUE NOT NULL,
    evento_id INT NOT NULL,
    nombre_comprador VARCHAR(150) NOT NULL,
    email_comprador VARCHAR(150) NOT NULL,
    telefono_comprador VARCHAR(50),
    cantidad INT NOT NULL DEFAULT 1,
    total_abonado DECIMAL(10, 2) NOT NULL,
    metodo_pago ENUM('Efectivo en Puerta', 'Transferencia', 'Beca Comunitaria') DEFAULT 'Transferencia',
    estado ENUM('Reservada', 'Abonada', 'Cancelada') DEFAULT 'Reservada',
    fecha_reserva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE
);

-- 7. TABLA: CONSULTAS (Mensajes de contacto)
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

-- 8. TABLA: PREGUNTAS_FRECUENTES (FAQ)
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

-- =========================================================================
-- CONSULTAS PRINCIPALES DEL SISTEMA (QUERIES)
-- =========================================================================

-- Q1: Obtener todos los talleres activos con nombre y especialidad del docente
-- SELECT t.id, t.nombre, t.instrumento, t.nivel, t.horario, t.dia_semana, 
--        t.vacantes_disponibles, t.arancel_mensual, t.modalidad,
--        CONCAT(d.nombre, ' ', d.apellido) AS docente_nombre, d.foto_url
-- FROM talleres t
-- JOIN docentes d ON t.docente_id = d.id
-- WHERE t.vacantes_disponibles > 0
-- ORDER BY t.instrumento ASC;

-- Q2: Registrar una nueva inscripción y decrementar vacantes
-- INSERT INTO inscripciones (codigo_inscripcion, taller_id, nombre, apellido, email, telefono, edad, modalidad, necesita_prestamo_instrumento, horario_seleccionado)
-- VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
-- UPDATE talleres SET vacantes_disponibles = vacantes_disponibles - 1 WHERE id = ?;

-- Q3: Consultar eventos próximos con entradas disponibles
-- SELECT * FROM eventos WHERE estado = 'proximo' AND entradas_disponibles > 0 ORDER BY fecha ASC;

-- Q4: Reservar entradas para un evento
-- INSERT INTO entradas_eventos (codigo_ticket, evento_id, nombre_comprador, email_comprador, cantidad, total_abonado)
-- VALUES (?, ?, ?, ?, ?, ?);
-- UPDATE eventos SET entradas_disponibles = entradas_disponibles - ? WHERE id = ?;
