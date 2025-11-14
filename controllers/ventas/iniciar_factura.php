<?php
require_once("../../config/conexion.php");

header('Content-Type: application/json; charset=utf-8');

try {
    // Obtener conexión PDO real
    $pdo = getConexion(); // o $conexion->getConexion() si usas clase

    $persona_documento = $_POST["persona_documento"] ?? null;
    $persona_nombre    = $_POST["persona_nombre"] ?? null;
    $persona_apellido  = $_POST["persona_apellido"] ?? null;
    $RELA_estado_factura = 1;

    if (empty($persona_documento)) {
        echo json_encode(['error' => 'No se recibió el documento del cliente.']);
        exit;
    }

    // Buscar cliente
    $stmt = $pdo->prepare("SELECT ID_persona FROM persona WHERE persona_documento = :doc");
    $stmt->bindParam(':doc', $persona_documento, PDO::PARAM_STR);
    $stmt->execute();
    $persona = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($persona) {
        $id_persona = $persona['ID_persona'];
    } else {
        // Crear cliente si no existe
        $stmt_insert = $pdo->prepare("
            INSERT INTO persona (persona_nombre, persona_apellido, persona_documento)
            VALUES (:nombre, :apellido, :documento)
        ");
        $stmt_insert->execute([
            ':nombre' => $persona_nombre,
            ':apellido' => $persona_apellido,
            ':documento' => $persona_documento
        ]);
        $id_persona = $pdo->lastInsertId();
    }

    // Crear factura
    $stmt_factura = $pdo->prepare("
        INSERT INTO factura (RELA_persona, RELA_estado_factura)
        VALUES (:RELA_persona, :RELA_estado_factura)
    ");
    $stmt_factura->execute([
        ':RELA_persona' => $id_persona,
        ':RELA_estado_factura' => $RELA_estado_factura
    ]);

    $id_factura = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'mensaje' => 'Factura creada correctamente',
        'id_factura' => $id_factura,
        'id_persona' => $id_persona
    ]);

} catch (PDOException $e) {
    error_log("Error en iniciar_factura.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error al iniciar la factura: ' . $e->getMessage()]);
}
