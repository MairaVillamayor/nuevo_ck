<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Administrador - Cake Party</title>
  <link rel="stylesheet" href="../../public/css/header.css" />
</head>
<body>
  <?php include("../../includes/header.php"); 
  require_once "../../includes/navegacion.php";
  ?>

  <main class="main-content">
    <h1>Dashboard - Administrador</h1>
    <p class="description">Desde aquí podés gestionar los recursos de Cake Party.</p>

    <div class="cards">
      <div class="card">📦<br><a href="../caja/listado_caja.php">Caja</a><br></div>
      <div class="card">📦<br><a href="../stock/dashboard_stock.php">Stock</a><br></div>
      <div class="card">📅​<br><a href="../ventas/listado_ventas.php">Ventas</a><br></div>
      <div class="card">​🎂​<br><a href="../productos/productos_finalizados.php">Productos</a><br></div>
      <div class="card">🗂️​<br><a href="listado_pedidos.php">Pedidos</a><br></div>    
      <div class="card">🍰<br><a href="admin_items.php">Items</a><br></div>
      <div class="card">​​🧑‍💻​<br><a href="listado_perfiles.php">Perfiles</a><br></div>
      <div class="card">🕵️<br><a href="../usuario/Listado_Usuarios.php">Usuarios</a><br></div>

    </div>
  </main>
</body>
</html>
