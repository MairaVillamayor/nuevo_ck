<?PHP
require_once __DIR__ . '/../../config/conexion.php';
include("../../includes/navegacion.php");

session_start();
$pdo = getConexion();

// -----------------
// 1) Validar sesión
// -----------------
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../views/usuario/login.php?error=not_logged');
    exit;
}
$id_usuario = $_SESSION['usuario_id']; ?>


<?php if (isset($_SESSION['success'])): ?>
    <div class="cp-alert cp-success">
        <div class="cp-icon">✅</div>
        <div class="cp-text"><?= $_SESSION['success'] ?></div>
        <a class="cp-btn" href="pago.php?id_pedido=<?= $id_pedido ?>">Ir a pagar</a>
        <form method="POST" action="cancelar_pedido.php" style="display:inline;">
            <input type="hidden" name="id_pedido" value="<?= $id_pedido ?>">
            <button type="submit" class="cp-btn-cancel">❌ Cancelar Pedido</button>
        </form>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Guardado - Documento no válido como factura</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* CSS para simular la factura no válida */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #fff5f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #333;
        }

        .factura-no-valida {
            width: 350px; /* Ancho similar al de la imagen */
            padding: 30px;
            background-color: #ffffff;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            font-size: 0.9em;
            line-height: 1.4;
        }

        .header-factura {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .box-x {
            border: 2px solid #000;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2.5em;
            font-weight: bold;
            margin-right: 15px;
        }

        .titulo-presupuesto {
            font-size: 1.5em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        .doc-no-valido {
            text-align: center;
            font-size: 0.8em;
            margin-top: -10px;
            margin-bottom: 20px;
            color: #555;
        }

        .linea-horizontal {
            border-bottom: 1px solid #000;
            margin-bottom: 15px;
        }

        .datos-cliente p {
            margin: 5px 0;
        }

        .datos-cliente p strong {
            display: inline-block;
            width: 80px; /* Ajusta esto si los labels son más largos */
        }
        
        .cuit-label {
            display: inline-block;
            margin-left: 20px;
            font-weight: bold;
        }

        .tabla-productos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .tabla-productos th, .tabla-productos td {
            border: 1px dashed #bbb; /* Simula las líneas punteadas */
            padding: 8px;
            text-align: left;
            vertical-align: top;
            font-family: 'Roboto Mono', monospace; /* Fuente monoespaciada para el contenido */
            font-size: 0.85em;
        }

        .tabla-productos th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        
        /* Columnas de la tabla, ajustadas al ancho de la imagen */
        .col-cantidad { width: 15%; text-align: right; }
        .col-descripcion { width: 60%; }
        .col-importe { width: 25%; text-align: right; }

        .total-container {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-top: 20px;
            font-size: 1.1em;
            font-family: 'Roboto Mono', monospace;
            font-weight: bold;
        }

        .total-container .label {
            background-color: #eee;
            padding: 5px 10px;
            border: 1px solid #ccc;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .total-container .valor {
            border-bottom: 2px solid #000; /* Línea debajo del total */
            padding-bottom: 2px;
            min-width: 80px; /* Para que la línea se vea bien */
            text-align: right;
        }

        .pie-factura {
            text-align: center;
            font-size: 0.7em;
            margin-top: 30px;
            color: #888;
        }
    </style>
</head>
<body>
    
<?php
// -----------------
// 2) Recibir POST
// -----------------
$color         = isset($_POST['RELA_color_pastel']) ? (int)$_POST['RELA_color_pastel'] : null;
$decoracion    = isset($_POST['RELA_decoracion']) ? (int)$_POST['RELA_decoracion'] : null;
$base          = isset($_POST['RELA_base_pastel']) ? (int)$_POST['RELA_base_pastel'] : null;
$pisos         = $_POST['pisos'] ?? [];
$materiales    = $_POST['material_extra_nombre'] ?? [];
$direccion_envio = trim($_POST['pedido_direccion_envio'] ?? '');
$metodos_pago  = isset($_POST['RELA_metodo_pago']) ? (int)$_POST['RELA_metodo_pago'] : null;

// Validaciones mínimas
if (!$color || !$decoracion || !$base || empty($direccion_envio) || !$metodos_pago) {
    die("Faltan datos obligatorios.");
}

// ------------------------------------
// 3) INICIAMOS LA TRANSACCIÓN
// ------------------------------------
try {
    $pdo->beginTransaction();

    // -----------------
    // 4) Calcular precio total y construir descripción
    // -----------------
    $total = 0;
    $descripcion = "";

    // Precio y descripción de la base, decoración y pisos
    // Este código necesita optimización para evitar múltiples consultas en bucles.
    // Un solo query con `IN` sería ideal. Por ahora, lo dejamos como está para corregir lo más crítico.

    // Base
    $stmt = $pdo->prepare("SELECT base_pastel_nombre, base_pastel_precio FROM base_pastel WHERE ID_base_pastel = ?");
    $stmt->execute([$base]);
    $base_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($base_data) $total += $base_data['base_pastel_precio'];

    // Decoración
    $stmt = $pdo->prepare("SELECT decoracion_nombre, decoracion_precio FROM decoracion WHERE ID_decoracion = ?");
    $stmt->execute([$decoracion]);
    $decor_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($decor_data) $total += $decor_data['decoracion_precio'];

    // Pisos
    $descripcion .= "Pastel de " . max(1, count($pisos)) . " pisos";
    $detalles = [];

    foreach ($pisos as $num => $datos) {
        $tamaño_id  = (int)($datos['RELA_tamaño'] ?? 0);
        $sabor_id   = (int)($datos['RELA_sabor'] ?? 0);
        $relleno_id = (int)($datos['RELA_relleno'] ?? 0);

        $nombreTam = $nombreSab = $nombreRel = '';

        if ($tamaño_id) {
            $stmt = $pdo->prepare("SELECT tamaño_nombre, tamaño_precio FROM tamaño WHERE ID_tamaño = ?");
            $stmt->execute([$tamaño_id]);
            if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $nombreTam = $r['tamaño_nombre'];
                $total += $r['tamaño_precio'];
            }
        }
        if ($sabor_id) {
            $stmt = $pdo->prepare("SELECT sabor_nombre, sabor_precio FROM sabor WHERE ID_sabor = ?");
            $stmt->execute([$sabor_id]);
            if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $nombreSab = $r['sabor_nombre'];
                $total += $r['sabor_precio'];
            }
        }
        if ($relleno_id) {
            $stmt = $pdo->prepare("SELECT relleno_nombre, relleno_precio FROM relleno WHERE ID_relleno = ?");
            $stmt->execute([$relleno_id]);
            if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $nombreRel = $r['relleno_nombre'];
                $total += $r['relleno_precio'];
            }
        }
        $detalles[] = "Piso $num: $nombreTam de $nombreSab con relleno de $nombreRel";
    }

    // Materiales extra
    if (!empty($materiales)) {
        $nombresExtra = [];
        $stmt = $pdo->prepare("SELECT material_extra_nombre, material_extra FROM material_extra WHERE ID_material_extra = ?");
        foreach ($materiales as $mid) {
            $stmt->execute([(int)$mid]);
            if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $nombresExtra[] = $r['material_extra_nombre'];
                $total += $r['material_extra'];
            }
        }
        if ($nombresExtra) $descripcion .= ". Materiales extra: " . implode(", ", $nombresExtra);
    }

    // Construir descripción final
    if ($detalles) $descripcion .= ". " . implode("; ", $detalles);


    // -----------------
    // 5) Guardar pastel_personalizado
    // -----------------
    $ins = $pdo->prepare("INSERT INTO pastel_personalizado 
        (pastel_personalizado_descripcion, pastel_personalizado_pisos_total, RELA_color_pastel, RELA_decoracion, RELA_base_pastel) 
        VALUES (?, ?, ?, ?, ?)");
    $ins->execute([$descripcion, count($pisos), $color, $decoracion, $base]);
    $id_pastel_personalizado = $pdo->lastInsertId();
    if (!$id_pastel_personalizado) throw new Exception("No se pudo insertar pastel personalizado.");

    // Guardar relación con materiales extra
    if (!empty($materiales)) {
        $stmtExtra = $pdo->prepare("INSERT INTO pastel_material_extra (RELA_pastel_personalizado, RELA_material_extra) VALUES (?, ?)");
        foreach ($materiales as $mid) {
            $stmtExtra->execute([$id_pastel_personalizado, (int)$mid]);
        }
    }

    // -----------------
    // 6) Guardar pisos, sabores y rellenos
    // -----------------
    $insP = $pdo->prepare("INSERT INTO pisos (pisos_numero, RELA_pastel_personalizado, RELA_tamaño) VALUES (?, ?, ?)");
    $insS = $pdo->prepare("INSERT INTO pisos_sabor (RELA_sabor, RELA_pisos) VALUES (?, ?)");
    $insR = $pdo->prepare("INSERT INTO pisos_relleno (RELA_pisos, RELA_relleno) VALUES (?, ?)");

    foreach ($pisos as $piso_numero => $datos) {
        $tamaño = isset($datos['RELA_tamaño']) ? (int)$datos['RELA_tamaño'] : null;
        $sabor  = isset($datos['RELA_sabor']) ? (int)$datos['RELA_sabor'] : null;
        $relleno = isset($datos['RELA_relleno']) ? (int)$datos['RELA_relleno'] : null;

        $insP->execute([$piso_numero, $id_pastel_personalizado, $tamaño]);
        $id_piso = $pdo->lastInsertId();
        if (!$id_piso) throw new Exception("No se pudo insertar piso.");

        if ($sabor) $insS->execute([$sabor, $id_piso]);
        if ($relleno) $insR->execute([$id_piso, $relleno]);
    }

    // -----------------
    // 7) Guardar pedido principal
    // -----------------
    $sql_pedido = "INSERT INTO pedido 
        (pedido_fecha, pedido_direccion_envio, RELA_usuario, RELA_metodo_pago, RELA_estado) 
        VALUES (NOW(), :direccion_envio, :usuario_id, :metodo_pago, :estado)";

    $stmt = $pdo->prepare($sql_pedido);
    $stmt->execute([
        ':direccion_envio' => $direccion_envio,
        ':usuario_id'      => $_SESSION['usuario_id'],
        ':metodo_pago'     => $metodos_pago,
        ':estado'          => 1 // por defecto pendiente
    ]);

    $id_pedido = $pdo->lastInsertId();
    if (!$id_pedido) throw new Exception("No se pudo generar el ID del pedido.");

    // -----------------
    // 8) Guardar detalle del pedido (un solo pastel)
    // -----------------
    $sql_detalle = "INSERT INTO pedido_detalle (RELA_pedido, RELA_pastel_personalizado, pedido_detalle_cantidad, pedido_detalle_precio_total) 
                    VALUES (:pedido_id, :pastel_personalizado_id, :cantidad, :precio_total)";
    $stmt_detalle = $pdo->prepare($sql_detalle);
    $stmt_detalle->execute([
        ':pedido_id'                => $id_pedido,
        ':pastel_personalizado_id'  => $id_pastel_personalizado,
        ':cantidad'                 => 1, // Asumiendo que es un solo pastel por pedido_detalle
        ':precio_total'             => $total
    ]);

    // ------------------------------------
    // 9) SI TODO ES CORRECTO, CONFIRMAMOS LA TRANSACCIÓN
    // ------------------------------------
    $pdo->commit();



} catch (Exception $e) {
    // Si algo falló, revertimos todos los cambios y mostramos un error
    $pdo->rollBack();
    die("❌ Error al guardar el pedido: " . $e->getMessage());
}
?>

