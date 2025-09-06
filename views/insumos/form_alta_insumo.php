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
  <h2>Agregar Nuevo Insumo</h2>
  <form action="../../controllers/insumos/alta_insumo.php" method="post">
    <label for="insumo_nombre">Nombre del insumo:</label>
    <input type="text" name="insumo_nombre" id="insumo_nombre" required>
    <label for="insumo_unidad_medida">Unidad de medida:</label>
    <input type="text" name="insumo_unidad_medida" id="insumo_unidad_medida" required>
    <button type="submit">Guardar insumo</button>
  </form>
</div>
</body>
</html>