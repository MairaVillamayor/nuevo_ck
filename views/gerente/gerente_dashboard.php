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
    include("../../../includes/header.php"); 
    require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";
  ?>

  <main class="main-content">
    <h1>Dashboard - Gerente</h1>
    <p class="description">Bienvenido al panel del Gerente. Desde aquí podés supervisar pedidos, insumos y crear usuarios.</p>

    <div class="cards">
      <div class="card">📦<br><a href="../../insumos/listado_insumo.php">Insumos</a><br></div>
      <div class="card">📅<br><a href="#">Pedidos</a><br></div>
      <div class="card">👥<br><a href="#">Clientes</a><br></div>
      <div class="card">🕵️<br><a href="../../usuario/Listado_Usuarios.php">Usuarios<br></div>
      <div class="card">📝<br><a href="#">Historial Pedidos</a><br></div>
    </div>
  </main>
</body>
</html>
