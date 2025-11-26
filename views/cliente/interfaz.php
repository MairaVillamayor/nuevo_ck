<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cake Party</title>
  <link rel="stylesheet" href="../../public/css/style.css">
  <style>
    .main-menu {
      position: sticky;
      top: 90px;
      background-color: #f9cdd4;
      z-index: 998;
      padding: 5px 10px;
      display: flex;
      gap: 15px;
      border-bottom: 1px solid #eee;
    }

    .main-menu a {
      text-decoration: none;
      color: #333;
      font-weight: bold;
    }

    footer {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      z-index: 999;
      background-color: #f9cdd4;
      text-align: center;
      padding: 15px 0;
      box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    }

    body {
      margin: 0;
      padding-top: 150px;
      padding-bottom: 70px;
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
    }

    .carousel {
      display: flex;
      overflow-x: auto;
      gap: 10px;
      padding: 20px;
    }

    .carousel img {
      height: 200px;
      border-radius: 10px;
      object-fit: cover;
    }

    .hacer-pedido-container {
      background-color: #fff;
      padding: 50px;
      text-align: center;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      margin: 30px 20px;
    }

    .hacer-pedido-titulo {
      font-size: 32px;
      color: #333;
      font-weight: bold;
      margin-bottom: 30px;
    }

    .boton-hacer-pedido {
      display: inline-block;
      padding: 20px 40px;
      background-color: #ffb6c1;
      color: white;
      font-size: 18px;
      text-decoration: none;
      border-radius: 50px;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .boton-hacer-pedido:hover {
      background-color: #ff9aaf;
      transform: translateY(-5px);
    }

    .main-header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 9999;
      background-color: #fff;
      padding: 8px 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      display: grid;
      grid-template-columns: 1fr 2fr 1.5fr;
      align-items: center;
      gap: 20px;
    }

    .header-left h1 {
      margin: 0;
    }

    .header-nav {
      display: flex;
      justify-content: center;
      gap: 15px;
    }

    .header-nav a {
      text-decoration: none;
      color: #444;
      font-weight: bold;
    }

    .header-right {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 15px;
    }

    .user-menu {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 15px;
      background: #f8e4e8;
      border-radius: 8px;
      font-size: 14px;
      height: auto;
      text-decoration: none;
      white-space: nowrap;
    }

    .user-menu a {
  padding: 4px 8px;          /* más pequeño */
  font-size: 13px;
  height: 100%;              /* misma altura que el contenedor */
  display: flex;
  align-items: center;  
   border-radius: 20px; 
}

    .search-bar {
      padding: 7px 10px;
    }

    .welcome {
      font-size: 14px;
      white-space: nowrap;
    }
  </style>
</head>

