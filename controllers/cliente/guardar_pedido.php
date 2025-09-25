<?php
require_once __DIR__ . '/../../config/conexion.php';
session_start();
$pdo = getConexion();

// -----------------
// 1) Validar sesión
// -----------------
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../views/usuario/login.php?error=not_logged');
    exit;
}
$id_usuario = $_SESSION['usuario_id'];

// -----------------
// 2) Recibir POST
// -----------------
$color         = isset($_POST['RELA_color_pastel']) ? (int)$_POST['RELA_color_pastel'] : null;
$decoracion    = isset($_POST['RELA_decoracion']) ? (int)$_POST['RELA_decoracion'] : null;
$base          = isset($_POST['RELA_base_pastel']) ? (int)$_POST['RELA_base_pastel'] : null;
$pisos         = $_POST['pisos'] ?? [];
$materiales    = $_POST['material_extra'] ?? [];
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
        $relleno= isset($datos['RELA_relleno']) ? (int)$datos['RELA_relleno'] : null;

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
    if(!$id_pedido) throw new Exception("No se pudo generar el ID del pedido.");
    
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
    
    // -----------------
    // 10) Confirmación al usuario
    // -----------------
    echo "<h2>🎉 Pedido guardado con éxito</h2>";
    echo "<p><strong>Pedido #</strong> $id_pedido</p>";
    echo "<p><strong>Pastel #</strong> $id_pastel_personalizado</p>";
    echo "<p><strong>Descripción:</strong> $descripcion</p>";
    echo "<p><strong>Total:</strong> $" . number_format($total, 2) . "</p>";
    echo "<p><strong>Dirección de envío:</strong> " . htmlspecialchars($direccion_envio) . "</p>";

} catch (Exception $e) {
    // Si algo falló, revertimos todos los cambios y mostramos un error
    $pdo->rollBack();
    die("❌ Error al guardar el pedido: " . $e->getMessage());
}
?>