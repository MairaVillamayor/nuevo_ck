<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cake Party</title>
</head>
<body>
<?php include("../../includes/sidebar.php"); 
require_once "../../includes/navegacion.php";
?>

<div class="admin-form">
  <h2>Agregar Nuevo Tamaño</h2>
  <form action="../../controllers/pastel/alta_tamaño.php" method="post">

    <label for="tamaño_nombre">Tamaño:</label>
    <input type="text" name="tamaño_nombre" id="tamaño_nombre" required>

    <label for="tamaño_medidas">Medidas:</label>
    <input type="text" name="tamaño_medidas" id="tamaño_medidas" required>

    <label for="tamaño_precio">Precio:</label>
    <input type="text" name="tamaño_precio" id="tamaño_precio" required>

    <button type="submit">Guardar</button>
  </form>
</div>

</body>
</html>
