<?php
require_once __DIR__ . '/../../config/conexion.php';


$pdo = getConexion();

// Consulta de productos disponibles
$sql = "SELECT pf.*, c.categoria_producto_finalizado_nombre
        FROM producto_finalizado pf
        LEFT JOIN categoria_producto_finalizado c
          ON pf.RELA_categoria_producto_finalizado = c.ID_categoria_producto_finalizado
        WHERE pf.disponible_web = 1 AND pf.stock_actual > 0";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ruta_base = '/nuevo_ck/';
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Catálogo - Cake Party</title>
  <link rel="stylesheet" href="<?= $ruta_base ?>css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .card {
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s;
    }

    .card:hover {
      transform: scale(1.02);
    }

    .btn-pink {
      background-color: #ff6fa1;
      color: #fff;
      border: none;
      border-radius: 8px;
      transition: background 0.3s;
    }

    .btn-pink:hover {
      background-color: #ff4081;
      color: #fff;
    }

    /* Carrito flotante */
    #carrito-flotante {
      max-height: 80vh;
      overflow-y: auto;
      padding: 15px;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      position: sticky;
      top: 20px;
    }

    @media (max-width: 992px) {
      #carrito-flotante {
        position: static !important;
        max-height: none;
        margin-top: 20px;
      }
    }
  </style>
</head>

