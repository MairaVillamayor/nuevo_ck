<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cake Party | Registrar Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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

        h2,
        h3,
        h4,
        h5 {
            color: #e91e63;
            font-weight: 600;
        }

        p {
            color: #555;
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

        .table-dark thead {
            /* Estilo para la tabla de pagos */
            background-color: #4a4a4a;
            color: #f8bbd0;
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

        .btn-primary {
            background-color: #ff4081;
            border: none;
            border-radius: 10px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #e91e63;
        }

        .btn-outline-secondary {
            color: #e91e63;
            border-color: #f8bbd0;
            border-radius: 10px;
        }

        .btn-outline-secondary:hover {
            background-color: #f8bbd0;
            color: #c2185b;
        }

        /* ---- Inputs ---- */
        input,
        select {
            border-radius: 10px !important;
            border: 1px solid #f8bbd0 !important;
        }

        input:focus,
        select:focus {
            border-color: #ec407a !important;
            box-shadow: 0 0 4px rgba(236, 64, 122, 0.4) !important;
        }
    </style>
</head>

<body>

    <div class="container">
        <h3>Registrar Ventas</h3>

        <p>Por favor ingrese todos los datos de su venta!</p>

        <div class="input-group mb-3">
            <input type="text" class="form-control" id="docPersona" placeholder="Ingresar documento del cliente">
            <button class="btn btn-outline-secondary" onclick="consultarCliente();">Buscar</button>
        </div>


        <input type="text" readonly class="form-control-plaintext" id="personaNombre" value="Cliente">
        <input type="hidden" id="personaApellido">
        <input type="hidden" id="personaDireccion">
        <input type="hidden" id="personaDocumento">
        <input type="hidden" id="personaId">


        <button class="btn btn-primary btn-sm" onclick="insertarFactura();">Iniciar Factura</button>
        <h5 id="numFactura">Factura ID: </h5>

        <h4>Agrega un Producto</h4>

        <div class="row mb-4">
            <div class="col-md-7 mb-2 mb-md-0">
                <input type="text" class="form-control" id="nombreProducto" placeholder="Buscar producto por nombre">
                <input type="hidden" id="idProducto">
            </div>

            <div class="col-md-3 mb-2 mb-md-0">
                <input type="number" class="form-control" id="txtCantidad" placeholder="Cantidad" min="1" value="1">
            </div>

            <div class="col-md-2 d-grid">
                <button class="btn btn-cake" onclick="buscarProducto()">
                    Agregar
                </button>
            </div>
        </div>

        <h4>Productos Seleccionados</h4>


        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Subtotal</th>
                </tr>
            </thead>
            <tbody id="resultadoProducto">
            </tbody>
        </table>

        <h4 id="subtotal">Sub Total: $0.00</h4>
        <h4>
            Seleccionar IVA:
            <select id="selectorIVA" onchange="actualizarCalculos()">
                <option value="21">IVA 21%</option>
                <option value="27">IVA 27%</option>
            </select>
        </h4>
        <h4 id="montoIVA">Monto IVA: $0.00</h4>
        <h4 id="total">Total Factura: $0.00</h4>

        <hr style="margin-top: 30px; border-color: #f8bbd0;">

        <div class="row mt-4">
            <div class="col-12">
                <h3 style="display: inline-block; margin-right: 15px;">Formas de Pago</h3>
                <button class="btn btn-cake btn-sm" onclick="agregarFormaPago()">
                    + Agregar Forma de Pago
                </button>
            </div>
        </div>

        <div class="card my-3" style="border: none; background-color: #343a40; color: white;">
            <div class="card-body p-0">
                <table class="table table-dark mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 30%;">Forma de Pago</th>
                            <th style="width: 25%;">Interés %</th>
                            <th style="width: 25%;">Monto</th>
                            <th style="width: 15%;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="formasPagoContainer">
                    </tbody>
                </table>
            </div>
        </div>

        <h4 id="montoPagado" style="text-align: right; color: #d81b60;">Pagado: $0.00</h4>
        <h4 id="montoRestante" style="text-align: right; color: #e91e63;">Restante por Pagar: <span style="font-weight: 700;">$0.00</span></h4>

        <div class="mt-4">
            <div class="row g-2">
                <div class="col-6">
                    <button class="btn btn-primary btn-lg w-100" onclick="finalizarVenta()">PROCESAR VENTA</button>
                </div>
                <div class="col-6">
                    <button class="btn btn-cake btn-lg w-100" onclick="imprimirVenta()">
                        <i class="bi bi-printer"></i> IMPRIMIR VENTA
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <script>
        var id = 0;
        var idFactura = 0;
        var SubTotalGeneral = 0;
        var formasDePagoOpciones = [];
        var pagoIdCounter = 0;

        function actualizarCalculos() {
            const selector = document.getElementById('selectorIVA');
            const tasaIVA = selector ? parseFloat(selector.value) : 0;

            const montoIVA = SubTotalGeneral * (tasaIVA / 100);

            const TotalGeneral = SubTotalGeneral + montoIVA;

            document.getElementById('subtotal').textContent = `Sub Total: $${SubTotalGeneral.toFixed(2)}`;
            document.getElementById('montoIVA').textContent = `Monto IVA (${tasaIVA}%): $${montoIVA.toFixed(2)}`;
            document.getElementById('total').textContent = `Total Factura: $${TotalGeneral.toFixed(2)}`;

            calcularMontoPagado();
        }


        function cargarFormasPagoSelect() {
            $.ajax({
                url: '../../controllers/ventas/obtener_formas_pago.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    formasDePagoOpciones = data;
                    agregarFormaPago();
                },
                error: function() {
                    console.error("No se pudieron cargar las formas de pago. Usando datos de prueba.");
                    formasDePagoOpciones = [{
                            id: 1,
                            nombre: 'Efectivo'
                        },
                        {
                            id: 2,
                            nombre: 'Transferencia'
                        }
                    ];
                    agregarFormaPago();
                }
            });
        }

        function agregarFormaPago() {
            if (formasDePagoOpciones.length === 0) {
                alert("Primero debe cargar las formas de pago. Intente recargar la página.");
                return;
            }

            pagoIdCounter++;
            const filaId = `pago-row-${pagoIdCounter}`;

            let selectOptions = '';
            formasDePagoOpciones.forEach(pago => {
                selectOptions += `<option value="${pago.id}">${pago.nombre}</option>`;
            });

            const nuevaFila = document.createElement("tr");
            nuevaFila.id = filaId;
            nuevaFila.innerHTML = `
                <td><i class="bi bi-x-circle text-danger"></i></td>
                <td>
                    <select class="form-select form-select-sm" name="formaPagoId[]" data-row-id="${pagoIdCounter}">
                        ${selectOptions}
                    </select>
                </td>
                <td>
                    <input type="number" value="0" min="0" class="form-control form-control-sm"
                           name="interes[]" oninput="calcularMontoPagado()">
                </td>
                <td>
                    <input type="number" value="0.00" min="0" step="0.01" class="form-control form-control-sm monto-pago-input"
                           name="monto[]" oninput="calcularMontoPagado()">
                </td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="eliminarFormaPago('${filaId}')">
                        Eliminar
                    </button>
                </td>
            `;

            document.getElementById("formasPagoContainer").appendChild(nuevaFila);
            calcularMontoPagado();
        }

        function eliminarFormaPago(filaId) {
            const fila = document.getElementById(filaId);
            if (fila) {
                fila.remove();
                calcularMontoPagado();
            }
        }

        function calcularMontoPagado() {
            let montoPagadoTotal = 0;

            const totalFacturaTexto = document.getElementById('total').textContent;
            const totalFactura = parseFloat(totalFacturaTexto.split('$').pop().trim()) || 0;

            document.querySelectorAll('.monto-pago-input').forEach(input => {
                montoPagadoTotal += parseFloat(input.value) || 0;
            });

            const montoRestante = totalFactura - montoPagadoTotal;

            document.getElementById('montoPagado').textContent = `Pagado: $${montoPagadoTotal.toFixed(2)}`;

            const restanteElement = document.getElementById('montoRestante').querySelector('span');
            const color = montoRestante > 0 ? '#dc3545' : (montoRestante < 0 ? '#ffc107' : '#28a745');

            restanteElement.textContent = `$${montoRestante.toFixed(2)}`;
            restanteElement.style.color = color;

            return {
                totalFactura: totalFactura,
                pagado: montoPagadoTotal,
                restante: montoRestante
            };
        }

        function consultarCliente() {
            var docPersona = document.getElementById("docPersona").value;

            if (!docPersona) {
                alert("Por favor, ingrese un documento válido.");
                return;
            }
            $.ajax({
                url: '../../controllers/ventas/consultar_cliente.php',
                method: 'POST',
                data: {
                    persona_documento: docPersona
                },
                dataType: 'json',
                success: function(data) {
                    if (data.error) {
                        alert(data.error)
                    } else {

                        document.getElementById("personaNombre").value = data.persona_nombre + " " + data.persona_apellido;
                        document.getElementById("personaApellido").value = data.persona_apellido;
                        document.getElementById("personaDireccion").value = data.persona_direccion;
                        document.getElementById("personaDocumento").value = data.persona_documento;
                        document.getElementById("personaId").value = data.id_persona;

                        id = data.persona_documento;
                        console.log("Cliente encontrado:", id);
                    }
                }
            });
        }

        function insertarFactura() {
            const documento = document.getElementById("personaDocumento").value;
            const nombre = document.getElementById("personaNombre").value;
            const apellido = document.getElementById("personaApellido").value;

            if (!documento) {
                alert("ERROR: Primero debe buscar y seleccionar un cliente.");
                return;
            }

            $.ajax({
                url: '../../controllers/ventas/iniciar_factura.php',
                method: 'POST',
                data: {
                    persona_documento: documento,
                    persona_nombre: nombre,
                    persona_apellido: apellido
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        var id_factura = data.id_factura;
                        document.getElementById("numFactura").innerText = "Factura ID: " + id_factura;
                        idFactura = id_factura;
                        console.log("ID de Factura Establecido:", idFactura);
                    } else {
                        alert("Error al insertar factura: " + data.error);
                        console.error("AJAX Error:", data.error);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Error de comunicación al iniciar factura.");
                    console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
                }
            });
        }

        $(function() {
            $("#nombreProducto").autocomplete({
                source: '../../controllers/ventas/buscar_producto.php',
                minLength: 2,
                select: function(event, ui) {
                    $("#idProducto").val(ui.item.id);
                    $(this).val(ui.item.value);
                    if (parseInt(ui.item.stock) <= 0) {
                        alert("ADVERTENCIA: ¡El producto seleccionado está agotado (Stock: 0)!");
                    }
                    return false;
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                let stockColor = parseInt(item.stock) > 0 ? 'gray' : 'red';
                return $("<li>").append("<div>" + item.value +
                        " <span style='float:right; color: " + stockColor + "; font-weight: bold;'>[Stock: " + item.stock + "]</span></div>")
                    .appendTo(ul);
            };
        });

        function buscarProducto() {
            const idProducto = document.getElementById("idProducto").value;
            const cant = document.getElementById("txtCantidad").value;
            const cantidad = parseFloat(cant);

            if (!idProducto) {
                alert("ERROR: Debe seleccionar un producto.");
                return;
            }
            if (!cant || isNaN(cantidad) || cantidad <= 0) {
                alert("ERROR: Por favor, ingrese una cantidad válida.");
                return;
            }
            if (!idFactura || idFactura <= 0) {
                alert("ERROR: Primero debe iniciar la factura.");
                return;
                console.log("idFactura dentro de buscarProducto():", idFactura);

            }

            $.ajax({

                url: '../../controllers/ventas/agregar_producto.php',
                method: 'POST',
                data: {
                    idProducto: idProducto,
                    RELA_factura: idFactura,
                    factura_detalle_cantidad: cantidad
                },
                dataType: 'json',
                success: function(data) {
                    if (data.error || !data.id_producto_finalizado) {
                        alert(data.error || "Producto no encontrado.");
                    } else {
                        const productoFinalizadoId = data.id_producto_finalizado;
                        const precio = parseFloat(data.producto_finalizado_precio);
                        let subTotal = precio * cantidad;

                        SubTotalGeneral += subTotal;
                        actualizarCalculos();

                        insertarProductoFactura(idFactura, productoFinalizadoId, cant);

                        const fila = document.createElement("tr");
                        fila.innerHTML = `
                            <td>${productoFinalizadoId}</td>
                            <td>${data.producto_finalizado_nombre}</td>
                            <td>${precio.toFixed(2)}</td>
                            <td>${cantidad}</td>
                            <td>${subTotal.toFixed(2)}</td>
                        `;
                        document.getElementById("resultadoProducto").appendChild(fila);

                        document.getElementById("idProducto").value = '';
                        document.getElementById("nombreProducto").value = '';
                        document.getElementById("txtCantidad").value = '';
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error AJAX en buscarProducto:", textStatus, errorThrown);
                    alert("Error de comunicación con el servidor al buscar producto.");
                }
            });
        }

        function insertarProductoFactura(RELA_factura, RELA_producto_finalizado, cant) {
            $.ajax({
                url: '../../controllers/ventas/insertar_producto_factura.php',
                method: 'POST',
                data: {
                    RELA_factura: RELA_factura,
                    RELA_producto_finalizado: RELA_producto_finalizado,
                    factura_detalle_cantidad: cant
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        console.log("Producto agregado exitosamente a la BD.");
                    } else {
                        console.error("Error al insertar producto en BD:", data.error);
                        alert("Error al insertar producto en la BD. Detalles: " + data.error);
                    }
                }
            });
        }

        function finalizarVenta() {
            const resumenPagos = calcularMontoPagado();

            if (idFactura === 0) {
                alert("Debe iniciar la factura primero.");
                return;
            }
            if (SubTotalGeneral === 0) {
                alert("Debe agregar productos a la factura.");
                return;
            }
            if (resumenPagos.restante !== 0) {
                alert(`El monto pagado ($${resumenPagos.pagado.toFixed(2)}) no coincide con el Total Factura ($${resumenPagos.totalFactura.toFixed(2)}). Restante: $${resumenPagos.restante.toFixed(2)}`);
                return;
            }

            const formasPagoData = [];
            document.querySelectorAll('#formasPagoContainer tr').forEach(fila => {
                const id_pago = fila.querySelector('select[name="formaPagoId[]"]').value;
                const interes = fila.querySelector('input[name="interes[]"]').value;
                const monto = fila.querySelector('input[name="monto[]"]').value;

                formasPagoData.push({
                    id_metodo_pago: id_pago,
                    interes: interes,
                    monto: monto
                });
            });

            const selectorIVA = document.getElementById('selectorIVA');
            const tasaIVA = selectorIVA ? parseFloat(selectorIVA.value) : 0;
            const montoIVA = SubTotalGeneral * (tasaIVA / 100);
            const TotalGeneral = SubTotalGeneral + montoIVA;

            const datosFinales = {
                idFactura: idFactura,
                subTotal: SubTotalGeneral.toFixed(2),
                tasaIVA: tasaIVA,
                montoIVA: montoIVA.toFixed(2),
                totalFactura: TotalGeneral.toFixed(2),
                pagos: formasPagoData
            };

            console.log("Datos a enviar a PHP:", datosFinales);

            $.ajax({
                url: '../../controllers/ventas/finalizar_venta.php',
                method: 'POST',
                data: datosFinales,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert("¡Venta finalizada con éxito! Factura N° " + idFactura);

                    } else {
                        alert("Error al finalizar la venta: " + response.error);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Error de comunicación al finalizar la venta.");
                    console.error("Error AJAX en finalizarVenta:", textStatus, errorThrown, jqXHR.responseText);
                }
            });
        }


        function imprimirVenta() {
                if (!idFactura || idFactura <= 0) {
                    alert("ERROR: Primero debe iniciar la factura y agregar productos para poder imprimirla.");
                    return;
                }

                const rutaFactura = 'imprimir_venta.php?idFactura=' + idFactura;

                window.open(rutaFactura, '_blank');
            }
            
        window.onload = function() {
            actualizarCalculos();
            cargarFormasPagoSelect();
        };
    </script>
</body>

</html>