
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ventas - Panel Admin | Cake Party</title>
      <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
  body {
    background-color: #fff7fa;
    font-family: 'Poppins', sans-serif;
  }

  .container {
    max-width: 1000px;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    padding: 35px;
    margin: 40px auto;
  }

  h2, h3, h4, h5 {
    color: #e91e63;
    font-weight: 600;
  }

  /* ---- Tablas ---- */
  .table {
    border-radius: 10px;
    overflow: hidden;
  }

  .table thead {
    background-color: #f8bbd0;
    color: #4a4a4a;
  }

  .table tbody tr:hover {
    background-color: #ffe6ef;
  }

  /* ---- Botones ---- */
  .btn-cake {
    background-color: #f48fb1;
    color: #fff;
    border-radius: 10px;
    border: none;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
  }

  .btn-cake:hover {
    background-color: #ec407a;
    transform: scale(1.03);
  }

  .btn-secondary, .btn-warning {
    border-radius: 10px;
    font-weight: 500;
  }

  .btn-warning {
    background-color: #ffca28;
    border: none;
    color: #333;
  }

  .btn-warning:hover {
    background-color: #ffc107;
  }

  .btn-success {
    background-color: #e91e63;
    border: none;
    border-radius: 10px;
    color: #fff;
  }

  .btn-success:hover {
    background-color: #c2185b;
  }

  .btn-primary {
    background-color: #ff4081;
    border: none;
    border-radius: 10px;
  }

  .btn-primary:hover {
    background-color: #e91e63;
  }

  /* ---- Secciones ---- */
  .section-title {
    font-size: 1.3rem;
    margin-top: 30px;
    color: #d81b60;
    border-bottom: 2px solid #f8bbd0;
    padding-bottom: 8px;
  }

  /* ---- Totales ---- */
  .totales-box {
    background-color: #fff0f5;
    border: 1px solid #f8bbd0;
    border-radius: 10px;
    padding: 20px;
  }

  .totales-box input {
    border-radius: 10px;
    border: 1px solid #f8bbd0;
  }

  .totales-box label {
    color: #c2185b;
    font-weight: 500;
  }

  /* ---- Footer / Total ---- */
  .total-pago {
    background-color: #fce4ec;
    border-radius: 10px;
    padding: 15px;
    text-align: right;
    font-weight: 600;
    color: #e91e63;
  }

  /* ---- Inputs ---- */
  input, select {
    border-radius: 10px !important;
    border: 1px solid #f8bbd0 !important;
  }

  input:focus, select:focus {
    border-color: #ec407a !important;
    box-shadow: 0 0 4px rgba(236, 64, 122, 0.4) !important;
  }
</style>

</head>
<body>

<?php include("../../includes/navegacion.php"); ?>

