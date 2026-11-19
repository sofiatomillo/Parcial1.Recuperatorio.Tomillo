<?php
require_once __DIR__ . '/auth_admin.php';

$pagina_actual = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$cfg_header = isset($pdo) ? obtenerConfigPagina($pdo) : [];
$barra_izq = $cfg_header['banner_superior_izq'] ?? '• Inscripciones Abiertas Ciclo Lectivo 2026 • Préstamo gratuito de instrumentos disponible';
$barra_der = $cfg_header['banner_superior_der'] ?? 'Sedes: San Telmo & Almagro | WhatsApp: (011) 15-5829-1022';
$es_admin_logueado = adminLogueado();
?>
<header id="header-escuela" class="main-header">
    <div class="top-bar">
        <span><?= htmlspecialchars($barra_izq) ?></span>
        <span><?= htmlspecialchars($barra_der) ?></span>
    </div>

    <nav class="navbar">
        <a href="index.php" class="brand">
            <div class="logo-icon">🎵</div>
            <div class="brand-text">
                <h1>A Todo Ritmo</h1>
                <p>ESCUELA DE MÚSICA COMUNITARIA</p>
            </div>
        </a>
        <ul class="nav-links">
            <li><a href="index.php" class="<?= $pagina_actual === 'index.php' ? 'active' : '' ?>">Home</a></li>
            <li><a href="nosotros.php" class="<?= $pagina_actual === 'nosotros.php' ? 'active' : '' ?>">Nosotros</a></li>
            <li><a href="talleres.php" class="<?= $pagina_actual === 'talleres.php' ? 'active' : '' ?>">Talleres</a></li>
            <li><a href="eventos.php" class="<?= $pagina_actual === 'eventos.php' ? 'active' : '' ?>">Eventos</a></li>
            <li><a href="docentes.php" class="<?= $pagina_actual === 'docentes.php' ? 'active' : '' ?>">Docentes</a></li>
            <li><a href="inscripcion.php" class="<?= $pagina_actual === 'inscripcion.php' ? 'active' : '' ?>">Inscripción</a></li>
            <li><a href="consultas.php" class="<?= $pagina_actual === 'consultas.php' ? 'active' : '' ?>">Consultas</a></li>
        </ul>
        <div class="nav-actions">
            <a href="<?= $es_admin_logueado ? 'admin.php' : 'login.php' ?>" class="btn-sql" title="Acceso Administrador">
                <?= $es_admin_logueado ? '⚙ Panel Admin' : '🔐 Admin' ?>
            </a>
            <a href="sql.php" class="btn-sql">📋 SQL</a>
            <a href="inscripcion.php" class="btn-enroll">¡Inscribirme!</a>
        </div>
    </nav>
</header>
