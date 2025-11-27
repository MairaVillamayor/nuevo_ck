<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Administrador - Cake Party</title>
  <link rel="stylesheet" href="../../public/css/admin_style.css" />
</head>

<body>
  <?php include("../../includes/header.php"); 
  require_once "../../includes/navegacion.php";
  ?>

  <main class="main-content">
    <h1>Items</h1>
    <p class="description">Desde aquí podés gestionar los items de Cake Party.</p>

    <div class="cards">
      <div class="card">
        Relleno<br>
        <span>
          <a href="../pastel/listado_relleno.php" class="btn-add">Ingresar</a>
        </span>
      </div>
      <div class="card">
        Sabor<br>
        <span>
          <a href="../pastel/listado_sabor.php" class="btn-add">Ingresar</a>
        </span>
      </div>
      <div class="card">
        Tematica<br>
        <span>
          <a href="../pastel/listado_tematica.php" class="btn-add">Ingresar</a>
        </span>
      </div>
      <div class="card">
        Tamaño<br>
        <span>
          <a href="../pastel/listado_tamaño.php" class="btn-add">Ingresar</a>
        </span>
      </div>
      <!-- <div class="card">
        Insumos<br>
        <span>
          <a href="../insumos/listado_insumo.php" class="btn-add">Ingresar</a>
        </span>
      </div> -->
      <div class="card">
        Decoración<br>
        <span>
          <a href="../pastel/listado_decoracion.php" class="btn-add">Ingresar</a>
        </span>
      </div>
      <div class="card">
        Color del Pastel<br>
        <span>
          <a href="../pastel/listado_colorPastel.php" class="btn-add">Ingresar</a>
        </span>
      </div>
      <div class="card">
        Base del Pastel<br>
        <span>
          <a href="../pastel/listado_basePastel.php" class="btn-add">Ingresar</a>
        </span>
      </div>
      <div class="card">
        Material Extra<br>
        <span>
          <a href="../insumos/listado_materialExtra.php" class="btn-add">Ingresar</a>
        </span>
    </div>
  </main>
</body>

</html>