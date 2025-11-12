<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cake Party - Alta Usuario</title>
    <link rel="stylesheet" href="../../public/css/header.css" />
</head>
<body>
<?php 
include("../../includes/header.php"); 
require_once "../../includes/navegacion.php";
require_once "../../config/conexion.php";

// Traer perfiles
$pdo = getConexion();
$stmtPerfiles = $pdo->query("SELECT ID_perfil, perfil_rol FROM perfiles");
$perfiles = $stmtPerfiles->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-form">
  <h2>Agregar Nuevo Usuario</h2>
  <form action="../../controllers/usuario/Alta_Usuarios.php" method="post">
    <h3>Datos del Usuario</h3>
    <label for="usuario_nombre">Nombre de Usuario:</label>
    <input type="text" name="usuario_nombre" id="usuario_nombre" required>

    <label for="usuario_correo_electronico">Correo Electrónico:</label>
    <input type="email" name="usuario_correo_electronico" id="usuario_correo_electronico" required>

    <label for="usuario_contraseña">Contraseña:</label>
    <input type="password" name="usuario_contraseña" id="usuario_contraseña" required>

    <label for="usuario_numero_de_celular">Número de Celular:</label>
    <input type="text" name="usuario_numero_de_celular" id="usuario_numero_de_celular" required>

    <h3>Datos de la Persona</h3>
    <label for="persona_nombre">Nombre:</label>
    <input type="text" name="persona_nombre" id="persona_nombre" required>

    <label for="persona_apellido">Apellido:</label>
    <input type="text" name="persona_apellido" id="persona_apellido" required>

    <label for="persona_documento">Documento:</label>
    <input type="text" name="persona_documento" id="persona_documento" required>


    <label for="persona_fecha_nacimiento">Fecha de Nacimiento:</label>
    <input type="date" name="persona_fecha_nacimiento" id="persona_fecha_nacimiento" required>

    <label for="persona_direccion">Dirección:</label>
    <input type="text" name="persona_direccion" id="persona_direccion" required>

    <label for="RELA_perfil">Perfil:</label>
    <select name="RELA_perfil" id="RELA_perfil" required>
        <option value="">-- Seleccionar perfil --</option>
        <?php foreach ($perfiles as $perfil) { ?>
            <option value="<?php echo $perfil['ID_perfil']; ?>">
                <?php echo htmlspecialchars($perfil['perfil_rol']); ?>
            </option>
        <?php } ?>
    </select>

    <button type="submit">Guardar</button>
  </form>
</div>
</body>
</html>
