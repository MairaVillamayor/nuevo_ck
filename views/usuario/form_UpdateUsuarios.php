<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cake Party - Modificar Usuario</title>
</head>
<body>
<?php 
include("../../includes/sidebar.php"); 
require_once "../../includes/navegacion.php";
require_once "../../config/conexion.php";

if (!isset($_GET['ID_usuario'])) {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No se recibió el ID del usuario");
    exit();
}

$ID_usuario = intval($_GET['ID_usuario']);
$pdo = getConexion();

// Traer usuario con su persona
$stmt = $pdo->prepare("SELECT u.*, p.persona_nombre, p.persona_apellido, p.persona_documento, p.persona_fecha_nacimiento, p.persona_direccion
                       FROM usuarios u
                       JOIN persona p ON u.RELA_persona = p.ID_persona
                       WHERE u.ID_usuario = :id");
$stmt->execute([':id' => $ID_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Usuario no encontrado");
    exit();
}

// Traer perfiles
$stmtPerfiles = $pdo->query("SELECT ID_perfil, perfil_rol FROM perfiles");
$perfiles = $stmtPerfiles->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-form">
  <h2>Modificar Usuario</h2>
  <form action="../../controllers/usuario/Modificar_Usuarios.php" method="post">
    <h3>Datos del Usuario</h3>
    <label for="usuario_nombre">Nombre de Usuario:</label>
    <input type="text" name="usuario_nombre" id="usuario_nombre" value="<?php echo htmlspecialchars($usuario['usuario_nombre']); ?>" required>

    <label for="usuario_correo_electronico">Correo Electrónico:</label>
    <input type="email" name="usuario_correo_electronico" id="usuario_correo_electronico" value="<?php echo htmlspecialchars($usuario['usuario_correo_electronico']); ?>" required>

    <label for="usuario_contraseña">Contraseña (opcional):</label>
    <input type="password" name="usuario_contraseña" id="usuario_contraseña" placeholder="Dejar vacío para no cambiar">

    <label for="usuario_numero_de_celular">Número de Celular:</label>
    <input type="text" name="usuario_numero_de_celular" id="usuario_numero_de_celular" value="<?php echo htmlspecialchars($usuario['usuario_numero_de_celular']); ?>" required>

    <h3>Datos de la Persona</h3>
    <label for="persona_nombre">Nombre:</label>
    <input type="text" name="persona_nombre" id="persona_nombre" value="<?php echo htmlspecialchars($usuario['persona_nombre']); ?>" required>

    <label for="persona_apellido">Apellido:</label>
    <input type="text" name="persona_apellido" id="persona_apellido" value="<?php echo htmlspecialchars($usuario['persona_apellido']); ?>" required>

    <label for="persona_documento">Documento:</label>
    <input type="text" name="persona_documento" id="persona_documento" value="<?php echo htmlspecialchars($usuario['persona_documento']); ?>" required>

    <label for="persona_fecha_nacimiento">Fecha de Nacimiento:</label>
    <input type="date" name="persona_fecha_nacimiento" id="persona_fecha_nacimiento" value="<?php echo $usuario['persona_fecha_nacimiento']; ?>" required>

    <label for="persona_direccion">Dirección:</label>
    <input type="text" name="persona_direccion" id="persona_direccion" value="<?php echo htmlspecialchars($usuario['persona_direccion']); ?>" required>

    <label for="RELA_perfil">Perfil:</label>
    <select name="RELA_perfil" id="RELA_perfil" required>
        <?php foreach ($perfiles as $perfil) { ?>
            <option value="<?php echo $perfil['ID_perfil']; ?>" <?php if ($perfil['ID_perfil'] == $usuario['RELA_perfil']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($perfil['perfil_rol']); ?>
            </option>
        <?php } ?>
    </select>

    <input type="hidden" name="ID_usuario" value="<?php echo $ID_usuario; ?>">
    <input type="hidden" name="ID_persona" value="<?php echo $usuario['RELA_persona']; ?>">

    <button type="submit">Guardar Cambios</button>
  </form>
</div>
</body>
</html>
