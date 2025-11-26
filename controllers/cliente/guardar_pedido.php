<?php
require_once __DIR__ . '/../../config/conexion.php';
include("../../includes/navegacion.php");

session_start();
$pdo = getConexion();


// ------------------------------------
// 1) VALIDACIONES Y OBTENCIÓN DE DATOS
// ------------------------------------
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php?error=not_logged');
    exit;
}
$id_usuario = $_SESSION['usuario_id'];


$nombre_cliente = 'Cliente';
$apellido_cliente = 'Invitado';

try {
    $stmt_cliente = $pdo->prepare(" SELECT 
            p.persona_nombre, 
            p.persona_apellido 
        FROM usuarios u
        JOIN persona p ON u.RELA_persona = p.ID_persona 
        WHERE u.ID_usuario = ?");

    $stmt_cliente->execute([$id_usuario]);
    $datos_cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

    if ($datos_cliente) {
        $nombre_cliente = $datos_cliente['persona_nombre'];
        $apellido_cliente = $datos_cliente['persona_apellido'];
    }
} catch (Exception $e) {
    error_log("Error al buscar nombre del cliente: " . $e->getMessage());
    die("Error al obtener datos del cliente.");
}

// ------------------------------------
// 2) RECIBIR POST Y SANEAMIENTO
// ------------------------------------

$color = filter_input(INPUT_POST, 'RELA_color_pastel', FILTER_VALIDATE_INT);
$color = $color === false || $color === null ? null : $color;
$decoracion     = isset($_POST['RELA_decoracion']) ? (int)$_POST['RELA_decoracion'] : null;
$base           = isset($_POST['RELA_base_pastel']) ? (int)$_POST['RELA_base_pastel'] : null;
$pisos          = $_POST['pisos'] ?? [];
$materiales     = $_POST['material_extra'] ?? [];
$metodos_pago   = isset($_POST['RELA_metodo_pago']) ? (int)$_POST['RELA_metodo_pago'] : null;

// Datos de Envío
$hora_fecha_entrega = trim($_POST['envio_fecha_hora_entrega'] ?? '');
$calle_numero       = trim($_POST['envio_calle_numero'] ?? '');
$piso = trim($_POST['envio_piso'] ?? '');
$dpto = trim($_POST['envio_dpto'] ?? '');
$piso = (empty($piso) && $piso !== '0') ? null : $piso;
$dpto = (empty($dpto) && $dpto !== '0') ? null : $dpto;
$pedido_barrio      = trim($_POST['envio_barrio'] ?? '');
$pedido_localidad   = trim($_POST['envio_localidad'] ?? '');
$cp                 = trim($_POST['envio_cp'] ?? '');
$provincia          = trim($_POST['envio_provincia'] ?? '');
$referencias        = trim($_POST['envio_referencias'] ?? '');
$telefono_contacto  = trim($_POST['envio_telefono_contacto'] ?? '');

// Validación de datos esenciales
if (!$color || !$decoracion || !$base || empty($calle_numero) || empty($pedido_localidad) || empty($cp) || !$metodos_pago) {
    die("Faltan datos obligatorios para el pastel o la dirección.");
}


// ------------------------------------
// 3) TRANSACCIÓN DE GUARDADO EN BD
// ------------------------------------
$id_pedido = null;
$id_pastel_personalizado = null;
$total = 0;
$descripcion = "";


try {
    $pdo->beginTransaction();

    // 3.1) Guardar la Dirección de Envío
    $sql_envio = "INSERT INTO pedido_envio (
        envio_fecha_hora_entrega, envio_calle_numero, 
        envio_piso, envio_dpto, envio_barrio, 
        envio_localidad, envio_cp, envio_provincia, envio_referencias, 
        envio_telefono_contacto
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_envio = $pdo->prepare($sql_envio);
    $stmt_envio->execute([
        $hora_fecha_entrega,
        $calle_numero,
        $piso,
        $dpto,
        $pedido_barrio,
        $pedido_localidad,
        $cp,
        $provincia,
        $referencias,
        $telefono_contacto
    ]);
    $id_envio = $pdo->lastInsertId();
    if (!$id_envio) throw new Exception("No se pudo insertar la dirección de envío.");

    // 3.2) Calcular precio total y construir descripción

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
        // Lógica de cálculo de precios para tamaños, sabores y rellenos (reducida por espacio)
        $tamaño_id  = (int)($datos['RELA_tamaño'] ?? 0);
        $sabor_id   = (int)($datos['RELA_sabor'] ?? 0);
        $relleno_id = (int)($datos['RELA_relleno'] ?? 0);
        $nombreTam = $nombreSab = $nombreRel = 'Detalle'; // Valores de ejemplo para la descripción
        $detalles[] = "Piso $num: $nombreTam de $nombreSab con relleno de $nombreRel";
    }
    // Materiales extra
    if (!empty($materiales)) {
        $nombresExtra = [];
        $coloresExtra = $_POST['color_material_extra'] ?? []; // array asociativo: [id_material => color]

        // Preparar la consulta
        $stmt_mat = $pdo->prepare("SELECT material_extra_nombre, material_extra_precio FROM material_extra WHERE ID_material_extra = ?");

        foreach ($materiales as $mid) {
            $mid_int = (int)$mid;
            $stmt_mat->execute([$mid_int]);
            $mat_data = $stmt_mat->fetch(PDO::FETCH_ASSOC);

            if ($mat_data) {
                // Sumar precio
                $total += $mat_data['material_extra_precio'];

                // Obtener color si fue ingresado
                $color = trim($coloresExtra[$mid_int] ?? '');

                // Agregar nombre + color a la descripción
                if ($color !== '') {
                    $nombresExtra[] = "{$mat_data['material_extra_nombre']} (Color: {$color})";
                } else {
                    $nombresExtra[] = $mat_data['material_extra_nombre'];
                }
            }
        }

        if ($nombresExtra) {
            $descripcion .= ". Materiales extra: " . implode(", ", $nombresExtra);
        }
    }

    // 3.3) Guardar pastel_personalizado
    $ins = $pdo->prepare("INSERT INTO pastel_personalizado 
        (pastel_personalizado_descripcion, pastel_personalizado_pisos_total, RELA_color_pastel, RELA_decoracion, RELA_base_pastel) 
        VALUES (?, ?, ?, ?, ?)");
    error_log("Valor de \$color antes del INSERT: " . var_export($color, true));
    $ins->execute([$descripcion, count($pisos), $color, $decoracion, $base]);
    $id_pastel_personalizado = $pdo->lastInsertId();
    if (!$id_pastel_personalizado) throw new Exception("No se pudo insertar pastel personalizado.");

    // 3.4) Guardar relación de materiales extra
    if (!empty($materiales)) {
        $stmtExtra = $pdo->prepare("INSERT INTO pastel_material_extra (RELA_pastel_personalizado, RELA_material_extra) VALUES (?, ?)");
        foreach ($materiales as $mid) {
            $stmtExtra->execute([$id_pastel_personalizado, (int)$mid]);
        }
    }

    // 3.5) Guardar pisos, sabores y rellenos
    // Pisos
    $descripcion .= "Pastel de " . max(1, count($pisos)) . " pisos";
    $detalles = [];
    foreach ($pisos as $num => $datos) {
        $tamaño_id  = (int)($datos['RELA_tamaño'] ?? 0);
        $sabor_id   = (int)($datos['RELA_sabor'] ?? 0);
        $relleno_id = (int)($datos['RELA_relleno'] ?? 0);

        // 1. Obtener y sumar el precio del TAMAÑO
        $stmt_tam = $pdo->prepare("SELECT tamaño_nombre, tamaño_precio FROM tamaño WHERE ID_tamaño = ?");
        $stmt_tam->execute([$tamaño_id]);
        $tam_data = $stmt_tam->fetch(PDO::FETCH_ASSOC);
        if ($tam_data) {
            $total += $tam_data['tamaño_precio'];
            $nombreTam = $tam_data['tamaño_nombre'];
        } else {
            $nombreTam = 'Tamaño Desconocido';
        }

        // 2. Obtener y sumar el precio del SABOR
        $stmt_sab = $pdo->prepare("SELECT sabor_nombre, sabor_precio FROM sabor WHERE ID_sabor = ?");
        $stmt_sab->execute([$sabor_id]);
        $sab_data = $stmt_sab->fetch(PDO::FETCH_ASSOC);
        if ($sab_data) {
            $total += $sab_data['sabor_precio'];
            $nombreSab = $sab_data['sabor_nombre'];
        } else {
            $nombreSab = 'Sabor Desconocido';
        }

        // 3. Obtener y sumar el precio del RELLENO
        $stmt_rel = $pdo->prepare("SELECT relleno_nombre, relleno_precio FROM relleno WHERE ID_relleno = ?");
        $stmt_rel->execute([$relleno_id]);
        $rel_data = $stmt_rel->fetch(PDO::FETCH_ASSOC);
        if ($rel_data) {
            $total += $rel_data['relleno_precio'];
            $nombreRel = $rel_data['relleno_nombre'];
        } else {
            $nombreRel = 'Relleno Desconocido';
        }

        $detalles[] = "Piso $num: $nombreTam de $nombreSab con relleno de $nombreRel";
    }

    if (!empty($detalles)) {
        $descripcion .= ". " . implode(". ", $detalles);
    }

    // 3.6) Guardar pedido principal
    $sql_pedido = "INSERT INTO pedido 
        (pedido_fecha, RELA_pedido_envio, RELA_usuario, RELA_metodo_pago, RELA_estado) 
        VALUES (NOW(), :id_envio, :usuario_id, :metodo_pago, :estado)";

    $stmt = $pdo->prepare($sql_pedido);
    $stmt->execute([
        ':id_envio'  => $id_envio,
        ':usuario_id'    => $_SESSION['usuario_id'],
        ':metodo_pago'   => $metodos_pago,
        ':estado'        => 1 // Estado por defecto: Pendiente
    ]);

    $id_pedido = $pdo->lastInsertId();
    if (!$id_pedido) throw new Exception("No se pudo generar el ID del pedido.");

    // 3.7) Guardar detalle del pedido
    $sql_detalle = "INSERT INTO pedido_detalle (RELA_pedido, RELA_pastel_personalizado, pedido_detalle_cantidad, pedido_detalle_precio_total) 
                     VALUES (:pedido_id, :pastel_personalizado_id, :cantidad, :precio_total)";
    $stmt_detalle = $pdo->prepare($sql_detalle);
    $stmt_detalle->execute([
        ':pedido_id'                => $id_pedido,
        ':pastel_personalizado_id'  => $id_pastel_personalizado,
        ':cantidad'                 => 1,
        ':precio_total'             => $total
    ]);


    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    die("❌ Error al guardar el pedido: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Pedido Guardado</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #fff5f8;
            display: flex;
            flex-direction: column;
            /* Cambiado a columna para centrar mejor el contenido */
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #333;
            padding-top: 20px;
        }

        /* ESTILOS DEL CUADRADO ROSA */
        .resumen-pedido {
            width: 450px;
            max-width: 90%;
            padding: 30px;
            background-color: #fce4ec;
            /* Rosa muy pálido */
            border: 2px solid #e91e63;
            /* Borde del color principal */
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(233, 30, 99, 0.2);
            font-size: 1em;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #333;
        }

        .resumen-pedido h2 {
            text-align: center;
            color: #d81b60;
            margin-top: 0;
            font-size: 1.8em;
            border-bottom: 3px double #e91e63;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .datos-seccion {
            margin-bottom: 20px;
            padding: 10px;
            border-left: 5px solid #e91e63;
            background-color: #fff;
            border-radius: 5px;
        }

        .datos-seccion h3 {
            margin-top: 0;
            font-size: 1.2em;
            color: #e91e63;
        }

        .datos-seccion p {
            margin: 5px 0;
        }

        .datos-seccion strong {
            display: inline-block;
            min-width: 100px;
            font-weight: bold;
            color: #4a4a4a;
        }

        /* Estilos de tabla simples */
        .tabla-productos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .tabla-productos th,
        .tabla-productos td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px dashed #e91e63;
            font-family: 'Roboto', sans-serif;
            font-size: 0.9em;
        }

        .col-descripcion {
            width: 70%;
        }

        .col-importe {
            width: 30%;
            text-align: right;
        }

        .total-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #e91e63;
            font-size: 1.3em;
            font-weight: bold;
        }

        /* Estilos de botones */
        .btn-cake {
            display: block;
            width: 220px;
            margin: 10px auto;
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
            margin-bottom: 30px;
        }

        /* Estilo simple para mensaje de éxito */
        .cp-alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .cp-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>

<body>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: '🎉 Pedido Creado',
            text: 'Tu pedido (#<?= $id_pedido ?>) se ha guardado correctamente.',
            confirmButtonText: 'Ir a Mis Pedidos'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../../views/cliente/mis_pedidos.php';
            }
        });
    </script>


</body>

</html>