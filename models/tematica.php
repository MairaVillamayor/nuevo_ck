<?php
require_once __DIR__ . '/../config/conexion.php';

class Tematica {
    private $pdo;

    public function __construct() {
        $this->pdo = getConexion();
    }

    public function obtenerTodas() {
        $sql = "SELECT * FROM tematica";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM tematica WHERE ID_tematica = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertar($nombre) {
        $sql = "INSERT INTO tematica (tematica_nombre) VALUES (:nombre)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['nombre' => $nombre]);
    }

    public function actualizar($id, $nombre) {
        $sql = "UPDATE tematica SET tematica_nombre = :nombre WHERE ID_tematica = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['nombre' => $nombre, 'id' => $id]);
    }

    public function eliminarLogico($id) {
        $sql = "UPDATE tematica SET activo = 0 WHERE ID_tematica = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function eliminarFisico($id) {
        $sql = "DELETE FROM tematica WHERE ID_tematica = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}