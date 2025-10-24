<?php
require_once __DIR__ . '/../../config/conexion.php';
include("../../includes/navegacion.php");
session_start();

$pdo = getConexion();

// Verificar login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php?error=not_logged');
    exit;
}

// Obtener productos finalizados
$sql = "SELECT pf.ID_producto_finalizado, pf.producto_nombre, pf.producto_descri,
               c.categoria_de_producto_finalizado_nombre
        FROM Producto_finalizado pf
        JOIN Categoria_de_producto_finalizado c 
        ON pf.RELA_categoria_de_producto_finalizado = c.ID_categoria_de_producto_finalizado";
$stmt = $pdo->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Productos Finalizados - Cake Party</title>
  <link rel="stylesheet" href="../../style.css">
</head>
<body>
  <div class="contenedor-principal">
    <h1>🎂 Nuestros Productos Finalizados</h1>
    <div class="grid-productos">
      <?php foreach ($productos as $p): ?>
        <div class="card-producto">
          <h2><?= htmlspecialchars($p['producto_nombre']) ?></h2>
          <p><?= htmlspecialchars($p['producto_descri']) ?></p>
          <p><strong>Categoría:</strong> <?= htmlspecialchars($p['categoria_de_producto_finalizado_nombre']) ?></p>
          <button class="btn-agregar" 
                  data-id="<?= $p['ID_producto_finalizado'] ?>" 
                  data-nombre="<?= htmlspecialchars($p['producto_nombre']) ?>">
            🛒 Agregar al carrito
          </button>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script>
  // Agregar producto al carrito vía fetch
  document.querySelectorAll('.btn-agregar').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      const nombre = btn.dataset.nombre;

      const res = await fetch('../../controllers/cliente/agregar_carrito.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id_producto=${id}`
      });

      const data = await res.json();
      alert(data.mensaje);
    });
  });
  </script>
</body>
</html>
