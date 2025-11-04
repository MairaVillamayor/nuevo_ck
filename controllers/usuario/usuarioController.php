<?php
require_once __DIR__ . '/../../config/conexion.php';

if (!empty($_POST["registro"])) {
    $requeridos = [
        'persona_nombre',
        'persona_apellido',
        'persona_documento',
        'persona_fecha_nacimiento',
        'persona_direccion',
        'usuario_nombre',
        'usuario_contraseña'
    ];
    foreach ($requeridos as $campo) {
        if (empty($_POST[$campo])) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Uno%20de%20los%20campos%20est%C3%A1%20vac%C3%ADo");
            exit();
        }
    }
    $pdo = getConexion();
    try {
        $stmtPersona = $pdo->prepare("INSERT INTO persona (persona_nombre, persona_apellido, persona_documento, persona_fecha_nacimiento, persona_direccion) VALUES (:nombre, :apellido, :fecha_nacimiento, :direccion)");
        $stmtPersona->execute([
            'nombre' => $_POST['persona_nombre'],
            'apellido' => $_POST['persona_apellido'],
            'documento' => $_POST['persona_documento'],
            'fecha_nacimiento' => $_POST['persona_fecha_nacimiento'],
            'direccion' => $_POST['persona_direccion']
        ]);
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Registro%20exitoso&mensaje=La%20persona%20fue%20registrada%20correctamente");
        exit();
    } catch (PDOException $e) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Error%20en%20el%20registro:%20".urlencode($e->getMessage()));
        exit();
    }
}
?>