<div class="container">
  <h2>Registrar Venta</h2>

   <div class="row form-section">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Alicuota:</label>
                        <select class="form-select" name="RELA_condicion_iva_cliente" id="RELA_condicion_iva_cliente">
                            <option value="3">0%</option>
                            <option value="4">10.5%</option>
                            <option value="5" selected>21%</option>
                            <option value="6">27%</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">IVA 10.5:</label>
                            <input type="text" class="form-control readonly-field" name="factura_iva_105" id="factura_iva_105" value="0.00" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">IVA 21:</label>
                            <input type="text" class="form-control readonly-field" name="tabla26_ivaveintinuno" id="tabla26_ivaveintinuno" value="0.00" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">IVA 27:</label>
                            <input type="text" class="form-control readonly-field" name="tabla26_ivaveinticiete" id="tabla26_ivaveinticiete" value="0.00" readonly>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <label class="form-label text-primary"><strong>NETO:</strong></label>
                            <input type="text" class="form-control readonly-field" name="tabla26_totalneto" id="tabla26_totalneto" value="0.00" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-success"><strong>TOTAL:</strong></label>
                            <input type="text" class="form-control readonly-field" name="tabla26_importetotal" id="tabla26_importetotal" value="0.00" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Cliente -->
            <div class="row form-section">
                <div class="col-12">
                    <h5>Datos del Cliente</h5>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tipo Cliente:</label>
                        <select class="form-select" name="tabla26_tipocliente" id="tabla26_tipocliente">
                            <option value="0" selected>CONSUMIDOR FINAL</option>
                            <option value="1">CLIENTE</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Campos de Cliente (ocultos inicialmente) -->
            <div class="row form-section ocultarcliente" style="display: none;">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Apellido y Nombre:</label>
                        <input type="hidden" name="rela_tabla30" id="rela_tabla30" value="">
                        <input type="text" class="form-control" name="persona_recibos" id="persona_recibos" value="" placeholder="Buscar cliente...">
                        <div id="sugerencias-clientes" class="mt-2 border rounded" style="display:none; max-height: 150px; overflow-y: auto;"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">N° Documento:</label>
                        <input type="text" class="form-control readonly-field" name="tipoynro" id="tipoynro" value="" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Condición Frente al IVA:</label>
                        <select class="form-select" name="rela_tabla11" id="rela_tabla11">
                            <option value="">Seleccionar</option>
                            <option value="1">Responsable Inscripto</option>
                            <option value="2">Monotributista</option>
                            <option value="3">Consumidor Final</option>
                            <option value="4">Exento</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">CUIT/CUIL:</label>
                        <input type="text" class="form-control" name="tabla30_cuit" id="tabla30_cuit" maxlength="11" placeholder="Obligatorio">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Domicilio:</label>
                        <input type="text" class="form-control readonly-field" name="domicilio" id="domicilio" value="" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Teléfono:</label>
                        <input type="text" class="form-control readonly-field" name="tabla30_celular" id="tabla30_celular" value="" readonly>
                    </div>
                </div>
            </div>

            <!-- Sección de Productos -->
            <div class="row form-section">
                <div class="col-12">
                    <h5>Datos de los Productos</h5>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Cantidad:</label>
                        <input type="number" class="form-control" name="cantidad" id="cantidad" value="1" min="1">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Producto:</label>
                        <input type="hidden" name="rela_tabla20" id="rela_tabla20" value="">
                        <input type="text" class="form-control" name="producto" id="producto" value="" placeholder="Buscar producto...">
                        <div id="sugerencias-productos" class="mt-2 border rounded" style="display:none; max-height: 150px; overflow-y: auto;"></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Precio ($):</label>
                        <input type="text" class="form-control readonly-field" name="precio" id="precio" value="0.00" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-primary w-100" onclick="agregar_registro()">Agregar Producto</button>
                    </div>
                </div>
            </div>

            <!-- Tabla de Productos -->
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="grilla">
                            <thead class="table-dark">
                                <tr>
                                    <th width="3%">#</th>
                                    <th width="6%">Cantidad</th>
                                    <th width="22%">Producto</th>
                                    <th width="9%">Precio ($)</th>
                                    <th width="8%">IVA 10.5</th>
                                    <th width="8%">IVA 21</th>
                                    <th width="8%">IVA 27</th>
                                    <th width="8%">NETO</th>
                                    <th width="15%">Importe</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpo-tabla-productos">
                                <tr id="sinregistro">
                                    <td colspan="9" class="text-center text-muted">No se encuentran productos cargados</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <th colspan="7" class="text-end">TOTAL:</th>
                                    <th id="total-neto">$ 0.00</th>
                                    <th id="totales_importe_tabla">$ 0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sección de Formas de Pago y Totales -->
            <div class="row mt-3">
                <!-- Formas de Pago -->
                <div class="col-md-6">
                    <div class="form-section">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Formas de Pago</h5>
                            <button type="button" class="btn btn-success btn-sm" onclick="agregarFormaPago()">
                                + Agregar Forma de Pago
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="tabla_formas_pago">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="10%">#</th>
                                        <th width="25%">Forma de Pago</th>
                                        <th width="25%">Interés %</th>
                                        <th width="30%">Monto</th>
                                        <th width="10%">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="formas_pago_body">
                                    <tr id="sinRegistrofP">
                                        <td colspan="5" class="text-center text-muted">No se encuentran formas de pago cargadas</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3 p-2 bg-light rounded">
                            <strong>Total de Pago:</strong> 
                            <span class="total-display">$ <span id="total_pago">0.00</span></span>
                        </div>
                    </div>
                </div>

                <!-- Totales -->
                <div class="col-md-6">
                    <div class="form-section">
                        <h5>Totales de la Venta</h5>
                        <div class="mb-3">
                            <label class="form-label">Importe Subtotal:</label>
                            <input type="text" class="form-control readonly-field" name="tabla26_totalsubtotal" id="tabla26_totalsubtotal" value="0.00" readonly>
                        </div>
                        <div class="mb-3" id="campoBonificacion">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">% Bonificación:</label>
                                    <input type="number" class="form-control" name="tabla26_porc_bonificacion" id="tabla26_porc_bonificacion" value="0.00" min="0" max="50" step="0.01">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Bonificación:</label>
                                    <input type="text" class="form-control readonly-field" name="tabla26_bonificacion" id="tabla26_bonificacion" value="0.00" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Importe Total:</label>
                            <input type="text" class="form-control readonly-field total-display" name="tabla26_importetotal" id="tabla26_importetotal" value="0.00" readonly>
                        </div>
                        <input type="hidden" name="estructura" id="estructura" value="">
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="row mt-4 mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-secondary" onclick="cerrar_tab_actual()">Cerrar</button>
                            <button type="button" class="btn btn-warning" onclick="limpiarFormulario()">Cancelar</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-success" onclick="procesarVenta()">Procesar Venta</button>
                            <button type="button" class="btn btn-primary" onclick="procesarVenta(true)">Guardar e Imprimir</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

  
