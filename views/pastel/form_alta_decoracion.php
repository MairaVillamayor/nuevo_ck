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
  <h2>Agregar Nueva Decoración</h2>
  <form action="../../controllers/pastel/alta_decoracion.php" method="post">
    <label for="decoracion_nombre">Decoración:</label>
    <input type="text" name="decoracion_nombre" id="decoracion_nombre" required>
    <label for="decoracion_descripcion">Descripción:</label>
    <input type="text" name="decoracion_descripcion" id="decoracion_descripcion" required>
    <label for="decoracion_precio">Precio:</label>
    <input type="text" name="decoracion_precio" id="decoracion_precio" required>
    <button type="submit">Guardar decoración</button>
  </form>
</div>

</body>
</html>
