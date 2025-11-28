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
  <h2>Agregar Nuevo Material</h2>
  <form action="../../controllers/insumos/alta_materialExtra.php" method="post">

    <label for="material_extra_nombre">Material:</label>
    <input type="text" name="material_extra_nombre" id="material_extra_nombre" required>

    <label for="material_extra_descri">Descripción:</label>
    <input type="text" name="material_extra_descri" id="material_extra_descri" required>

    <label for="material_extra_precio">Precio: </label>
    <input type="text" name="material_extra_precio" id="material_extra_precio" required>


    <button type="submit">Guardar material</button>
  </form>
</div>
</body>
</html>
