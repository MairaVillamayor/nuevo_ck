<?php
// Detectar página actual y sesión
session_start();

$currentPath = $_SERVER['PHP_SELF'];
$usuarioLogueado = isset($_SESSION['usuario_id']);
$tipoUsuario = $usuarioLogueado ? $_SESSION['perfil_rol'] : null;
$perfilId = $usuarioLogueado ? $_SESSION['perfil_id'] : null;

// Bandera por rol
$esAdmin   = $usuarioLogueado && $perfilId == 1;
$esGerente = $usuarioLogueado && $perfilId == 4;
$esEmpleado= $usuarioLogueado && $perfilId == 2;
$esCliente = $usuarioLogueado && $perfilId == 3;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cake Party</title>

  <?php if ($esAdmin): ?>
    <link rel="stylesheet" href="/nuevo_ck/public/css/header.css" />
  <?php elseif ($esGerente): ?>
    <link rel="stylesheet" href="/nuevo_ck/public/css/header.css" />
  <?php elseif ($esEmpleado): ?>
    <link rel="stylesheet" href="/nuevo_ck/public/css/header.css" />
  <?php endif; ?>
</head>
<body>

<?php if ($esAdmin): ?>
  <div class="sidebar">
    <div class="profile">
      <div class="avatar"></div>
      <h3>Administrador</h3>
      <p>(Admin)</p>
    </div>
    <nav>
      <ul>
        <li><a href="../admin/admin_dashboard.php">Dashboard</a></li>
        <li><a href="../admin/listado_perfiles.php">Perfiles</a></li>
        <li><a href="#">Clientes</a></li>
        <li><a href="../admin/admin_items.php">Items</a></li>
        <li><a href="listado_pedidos.php">Pedidos</a></li>
        <li><a href="../../views/usuario/Listado_Usuarios.php">Usuarios</a></li>
        <li><a href="../../controllers/usuario/logout.php">Cerrar Sesión</a></li>
      </ul>
    </nav>
  </div>

<?php elseif ($esGerente): ?>
  <div class="sidebar gerente">
    <div class="profile">
      <div class="avatar"></div>
      <h3>Gerente</h3>
      <p>(Supervisión)</p>
    </div>
    <nav>
      <ul>
        <li><a href="../../modulos/gerente/gerente_dashboard.php">Dashboard</a></li>
        <li><a href="../../insumos/listado_insumo.php">Insumos</a></li>
        <li><a href="#">Pedidos</a></li>
        <li><a href="#">Clientes</a></li>
        <li><a href="#">Historial Pedidos</a></li>
        <li><a href="../../views/usuario/Listado_Usuarios.php">Usuarios</a></li>        
        <li><a href="../../../controllers/usuario/logout.php">Cerrar Sesión</a></li>
      </ul>
    </nav>
  </div>

<?php elseif ($esEmpleado): ?>
  <div class="sidebar empleado">
    <div class="profile">
      <div class="avatar"></div>
      <h3>Empleado</h3>
      <p>(Operación)</p>
    </div>
    <nav>
      <ul>
        <li><a href="../../../modulos/empleado/empleado_dashboard.php">Dashboard</a></li>
        <li><a href="#">Pedidos</a></li>
        <li><a href="#">Clientes</a></li>
        <li><a href="../../../views/usuario/Listado_Usuarios.php">Usuarios</a></li>
        <li><a href="../../../views/admin/admin_items.php">Items</a></li>
        <li><a href="../../controllers/usuario/logout.php">Cerrar Sesión</a></li>
      </ul>
    </nav>
  </div>
<?php endif; ?>

<main class="main-content">
