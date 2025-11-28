<?php
require_once __DIR__ . '/../config/conexion.php';

function registrarAuditoria($accion, $tabla, $registro_id = null, $descripcion = '') {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Usuario que realiza la acción
    $usuario_id = $_SESSION['usuario_id'] ?? 0;

    // IP
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'DESCONOCIDA';

    try {
        $pdo = getConexion();

        $stmt = $pdo->prepare("INSERT INTO auditoria 
            (RELA_usuario, auditoria_accion, auditoria_tabla_afectada, registro_id, auditoria_descripcion, auditoria_ip)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $usuario_id,
            $accion,
            $tabla,
            $registro_id,
            $descripcion,
            $ip
        ]);

    } catch (PDOException $e) {
        echo "ERROR AUDITORÍA: " . $e->getMessage();
    }
}
