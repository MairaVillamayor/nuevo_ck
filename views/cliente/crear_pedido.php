<?php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/navegacion.php';
session_start();

// ⚠️ Verificamos que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../usuario/login.php?error=login_required");
  exit();
}

$conexion = getConexion();

// 🔹 Obtener datos desde la BD
$colores = $conexion->query("SELECT id_color_pastel, color_pastel_nombre FROM color_pastel")->fetchAll(PDO::FETCH_ASSOC);
$decoraciones = $conexion->query("SELECT id_decoracion, decoracion_nombre FROM decoracion")->fetchAll(PDO::FETCH_ASSOC);
$bases = $conexion->query("SELECT id_base_pastel, base_pastel_nombre FROM base_pastel")->fetchAll(PDO::FETCH_ASSOC);
$tamanos = $conexion->query("SELECT id_tamaño, tamaño_nombre FROM tamaño")->fetchAll(PDO::FETCH_ASSOC);
$sabores = $conexion->query("SELECT id_sabor, sabor_nombre FROM sabor")->fetchAll(PDO::FETCH_ASSOC);
$rellenos = $conexion->query("SELECT id_relleno, relleno_nombre FROM relleno")->fetchAll(PDO::FETCH_ASSOC);
$materiales = $conexion->query("SELECT ID_material_extra, material_extra_nombre, material_extra_descri 
                                FROM material_extra WHERE RELA_estado_insumos = 1")->fetchAll(PDO::FETCH_ASSOC);
$metodos_pago = $conexion->query("SELECT ID_metodo_pago, metodo_pago_descri 
                                  FROM metodo_pago")->fetchAll(PDO::FETCH_ASSOC);
?>
<script>
  console.log(<?= json_encode($metodos_pago) ?>);
</script>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Crear tu pastel - Cake Party</title>
  <link rel="stylesheet" href="../../public/css/crear_pedido.css">
  <script>
    let pisoCount = 0;

    function agregarPiso() {
      pisoCount++;
      const container = document.getElementById("pisos-container");

      const div = document.createElement("div");
      div.classList.add("piso");
      div.innerHTML = `
        <h4>Piso ${pisoCount}</h4>

        <label>Tamaño:</label>
        <select name="pisos[${pisoCount}][RELA_tamaño]" required>
          <?php foreach ($tamanos as $t): ?>
            <option value="<?= $t['id_tamaño'] ?>"><?= $t['tamaño_nombre'] ?></option>
          <?php endforeach; ?>
        </select>

        <label>Sabor:</label>
        <select name="pisos[${pisoCount}][RELA_sabor]" required>
          <?php foreach ($sabores as $s): ?>
            <option value="<?= $s['id_sabor'] ?>"><?= $s['sabor_nombre'] ?></option>
          <?php endforeach; ?>
        </select>

        <label>Relleno:</label>
        <select name="pisos[${pisoCount}][RELA_relleno]" required>
          <?php foreach ($rellenos as $r): ?>
            <option value="<?= $r['id_relleno'] ?>"><?= $r['relleno_nombre'] ?></option>
          <?php endforeach; ?>
        </select>

        <hr>
      `;
      container.appendChild(div);
    }
  </script>
</head>

<body>
  <h2>🎂 Crear tu pastel personalizado</h2>

  <form action="../../controllers/cliente/guardar_pedido.php" method="POST" class="pastel-form">

    <!-- Color -->
    <label>Color del pastel:</label>
    <select name="RELA_color_pastel" required>
      <?php foreach ($colores as $c): ?>
        <option value="<?= $c['id_color_pastel'] ?>"><?= $c['color_pastel_nombre'] ?></option>
      <?php endforeach; ?>
    </select>

    <!-- Decoración -->
    <label>Decoración:</label>
    <select name="RELA_decoracion" required>
      <?php foreach ($decoraciones as $d): ?>
        <option value="<?= $d['id_decoracion'] ?>"><?= $d['decoracion_nombre'] ?></option>
      <?php endforeach; ?>
    </select>

    <!-- Base -->
    <label>Base:</label>
    <select name="RELA_base_pastel" required>
      <?php foreach ($bases as $b): ?>
        <option value="<?= $b['id_base_pastel'] ?>"><?= $b['base_pastel_nombre'] ?></option>
      <?php endforeach; ?>
    </select>

    <!-- Material extra -->
    <h3>🎁 Materiales extra</h3>
    <?php foreach ($materiales as $m): ?>
      <label>
        <input type="checkbox" name="material_extra[]" value="<?= $m['ID_material_extra'] ?>">
        <?= $m['material_extra_nombre'] ?> (<?= $m['material_extra_descri'] ?>)
      </label><br>
    <?php endforeach; ?>

    <!-- Pisos dinámicos -->
    <h3>⚡ Pisos</h3>
    <div id="pisos-container"></div>
    <button type="button" onclick="agregarPiso()">➕ Agregar Piso</button>

    <!-- Dirección de envío -->
    <h3>🚚 Datos de envío</h3>
    <label>Dirección de envío:</label><br>
    <input type="text" name="pedido_direccion_envio" required style="width:100%">

    <label>Metodo de Pago:</label>
    <select name="RELA_metodo_pago" required>
      <?php foreach ($metodos_pago as $mp): ?>
        <option value="<?= $mp['ID_metodo_pago'] ?>"><?= $mp['metodo_pago_descri'] ?></option>
      <?php endforeach; ?>
    </select>


    <br><br>
    <button type="submit">✅ Finalizar Pedido</button>
  </form>
</body>

</html>