<?php
session_start();

// Si el usuario no está logueado, lo redirigimos al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../usuario/login.php?error=not_logged");
    exit;
}

// Obtenemos el carrito desde la sesión
$carrito = $_SESSION['carrito'] ?? [];
$total = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu carrito - Cake Party</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #fff6fa;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            margin-top: 80px;
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #d63384;
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
        }
        th {
            background-color: #ffcee0;
            color: #d63384;
            text-align: center;
        }
        td {
            vertical-align: middle;
            text-align: center;
        }
        .btn-pink {
            background-color: #ff6fa1;
            color: #fff;
            border: none;
        }
        .btn-pink:hover {
            background-color: #e65c8a;
        }
        .total {
            text-align: right;
            font-size: 1.3rem;
            font-weight: bold;
            margin-top: 20px;
        }
        .acciones {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .acciones a {
            border-radius: 10px;
            padding: 10px 20px;
            text-decoration: none;
        }
        .alert {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php include("../../includes/navegacion.php"); ?>

<div class="container">
    <h1>🛒 Tu Carrito</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">¡Producto agregado al carrito!</div>
    <?php endif; ?>

    <?php if (empty($carrito)): ?>
        <p style="text-align:center;">Tu carrito está vacío por ahora.</p>
        <div style="text-align:center;">
            <a href="catalogo_web.php" class="btn btn-pink">Volver al catálogo 🍰</a>
        </div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio Unitario</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrito as $id => $item): 
                    $subtotal = $item['precio'] * $item['cantidad'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['nombre']) ?></td>
                    <td>$<?= number_format($item['precio'], 2, ',', '.') ?></td>
                    <td><?= (int)$item['cantidad'] ?></td>
                    <td>$<?= number_format($subtotal, 2, ',', '.') ?></td>
                
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="total">Total: $<?= number_format($total, 2, ',', '.') ?></p>

        <div class="acciones">
            <a href="catalogo_web.php" class="btn btn-secondary">← Seguir comprando</a>
            <a href="../../controllers/productos/checkout.php" class="btn btn-pink">Ir a pagar 💳</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
