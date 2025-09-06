<?php
require_once __DIR__ . '/../../config/conexion.php';
$pdo = getConexion();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        !isset($_POST["ID_usuario"], $_POST["ID_persona"], $_POST["usuario_nombre"], $_POST["usuario_correo_electronico"], 
               $_POST["usuario_numero_de_celular"], $_POST["persona_nombre"], $_POST["persona_apellido"],
               $_POST["persona_fecha_nacimiento"], $_POST["persona_direccion"], $_POST["RELA_perfil"])
    ) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan datos para modificar el usuario");
        exit();
    }

    $ID_usuario = intval($_POST["ID_usuario"]);
    $ID_persona = intval($_POST["ID_persona"]);
    $usuarioNombre = trim($_POST["usuario_nombre"]);
    $usuarioCorreo = trim($_POST["usuario_correo_electronico"]);
    $usuarioCelular = trim($_POST["usuario_numero_de_celular"]);
    $perfilId = intval($_POST["RELA_perfil"]);

    $personaNombre = trim($_POST["persona_nombre"]);
    $personaApellido = trim($_POST["persona_apellido"]);
    $personaFN = $_POST["persona_fecha_nacimiento"];
    $personaDireccion = trim($_POST["persona_direccion"]);

    $usuarioPassword = isset($_POST["usuario_contraseña"]) ? trim($_POST["usuario_contraseña"]) : "";

    try {
        $pdo->beginTransaction();

        // 1️⃣ Actualizar persona
        $stmtPersona = $pdo->prepare("UPDATE persona SET 
                                        persona_nombre = :nombre,
                                        persona_apellido = :apellido,
                                        persona_fecha_nacimiento = :fn,
                                        persona_direccion = :direccion
                                      WHERE ID_persona = :id");
        $stmtPersona->execute([
            ':nombre' => $personaNombre,
            ':apellido' => $personaApellido,
            ':fn' => $personaFN,
            ':direccion' => $personaDireccion,
            ':id' => $ID_persona
        ]);

        // 2️⃣ Actualizar usuario
        if ($usuarioPassword !== "") {
            $passwordHash = password_hash($usuarioPassword, PASSWORD_BCRYPT);
            $sql = "UPDATE usuarios SET 
                        usuario_nombre = :nombre,
                        usuario_correo_electronico = :correo,
                        usuario_contraseña = :pass,
                        usuario_numero_de_celular = :celular,
                        RELA_perfil = :perfil
                    WHERE ID_usuario = :id";
            $params = [
                ':nombre' => $usuarioNombre,
                ':correo' => $usuarioCorreo,
                ':pass' => $passwordHash,
                ':celular' => $usuarioCelular,
                ':perfil' => $perfilId,
                ':id' => $ID_usuario
            ];
        } else {
            $sql = "UPDATE usuarios SET 
                        usuario_nombre = :nombre,
                        usuario_correo_electronico = :correo,
                        usuario_numero_de_celular = :celular,
                        RELA_perfil = :perfil
                    WHERE ID_usuario = :id";
            $params = [
                ':nombre' => $usuarioNombre,
                ':correo' => $usuarioCorreo,
                ':celular' => $usuarioCelular,
                ':perfil' => $perfilId,
                ':id' => $ID_usuario
            ];
        }

        $stmtUsuario = $pdo->prepare($sql);
        $stmtUsuario->execute($params);

        $pdo->commit();

        header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Usuario%20modificado&mensaje=Los cambios se guardaron&redirect_to=../views/usuario/Listado_Usuarios.php&delay=2");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Acceso no permitido");
    exit();
}
