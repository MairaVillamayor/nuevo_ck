<?php
require_once __DIR__ . '/../../config/conexion.php';
session_start();
header('Content-Type: application/json');

// Desactivar caché
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

$response = ['success' => false, 'error' => ''];

try {

    $raw_json = file_get_contents('php://input');
    if ($raw_json === false) {
        throw new Exception("Error al leer el JSON de la solicitud.");
    }

    $data = json_decode($raw_json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error al decodificar el JSON: " . json_last_error_msg());
    }

    $pedido_id    = isset($data['pedido_id']) ? (int)$data['pedido_id'] : 0;
    $RELA_estado  = isset($data['RELA_estado']) ? (int)$data['RELA_estado'] : 0;

    if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil_id'] != 1) {
        throw new Exception("No autorizado");
    }

    if (!$pedido_id || !$RELA_estado) {
        throw new Exception("Datos inválidos. Se recibió pedido_id=$pedido_id, RELA_estado=$RELA_estado");
    }

    $pdo = getConexion();
    $pdo->beginTransaction();

    /* =========================
        OBTENER ESTADO ACTUAL
    ==========================*/
    $stmt_actual = $pdo->prepare("
        SELECT e.estado_descri 
        FROM pedido p
        JOIN estado e ON p.RELA_estado = e.ID_estado
        WHERE p.ID_pedido = :pedido_id
    ");
    $stmt_actual->bindValue(':pedido_id', $pedido_id, PDO::PARAM_INT);
    $stmt_actual->execute();
    $estadoActual = $stmt_actual->fetchColumn();

    if (!$estadoActual) {
        throw new Exception("No se encontró el pedido o su estado actual");
    }

    $estadoActual = strtoupper($estadoActual);

    /* =========================
        OBTENER ESTADO NUEVO
    ==========================*/
    $stmt_nuevo = $pdo->prepare("SELECT estado_descri FROM estado WHERE ID_estado = :estado");
    $stmt_nuevo->bindValue(':estado', $RELA_estado, PDO::PARAM_INT);
    $stmt_nuevo->execute();
    $estadoNuevo = $stmt_nuevo->fetchColumn();

    if (!$estadoNuevo) {
        throw new Exception("Estado nuevo inválido");
    }

    $estadoNuevo = strtoupper($estadoNuevo);

    /* =========================
        REGLAS DE SEGURIDAD
    ==========================*/

    // Si está cancelado → no cambia más
    if ($estadoActual === 'CANCELADO') {
        throw new Exception("Un pedido CANCELADO no puede cambiar de estado");
    }

    // Si está finalizado → no cambia más
    if ($estadoActual === 'FINALIZADO') {
        throw new Exception("Un pedido FINALIZADO no puede cambiar de estado");
    }

    // Si está pagado → NO puede cancelarse
    if ($estadoActual === 'PAGADO' && $estadoNuevo === 'CANCELADO') {
        throw new Exception("Un pedido PAGADO no puede ser CANCELADO");
    }
    // ✅ Si está EN PROCESO → solo puede ir a FINALIZADO
    if ($estadoActual === 'EN PROCESO' && $estadoNuevo !== 'FINALIZADO') {
        throw new Exception("Un pedido EN PROCESO solo puede pasar a FINALIZADO");
    }


    /* =========================
        ACTUALIZAR ESTADO
    ==========================*/
    $stmt = $pdo->prepare("
        UPDATE pedido 
        SET RELA_estado = :estado 
        WHERE ID_pedido = :pedido_id
    ");
    $stmt->bindValue(':estado', $RELA_estado, PDO::PARAM_INT);
    $stmt->bindValue(':pedido_id', $pedido_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        throw new Exception("El pedido no se actualizó (puede que ya tenga ese estado)");
    }

    $pdo->commit();

    $response = [
        'success' => true,
        'estado'  => $estadoNuevo
    ];
} catch (Exception $e) {

    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $response['error'] = $e->getMessage();
}

echo json_encode($response);
