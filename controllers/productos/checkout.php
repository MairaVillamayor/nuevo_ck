<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';
$pdo = getConexion();

// Verificar login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php?error=not_logged');
    exit;
}

// Obtener datos del usuario (nombre y apellido)
$stmtUser = $pdo->prepare("
    select p.persona_nombre, p.persona_apellido from persona p inner join usuarios s on 
s.RELA_persona=p.id_persona
    WHERE s.ID_usuario = ?
");
$stmtUser->execute([$_SESSION['usuario_id']]);
$usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Verificar carrito
if (empty($_SESSION['carrito'])) {
    header('Location: ../../views/productos/carrito.php?error=empty');
    exit;
}

// Calcular total
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

// Si se confirma la compra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Crear la operación
        $stmt = $pdo->prepare("
            INSERT INTO operacion (RELA_usuario, operacion_fecha, operacion_total)
            VALUES (?, NOW(), ?)
        ");
        $stmt->execute([$_SESSION['usuario_id'], $total]);
        $id_operacion = $pdo->lastInsertId();

        // Insertar los productos en la tabla intermedia
        $stmtItem = $pdo->prepare("
            INSERT INTO operacion_producto_finalizado (RELA_operacion, RELA_producto_finalizado, cantidad)
            VALUES (?, ?, ?)
        ");

        foreach ($_SESSION['carrito'] as $item) {
            $stmtItem->execute([$id_operacion, $item['id'], $item['cantidad']]);
        }

        $pdo->commit();

        // Vaciar carrito
        unset($_SESSION['carrito']);

        header("Location: checkout.php?success=1");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error al procesar la compra: " . $e->getMessage());
    }
}
?>

<div class="checkout-container">
    <h1>🧾 Finalizar compra</h1>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fff0f6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .checkout-container {
            max-width: 800px;
            margin: 60px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        h1 {
            color: #d63384;
            text-align: center;
            margin-bottom: 25px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #f1f1f1;
            padding: 12px 0;
            font-size: 1rem;
        }
        .total {
            text-align: right;
            font-size: 1.3rem;
            margin-top: 20px;
            font-weight: bold;
            color: #444;
        }
        .btn-finalizar {
            display: block;
            width: 100%;
            background-color: #d63384;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-size: 1.1em;
            cursor: pointer;
            margin-top: 25px;
            transition: 0.3s;
        }
        .btn-finalizar:hover {
            background-color: #b32f70;
            transform: scale(1.02);
        }
        .mensaje {
            text-align: center;
            color: green;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        a.volver {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #d63384;
            font-weight: 500;
            transition: 0.3s;
        }
        a.volver:hover {
            color: #b32f70;
        }
        .info-cliente {
            background: #ffe6f0;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
    </style>

    <?php if (isset($_GET['success'])): ?>
        <p class="mensaje">¡Compra realizada con éxito! 🎉</p>
        <p>Cliente: <?= htmlspecialchars($usuario['persona_nombre'] . ' ' . $usuario['persona_apellido']) ?></p>
        <p>Fecha del pedido: <?= date('d/m/Y H:i') ?></p>
        <a href="../../views/productos/catalogo_web.php" class="volver">Volver al catálogo</a>
    <?php else: ?>
        <p>Cliente: <?= htmlspecialchars($usuario['persona_nombre'] . ' ' . $usuario['persona_apellido']) ?></p>
        <p>Fecha del pedido: <?= date('d/m/Y H:i') ?></p>

        <?php foreach ($_SESSION['carrito'] as $item): ?>
            <div class="cart-item">
                <span><?= htmlspecialchars($item['nombre']) ?> x <?= $item['cantidad'] ?></span>
                <span>$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></span>
            </div>
        <?php endforeach; ?>

        <div class="total">
            <strong>Total: $<?= number_format($total, 2) ?></strong>
        </div>

        <form method="POST">
            <button type="submit" class="btn-finalizar">Confirmar compra 💳</button>
        </form>
        <a href="../../views/productos/carrito.php" class="volver">← Volver al carrito</a>
    <?php endif; ?>
</div>
