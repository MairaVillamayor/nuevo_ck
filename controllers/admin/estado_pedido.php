<?php
require_once __DIR__ . '/../../config/conexion.php';
session_start();
header('Content-Type: application/json');

// Desactivar la caché para la depuración
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

$response = ['success' => false, 'error' => ''];

try {
    // Loguear el JSON recibido
    $raw_json = file_get_contents('php://input');
    if ($raw_json === false) {
        throw new Exception("Error al leer el JSON de la solicitud.");
    }
    
    $data = json_decode($raw_json, true);

    // Validar si la decodificación fue exitosa
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error al decodificar el JSON: " . json_last_error_msg());
    }

    $pedido_id = isset($data['pedido_id']) ? (int)$data['pedido_id'] : 0;
    $RELA_estado = isset($data['RELA_estado']) ? (int)$data['RELA_estado'] : 0;

    if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil_id'] != 1) {
        throw new Exception("No autorizado");
    }

    if (!$pedido_id || !$RELA_estado) {
        throw new Exception("Datos de entrada inválidos. Recibido: pedido_id=$pedido_id, RELA_estado=$RELA_estado");
    }

    // Conectar a la base de datos
    $pdo = getConexion();

    // Iniciar una transacción
    $pdo->beginTransaction();

    // Preparar y ejecutar la consulta de actualización
    $stmt = $pdo->prepare("UPDATE pedido SET RELA_estado = :estado WHERE ID_pedido = :pedido_id");
    $stmt->bindValue(':estado', $RELA_estado, PDO::PARAM_INT);
    $stmt->bindValue(':pedido_id', $pedido_id, PDO::PARAM_INT);
    $stmt->execute();

    // Verificar si se actualizó alguna fila
    if ($stmt->rowCount() === 0) {
        throw new Exception("El pedido con ID $pedido_id no existe o ya tiene el estado $RELA_estado.");
    }

    // Obtener el nombre del estado para la respuesta
    $stmt2 = $pdo->prepare("SELECT estado_descri FROM estado WHERE ID_estado = :estado");
    $stmt2->bindValue(':estado', $RELA_estado, PDO::PARAM_INT);
    $stmt2->execute();
    $estado_nombre = $stmt2->fetchColumn();

    if ($estado_nombre === false) {
        throw new Exception("El ID de estado $RELA_estado no es válido en la tabla de estados.");
    }

    // Si todo fue bien, confirmar la transacción y preparar la respuesta
    $pdo->commit();
    $response = ['success' => true, 'estado' => $estado_nombre];

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response['error'] = 'Error: ' . $e->getMessage();
}

// Imprimir el resultado para que lo veas en la pestaña "Response"
echo json_encode($response);