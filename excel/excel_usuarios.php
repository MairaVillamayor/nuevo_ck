<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Generar un Reporte de Usuarios en Excel</title>
  <script src="tableToExcel.js"></script>
  <style>
    /* Contenedor con scroll horizontal */
    .tabla-scroll {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      width: 90%;
      margin: 0 auto;
      padding-bottom: 15px;
    }

    /* Tabla Cake Party en Blanco y Negro */
    .tablalistado {
      border-collapse: separate;
      border-spacing: 0;
      min-width: 900px;
      /* Puedes ajustar esto si necesitas más columnas */
      background: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(247, 145, 225, 0.05);
      font-family: 'Poppins', sans-serif;
    }

    /* Encabezados */
    .tablalistado th {
      background-color: #333;
      color: #ffffff;
      font-weight: 600;
      font-size: 15px;
      padding: 12px;
      text-align: center;
      letter-spacing: 0.5px;
    }

    /* Filas y celdas */
    .tablalistado td {
      padding: 10px 12px;
      font-size: 14px;
      color: #222222;
      text-align: center;
      border-bottom: 1px solid #dddddd;
      transition: background 0.3s ease;
    }

    /* Filas alternas */
    .tablalistado tr:nth-child(even) td {
      background-color: #f9f9f9;
    }

    /* Hover */
    .tablalistado tr:hover td {
      background-color: #eaeaea;
    }

    /* Última fila sin borde */
    .tablalistado tr:last-child td {
      border-bottom: none;
    }

    /* Título centrado */
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
    <h1>Reporte Excel de Usuarios</h1>
    <p>Exportando un Reporte</p>
    <input type="button" onclick="tableToExcel('usuariosTable', 'Reporte de Usuarios')" value="Exportar a Excel">
  </div>

  <!-- Scroll horizontal -->
  <div class="tabla-scroll">
    <table class="tablalistado" id="usuariosTable" border="2">
      <thead>
        <tr>
          <th>ID Usuario</th>
          <th>NOMBRE USUARIO</th>
          <th>CORREO ELECTRONICO</th>
          <th>CONTRASEÑA</th>
          <th>NUM CELULAR</th>
          <th>NOMBRE</th>
          <th>APELLIDO</th>
          <th>FECHA DE NACIMIENTO</th>
          <th>DIRECCIÓN</th>
          <th>ROL</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $conn = mysqli_connect("localhost", "root", "Maira_2023", "cake_party") or die("Error de conexión");
        $sql = " SELECT ID_usuario, 
                        usuario_nombre, 
                        usuario_correo_electronico, 
                        usuario_contraseña,  
                        usuario_numero_de_celular, 
                        persona_nombre, 
                        persona_apellido, 
                        persona_fecha_nacimiento, 
                        persona_direccion,
                        perfil_rol
                  FROM usuarios AS u 
                  JOIN persona AS p ON u.RELA_persona = p.ID_persona
                  JOIN perfiles AS pe ON pe.ID_perfil = u.RELA_perfil";

        $resultset = mysqli_query($conn, $sql);
        if (mysqli_num_rows($resultset) > 0) {
          while ($row = mysqli_fetch_assoc($resultset)) {
        ?>
            <tr>
              <td><?php echo $row['ID_usuario']; ?></td>
              <td><?php echo $row['usuario_nombre']; ?></td>
              <td><?php echo $row['usuario_correo_electronico']; ?></td>
              <td><?php echo $row['usuario_contraseña']; ?></td>
              <td><?php echo $row['usuario_numero_de_celular']; ?></td>
              <td><?php echo $row['persona_nombre']; ?></td>
              <td><?php echo $row['persona_apellido']; ?></td>
              <td><?php echo $row['persona_fecha_nacimiento']; ?></td>
              <td><?php echo $row['persona_direccion']; ?></td>
              <td><?php echo $row['perfil_rol']; ?></td>

            </tr>
          <?php
          }
        } else {
          ?>
          <tr>
            <td colspan="7">No hay registros para mostrar.</td>
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