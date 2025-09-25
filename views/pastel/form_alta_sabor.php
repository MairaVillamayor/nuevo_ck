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
  <h2>Agregar Nuevo Sabor</h2>
  <form action="../../controllers/pastel/alta_sabor.php" method="post">
    <label for="sabor_nombre">Nombre del Sabor:</label>
    <input type="text" name="sabor_nombre" id="sabor_nombre" required>
    <label for="sabor_descripcion">Descripción del Sabor:</label>
    <input type="text" name="sabor_descripcion" id="sabor_descripcion" required>
    <label for="sabor_precio">Precio del Sabor: </label>
    <input type="text" name="sabor_precio" id="sabor_precio" required> 
    <button type="submit">Guardar sabor</button>
  </form>
</div>

</body>
</html>
