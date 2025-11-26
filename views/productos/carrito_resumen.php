<?php
session_start();
$ruta_base = '/nuevo_ck/';

if (empty($_SESSION['carrito'])) {
    echo "<div class='alert alert-info'>Tu carrito está vacío.</div>";
    return;
}

// Calcular total
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}
?>

<div class="card p-3 shadow-sm">
    <h4 class="mb-3">🛒 Carrito actual</h4>

    <div id="carrito-items">
        <?php foreach ($_SESSION['carrito'] as $item): ?>
            <div class="d-flex justify-content-between align-items-center border-bottom py-2" data-id="<?= $item['id'] ?>">
                <div>
                    <strong><?= htmlspecialchars($item['nombre']) ?></strong><br>
                    <div class="d-flex align-items-center gap-1 mt-1">
                        <button class="btn btn-sm btn-secondary btn-disminuir" data-id="<?= $item['id'] ?>">-</button>
                        <input type="number" class="cantidad-carrito form-control form-control-sm" data-id="<?= $item['id'] ?>" value="<?= $item['cantidad'] ?>" min="1" style="width:60px;">
                        <button class="btn btn-sm btn-secondary btn-aumentar" data-id="<?= $item['id'] ?>">+</button>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-success fw-bold subtotal">
                        $<?= number_format($item['precio'] * $item['cantidad'], 2, ',', '.') ?>
                    </span>
                    <button class="btn btn-sm btn-danger btn-eliminar-carrito" data-id="<?= $item['id'] ?>">Eliminar</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-3 text-end fw-bold">
        Total: <span id="total-carrito">$<?= number_format($total, 2, ',', '.') ?></span>
    </div>

    <a href="<?= $ruta_base ?>views/productos/carrito.php" class="btn btn-pink w-100 mt-3">
        Finalizar compra
    </a>
</div>
