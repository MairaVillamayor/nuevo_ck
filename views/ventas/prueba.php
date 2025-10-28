<?php
include ('../../config/conexion.php');

// Lógica de manejo del formulario al enviarse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recoger datos de la CABECERA de la factura (Tabla 'factura')
    $factura_tipo = $_POST['factura_tipo'] ?? 'A'; // Ejemplo: Tipo de factura
    $factura_fecha_emision = date('Y-m-d'); // Fecha actual
    
    // Asumiendo que RELA_cliente, RELA_caja y RELA_condicion_iva_cliente
    // se obtienen de los select o de la sesión del usuario.
    $RELA_cliente = $_POST['RELA_cliente'] ?? 1; 
    $RELA_caja = 1; // Asumir la caja 1 por defecto
    $RELA_condicion_iva_cliente = $_POST['RELA_condicion_iva_cliente'] ?? 1;

    // Los valores de totales provendrán de campos ocultos llenados por JavaScript
    $factura_iva_105 = $_POST['factura_iva_105'] ?? 0.00;
    $factura_iva_21 = $_POST['factura_iva_21'] ?? 0.00;
    $factura_iva_28 = $_POST['factura_iva_28'] ?? 0.00;
    $factura_total_neto = $_POST['factura_total_neto'] ?? 0.00;
    $factura_total_subtotal = $_POST['factura_total_subtotal'] ?? 0.00; // Subtotal antes de bonificación
    $factura_porc_bonificacion = $_POST['factura_porc_bonificacion'] ?? 0.00;
    $factura_bonificacion = $_POST['factura_bonificacion'] ?? 0.00;
    $factura_importe_total = $_POST['factura_importe_total'] ?? 0.00; 

    // 2. Recoger datos de los DETALLES de la factura (Tabla 'factura_detalle')
    // Esto se recibe como un array JSON o un conjunto de campos array generados por JS
    $detalles = json_decode($_POST['detalles_json'] ?? '[]', true); 

    /* Aquí iría la lógica de INSERCIÓN:
    1. Iniciar una transacción.
    2. Insertar en la tabla 'factura' (usando los datos de arriba). Obtener el ID_factura.
    3. Iterar sobre $detalles e insertar en 'factura_detalle' usando el ID_factura.
    4. Si todo es OK, hacer COMMIT. Si hay error, hacer ROLLBACK.
    */
    
    // Redireccionar o mostrar mensaje
    // header('Location: venta_exitosa.php?id=' . $ID_factura);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Venta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-header { background-color: #f8f9fa; }
        .section-title { color: #d63384; font-weight: bold; margin-top: 1.5rem; margin-bottom: 0.5rem; }
        .btn-pink { background-color: #d63384; border-color: #d63384; color: white; }
        .btn-pink:hover { background-color: #c02d76; border-color: #c02d76; color: white; }
        .table-products thead th { background-color: #343a40; color: white; }
    </style>
</head>
<body>

<div class="container my-5">
    <form method="POST" action="registrar_venta.php" id="formVenta">
        <div class="card shadow-sm">
            <div class="card-header"><h3 class="mb-0">Registrar Venta</h3></div>
            <div class="card-body">
                
                <input type="hidden" id="input_iva_105" name="factura_iva_105" value="0.00">
                <input type="hidden" id="input_iva_21" name="factura_iva_21" value="0.00">
                <input type="hidden" id="input_iva_28" name="factura_iva_28" value="0.00">
                <input type="hidden" id="input_total_neto" name="factura_total_neto" value="0.00">
                <input type="hidden" id="input_total_subtotal_cabecera" name="factura_total_subtotal" value="0.00"> 
                <input type="hidden" id="input_bonificacion" name="factura_bonificacion" value="0.00">
                <input type="hidden" id="input_importe_total_cabecera" name="factura_importe_total" value="0.00">
                <input type="hidden" id="detalles_json" name="detalles_json" value="[]"> <div class="row mb-4">
                    <p class="section-title">Alícuota:</p>
                    <div class="col-md-3">
                        <label for="alicuota" class="form-label">Alícuota:</label>
                        <select id="alicuota" name="alicuota_seleccionada" class="form-select">
                            <option value="21">21%</option>
                            <option value="10.5">10.5%</option>
                            <option value="28">27%</option> </select>
                    </div>
                    <div class="col-md-9 d-flex flex-wrap align-items-end">
                        <div class="me-3 mb-2"><label for="iva105" class="form-label small">IVA 10.5:</label><input type="text" id="iva105" class="form-control form-control-sm" value="0.00" readonly style="width: 80px;"></div>
                        <div class="me-3 mb-2"><label for="iva21" class="form-label small">IVA 21:</label><input type="text" id="iva21" class="form-control form-control-sm" value="0.00" readonly style="width: 80px;"></div>
                        <div class="me-3 mb-2"><label for="iva28" class="form-label small">IVA 27:</label><input type="text" id="iva28" class="form-control form-control-sm" value="0.00" readonly style="width: 80px;"></div>
                        <div class="me-3 mb-2"><label for="neto" class="form-label small text-danger">NETO:</label><input type="text" id="neto" class="form-control form-control-sm" value="0.00" readonly></div>
                        <div class="mb-2"><label for="total" class="form-label small text-success">TOTAL:</label><input type="text" id="total" class="form-control form-control-sm" value="0.00" readonly></div>
                    </div>
                </div>

                <hr>

                <div class="row mb-4">
                    <p class="section-title">Datos del Cliente</p>
                    <div class="col-md-4">
                        <label for="RELA_cliente" class="form-label">Cliente:</label>
                        <select id="RELA_cliente" name="RELA_cliente" class="form-select">
                            <option value="1">CONSUMIDOR FINAL</option>
                            </select>
                    </div>
                    <div class="col-md-4">
                        <label for="RELA_condicion_iva_cliente" class="form-label">Tipo Cliente:</label>
                        <select id="RELA_condicion_iva_cliente" name="RELA_condicion_iva_cliente" class="form-select">
                            <option value="1">CONSUMIDOR FINAL</option>
                            <option value="2">Responsable Inscripto</option>
                            <option value="3">Exento</option>
                        </select>
                    </div>
                </div>

                <hr>

                <p class="section-title">Datos de los Productos</p>
                <div class="row mb-4 align-items-end">
                    <div class="col-md-1">
                        <label for="cantidad" class="form-label">Cantidad:</label>
                        <input type="number" id="cantidad" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-md-5">
                        <label for="RELA_producto_finalizado" class="form-label">Producto:</label>
                        <input type="text" id="producto_buscar" class="form-control" placeholder="Buscar producto..." list="productos-datalist">
                        <datalist id="productos-datalist">
                            </datalist>
                        <input type="hidden" id="RELA_producto_finalizado" name="RELA_producto_finalizado" value="">
                    </div>
                    <div class="col-md-2">
                        <label for="precio" class="form-label">Precio ($):</label>
                        <input type="text" id="precio" class="form-control" value="0.00" readonly>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="button" class="btn btn-pink" id="agregarProducto">Agregar Producto</button>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-products" id="tablaProductos">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 10%;">Cantidad</th>
                                <th style="width: 35%;">Producto</th>
                                <th style="width: 10%;">Precio ($) <br>(s/IVA)</th>
                                <th style="width: 8%;">IVA 10.5</th>
                                <th style="width: 8%;">IVA 21</th>
                                <th style="width: 8%;">IVA 27</th>
                                <th style="width: 8%;">NETO</th>
                                <th style="width: 8%;">Importe <br>(c/IVA)</th>
                                <th style="width: 8%;">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoTablaProductos">
                            <tr>
                                <td colspan="10" class="text-center">No se encuentran productos cargados</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8" class="text-end"><strong>TOTAL:</strong></td>
                                <td id="totalTablaNeto"><strong>$ 0.00</strong></td>
                                <td id="totalTablaImporte"><strong>$ 0.00</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <p class="section-title">Formas de Pago</p>
                        <button type="button" class="btn btn-sm btn-pink mb-2" id="agregarFormaPago">+ Agregar Forma de Pago</button>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="tablaFormasPago">
                                <thead>
                                    <tr><th style="width: 5%;">#</th><th style="width: 30%;">Forma de Pago</th><th style="width: 20%;">Interés %</th><th style="width: 30%;">Monto</th><th style="width: 15%;">Acción</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="5" class="text-center small">No se encuentran formas de pago cargadas</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="mt-2"><strong>Total de Pago:</strong> <span id="totalPago">$ 0.00</span></p>
                    </div>

                    <div class="col-md-6">
                        <p class="section-title">Totales de la Venta</p>
                        <div class="mb-2">
                            <label for="importeSubtotal" class="form-label small">Importe Subtotal (Neto + IVA):</label>
                            <input type="text" id="importeSubtotal" class="form-control" value="0.00" readonly>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <label for="porcBonificacion" class="form-label small">% Bonificación:</label>
                                <input type="number" id="porcBonificacion" class="form-control" name="factura_porc_bonificacion_visible" value="0.00" min="0" max="100">
                            </div>
                            <div class="col-6">
                                <label for="montoBonificacion" class="form-label small">Bonificación:</label>
                                <input type="text" id="montoBonificacion" class="form-control" value="0.00" readonly>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="importeTotalFinal" class="form-label small">Importe Total:</label>
                            <input type="text" id="importeTotalFinal" class="form-control form-control-lg text-end" value="0.00" readonly>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between mt-4">
                    <div>
                        <button type="button" class="btn btn-secondary me-2">Cerrar</button>
                        <button type="button" class="btn btn-warning">Cancelar</button>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-pink me-2">Procesar Venta</button>
                        <button type="submit" class="btn btn-pink">Guardar e Imprimir</button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="logica_venta.js"></script> 
</body>
</html>