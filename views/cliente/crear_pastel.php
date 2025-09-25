<?php
require_once ('../../config/conexion.php');


$colores = $conexion->query("SELECT id_color_pastel, color_pastel_nombre FROM color_pastel");
$decoraciones = $conexion->query("SELECT id_decoracion, decoracion_nombre FROM decoracion");
$bases = $conexion->query("SELECT id_base_pastel, base_pastel_nombre FROM base_pastel");
$tamanos = $conexion->query("SELECT id_tamaño, tamaño_nombre FROM tamaño");
$sabores = $conexion->query("SELECT id_sabor, sabor_nombre FROM sabor");
$rellenos = $conexion->query("SELECT id_relleno, relleno_nombre FROM relleno");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear tu pastel - Cake Party</title>
  <link rel="stylesheet" href="">
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
        <?php while($t = $tamanos->fetch_assoc()): ?>
          <option value="<?= $t['id_tamaño'] ?>"><?= $t['tamaño_nombre'] ?></option>
        <?php endwhile; ?>
      </select>

      <label>Sabor:</label>
      <select name="pisos[${pisoCount}][RELA_sabor]" required>
        <?php while($s = $sabores->fetch_assoc()): ?>
          <option value="<?= $s['id_sabor'] ?>"><?= $s['sabor_nombre'] ?></option>
        <?php endwhile; ?>
      </select>

      <label>Relleno:</label>
      <select name="pisos[${pisoCount}][RELA_relleno]" required>
        <?php while($r = $rellenos->fetch_assoc()): ?>
          <option value="<?= $r['id_relleno'] ?>"><?= $r['relleno_nombre'] ?></option>
        <?php endwhile; ?>
      </select>
    `;
    container.appendChild(div);
  }
  </script>
</head>
<body>
  <h2>🎂 Crear tu pastel personalizado</h2>
  <form action="../../controllers/cliente/guardar_pastel.php" method="POST" class="pastel-form">

    <label>Color del pastel:</label>
    <select name="RELA_color_pastel" required>
      <?php while($c = $colores->fetch_assoc()): ?>
        <option value="<?= $c['id_color_pastel'] ?>"><?= $c['color_pastel_nombre'] ?></option>
      <?php endwhile; ?>
    </select>

    <label>Decoración:</label>
    <select name="RELA_decoracion" required>
      <?php while($d = $decoraciones->fetch_assoc()): ?>
        <option value="<?= $d['id_decoracion'] ?>"><?= $d['decoracion_nombre'] ?></option>
      <?php endwhile; ?>
    </select>

    <label>Base:</label>
    <select name="RELA_base_pastel" required>
      <?php while($b = $bases->fetch_assoc()): ?>
        <option value="<?= $b['id_base_pastel'] ?>"><?= $b['base_pastel_nombre'] ?></option>
      <?php endwhile; ?>
    </select>

    <h3>⚡ Pisos</h3>
    <div id="pisos-container"></div>
    <button type="button" onclick="agregarPiso()">➕ Agregar Piso</button>

    <button type="submit">Guardar Pastel</button>
  </form>
</body>
</html>
