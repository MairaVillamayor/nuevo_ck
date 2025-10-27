<?php
// controllers/agregar_carrito.php
session_start();
require_once __DIR__ . '/../../config/conexion.php'; // ajustar si tu config está en otra ruta

// Forzar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./../views/productos/catalogo_web.php');
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    // Usuario no logueado: redirigir al login (opcional)
    header('Location: ../../index.php?error=not_logged');
    exit;
}

$producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;
$cantidad = isset($_POST['cantidad']) ? max(1, (int)$_POST['cantidad']) : 1;

if ($producto_id <= 0) {
    header('Location: /views/productos/catalogo_web.php?error=invalid_product');
    exit;
}

try {
    $pdo = getConexion();

    // Traer info del producto (precio, stock)
    $stmt = $pdo->prepare("SELECT producto_finalizado_nombre, producto_finalizado_precio, stock_actual FROM producto_finalizado WHERE ID_producto_finalizado = :id LIMIT 1");
    $stmt->execute([':id' => $producto_id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$p) {
        header('Location: /views/productos/catalogo_web.php?error=not_found');
        exit;
    }

    if ((int)$p['stock_actual'] < $cantidad) {
        // Stock insuficiente
        header('Location: /views/productos/catalogo_web.php?error=stock_insufficient');
        exit;
    }

    // Inicializar carrito si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Agregar o sumar cantidad
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]['cantidad'] += $cantidad;
    } else {
        $_SESSION['carrito'][$producto_id] = [
            'id' => $producto_id,
            'nombre' => $p['producto_finalizado_nombre'],
            'precio' => (float)$p['producto_finalizado_precio'],
            'cantidad' => $cantidad
        ];
    }

    // Opcional: podrías decrementar stock aquí (pero normalmente se hace al confirmar pago/pedido)
    // $stmt2 = $pdo->prepare("UPDATE producto_finalizado SET stock_actual = stock_actual - :cant WHERE ID_producto_finalizado = :id");
    // $stmt2->execute([':cant' => $cantidad, ':id' => $producto_id]);

    header('Location: ../../views/productos/carrito.php?success=added');
    exit;

} catch (Exception $e) {
    // Para debug: comentar la siguiente línea en producción
    // echo 'Error: ' . $e->getMessage(); exit;

    header('Location: /views/productos/catalogo_web.php?error=exception');
    exit;
}
