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
  <h2>Agregar Nuevo Color</h2>
  <form action="../../controllers/pastel/alta_colorPastel.php" method="post">
    <label for="color_pastel_nombre">Color:</label>
    <input type="text" name="color_pastel_nombre" id="color_pastel_nombre" required>

    <label for="color_pastel_codigo">Código:</label>
    <input type="text" name="color_pastel_codigo" id="color_pastel_codigo" required>
    
    <button type="submit">Guardar color</button>
  </form>
</div>

</body>
</html>
