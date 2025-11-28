<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cake Party</title>
</head>
<body>
<?php include("../../includes/sidebar.php"); 
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";
?>
<div class="admin-form">
  <h2>Agregar Nuevo Modulo</h2>
  <form action="../../controllers/modulos/alta_modulos.php" method="post">
    <label for="modulos_nombre">Agregar Nuevo Modulo:</label>
    <input type="text" name="modulos_nombre" id="modulos_nombre" required>
    <button type="submit">Guardar</button>
  </form>
</div>
</body>
</html>
