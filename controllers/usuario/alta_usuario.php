<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="../public/css/style.css" type="text/css">
</head>

<body>
    <div class="center-wrapper">
        <div class="container">
            <h1>Resultado del Registro</h1>
            <?php
            require_once __DIR__ . '/../../config/conexion.php';

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $requeridos = [
                    'persona_nombre', 
                    'persona_apellido', 
                    'persona_documento',
                    'persona_fecha_nacimiento', 
                    'persona_direccion', 
                    'usuario_nombre', 
                    'usuario_correo_electronico',
                    'usuario_contraseña',
                    'confirmar_contraseña'
                ];
                foreach ($requeridos as $campo) {
                    if (empty($_POST[$campo])) {
                        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20completar%20el%20campo:%20$campo");
                        exit();
                    }
                }
                if ($_POST['usuario_contraseña'] !== $_POST['confirmar_contraseña']) {
                    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Las%20contrase%C3%B1as%20no%20coinciden");
                    exit();
                }
                if (strlen($_POST['usuario_contraseña']) < 8) {
                    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=La%20contrase%C3%B1a%20debe%20tener%20al%20menos%208%20caracteres");
                    exit();
                }
                if (!filter_var($_POST['usuario_correo_electronico'], FILTER_VALIDATE_EMAIL)) {
                    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=El%20correo%20electr%C3%B3nico%20no%20es%20v%C3%A1lido");
                    exit();
                }
                $pdo = getConexion();
                try {
                    $pdo->beginTransaction();
                    $stmtCheckUser = $pdo->prepare("SELECT ID_usuario FROM usuarios WHERE usuario_nombre = :usuario_nombre OR usuario_correo_electronico = :usuario_correo_electronico");
                    $stmtCheckUser->execute([
                        'usuario_nombre' => $_POST['usuario_nombre'],
                        'usuario_correo_electronico' => $_POST['usuario_correo_electronico']
                    ]);
                    if ($stmtCheckUser->fetch()) {
                        throw new Exception("El nombre de usuario o correo electrónico ya está registrado");
                    }
                    $stmtPersona = $pdo->prepare("INSERT INTO persona (persona_nombre, persona_apellido, persona_documento, persona_fecha_nacimiento, persona_direccion) VALUES (:nombre, :apellido, :documento, :fecha_nacimiento, :direccion)");
                    $stmtPersona->execute([
                        'nombre' => $_POST['persona_nombre'],
                        'apellido' => $_POST['persona_apellido'],
                        'documento' => $_POST['persona_documento'],
                        'fecha_nacimiento' => $_POST['persona_fecha_nacimiento'],
                        'direccion' => $_POST['persona_direccion']
                    ]);
                    $idPersona = $pdo->lastInsertId();
                    $usuario_contraseña_encriptada = password_hash($_POST['usuario_contraseña'], PASSWORD_BCRYPT);
                    $sql_debug = "INSERT INTO usuarios (usuario_nombre, usuario_correo_electronico, usuario_contraseña, usuario_numero_de_celular, RELA_persona, RELA_perfil) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmtUsuario = $pdo->prepare($sql_debug);
                    $stmtUsuario->execute([
                        $_POST['usuario_nombre'],
                        $_POST['usuario_correo_electronico'],
                        $usuario_contraseña_encriptada,
                        $_POST['usuario_numero_de_celular'],
                        $idPersona,
                        $_POST['RELA_perfil']
                    ]);
                    $idUsuario = $pdo->lastInsertId();
                    $pdo->commit();

                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    $stmtDatos = $pdo->prepare("SELECT u.*, p.perfil_rol FROM usuarios u JOIN perfiles p ON u.RELA_perfil = p.ID_perfil WHERE u.ID_usuario = :id");
                    $stmtDatos->execute(['id' => $idUsuario]);
                    $usuario_data = $stmtDatos->fetch(PDO::FETCH_ASSOC);
                    if ($usuario_data) {
                        $_SESSION['usuario_id'] = $usuario_data['ID_usuario'];
                        $_SESSION['usuario_nombre'] = $usuario_data['usuario_nombre'];
                        $_SESSION['perfil_rol'] = $usuario_data['perfil_rol'];
                        $_SESSION['perfil_id'] = $usuario_data['RELA_perfil'];
                    }

                    $redir = '/nuevo_ck/views/cliente/interfaz.php';
                    header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Usuario%20registrado&mensaje=El%20usuario%20fue%20registrado%20correctamente&redirect_to=" . urlencode($redir) . "&delay=1");
                    exit();
                } catch (Exception $e) {
                    $pdo->rollBack();
                    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=".urlencode($e->getMessage()));
                    exit();
                }
            } else {
                header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Acceso%20no%20permitido%20directamente.");
                exit();
            }
            ?>
        </div>
    </div>
</body>

</html>