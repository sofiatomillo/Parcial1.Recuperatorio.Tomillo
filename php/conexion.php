<?php
/**
 * Conexión a la Base de Datos MySQL con PDO
 * Proyecto: Escuela de Música "A Todo Ritmo"
 * Parcial 2 - ISEC - Sofía Tomillo
 */

$host = '127.0.0.1';
$db   = 'a_todo_ritmo';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Si la conexión falla, se detalla el error
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>
