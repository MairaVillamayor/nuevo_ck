<?php
require_once '../../config/conexion.php';

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);


try{

    $docPersona = $_POST["persona_documento"] ?? null;

    if (empty($docPersona)) {
        echo json_encode(['error' => 'No se recibió el documento del cliente.']);
        exit;
    }

    $stmt = $conexion->prepare("SELECT  id_persona, 
                                        persona_nombre, 
                                        persona_apellido,
                                        persona_documento
                                FROM  persona
                                WHERE persona_documento = :docPersona");
    $stmt->bindParam(':docPersona', $docPersona, PDO::PARAM_INT);
    $stmt->execute();

    $persona = $stmt->fetch(PDO::FETCH_ASSOC);

    if($persona){
        $response = array(
            'id_persona' => $persona['id_persona'],
            'persona_nombre' => $persona['persona_nombre'],
            'persona_apellido' => $persona['persona_apellido'],
            'persona_documento' => $persona['persona_documento']      
        );
        echo json_encode($response);
    }else{
        echo json_encode(array('error'=>'Usuario no encontrado'));
    }
}catch (PDOException $e){
    // Captura cualquier error de la base de datos y lo devuelve como JSON
    error_log("Error de conexión/SQL en consultar_cliente.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error de conexión a la base de datos.']);
}

