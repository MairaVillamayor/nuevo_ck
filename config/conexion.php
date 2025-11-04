<?php
/**
 * Configuración de conexión a la base de datos
 * Cake Party - Sistema de Gestión
 */

// Incluir configuración
require_once __DIR__ . '/database.php';

class Conexion {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (ENVIRONMENT === 'production') {
                die("Error de conexión a la base de datos. Contacte al administrador.");
            } else {
                die("Error de conexión: " . $e->getMessage());
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    // Método para compatibilidad con código existente que usa mysqli
    public function query($sql) {
        return $this->pdo->query($sql);
    }

    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }

    public function real_escape_string($string) {
        return substr($this->pdo->quote($string), 1, -1);
    }

    public function insert_id() {
        return $this->pdo->lastInsertId();
    }

    public function begin_transaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollback() {
        return $this->pdo->rollback();
    }

    public function close() {
        $this->pdo = null;
    }

    // Prevenir clonación
    private function __clone() {}

    // Prevenir deserialización
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }

    
}

// Función helper para obtener conexión
function getConexion() {
    return Conexion::getInstance()->getConnection();
}

// Variable global para compatibilidad con código existente
$conexion = Conexion::getInstance();
?>