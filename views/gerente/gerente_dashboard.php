<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gerente - Cake Party</title>
  <link rel="stylesheet" href="/css/header.css">
</head>
<body>
  <?php 
    include("../../includes/header.php"); 
    include ("../../includes/navegacion.php");

  ?>

  <main class="main-content">
    <h1>Dashboard - Gerente</h1>
    <p class="description">Bienvenido al panel del Gerente. Desde aquí podés supervisar pedidos, insumos y crear usuarios.</p>

    <div class="cards">
      <div class="card">📦<br><a href="../caja/dashboard_caja.php">Caja</a><br></div>
      <div class="card">📦<br><a href="../stock/dashboard_stock.php">Stock</a><br></div>
      <div class="card">📅​<br><a href="../ventas/nueva_venta.php">Ventas</a><br></div>
      <div class="card">​🎂​<br><a href="../productos/productos_finalizados.php">Productos</a><br></div>
      <div class="card">🗂️​<br><a href="../admin/listado_pedidos.php">Pedidos</a><br></div>    
      <div class="card">🍰<br><a href="../admin/admin_items.php">Items</a><br></div>
      <div class="card">🕵️<br><a href="../usuario/Listado_Usuarios.php">Usuarios</a><br></div>

    </div>
  </main>
</body>
</html>
