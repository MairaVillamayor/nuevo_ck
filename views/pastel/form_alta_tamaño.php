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
  <h2>Agregar Nuevo Tamaño</h2>
  <form action="../../controllers/pastel/alta_tamaño.php" method="post">
    <label for="tamaño_nombre">Tamaño:</label>
    <input type="text" name="tamaño_nombre" id="tamaño_nombre" required>
    <label for="tamaño_medidas">Medidas:</label>
    <input type="text" name="tamaño_medidas" id="tamaño_medidas" required>
    <button type="submit">Guardar</button>
  </form>
</div>

</body>
</html>
