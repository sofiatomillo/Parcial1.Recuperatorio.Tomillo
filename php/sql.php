<?php
require_once __DIR__ . '/conexion.php';

$tablas = [
    'sedes', 'docentes', 'talleres', 'eventos', 'inscripciones',
    'entradas_eventos', 'consultas', 'preguntas_frecuentes',
    'resenas', 'administradores', 'configuracion_pagina'
];
$conteos = [];

foreach ($tablas as $tabla) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$tabla`");
        $conteos[$tabla] = (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        $conteos[$tabla] = '—';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lógica SQL | Escuela de Música A Todo Ritmo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Carter+One&family=Mukta:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <style>
        .sql-container { max-width: 1050px; margin: 40px auto 70px auto; padding: 0 20px; }
        .sql-card { background: #FFFDF5; border: 2px solid #181818; border-radius: 20px; padding: 26px; box-shadow: 5px 5px 0px #181818; margin-bottom: 24px; }
        .sql-card h3 { font-family: 'Carter One', cursive; font-size: 20px; margin-bottom: 12px; color: #181818; }
        .sql-code { background: #181818; color: #F3D84F; padding: 16px; border-radius: 12px; font-family: monospace; font-size: 13px; overflow-x: auto; line-height: 1.5; }
        .tabla-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
        .tabla-pill { background: #fff; border: 2px solid #181818; border-radius: 12px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; font-weight: 700; }
        .tabla-pill span { background: #24AB58; color: #fff; padding: 2px 10px; border-radius: 20px; font-size: 12px; }
        @media (max-width: 768px) { .tabla-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="sql-container">
        <div style="text-align:center; margin-bottom:32px;">
            <span style="color:#24AB58; font-weight:800; font-size:12px; text-transform:uppercase;">Arquitectura Relacional MySQL + PDO</span>
            <h2 class="seccion-titulo" style="margin-top:6px;">Lógica SQL del Sistema</h2>
            <p style="color:#555; margin-top:6px;">Estado de las tablas relacionales y consultas principales ejecutadas en PHP PDO.</p>
        </div>

        <section class="sql-card">
            <h3>Estado en Vivo de la Base de Datos (<code>a_todo_ritmo</code>)</h3>
            <div class="tabla-grid">
                <?php foreach ($conteos as $nombre => $cant): ?>
                    <div class="tabla-pill">
                        <code><?= htmlspecialchars($nombre) ?></code>
                        <span><?= htmlspecialchars((string)$cant) ?> reg.</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="sql-card">
            <h3>Q1 · Listado de Talleres con Docente y Sede (<code>INNER JOIN</code>)</h3>
            <pre class="sql-code">SELECT t.*, CONCAT(d.nombre, ' ', d.apellido) AS docente_nombre, s.nombre AS sede_nombre
FROM talleres t
INNER JOIN docentes d ON t.docente_id = d.id
LEFT JOIN sedes s ON t.sede_id = s.id
ORDER BY t.destacado DESC, t.id ASC;</pre>
        </section>

        <section class="sql-card">
            <h3>Q2 · Transacción ACID de Inscripción y Descuento de Vacante</h3>
            <pre class="sql-code">INSERT INTO inscripciones (codigo_inscripcion, taller_id, nombre, apellido, email, telefono, edad, genero, modalidad, necesita_prestamo_instrumento, posee_instrumento_propio, sede_id, horario_seleccionado, comentarios)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);

UPDATE talleres SET vacantes_disponibles = vacantes_disponibles - 1
WHERE id = ? AND vacantes_disponibles &gt; 0;</pre>
        </section>

        <section class="sql-card">
            <h3>Q3 · Autenticación de Administradores y Gestión del Panel</h3>
            <pre class="sql-code">SELECT id, usuario, nombre_completo, email, password_hash, rol, activo
FROM administradores
WHERE (usuario = ? OR email = ?)
LIMIT 1;</pre>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