<div class="factura-no-valida">
    <div class="header-factura">
        <div class="box-x">X</div>
        <div>
            <div class="titulo-presupuesto">PRESUPUESTO</div>
            <div style="display: flex; gap: 5px; margin-top: 5px;">
                <div style="border: 1px solid #000; width: 20px; height: 18px;"></div>
                <div style="border: 1px solid #000; width: 20px; height: 18px;"></div>
                <div style="border: 1px solid #000; width: 20px; height: 18px;"></div>
            </div>
        </div>
    </div>
    
    <div class="doc-no-valido">Documento no válido como factura</div>

    <div class="datos-cliente">
        <p><strong>Sr/les:</strong> Cliente de Cake Party</p>
        <p><strong>Dirección:</strong> <?= htmlspecialchars($direccion_envio) ?></p>
        <p><strong>Localidad:</strong> Tu Localidad <span class="cuit-label">C.U.I.T.:</span> ___________</p>
    </div>

    <div class="linea-horizontal"></div>

    <table class="tabla-productos">
        <thead>
            <tr>
                <th class="col-cantidad">Cant.</th>
                <th class="col-descripcion">Descripción</th>
                <th class="col-importe">Importe</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="col-cantidad">1</td>
                <td class="col-descripcion">Pastel Personalizado #<?= htmlspecialchars($id_pastel_personalizado) ?>: <?= htmlspecialchars($descripcion) ?></td>
                <td class="col-importe">$<?= number_format($total, 2) ?></td>
            </tr>
            <tr>
                <td class="col-cantidad"></td>
                <td class="col-descripcion"></td>
                <td class="col-importe"></td>
            </tr>
            <tr>
                <td class="col-cantidad"></td>
                <td class="col-descripcion"></td>
                <td class="col-importe"></td>
            </tr>
            <tr>
                <td class="col-cantidad"></td>
                <td class="col-descripcion"></td>
                <td class="col-importe"></td>
            </tr>
        </tbody>
    </table>

    <div class="total-container">
        <div class="label">Total</div>
        <div class="valor">$<?= number_format($total, 1) ?></div>
    </div>

    <div class="pie-factura">
        <span>Gracias por su compra</span>
        <span style="margin-left: 20px;">00033951</span>
    </div>
