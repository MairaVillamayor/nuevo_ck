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
$tamanos = $conexion->query("SELECT id_tamaño, tamaño_nombre, tamaño_medidas FROM tamaño")->fetchAll(PDO::FETCH_ASSOC);
$sabores = $conexion->query("SELECT id_sabor, sabor_nombre FROM sabor")->fetchAll(PDO::FETCH_ASSOC);
$rellenos = $conexion->query("SELECT id_relleno, relleno_nombre FROM relleno")->fetchAll(PDO::FETCH_ASSOC);
$materiales = $conexion->query("SELECT ID_material_extra, material_extra_nombre, material_extra_descri 
                                FROM material_extra WHERE RELA_estado_insumos = 1")->fetchAll(PDO::FETCH_ASSOC);

$materiales_agrupados = [];
foreach ($materiales as $m) {
  $nombre = $m['material_extra_nombre'];
  if (!isset($materiales_agrupados[$nombre])) {
    $materiales_agrupados[$nombre] = [];
  }
  $materiales_agrupados[$nombre][] = $m;
}

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
      const container = document.getElementById("pisos-container");
      const alerta = document.getElementById("alerta-limite-pisos");

      const MAX_PISOS = 3;
      const numPisosActual = container.getElementsByClassName("piso").length;

      alerta.style.display = 'none';

      if (numPisosActual >= MAX_PISOS) {
        alerta.style.display = 'block';
        return;
      }

      pisoCount++;

      const div = document.createElement("div");
      div.classList.add("piso");
      div.innerHTML = `
        <h4>Piso ${pisoCount}</h4>

        <label>Tamaño:</label>
        <select name="pisos[${pisoCount}][RELA_tamaño]" required>
          <?php foreach ($tamanos as $t): ?>
            <option value="<?= $t['id_tamaño'] ?>"> <?= $t['tamaño_nombre'] ?> (<?= $t['tamaño_medidas'] ?>) </option>
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
        <button type="button" onclick="eliminarPiso(this)">❌ Eliminar Piso</button>
        <hr>
      `;
      container.appendChild(div);
    }

    function eliminarPiso(button) {
      const pisoDiv = button.closest('.piso');
      if (pisoDiv) {
        pisoDiv.remove();

        document.getElementById("alerta-limite-pisos").style.display = 'none';

      }
    }
  </script>
</head>

<body>
  <h2>🎂 Crear pastel personalizado</h2>

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

    <?php foreach ($materiales_agrupados as $nombre_grupo => $opciones_grupo): ?>
      <details class="material-extra-group">
        <summary>
          <strong><?= htmlspecialchars($nombre_grupo) ?></strong>
        </summary>

        <div class="opciones-container">
          <?php foreach ($opciones_grupo as $opcion): ?>
            <label class="opcion-item">
              <input
                type="checkbox"
                name="material_extra[]"
                value="<?= htmlspecialchars($opcion['ID_material_extra']) ?>">
              <?= htmlspecialchars($opcion['material_extra_descri']) ?>

            </label><br>
          <?php endforeach; ?>
        </div>
      </details>
    <?php endforeach; ?>

    <!-- Pisos dinámicos -->
    <h3>⚡ Pisos</h3>
    <div id="pisos-container"></div>
    <div id="alerta-limite-pisos" class="alerta-personalizada" style="display: none;">
      ⚠️ ¡Atención! Solo puedes agregar un máximo de 3 pisos.
    </div>
    <button type="button" onclick="agregarPiso()">➕ Agregar Piso</button>

    <!-- Dirección de envío -->
    <h3>🚚 Datos de envío</h3>

    <div style="
    display: flex; 
    flex-wrap: wrap; 
    gap: 15px; 
    justify-content: space-between;
    margin-bottom: 15px;">

      <div style="flex: 1 1 100%;">
        <label for="envio_fecha_hora_entrega">Fecha y Hora de Entrega:</label>
        <input
          type="datetime-local"
          id="envio_fecha_hora_entrega"
          name="envio_fecha_hora_entrega"
          placeholder="Ej: 2024-12-31 15:30"
          required
          style="width: 100%;">
      </div>

      <div style="flex: 1 1 100%;">
        <label for="envio_calle_numero">Calle y Número:</label>
        <input
          type="text"
          id="envio_calle_numero"
          name="envio_calle_numero"
          placeholder="Ej: Av. 25 de Mayo 1234"
          required
          style="width: 100%;">
      </div>

      <div style="display: flex; gap: 10px; width: 100%;">
        <div style="flex: 1;">
          <label for="envio_piso">Piso (Opcional):</label>
          <input
            type="text"
            id="envio_piso"
            name="envio_piso"
            placeholder="Ej: 5"
            style="width: 100%;">
        </div>

        <div style="flex: 1;">
          <label for="envio_dpto">Dpto (Opcional):</label>
          <input
            type="text"
            id="envio_dpto"
            name="envio_dpto"
            placeholder="Ej: A"
            style="width: 100%;">
        </div>
      </div>

      <div style="flex: 1 1 30%;">
        <label for="envio_localidad">Localidad:</label>
        <input
          type="text"
          id="envio_localidad"
          name="envio_localidad"
          placeholder="Ej: Formosa"
          required
          style="width: 100%;">
      </div>

      <div style="flex: 1 1 30%;">
        <label for="envio_barrio">Barrio:</label>
        <input
          type="text"
          id="envio_barrio"
          name="envio_barrio"
          placeholder="Ej:  Centro"
          required
          style="width: 100%;">
      </div>

      <div style="flex: 1 1 30%;">
        <label for="envio_cp">Código Postal (CP):</label>
        <input
          type="text"
          id="envio_cp"
          name="envio_cp"
          placeholder="Ej: 3600"
          required
          style="width: 100%;">
      </div>
      <div style="flex: 1 1 30%;">
        <label for="envio_provincia">Provincia:</label>
        <input
          type="text"
          id="envio_provincia"
          name="envio_provincia"
          placeholder="Ej: Formosa"
          required
          style="width: 100%;">
      </div>

      <div style="flex: 1 1 100%;">
        <label for="envio_telefono_contacto">Teléfono de Contacto (con código de área):</label>
        <input
          type="tel"
          id="envio_telefono_contacto"
          name="envio_telefono_contacto"
          placeholder="Ej: 54 3704 1234"
          required
          style="width: 100%;">
      </div>
    </div>

    <label for="envio_referencias">Referencias para el Repartidor (Máx. 250 caracteres):</label>
    <textarea
      id="envio_referencias"
      name="envio_referencias"
      rows="3"
      maxlength="250"
      placeholder="Ej: Portón verde, casa con rejas blancas, tocar timbre de la izquierda."
      style="width: 100%;"></textarea>

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