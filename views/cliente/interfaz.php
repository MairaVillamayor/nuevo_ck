<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cake Party</title>
  <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<title>Cake Party</title>
  <link rel="stylesheet" href="../../public/css/style.css">
  <style>
    /* CSS temporal para el menú de usuario */
    .user-menu {
      display: flex !important;
      align-items: center !important;
      gap: 20px !important;
      background: linear-gradient(135deg, #fff5f7 0%, #ffeef2 100%) !important;
      padding: 15px 25px !important;
      border-radius: 30px !important;
      border: 2px solid #ffb6c1 !important;
      box-shadow: 0 4px 15px rgba(233, 30, 99, 0.1) !important;
    }
    
    .welcome {
      color: #e91e63 !important;
      font-weight: bold !important;
      font-size: 15px !important;
    }
    
    .btn-mi-cuenta {
      background: linear-gradient(135deg, #ffb6c1 0%, #ff9aaf 100%) !important;
      color: #333 !important;
      padding: 12px 22px !important;
      border-radius: 25px !important;
      text-decoration: none !important;
      font-weight: bold !important;
      font-size: 14px !important;
      border: 2px solid #ff9aaf !important;
      box-shadow: 0 3px 10px rgba(255, 182, 193, 0.3) !important;
    }
    
    .btn-cerrar-sesion {
      background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%) !important;
      color: white !important;
      padding: 12px 22px !important;
      border-radius: 25px !important;
      text-decoration: none !important;
      font-weight: bold !important;
      font-size: 14px !important;
      border: 2px solid #d32f2f !important;
      box-shadow: 0 3px 10px rgba(244, 67, 54, 0.3) !important;
    }
  </style>
<?php include("../../includes/header.php"); 
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";
?>

<!-- Barra superior -->
  <div class="top-bar">
    <span>📞 +54 9 11 6110-8751</span>
    <nav>
      <a href="#">NOSOTROS</a>
      <a href="#">RECETAS</a>
      <a href="#">CONTACTO</a>
    </nav>
  </div>

  <!-- Encabezado principal -->
  <header class="main-header">
    <h1>CAKE PARTY</h1>
    <input type="text" placeholder="¿Qué buscas?" class="search-bar">
    <?php if (!isset($_SESSION['usuario_id'])): ?>
      <a href="../usuario/login.php" class="btn-ingresar">Ingresar</a>
    <?php else: ?>
      <div class="user-menu">
        <span class="welcome">¡Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</span>
        <a href="../usuario/mi_cuenta.php" class="btn-mi-cuenta">Mi Cuenta</a>
        <a href="../../controllers/usuario/logout.php" class="btn-cerrar-sesion">Cerrar Sesión</a>
      </div>
    <?php endif; ?>
    <span class="cart">🛒</span>
  </header>

  <!-- Menú negro -->
  <nav class="main-menu">
    <a href="#">CAJITAS DULCES &gt;</a>
    <a href="#">DESAYUNOS &gt;</a>
    <a href="crear_pastel.html">¡CREA TU PASTEL!</a>
    <a href="#">COMBOS</a>
    <a href="#">¡PROMOS!</a>
    <a href="#">PRODUCTOS</a>
  </nav>

  <!-- Carrusel estático -->
  <section class="carousel">
    <img src="https://via.placeholder.com/200x250" alt="Tarta 1">
    <img src="https://via.placeholder.com/200x250" alt="Postre 2">
    <img src="https://via.placeholder.com/200x250" alt="Tarta 3">
    <img src="https://via.placeholder.com/200x250" alt="Macarons">
    <div class="promo">
      <h2>¡CAKE PARTY!</h2>
      <h1>20% <br><span>OFF</span></h1>
      <p class="resaltado">EN TODOS <br> LOS <br> PRODUCTOS</p>
    </div>
  </section>

  <!-- Pie de página -->
  <footer>
    <p>🚚 ENVIOS GRATIS <br> PEDIDOS MAYORES A $100.000</p>
  </footer>
</body>
</html>
