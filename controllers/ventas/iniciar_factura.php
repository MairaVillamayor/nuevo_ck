<?php
require_once("../../config/conexion.php");
require_once("../../models/caja/caja.php");

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = getConexion(); 

    $persona_documento = $_POST["persona_documento"] ?? null;
    $persona_nombre    = $_POST["persona_nombre"] ?? null;
    $persona_apellido  = $_POST["persona_apellido"] ?? null;
    
    // Estado 1 = Pendiente/Pagado (según tu lógica, asegúrate que 1 sea el correcto)
    $RELA_estado_factura = 1; 

    // ---------------------------------------------------------
    // 1. OBTENCIÓN DE CAJA (Esto lo tenías bien, pero agregamos seguridad)
    // ---------------------------------------------------------
    $cajaModel = new Caja();
    $caja_abierta = $cajaModel->getCajaAbierta(); 
    
    if (!$caja_abierta || !isset($caja_abierta['ID_caja'])) {
        echo json_encode(['error' => '⛔ No hay caja abierta. Debes abrir la caja para poder facturar.']);
        exit;
    }
    $id_caja_abierta = $caja_abierta['ID_caja'];

    if (empty($persona_documento)) {
        echo json_encode(['error' => 'No se recibió el documento del cliente.']);
        exit;
    }

    // ---------------------------------------------------------
    // 2. GESTIÓN DE CLIENTE (Correcto)
    // ---------------------------------------------------------
    $stmt = $pdo->prepare("SELECT ID_persona FROM persona WHERE persona_documento = :doc");
    $stmt->bindParam(':doc', $persona_documento, PDO::PARAM_STR);
    $stmt->execute();
    $persona = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($persona) {
        $id_persona = $persona['ID_persona'];
    } else {
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

    // ---------------------------------------------------------
    // 3. CREACIÓN DE LA FACTURA (AQUÍ ESTABA EL RIESGO)
    // ---------------------------------------------------------
    // Agregamos: 
    // - factura_fecha_emision: Para que aparezca en el día de hoy.
    // - factura_total, subtotal, iva: En 0 para evitar error si la BD es estricta.
    
    $sql_factura = "INSERT INTO factura (
                        RELA_persona, 
                        RELA_estado_factura, 
                        RELA_caja, 
                        factura_fecha_emision,
                        factura_subtotal,
                        factura_iva_monto,
                        factura_total,
                        factura_iva_tasa
                    ) VALUES (
                        :RELA_persona, 
                        :RELA_estado_factura, 
                        :RELA_caja, 
                        NOW(),
                        0, 0, 0, 0
                    )";

    $stmt_factura = $pdo->prepare($sql_factura);
    $stmt_factura->execute([
        ':RELA_persona' => $id_persona,
        ':RELA_estado_factura' => $RELA_estado_factura,
        ':RELA_caja' => $id_caja_abierta
    ]);

    $id_factura = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'mensaje' => 'Factura iniciada correctamente vinculada a Caja #' . $id_caja_abierta,
        'id_factura' => $id_factura,
        'id_persona' => $id_persona
    ]);

} catch (PDOException $e) {
    error_log("Error en iniciar_factura.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error SQL: ' . $e->getMessage()]);
}
?>