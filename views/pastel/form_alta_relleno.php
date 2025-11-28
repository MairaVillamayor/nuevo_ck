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
  <h2>Agregar Nuevo Relleno</h2>
  <form action="../../controllers/pastel/alta_relleno.php" method="post">
    <label for="relleno_nombre">Nombre del Relleno:</label>
    <input type="text" name="relleno_nombre" id="relleno_nombre" required>
    <label for="relleno_descripcion">Descripción del Relleno:</label>
    <input type="text" name="relleno_descripcion" id="relleno_descripcion" required>
    <label for="relleno_precio">Precio del Relleno:</label>
    <input type="text" name="relleno_precio" id="relleno_precio" required>
    <button type="submit">Guardar relleno</button>
  </form>
</div>

</body>
</html>