</div>

<!-- Modal para mensajes -->
    <div class="modal fade" id="dialogConfirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Notificación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="dialogConfirmBody">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // =============================================
        // DATOS DE PRUEBA - SIMULACIÓN DE BASE DE DATOS
        // =============================================

        const productosDemo = [
            { id: 1, nombre: "Coca Cola 2.25L", precio: 1200, stock: 50, iva: 21 },
            { id: 2, nombre: "Pancho Completo", precio: 2500, stock: 30, iva: 21 },
            { id: 3, nombre: "Hamburguesa Especial", precio: 4500, stock: 25, iva: 21 },
            { id: 4, nombre: "Pizza Muzzarella", precio: 6800, stock: 15, iva: 21 },
            { id: 5, nombre: "Agua Mineral 500ml", precio: 800, stock: 40, iva: 10.5 },
            { id: 6, nombre: "Cerveza Lata 473ml", precio: 1500, stock: 60, iva: 21 },
            { id: 7, nombre: "Ensalada César", precio: 3200, stock: 20, iva: 10.5 },
            { id: 8, nombre: "Café Expreso", precio: 900, stock: 100, iva: 21 }
        ];

        const clientesDemo = [
            { id: 1, nombre: "Juan Pérez", documento: "30123456", cuit: "20-30123456-1", direccion: "Av. Siempre Viva 123", telefono: "11-1234-5678", iva: "Responsable Inscripto" },
            { id: 2, nombre: "María García", documento: "28987654", cuit: "27-28987654-9", direccion: "Calle Falsa 456", telefono: "11-8765-4321", iva: "Monotributista" },
            { id: 3, nombre: "Carlos López", documento: "33445566", cuit: "20-33445566-2", direccion: "Pje. Real 789", telefono: "11-5566-7788", iva: "Responsable Inscripto" },
            { id: 4, nombre: "Ana Martínez", documento: "31223344", cuit: "27-31223344-8", direccion: "Av. Libertador 321", telefono: "11-4433-2211", iva: "Consumidor Final" }
        ];

        const formasPagoDemo = [
            { id: 1, nombre: "Efectivo", interes: 0 },
            { id: 2, nombre: "Tarjeta Crédito", interes: 10 },
            { id: 3, nombre: "Tarjeta Débito", interes: 0 },
            { id: 9, nombre: "Transferencia", interes: 0 },
            { id: 7, nombre: "MercadoPago", interes: 5 }
        ];

        // =============================================
        // VARIABLES GLOBALES
        // =============================================

        let productosAgregados = [];
        let formasPagoAgregadas = [];
        let contadorFilas = 0;

        // =============================================
        // FUNCIONES DE INICIALIZACIÓN
        // =============================================

        $(document).ready(function() {
            // Inicializar datepicker
            $("#tabla26_fecha").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd/mm/yy'
            });

            // Actualizar hora actual
            actualizarHora();
            setInterval(actualizarHora, 1000);

            // Event listeners
            $("#tabla26_tipocliente").change(toggleClienteFields);
            $("#producto").on('input', buscarProductos);
            $("#persona_recibos").on('input', buscarClientes);
            $("#tabla26_porc_bonificacion").on('input', aplicarBonificacion);
            $("#rela_tabla15").change(calcularTotales);

            // Inicializar campos
            toggleClienteFields();
        });

        function actualizarHora() {
            const ahora = new Date();
            const hora = ahora.getHours().toString().padStart(2, '0');
            const minutos = ahora.getMinutes().toString().padStart(2, '0');
            const segundos = ahora.getSeconds().toString().padStart(2, '0');
            $("#hora-actual").text(`${hora}:${minutos}:${segundos}`);
        }

        // =============================================
        // FUNCIONES DE CLIENTE
        // =============================================

        function toggleClienteFields() {
            const tipoCliente = $("#tabla26_tipocliente").val();
            if (tipoCliente === "1") {
                $(".ocultarcliente").show();
            } else {
                $(".ocultarcliente").hide();
                limpiarCamposCliente();
            }
        }

        function buscarClientes() {
            const termino = $("#persona_recibos").val().toLowerCase();
            const sugerencias = $("#sugerencias-clientes");
            
            if (termino.length < 2) {
                sugerencias.hide().empty();
                return;
            }

            const resultados = clientesDemo.filter(cliente => 
                cliente.nombre.toLowerCase().includes(termino) || 
                cliente.documento.includes(termino)
            );

            sugerencias.empty();
            
            if (resultados.length > 0) {
                resultados.forEach(cliente => {
                    const item = $(`<div class="producto-item p-2 border-bottom">${cliente.nombre} - DOC: ${cliente.documento}</div>`);
                    item.click(() => seleccionarCliente(cliente));
                    sugerencias.append(item);
                });
                sugerencias.show();
            } else {
                sugerencias.hide();
            }
        }

        function seleccionarCliente(cliente) {
            $("#rela_tabla30").val(cliente.id);
            $("#persona_recibos").val(cliente.nombre);
            $("#tipoynro").val(cliente.documento);
            $("#tabla30_cuit").val(cliente.cuit);
            $("#domicilio").val(cliente.direccion);
            $("#tabla30_celular").val(cliente.telefono);
            $("#rela_tabla11").val(cliente.iva);
            $("#sugerencias-clientes").hide().empty();
        }

        function limpiarCamposCliente() {
            $("#rela_tabla30").val("");
            $("#persona_recibos").val("");
            $("#tipoynro").val("");
            $("#tabla30_cuit").val("");
            $("#domicilio").val("");
            $("#tabla30_celular").val("");
            $("#rela_tabla11").val("");
        }

        // =============================================
        // FUNCIONES DE PRODUCTOS
        // =============================================

        function buscarProductos() {
            const termino = $("#producto").val().toLowerCase();
            const sugerencias = $("#sugerencias-productos");
            
            if (termino.length < 2) {
                sugerencias.hide().empty();
                return;
            }

            const resultados = productosDemo.filter(producto => 
                producto.nombre.toLowerCase().includes(termino)
            );

            sugerencias.empty();
            
            if (resultados.length > 0) {
                resultados.forEach(producto => {
                    const item = $(`<div class="producto-item p-2 border-bottom">${producto.nombre} - $${producto.precio}</div>`);
                    item.click(() => seleccionarProducto(producto));
                    sugerencias.append(item);
                });
                sugerencias.show();
            } else {
                sugerencias.hide();
            }
        }

        function seleccionarProducto(producto) {
            $("#rela_tabla20").val(producto.id);
            $("#producto").val(producto.nombre);
            $("#precio").val(producto.precio.toFixed(2));
            
            // Seleccionar la alicuota correspondiente
            const ivaMap = { 10.5: "4", 21: "5", 27: "6" };
            $("#rela_tabla15").val(ivaMap[producto.iva] || "5");
            
            $("#sugerencias-productos").hide().empty();
            $("#cantidad").focus();
        }

        function agregar_registro() {
            const cantidad = parseFloat($("#cantidad").val()) || 0;
            const productoId = $("#rela_tabla20").val();
            const productoNombre = $("#producto").val();
            const precio = parseFloat($("#precio").val()) || 0;
            const alicuota = $("#rela_tabla15").val();

            // Validaciones
            if (cantidad <= 0) {
                mostrarMensaje("Debe ingresar una cantidad válida");
                return false;
            }

            if (!productoId) {
                mostrarMensaje("Debe seleccionar un producto");
                return false;
            }

            if (precio <= 0) {
                mostrarMensaje("El precio debe ser mayor a cero");
                return false;
            }

            // Ocultar mensaje de "sin registros"
            $("#sinregistro").hide();

            // Calcular valores
            const precioTotal = precio * cantidad;
            const porcentajeIVA = obtenerPorcentajeIVA(alicuota);
            const neto = precioTotal / (1 + porcentajeIVA / 100);
            const iva = precioTotal - neto;

            // Agregar a array de productos
            const producto = {
                id: productoId,
                nombre: productoNombre,
                cantidad: cantidad,
                precio: precio,
                neto: neto,
                iva: iva,
                alicuota: alicuota,
                total: precioTotal
            };

            productosAgregados.push(producto);
            contadorFilas++;

            // Actualizar tabla
            actualizarTablaProductos();
            limpiarCamposProducto();
            calcularTotales();
        }

        function actualizarTablaProductos() {
            const tbody = $("#cuerpo-tabla-productos");
            tbody.empty();

            productosAgregados.forEach((producto, index) => {
                const iva10 = producto.alicuota === "4" ? producto.iva : 0;
                const iva21 = producto.alicuota === "5" ? producto.iva : 0;
                const iva27 = producto.alicuota === "6" ? producto.iva : 0;

                const fila = `
                    <tr>
                        <td>
                            <span class="delete-btn" onclick="eliminarProducto(${index})">✕</span>
                        </td>
                        <td>${producto.cantidad}</td>
                        <td>${producto.nombre}</td>
                        <td>$${producto.precio.toFixed(2)}</td>
                        <td>$${iva10.toFixed(2)}</td>
                        <td>$${iva21.toFixed(2)}</td>
                        <td>$${iva27.toFixed(2)}</td>
                        <td>$${producto.neto.toFixed(2)}</td>
                        <td>$${producto.total.toFixed(2)}</td>
                    </tr>
                `;
                tbody.append(fila);
            });

            if (productosAgregados.length === 0) {
                tbody.append('<tr id="sinregistro"><td colspan="9" class="text-center text-muted">No se encuentran productos cargados</td></tr>');
            }
        }

        function eliminarProducto(index) {
            productosAgregados.splice(index, 1);
            actualizarTablaProductos();
            calcularTotales();
        }

        function limpiarCamposProducto() {
            $("#cantidad").val("1");
            $("#rela_tabla20").val("");
            $("#producto").val("");
            $("#precio").val("0.00");
            $("#sugerencias-productos").hide().empty();
            $("#producto").focus();
        }

        // =============================================
        // FUNCIONES DE FORMAS DE PAGO
        // =============================================

        function agregarFormaPago() {
            const tbody = $("#formas_pago_body");
            const mensajeSinRegistros = $("#sinRegistrofP");

            // Validar si ya existe una fila vacía
            const filasExistentes = tbody.find('tr:not(#sinRegistrofP)');
            if (filasExistentes.length > 0) {
                const ultimaFila = filasExistentes.last();
                const formaPago = ultimaFila.find('select').val();
                const monto = parseFloat(ultimaFila.find('input[type="number"]').val()) || 0;

                if (!formaPago) {
                    mostrarMensaje("Debe seleccionar una forma de pago");
                    return false;
                }

                if (monto <= 0) {
                    mostrarMensaje("Debe ingresar un monto válido");
                    return false;
                }
            }

            // Ocultar mensaje de sin registros
            mensajeSinRegistros.hide();

            // Crear nueva fila
            const nuevaFila = `
                <tr>
                    <td>
                        <span class="delete-btn" onclick="eliminarFormaPago(this)">✕</span>
                    </td>
                    <td>
                        <select class="form-select form-select-sm" onchange="actualizarInteres(this); recalcularTotalPagos();">
                            <option value="">Seleccione...</option>
                            ${formasPagoDemo.map(fp => 
                                `<option value="${fp.id}" data-interes="${fp.interes}">${fp.nombre}</option>`
                            ).join('')}
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm readonly-field" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm" min="0" step="0.01" 
                               oninput="recalcularTotalPagos();" placeholder="0.00">
                    </td>
                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarFormaPago(this)">Eliminar</button>
                    </td>
                </tr>
            `;

            tbody.append(nuevaFila);
            recalcularTotalPagos();
        }

        function actualizarInteres(select) {
            const fila = $(select).closest('tr');
            const interes = $(select).find('option:selected').data('interes') || 0;
            fila.find('td:eq(2) input').val(interes + '%');
            recalcularTotalPagos();
        }

        function eliminarFormaPago(boton) {
            $(boton).closest('tr').remove();
            const tbody = $("#formas_pago_body");
            if (tbody.find('tr:not(#sinRegistrofP)').length === 0) {
                $("#sinRegistrofP").show();
            }
            recalcularTotalPagos();
        }

        function recalcularTotalPagos() {
            let total = 0;
            $("#formas_pago_body tr:not(#sinRegistrofP)").each(function() {
                const monto = parseFloat($(this).find('td:eq(3) input').val()) || 0;
                total += monto;
            });
            
            $("#total_pago").text(total.toFixed(2));
            
            // Validar que el total de pagos no supere el total de la venta
            const totalVenta = parseFloat($("#tabla26_importetotal").val()) || 0;
            if (total > totalVenta) {
                $("#total_pago").addClass('text-danger');
            } else {
                $("#total_pago").removeClass('text-danger');
            }
        }

        // =============================================
        // FUNCIONES DE CÁLCULO
        // =============================================

        function calcularTotales() {
            let totalNeto = 0;
            let totalIVA10 = 0;
            let totalIVA21 = 0;
            let totalIVA27 = 0;
            let totalGeneral = 0;

            productosAgregados.forEach(producto => {
                totalNeto += producto.neto;
                totalGeneral += producto.total;

                switch(producto.alicuota) {
                    case "4": totalIVA10 += producto.iva; break;
                    case "5": totalIVA21 += producto.iva; break;
                    case "6": totalIVA27 += producto.iva; break;
                }
            });

            // Actualizar campos
            $("#tabla26_totalneto").val(totalNeto.toFixed(2));
            $("#tabla26_ivadiez").val(totalIVA10.toFixed(2));
            $("#tabla26_ivaveintinuno").val(totalIVA21.toFixed(2));
            $("#tabla26_ivaveinticiete").val(totalIVA27.toFixed(2));
            $("#tabla26_totalsubtotal").val(totalGeneral.toFixed(2));
            $("#tabla26_importetotal").val(totalGeneral.toFixed(2));
            $("#total-neto").text("$ " + totalNeto.toFixed(2));
            $("#totales_importe_tabla").text("$ " + totalGeneral.toFixed(2));

            // Recalcular bonificación si existe
            aplicarBonificacion();
            recalcularTotalPagos();
        }

        function aplicarBonificacion() {
            const porcentaje = parseFloat($("#tabla26_porc_bonificacion").val()) || 0;
            const subtotal = parseFloat($("#tabla26_totalsubtotal").val()) || 0;
            
            if (porcentaje > 50) {
                mostrarMensaje("La bonificación no puede superar el 50%");
                $("#tabla26_porc_bonificacion").val("0.00");
                return;
            }

            const bonificacion = (subtotal * porcentaje) / 100;
            const totalConBonificacion = subtotal - bonificacion;

            $("#tabla26_bonificacion").val(bonificacion.toFixed(2));
            $("#tabla26_importetotal").val(totalConBonificacion.toFixed(2));
            recalcularTotalPagos();
        }

        function obtenerPorcentajeIVA(alicuota) {
            const ivaMap = {
                "3": 0,    // 0%
                "4": 10.5, // 10.5%
                "5": 21,   // 21%
                "6": 27,   // 27%
                "7": 5,    // 5%
                "8": 2.5   // 2.5%
            };
            return ivaMap[alicuota] || 21;
        }

        // =============================================
        // FUNCIONES DE UTILIDAD
        // =============================================

        function mostrarMensaje(mensaje) {
            $("#dialogConfirmBody").html(`<p>${mensaje}</p>`);
            $("#dialogConfirmModal").modal('show');
        }

        function limpiarFormulario() {
            if (confirm("¿Está seguro de que desea cancelar y limpiar el formulario?")) {
                productosAgregados = [];
                formasPagoAgregadas = [];
                contadorFilas = 0;
                
                $("#form_ventas")[0].reset();
                $("#cuerpo-tabla-productos").html('<tr id="sinregistro"><td colspan="9" class="text-center text-muted">No se encuentran productos cargados</td></tr>');
                $("#formas_pago_body").html('<tr id="sinRegistrofP"><td colspan="5" class="text-center text-muted">No se encuentran formas de pago cargadas</td></tr>');
                
                $("#tabla26_totalneto").val("0.00");
                $("#tabla26_importetotal").val("0.00");
                $("#tabla26_totalsubtotal").val("0.00");
                $("#total_pago").text("0.00");
                
                toggleClienteFields();
                mostrarMensaje("Formulario limpiado correctamente");
            }
        }

        function procesarVenta(imprimir = false) {
            // Validaciones básicas
            if (productosAgregados.length === 0) {
                mostrarMensaje("Debe agregar al menos un producto a la venta");
                return;
            }

            const totalVenta = parseFloat($("#tabla26_importetotal").val()) || 0;
            const totalPago = parseFloat($("#total_pago").text()) || 0;

            if (Math.abs(totalVenta - totalPago) > 0.01) {
                mostrarMensaje("El total de pagos debe coincidir con el importe total de la venta");
                return;
            }

            // Simular procesamiento
            const mensaje = imprimir ? 
                "¡Venta procesada e impresa correctamente! (Demo)" : 
                "¡Venta guardada correctamente! (Demo)";
            
            mostrarMensaje(mensaje);
            
            // Aquí los estudiantes agregarán el código para enviar al servidor
            console.log("Datos a enviar al servidor:", {
                productos: productosAgregados,
                formasPago: formasPagoAgregadas,
                totales: {
                    neto: $("#tabla26_totalneto").val(),
                    iva10: $("#tabla26_ivadiez").val(),
                    iva21: $("#tabla26_ivaveintinuno").val(),
                    iva27: $("#tabla26_ivaveinticiete").val(),
                    subtotal: $("#tabla26_totalsubtotal").val(),
                    bonificacion: $("#tabla26_bonificacion").val(),
                    total: $("#tabla26_importetotal").val()
                }
            });
        }

        function cerrar_tab_actual() {
            if (confirm("¿Está seguro de que desea cerrar? Se perderán los datos no guardados.")) {
                // En un entorno real, aquí se cerraría la pestaña o ventana
                mostrarMensaje("Función cerrar - En producción cerraría la ventana actual");
            }
        }

        // Cerrar sugerencias al hacer clic fuera
        $(document).click(function(e) {
            if (!$(e.target).closest('#sugerencias-productos, #producto').length) {
                $('#sugerencias-productos').hide();
            }
            if (!$(e.target).closest('#sugerencias-clientes, #persona_recibos').length) {
                $('#sugerencias-clientes').hide();
            }
        });
    </script>

</body>
</html>
