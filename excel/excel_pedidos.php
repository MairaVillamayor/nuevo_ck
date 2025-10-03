<?php include('../includes/navegacion.php')?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reporte de Pedidos en Excel</title>
  <script src="tableToExcel.js"></script>
  <style>
    .tabla-scroll {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      width: 90%;
      margin: 0 auto;
      padding-bottom: 15px;
    }

    .tablalistado {
      border-collapse: separate;
      border-spacing: 0;
      min-width: 1100px;
      background: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(247, 145, 225, 0.05);
      font-family: 'Poppins', sans-serif;
    }

    .tablalistado th {
      background-color: #333;
      color: #ffffff;
      font-weight: 600;
      font-size: 15px;
      padding: 12px;
      text-align: center;
      letter-spacing: 0.5px;
    }

    .tablalistado td {
      padding: 10px 12px;
      font-size: 14px;
      color: #222222;
      text-align: center;
      border-bottom: 1px solid #dddddd;
      transition: background 0.3s ease;
    }

    .tablalistado tr:nth-child(even) td {
      background-color: #f9f9f9;
    }

    .tablalistado tr:hover td {
      background-color: #eaeaea;
    }

    .tablalistado tr:last-child td {
      border-bottom: none;
    }

    .titulo-reporte {
      text-align: center;
      font-family: 'Poppins', sans-serif;
      color: #333;
    }

    body {
      background-color: #f3c1d7;
      margin: 0;
      padding: 20px 0;
    }

    input[type="button"] {
      padding: 10px 20px;
      margin: 20px;
      font-size: 14px;
      border: none;
      border-radius: 5px;
      background-color: #333;
      color: #ffffff;
      cursor: pointer;
    }

    input[type="button"]:hover {
      background-color: #555555;
    }
  </style>
</head>

<body>

  <div class="titulo-reporte">
    <h1>📦 Reporte Excel de Pedidos</h1>
    <p>Exportando pedidos registrados</p>
    <input type="button" onclick="tableToExcel('pedidosTable', 'Reporte de Pedidos')" value="Exportar a Excel">
  </div>

  <div class="tabla-scroll">
    <table class="tablalistado" id="pedidosTable" border="2">
      <thead>
        <tr>
          <th>ID Pedido</th>
          <th>Fecha</th>
          <th>Usuario</th>
          <th>Nombre</th>
          <th>Apellido</th>
          <th>Descripción Pastel</th>
          <th>Fecha y Hora de Entrega</th>
          <th>Dirección de Envío</th>
          <th>Localidad</th>
          <th>Barrio</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $conn = mysqli_connect("localhost", "root", "Maira_2023", "cake_party") or die("Error de conexión");
        $sql = "SELECT 
    pe.ID_pedido,
    pe.pedido_fecha,
    p_envio.envio_fecha_hora_entrega,
    p_envio.envio_calle_numero,
    p_envio.envio_barrio,
    p_envio.envio_localidad,
    u.usuario_nombre,
    per.persona_nombre,
    per.persona_apellido,
    pp.pastel_personalizado_descripcion,
    mp.metodo_pago_descri AS metodo_pago,
    e.ID_estado,
    e.estado_descri AS estado_descri
FROM pedido pe
LEFT JOIN usuarios u 
    ON pe.RELA_usuario = u.ID_usuario
LEFT JOIN persona per
    ON per.ID_persona = u.RELA_persona
LEFT JOIN pedido_detalle pd 
    ON pd.RELA_pedido = pe.ID_pedido
LEFT JOIN pastel_personalizado pp 
    ON pp.ID_pastel_personalizado = pd.RELA_pastel_personalizado
LEFT JOIN metodo_pago mp 
    ON pe.RELA_metodo_pago = mp.ID_metodo_pago
LEFT JOIN estado e 
    ON pe.RELA_estado = e.ID_estado
LEFT JOIN pedido_envio p_envio
    ON pe.RELA_pedido_envio = p_envio.ID_pedido_envio
ORDER BY pe.ID_pedido ASC;";

        $resultset = mysqli_query($conn, $sql);
        if (mysqli_num_rows($resultset) > 0) {
          while ($row = mysqli_fetch_assoc($resultset)) {
        ?>
            <tr>
              <td><?php echo $row['ID_pedido']; ?></td>
              <td><?php echo $row['pedido_fecha']; ?></td>
              <td><?php echo $row['usuario_nombre']; ?></td>
              <td><?php echo $row['persona_nombre']; ?></td>
              <td><?php echo $row['persona_apellido']; ?></td>
              <td><?php echo $row['pastel_personalizado_descripcion']; ?></td>
              <td><?php echo $row['envio_fecha_hora_entrega']; ?></td>
              <td><?php echo $row['envio_calle_numero']; ?></td>
              <td><?php echo $row['envio_localidad']; ?></td>
              <td><?php echo $row['envio_barrio']; ?></td>

              <td><?php echo $row['estado_descri']; ?></td>
            </tr>
          <?php
          }
        } else {
          ?>
          <tr>
            <td colspan="7">No hay pedidos registrados.</td>
          </tr>
        <?php
        }
        mysqli_close($conn);
        ?>
      </tbody>
    </table>
  </div>

</body>

</html>