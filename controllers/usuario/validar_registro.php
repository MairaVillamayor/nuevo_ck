<?php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '../../../helpers/auditoria.php';
header('Content-Type: application/json');

$response = ["success" => false, "message" => "", "field" => ""];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pdo = getConexion();
    // Validar usuario existente
    if (!empty($_POST['usuario_nombre'])) {
        $stmt = $pdo->prepare("SELECT ID_usuario FROM usuarios WHERE usuario_nombre = ?");
        $stmt->execute([$_POST['usuario_nombre']]);
        if ($stmt->fetch()) {
            $response["success"] = false;
            $response["message"] = "El nombre de usuario ya está registrado.";
            $response["field"] = "usuario_nombre";
        } else {
            $response["success"] = true;
            $response["message"] = "Nombre de usuario disponible.";
            $response["field"] = "usuario_nombre";
        }
        echo json_encode($response);
        exit();
        registrarAuditoria(
            'Registro',
            'usuario',
            $_SESSION['usuario_id'],
            'Registro de usuario'
        );
    }
    // Validar email existente
    if (!empty($_POST['usuario_correo_electronico'])) {
        $stmt = $pdo->prepare("SELECT ID_usuario FROM usuarios WHERE usuario_correo_electronico = ?");
        $stmt->execute([$_POST['usuario_correo_electronico']]);
        if ($stmt->fetch()) {
            $response["success"] = false;
            $response["message"] = "El correo electrónico ya está registrado.";
            $response["field"] = "usuario_correo_electronico";
        } else {
            $response["success"] = true;
            $response["message"] = "Correo electrónico disponible.";
            $response["field"] = "usuario_correo_electronico";
        }
        echo json_encode($response);
        exit();
    }
}
$response["message"] = "Petición inválida.";
echo json_encode($response); 