</div>

</body>
</html>

<style>
  .btn-cake {
    display: block;              /* ✅ uno debajo del otro */
    width: 220px;                /* ancho fijo, podés ajustarlo */
    margin: 10px auto;           /* centrados con auto */
    padding: 12px;
    font-size: 15px;
    font-weight: bold;
    border-radius: 8px;
    border: none;
    text-decoration: none;
    text-align: center;
    color: #fff;
    transition: background 0.3s ease, transform 0.2s ease;
  }

  /* Variantes */
  .btn-primary {
    background-color: #e91e63;
  }
  .btn-primary:hover {
    background-color: #d81b60;
    transform: translateY(-2px);
  }

  .btn-success {
    background-color: #4caf50;
  }
  .btn-success:hover {
    background-color: #388e3c;
    transform: translateY(-2px);
  }

  .btn-danger {
    background-color: #de0505ff;
  }
  .btn-danger:hover {
    background-color: #c40303ff;
    transform: translateY(-2px);
  }

  .botones-container {
    text-align: center;
    margin-top: 20px;
  }
</style>

<div class="botones-container">
  <a href="../../views/cliente/mis_pedidos.php" class="btn-cake btn-primary">Volver a Mis Pedidos</a>
  <a href="#" class="btn-cake btn-success">Pagar Ahora 💳</a>
  <a href="#" class="btn-cake btn-danger">Cancelar Pedido ❌</a>
</div>
