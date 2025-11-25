<?php
require_once ("../../config/conexion.php");
include ("../../includes/navegacion.php");

if (!isset($_GET['idFactura']) || !is_numeric($_GET['idFactura'])) {
    die("Error: ID de Factura no proporcionado o inválido.");
}

$idFactura = intval($_GET['idFactura']);
$conexion = Conexion::getInstance();

$datosFactura = null;
$detallesProducto = [];
$formasPago = [];
$datosCliente = [];
$errorMsg = null;

try {
    $conn = $conexion->getConnection();

    $stmtFactura = $conn->prepare("SELECT 
            f.factura_fecha_emision, f.factura_subtotal,  f.factura_iva_tasa, f.factura_iva_monto, f.factura_total,
            p.persona_documento, p.persona_nombre, p.persona_apellido, p.persona_direccion
        FROM factura f
        JOIN persona p ON f.RELA_persona = p.id_persona 
        WHERE f.id_factura = :idFactura
    ");
    $stmtFactura->bindParam(':idFactura', $idFactura, PDO::PARAM_INT);
    $stmtFactura->execute();
    $datosFactura = $stmtFactura->fetch(PDO::FETCH_ASSOC);

    if (!$datosFactura) {
        $errorMsg = "Factura con ID $idFactura no encontrada.";
    } else {
        $datosCliente = [
            'documento' => $datosFactura['persona_documento'],
            'nombre' => $datosFactura['persona_nombre'] . ' ' . $datosFactura['persona_apellido'],
            'direccion' => $datosFactura['persona_direccion']
        ];
        
        $stmtDetalle = $conn->prepare(" SELECT 
                fd.factura_detalle_cantidad AS cantidad,
                pf.producto_finalizado_nombre AS nombre,
                pf.producto_finalizado_precio AS precio_unitario
            FROM factura_detalle fd
            JOIN producto_finalizado pf ON fd.RELA_producto_finalizado = pf.id_producto_finalizado
            WHERE fd.RELA_factura = :idFactura
        ");
        $stmtDetalle->bindParam(':idFactura', $idFactura, PDO::PARAM_INT);
        $stmtDetalle->execute();
        $detallesProducto = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

        $stmtPago = $conn->prepare(" SELECT 
                mp.metodo_pago_descri AS nombre_pago,
                fp.pago_monto AS monto,
                fp.pago_interes AS interes
            FROM factura_pagos fp
            JOIN metodo_pago mp ON fp.RELA_metodo_pago = mp.id_metodo_pago
            WHERE fp.RELA_factura = :idFactura
        ");
        $stmtPago->bindParam(':idFactura', $idFactura, PDO::PARAM_INT);
        $stmtPago->execute();
        $formasPago = $stmtPago->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $errorMsg = "Error de base de datos: " . $e->getMessage();
}

if ($errorMsg) {
    die("<h2>Error al cargar la factura</h2><p>$errorMsg</p>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura N° <?php echo $idFactura; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .total-row td {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .footer-info {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        @media print {
        .no-print {
            display: none !important;
        }
    }
    </style>
</head>
<body>

<div class="invoice-box">
    <div class="text-center mb-4">
        <h2 style="color: #e91e63;">Cake Party</h2>
        <p>Tu Repostería de Confianza</p>
    </div>

    <table cellpadding="0" cellspacing="0" class="mb-4">
        <tr>
            <td style="width: 50%;">
                **Factura N°:** <?php echo $idFactura; ?><br>
                **Fecha de Emisión:** <?php echo date('d/m/Y', strtotime($datosFactura['factura_fecha_emision'])); ?><br>
            </td>
            <td>
                **Cliente:** <?php echo htmlspecialchars($datosCliente['nombre']); ?><br>
                **Documento:** <?php echo htmlspecialchars($datosCliente['documento']); ?><br>
                **Dirección:** <?php echo htmlspecialchars($datosCliente['direccion']); ?>
            </td>
        </tr>
    </table>

    <h4 class="mt-4" style="color: #e91e63;">Detalle de Productos</h4>
    <table cellpadding="0" cellspacing="0" class="table table-bordered">
        <tr class="heading">
            <td style="width: 5%;">Cant.</td>
            <td style="width: 50%;">Producto</td>
            <td style="width: 20%;">P. Unitario</td>
            <td style="width: 25%; text-align: right;">Subtotal</td>
        </tr>

        <?php 
        $totalPagado = 0;
        foreach ($detallesProducto as $item) : 
            $subtotalItem = $item['cantidad'] * $item['precio_unitario'];
        ?>
        <tr class="item">
            <td><?php echo $item['cantidad']; ?></td>
            <td><?php echo htmlspecialchars($item['nombre']); ?></td>
            <td>$<?php echo number_format($item['precio_unitario'], 2); ?></td>
            <td style="text-align: right;">$<?php echo number_format($subtotalItem, 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="row">
        <div class="col-6">
            <h4 class="mt-4" style="color: #e91e63;">Formas de Pago</h4>
            <table cellpadding="0" cellspacing="0" class="table table-sm table-dark">
                <thead>
                    <tr class="heading">
                        <td>Método</td>
                        <td style="text-align: right;">Monto</td>
                    </tr>
                </thead>
                <tbody>
                <?php 
                foreach ($formasPago as $pago) : 
                    $totalPagado += $pago['monto'];
                ?>
                    <tr class="item">
                        <td><?php echo htmlspecialchars($pago['nombre_pago']); ?> (Int. <?php echo $pago['interes']; ?>%)</td>
                        <td style="text-align: right;">$<?php echo number_format($pago['monto'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="col-6">
            <h4 class="mt-4" style="color: #e91e63;">Resumen de Totales</h4>
            <table cellpadding="0" cellspacing="0" class="table table-sm">
                <tr class="total-row">
                    <td>Subtotal (antes de IVA):</td>
                    <td style="text-align: right;">$<?php echo number_format($datosFactura['factura_subtotal'], 2); ?></td>
                </tr>
                <tr class="total-row">
                    <td>IVA (<?php echo $datosFactura['factura_iva_tasa']; ?>%):</td>
                    <td style="text-align: right;">$<?php echo number_format($datosFactura['factura_iva_monto'], 2); ?></td>
                </tr>
                <tr class="total-row bg-pink-light">
                    <td class="text-primary">**TOTAL FACTURA:**</td>
                    <td style="text-align: right; font-size: 1.2em; color: #e91e63;">**$<?php echo number_format($datosFactura['factura_total'], 2); ?>**</td>
                </tr>
                <tr class="total-row">
                    <td>Monto Pagado:</td>
                    <td style="text-align: right;">$<?php echo number_format($totalPagado, 2); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer-info text-center">
        <p>Gracias por su compra. ¡Vuelva pronto!</p>
        <button class="btn btn-primary mt-3 no-print" onclick="window.print()">Imprimir Factura</button>
    </div>
</div>

</body>
</html>