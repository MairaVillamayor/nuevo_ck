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
  <h2>Agregar Nueva Base</h2>
  <form action="../../controllers/pastel/alta_basePastel.php" method="post">
    <label for="base_pastel_nombre">Nombre de la base:</label>
    <input type="text" name="base_pastel_nombre" id="base_pastel_nombre" required>
    <label for="base_pastel_decoracion">Decoración de la base:</label>
    <input type="text" name="base_pastel_decoracion" id="base_pastel_decoracion" required>
    <label for="base_pastel_precio">Precio de la base:</label>
    <input type="text" name="base_pastel_precio" id="base_pastel_precio" required>
    <button type="submit">Guardar base</button>
  </form>
</div>

</body>
</html>
