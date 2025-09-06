<?php
/**
 * Configuración de la base de datos
 * Cake Party - Sistema de Gestión
 */

// Configuración del entorno
define('ENVIRONMENT', 'development'); // development, production

// Configuración de la base de datos según el entorno
if (ENVIRONMENT === 'production') {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'cake_party_prod');
    define('DB_USER', 'cake_user');
    define('DB_PASS', 'secure_password_here');
} else {
    // Configuración para desarrollo
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'cake_party');
    define('DB_USER', 'root');
    define('DB_PASS', 'Maira_2023');
}

// Configuración general
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');

// Configuración de timezone
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Configuración de errores según entorno
if (ENVIRONMENT === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
?> 