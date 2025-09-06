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
  <h2>Agregar Nueva Temática</h2>
  <form action="../../controllers/pastel/alta_tematica.php" method="post">
    <label for="tematica_descripcion">Descripción de la temática:</label>
    <input type="text" name="tematica_descripcion" id="tematica_descripcion" required>
    <button type="submit">Guardar temática</button>
  </form>
</div>

</body>
</html>
