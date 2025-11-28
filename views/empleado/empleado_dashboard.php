<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Empleado - Cake Party</title>
  <link rel="stylesheet" href="/css/sidebar.css">
</head>
<body>
  <?php 
  include("../../includes/sidebar.php");
    include ("../../includes/navegacion.php");
  ?>

  <main class="main-content">
    <h1>Dashboard - Empleado</h1>
    <p class="description">Bienvenido al panel del Gerente. Desde aquí podés supervisar pedidos, insumos y crear usuarios.</p>

    <div class="admin-table" style="overflow-x:auto;">

    <div class="cards">
      <div class="card">📦<br><a href="../caja/dashboard_caja.php">Caja</a><br></div>
      <div class="card">📅<br><a href="../ventas/nueva_venta.php">Ventas</a><br></div>
      <div class="card">🗂️<br><a href="../admin/listado_pedidos.php">Pedidos</a><br></div>
      <div class="card">🍰<br><a href="../admin/admin_items.php">Items</a><br></div>
    </div>

    </div>
  </main>
</body>
</html>