<body>

  <?php include __DIR__ . '/../../includes/navegacion.php'; ?>

  <div class="container py-4">
    <div class="row">
      <!-- Productos -->
      <div class="col-lg-8">
        <h1 class="mb-4 text-center text-secondary">🎂 Catálogo de Productos</h1>
        <div class="row">
          <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $p): ?>
              <?php
              $img = !empty($p['imagen_url'])
                ? $ruta_base . ltrim($p['imagen_url'], '/')
                : $ruta_base . 'img/default.png';
              ?>
              <div class="col-md-6 col-lg-6 mb-4">
                <div class="card h-100">
                  <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" style="height:220px;object-fit:cover;" alt="<?= htmlspecialchars($p['producto_finalizado_nombre'] ?? 'Producto') ?>">
                  <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($p['producto_finalizado_nombre'] ?? 'Sin nombre') ?></h5>
                    <p class="card-text"><?= htmlspecialchars($p['producto_finalizado_descri'] ?? 'Sin descripción') ?></p>
                    <p class="mb-1"><strong>Categoría:</strong> <?= htmlspecialchars($p['categoria_producto_finalizado_nombre'] ?? '') ?></p>
                    <p class="h5 text-success mt-auto">$<?= number_format($p['producto_finalizado_precio'] ?? 0, 2, ',', '.') ?></p>
                    <button class="btn btn-pink flex-grow-1 mt-2 agregar-carrito"
                      data-id="<?= (int)$p['ID_producto_finalizado'] ?>"
                      data-nombre="<?= htmlspecialchars($p['producto_finalizado_nombre']) ?>"
                      data-precio="<?= (float)$p['producto_finalizado_precio'] ?>"
                      data-stock="<?= (int)$p['stock_actual'] ?>">Agregar al carrito</button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-center text-muted">No hay productos disponibles.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Carrito -->
      <div class="col-lg-4">
        <div id="carrito-flotante">
          <h4>🛒 Carrito</h4>
          <div id="carrito-items"></div>
          <hr>
          <p><strong>Total: $<span id="total-carrito">0,00</span></strong></p>
          <button id="vaciar-carrito" class="btn btn-secondary w-100 mb-2">Vaciar carrito</button>
          <button id="mostrar-checkout" class="btn btn-pink w-100">Hacer Pedido</button>

          <!-- Checkout -->
          <div id="checkout" class="checkout mt-3" style="display:none;">
            <h5>🧾 Finalizar Compra</h5>
            <div id="checkout-items"></div>
            <hr>
            <p><strong>Total: $<span id="checkout-total">0,00</span></strong></p>
            <form id="checkout-form" method="POST" action="<?= $ruta_base ?>controllers/productos/checkout.php">
              <input type="hidden" name="carrito_data" id="carrito_data">
              <button type="submit" class="btn btn-success w-100">Confirmar Compra</button>
            </form>
          </div>

        </div>

      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    let carrito = {};

    function actualizarCarrito() {
      const $items = $('#carrito-items');
      $items.empty();
      let total = 0;

      for (const id in carrito) {
        const item = carrito[id];
        const subtotal = item.precio * item.cantidad;
        total += subtotal;
        $items.append(`
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span>${item.nombre} x 
          <input type="number" min="1" max="${item.stock}" value="${item.cantidad}" data-id="${id}" class="form-control form-control-sm cantidad-carrito" style="width:60px;display:inline-block;">
        </span>
        <span>$${subtotal.toFixed(2)}</span>
        <button class="btn btn-sm btn-danger eliminar-item" data-id="${id}">X</button>
      </div>
    `);
      }

      $('#total-carrito').text(total.toFixed(2).replace('.', ','));
    }

    // Agregar producto
    $(document).on('click', '.agregar-carrito', function() {
      const id = $(this).data('id');
      const nombre = $(this).data('nombre');
      const precio = parseFloat($(this).data('precio'));
      const stock = parseInt($(this).data('stock'));

      if (carrito[id]) {
        if (carrito[id].cantidad < stock) carrito[id].cantidad++;
        else {
          Swal.fire({
            icon: 'warning',
            title: 'Stock máximo alcanzado',
            text: `No puedes agregar más unidades de ${nombre}.`,
            timer: 1500,
            showConfirmButton: false
          });
        }
      } else {
        carrito[id] = {
          id,
          nombre,
          precio,
          cantidad: 1,
          stock
        };
        Swal.fire({
          icon: 'success',
          title: 'Producto agregado',
          text: `${nombre} ha sido agregado al carrito.`,
          timer: 1200,
          showConfirmButton: false
        });
      }

      actualizarCarrito();
    });

    // Cambiar cantidad
    $(document).on('input', '.cantidad-carrito', function() {
      const id = $(this).data('id');
      let cantidad = parseInt($(this).val());
      if (cantidad < 1) cantidad = 1;
      if (cantidad > carrito[id].stock) cantidad = carrito[id].stock;
      carrito[id].cantidad = cantidad;
      actualizarCarrito();
    });

    // Eliminar producto
    $(document).on('click', '.eliminar-item', function() {
      const id = $(this).data('id');
      const nombre = carrito[id].nombre;
      delete carrito[id];
      actualizarCarrito();
      Swal.fire({
        icon: 'info',
        title: 'Producto eliminado',
        text: `${nombre} ha sido removido del carrito.`,
        timer: 1200,
        showConfirmButton: false
      });
    });

    // Vaciar carrito
    $('#vaciar-carrito').click(function() {
      if (Object.keys(carrito).length === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Carrito vacío',
          text: 'No hay productos que vaciar.',
          timer: 1200,
          showConfirmButton: false
        });
        return;
      }
      Swal.fire({
        title: '¿Estás seguro?',
        text: "Se vaciará todo el carrito.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e91e63',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, vaciar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          carrito = {};
          actualizarCarrito();
          Swal.fire({
            icon: 'success',
            title: 'Carrito vaciado',
            timer: 1200,
            showConfirmButton: false
          });
        }
      });
    });

    // Mostrar/ocultar checkout
    $('#mostrar-checkout').click(function() {
      if (Object.keys(carrito).length === 0) {
        Swal.fire({
          icon: 'error',
          title: 'Carrito vacío',
          text: 'Agrega productos antes de hacer el pedido.'
        });
        return;
      }

      // Mostrar checkout
      $('#checkout').slideToggle();

      // Actualizar items del checkout
      const $checkoutItems = $('#checkout-items');
      $checkoutItems.empty();
      let total = 0;
      for (const id in carrito) {
        const item = carrito[id];
        const subtotal = item.precio * item.cantidad;
        total += subtotal;
        $checkoutItems.append(`
      <div class="d-flex justify-content-between mb-2">
        <span>${item.nombre} x ${item.cantidad}</span>
        <span>$${subtotal.toFixed(2)}</span>
      </div>
    `);
      }
      $('#checkout-total').text(total.toFixed(2).replace('.', ','));

      $('#carrito_data').val(JSON.stringify(carrito));
    });
  </script>


</body>

</html>