<body>

  <header class="main-header">
    <h1>CAKE PARTY</h1>
    <div>
      <div class="header-right">
        <input type="text" placeholder="¿Qué buscas?" class="search-bar">
        <span class="cart"><a href="../productos/carrito.php" class="cart-link">🛒</a></span>
        <?php if (session_status() === PHP_SESSION_NONE) {
          session_start();
        } ?>
        <?php if (!isset($_SESSION['usuario_id'])): ?>
          <a href="../../index.php" class="btn-ingresar">Ingresar</a>

        <?php else: ?>
          <div class="user-menu">
            <span class="welcome">¡Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</span>
            <a href="#misPedidos" id="btnMisPedidos" class="btn-mi-cuenta">Mi Cuenta</a>
            <a href="../../controllers/usuario/logout.php" class="btn-cerrar-sesion">Cerrar Sesión</a>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </header>

  <nav class="main-menu">
    <a href="#">CAJITAS DULCES &gt;</a>
    <a href="#">DESAYUNOS &gt;</a>
    <a href="#hacerPedido">¡CREA TU PASTEL!</a>
    <a href="#">COMBOS</a>
    <a href="#">¡PROMOS!</a>
    <a href="../../views/productos/catalogo_web.php">PRODUCTOS</a>
  </nav>

  <section class="carousel">
    <img src="../../public/images/crear_pastel.png" alt="Torta 1">
    <img src="../../public/images/crear_pastel2.png" alt="Torta 2">
    <img src="../../public/images/crear_pastel3.png" alt="Torta 3">
  </section>

  <div id="hacerPedido" class="hacer-pedido-container">
    <h2 class="hacer-pedido-titulo">¡Haz tu pedido aquí!</h2>
    <button id="btnHacerPedido" class="boton-hacer-pedido">¡Hacer pedido!</button>
    <div id="contenedorFormularioPedido" class="ajax-form-container"></div>
  </div>


  <div id="misPedidos" class="mis-pedidos-container" style="margin: 30px 20px;">
    <h2 class="hacer-pedido-titulo">Mis pedidos</h2>
    <div id="contenedorMisPedidos" class="ajax-form-container"></div>
  </div>


  <div id="catalogoContainer" style="margin: 30px 20px;">
    <?php include "../../views/productos/catalogo_web.php"; ?>
  </div>

  <footer>
    <p>🚚 ENVIOS GRATIS | PEDIDOS MAYORES A $100.000</p>
  </footer>

  <script>
    document.getElementById("btnHacerPedido").addEventListener("click", function() {
      const contenedor = document.getElementById("contenedorFormularioPedido");

      if (contenedor.dataset.loaded === "1") {
        contenedor.classList.toggle("activo");
        if (contenedor.classList.contains("activo")) {
          contenedor.scrollIntoView({
            behavior: "smooth"
          });
        }
        return;
      }

      fetch("../cliente/crear_pedido.php")
        .then(res => res.text())
        .then(html => {
          contenedor.innerHTML = html;
          contenedor.dataset.loaded = "1";
          contenedor.classList.add("activo");
          contenedor.scrollIntoView({
            behavior: "smooth"
          });

          const scripts = contenedor.querySelectorAll("script");

          scripts.forEach(oldScript => {
            const newScript = document.createElement("script");

            if (oldScript.textContent) {
              newScript.textContent = oldScript.textContent;
            } else if (oldScript.src) {
              newScript.src = oldScript.src;
            }

            document.body.appendChild(newScript);
          });

        })
        .catch(err => {
          contenedor.innerHTML = "<p style='color:red;'>Error al cargar el formulario.</p>";
          console.error(err);
        });
    });
  </script>
  <script>
    document.getElementById("btnMisPedidos").addEventListener("click", function(e) {
      e.preventDefault();

      const contenedor = document.getElementById("contenedorMisPedidos");

      // Si ya cargó una vez, solo desplegar y hacer scroll
      if (contenedor.dataset.loaded === "1") {
        contenedor.classList.toggle("activo");
        if (contenedor.classList.contains("activo")) {
          contenedor.scrollIntoView({
            behavior: "smooth"
          });
        }
        return;
      }

      // CARGA AJAX
      fetch("../cliente/mis_pedidos.php")
        .then(res => res.text())
        .then(html => {
          contenedor.innerHTML = html;
          contenedor.dataset.loaded = "1";
          contenedor.classList.add("activo");

          contenedor.scrollIntoView({
            behavior: "smooth"
          });

          // Ejecutar scripts incluidos en mis_pedidos.php
          const scripts = contenedor.querySelectorAll("script");

          scripts.forEach(oldScript => {
            const newScript = document.createElement("script");
            if (oldScript.textContent) newScript.textContent = oldScript.textContent;
            else if (oldScript.src) newScript.src = oldScript.src;
            document.body.appendChild(newScript);
          });
        })
        .catch(err => {
          contenedor.innerHTML = "<p style='color:red;'>Error al cargar los pedidos.</p>";
          console.error(err);
        });
    });
  </script>


</body>

</html>