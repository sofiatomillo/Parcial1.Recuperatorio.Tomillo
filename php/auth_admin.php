<?php
/**
 * auth_admin.php - Gestión de sesión segura, CSRF y configuración editable de página
 * Escuela de Música "A Todo Ritmo"
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function adminLogueado(): bool {
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_usuario']);
}

function requerirAdmin(): void {
    if (!adminLogueado()) {
        header('Location: login.php');
        exit;
    }
}

function generarCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verificarCsrfToken(?string $token): bool {
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Obtiene el mapa clave => valor de configuracion_pagina para usar en las vistas públicas.
 * Nunca lanza excepción si la tabla aún no fue creada.
 */
function obtenerConfigPagina(PDO $pdo): array {
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $cache = [];
    try {
        $stmt = $pdo->query("SELECT clave, valor FROM configuracion_pagina");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $cache[$fila['clave']] = $fila['valor'];
        }
    } catch (PDOException $e) {
        $cache = [];
    }
    return $cache;
}
