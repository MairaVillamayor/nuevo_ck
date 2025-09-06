<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cake Party</title>
</head>
<body>
<?php include("../../includes/header.php"); 
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";
?>
<div class="admin-form">
  <h2>Agregar Nuevo Perfil</h2>
  <form action="../../controllers/admin/alta_perfiles.php" method="post">
    <label for="perfil_rol">Nuevo Perfil:</label>
    <input type="text" name="perfil_rol" id="perfil_rol" required>
    <button type="submit">Guardar</button>
  </form>
</div>
</body>
</html>
