<?php
require_once __DIR__ . '/../../config/conexion.php';
$pdo = getConexion();

if (
    isset($_POST["usuario_nombre"], $_POST["usuario_correo_electronico"], $_POST["usuario_contraseña"], 
          $_POST["usuario_numero_de_celular"], $_POST["persona_nombre"], $_POST["persona_apellido"],
          $_POST['persona_documento'], $_POST["persona_fecha_nacimiento"], $_POST["persona_direccion"], 
          $_POST["RELA_perfil"])
) {
    // Datos de usuario
    $usuarioNombre = trim($_POST["usuario_nombre"]);
    $usuarioCorreo = trim($_POST["usuario_correo_electronico"]);
    $usuarioPassword = trim($_POST["usuario_contraseña"]);
    $usuarioCelular = trim($_POST["usuario_numero_de_celular"]);
    $perfilId = intval($_POST["RELA_perfil"]);

    // Datos de persona
    $personaNombre = trim($_POST["persona_nombre"]);
    $personaApellido = trim($_POST["persona_apellido"]);
    $personaDocumento = trim($_POST['persona_documento']);
    $personaFN = $_POST["persona_fecha_nacimiento"];
    $personaDireccion = trim($_POST["persona_direccion"]);

    if ($usuarioNombre && $usuarioCorreo && $usuarioPassword && $usuarioCelular 
        && $personaNombre && $personaApellido && $personaDocumento && $personaFN && $personaDireccion && $perfilId > 0) {
        try {
            $pdo->beginTransaction();

            // 1️⃣ Insertar persona
            $stmtPersona = $pdo->prepare("INSERT INTO persona (persona_nombre, persona_apellido, persona_documento, persona_fecha_nacimiento, persona_direccion) 
                                          VALUES (:nombre, :apellido, :documento, :fn, :direccion)");
            $stmtPersona->execute([
                ':nombre' => $personaNombre,
                ':apellido' => $personaApellido,
                ':documento' => $personaDocumento,
                ':fn' => $personaFN,
                ':direccion' => $personaDireccion
            ]);
            $personaId = $pdo->lastInsertId();

            // 2️⃣ Insertar usuario
            $passwordHash = password_hash($usuarioPassword, PASSWORD_BCRYPT);
            $stmtUsuario = $pdo->prepare("INSERT INTO usuarios 
                (usuario_nombre, usuario_correo_electronico, usuario_contraseña, usuario_numero_de_celular, RELA_persona, RELA_perfil)
                VALUES (:usuario, :correo, :pass, :celular, :personaId, :perfilId)");
            $stmtUsuario->execute([
                ':usuario' => $usuarioNombre,
                ':correo' => $usuarioCorreo,
                ':pass' => $passwordHash,
                ':celular' => $usuarioCelular,
                ':personaId' => $personaId,
                ':perfilId' => $perfilId
            ]);

            $pdo->commit();

            header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Usuario%20creado&mensaje=El%20nuevo%20usuario%20fue%20creado&redirect_to=../../views/usuario/Listado_Usuarios.php&delay=2");
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20valores%20requeridos");
        exit();
    }
} else {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibieron%20datos");
    exit();
}
