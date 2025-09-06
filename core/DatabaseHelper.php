<?php
/**
 * Helper para consultas de base de datos
 * Cake Party - Sistema de Gestión
 */

require_once __DIR__ . '/../config/conexion.php';

class DatabaseHelper {
    private $pdo;

    public function __construct() {
        $this->pdo = getConexion();
    }

    /**
     * Ejecutar consulta SELECT
     */
    public function select($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }

    /**
     * Ejecutar consulta SELECT y obtener una sola fila
     */
    public function selectOne($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }

    /**
     * Ejecutar consulta INSERT
     */
    public function insert($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }

    /**
     * Ejecutar consulta UPDATE
     */
    public function update($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }

    /**
     * Ejecutar consulta DELETE
     */
    public function delete($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }

    /**
     * Iniciar transacción
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     * Confirmar transacción
     */
    public function commit() {
        return $this->pdo->commit();
    }

    /**
     * Revertir transacción
     */
    public function rollback() {
        return $this->pdo->rollback();
    }

    /**
     * Verificar si existe un registro
     */
    public function exists($table, $column, $value) {
        $sql = "SELECT COUNT(*) as count FROM $table WHERE $column = ?";
        $result = $this->selectOne($sql, [$value]);
        return $result && $result['count'] > 0;
    }

    /**
     * Obtener todos los registros de una tabla
     */
    public function getAll($table, $orderBy = null) {
        $sql = "SELECT * FROM $table";
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        return $this->select($sql);
    }

    /**
     * Obtener registro por ID
     */
    public function getById($table, $id, $idColumn = 'id') {
        $sql = "SELECT * FROM $table WHERE $idColumn = ?";
        return $this->selectOne($sql, [$id]);
    }

    /**
     * Manejar errores de base de datos
     */
    private function handleError($e) {
        if (ENVIRONMENT === 'production') {
            error_log("Database Error: " . $e->getMessage());
            throw new Exception("Error en la base de datos");
        } else {
            throw $e;
        }
    }

    /**
     * Sanitizar entrada de usuario
     */
    public function sanitize($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validar email
     */
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validar DNI (formato argentino)
     */
    public function validateDNI($dni) {
        return preg_match('/^\d{7,8}$/', $dni);
    }
}
?> 