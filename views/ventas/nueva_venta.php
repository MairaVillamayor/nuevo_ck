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
    <button class="btn btn-primary btn-sm" onclick="insertarFactura();">Iniciar Factura</button>
    <h5 id="numFactura">Factura ID: </h5>

    <h4>Agrega un Producto</h4>

    <div class="row mb-4">
      <div class="col-md-7 mb-2 mb-md-0">
        <input type="text" class="form-control" id="nombreProductoBuscador" placeholder="Buscar producto por nombre">
        <input type="hidden" id="idProductoSeleccionado">
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
          <button class="btn btn-primary btn-lg w-100" onclick="finalizarVenta()">
            PROCESAR VENTA
          </button>
        </div>

        <div class="col-6">
          <button class="btn btn-outline-secondary btn-lg w-100" onclick="imprimirVenta()">
            <i class="bi bi-printer-fill"></i> Imprimir Venta
          </button>
        </div>
      </div>

      <!-- 🧾 Comprobante de Venta / Presupuesto -->
<div id="comprobanteVenta" class="factura" style="display:none;">
    <header>
        <h1>PRESUPUESTO</h1>
        <small>Documento no válido como factura</small>
    </header>

    <div class="datos-cliente">
        <p><label>Sr/es:</label> <span id="comp_cliente_nombre"></span></p>
        <p><label>Dirección:</label> <span id="comp_cliente_direccion"></span></p>
        <p><label>DNI:</label> <span id="comp_cliente_dni"></span></p>
    </div>

    <table id="tablaComprobanteProductos">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>Precio</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <td colspan="3">TOTAL</td>
                <td id="comp_total">$0.00</td>
            </tr>
        </tfoot>
    </table>

    <p style="text-align:right; margin-top:10px;">N° 00033951</p>
</div>

<style>
.factura {
    width: 700px;
    margin: auto;
    border: 1px solid #000;
    padding: 20px;
    background: #fff;
    color: #000;
    font-family: Arial, sans-serif;
}
.factura header {
    text-align: center;
    border-bottom: 1px solid #000;
    margin-bottom: 15px;
}
.factura header h1 {
    font-size: 20px;
    margin: 0;
}
.factura header small {
    font-size: 12px;
    display: block;
}
.factura .datos-cliente label {
    display: inline-block;
    width: 90px;
    font-weight: bold;
}
.factura table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.factura table th, .factura table td {
    border: 1px solid #000;
    padding: 5px;
    text-align: left;
}
.factura tfoot td {
    text-align: right;
    font-weight: bold;
}
@media print {
    body * { visibility: hidden; }
    #comprobanteVenta, #comprobanteVenta * {
        visibility: visible;
    }
    #comprobanteVenta {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>

<script>
function imprimirVenta() {
    const comprobante = document.getElementById('comprobanteVenta');
    comprobante.style.display = 'block'; // Mostrar antes de imprimir

    // ✅ Datos reales del cliente (los que obtuviste de consultar_cliente())
    const nombre = document.getElementById("personaNombre").value || "";
    const direccion = document.getElementById("personaDireccion")?.value || "—";
    const dni = document.getElementById("docPersona").value || "—";

    document.getElementById("comp_cliente_nombre").innerText = nombre;
    document.getElementById("comp_cliente_direccion").innerText = direccion;
    document.getElementById("comp_cliente_dni").innerText = dni;

    // ✅ Productos reales agregados a la tabla de venta
    const filas = document.querySelectorAll("#resultadoProducto tr");
    const tbody = document.querySelector("#tablaComprobanteProductos tbody");
    tbody.innerHTML = "";
    let total = 0;

    filas.forEach(fila => {
        const celdas = fila.querySelectorAll("td");
        if (celdas.length >= 5) {
            const nombre = celdas[1].innerText;
            const precio = parseFloat(celdas[2].innerText);
            const cantidad = parseFloat(celdas[3].innerText);
            const subtotal = parseFloat(celdas[4].innerText);
            total += subtotal;

            tbody.innerHTML += `
                <tr>
                    <td>${nombre}</td>
                    <td>${cantidad}</td>
                    <td>$${precio.toFixed(2)}</td>
                    <td>$${subtotal.toFixed(2)}</td>
                </tr>
            `;
        }
    });

    document.getElementById("comp_total").innerText = "$" + total.toFixed(2);

    // 🖨️ Imprimir
    window.print();

    // Ocultar comprobante después de imprimir
    setTimeout(() => comprobante.style.display = 'none', 1000);
}
</script>


    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

  <script>
    // ============================================
    // === VARIABLES GLOBALES =====================
    // ============================================
    var id = 0; // ID de la persona/cliente
    var idFactura = 0;
    var SubTotalGeneral = 0; // El precio base acumulado (sin impuestos)
    var formasDePagoOpciones = []; // Para almacenar las opciones de pago de la BD
    var pagoIdCounter = 0; // Contador para IDs únicos de filas de pago

    // ============================================
    // === LÓGICA DE IVA Y TOTALES ================
    // ============================================

    /**
     * Lógica Central de Cálculo de IVA (21% o 27%) y actualización de totales de factura.
     * Llama a calcularMontoPagado() al finalizar.
     */
    function actualizarCalculos() {
      const selector = document.getElementById('selectorIVA');
      // Obtiene la tasa, si no existe o es inválida, usa 0
      const tasaIVA = selector ? parseFloat(selector.value) : 0;

      // 1. Calcular el Monto del IVA
      const montoIVA = SubTotalGeneral * (tasaIVA / 100);

      // 2. Calcular el Total General
      const TotalGeneral = SubTotalGeneral + montoIVA;

      // 3. Actualizar la Interfaz (HTML)
      document.getElementById('subtotal').textContent = `Sub Total: $${SubTotalGeneral.toFixed(2)}`;
      document.getElementById('montoIVA').textContent = `Monto IVA (${tasaIVA}%): $${montoIVA.toFixed(2)}`;
      document.getElementById('total').textContent = `Total Factura: $${TotalGeneral.toFixed(2)}`;

      // 4. Recalcular el estado de los pagos después de que el Total Factura cambie
      calcularMontoPagado();
    }

    // ============================================
    // === LÓGICA DE FORMAS DE PAGO ===============
    // ============================================

    /**
     * Obtiene las opciones de formas de pago desde el controlador PHP.
     */
    function cargarFormasPagoSelect() {
      // **IMPORTANTE**: Si el controlador 'obtener_formas_pago.php' no existe o falla,
      // se usan los datos de prueba. ¡Asegúrate de crear ese controlador!
      $.ajax({
        url: '../../controllers/ventas/obtener_formas_pago.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
          formasDePagoOpciones = data;
          agregarFormaPago(); // Agrega la primera fila de pago al cargar
        },
        error: function() {
          console.error("No se pudieron cargar las formas de pago. Usando datos de prueba.");
          formasDePagoOpciones = [{
              id: 1,
              nombre: 'Efectivo'
            },
            {
              id: 2,
              nombre: 'Tarjeta Crédito'
            },
            {
              id: 3,
              nombre: 'Transferencia'
            }
          ];
          agregarFormaPago();
        }
      });
    }

    /**
     * Agrega una nueva fila a la tabla de formas de pago.
     */
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

    /**
     * Elimina una fila de pago y recalcula los totales.
     */
    function eliminarFormaPago(filaId) {
      const fila = document.getElementById(filaId);
      if (fila) {
        fila.remove();
        calcularMontoPagado();
      }
    }

    /**
     * Suma los montos de pago y calcula el restante versus el Total Factura.
     */
    function calcularMontoPagado() {
      let montoPagadoTotal = 0;

      // Obtener el Total Factura (asume que ya está actualizado por actualizarCalculos())
      const totalFacturaTexto = document.getElementById('total').textContent;
      const totalFactura = parseFloat(totalFacturaTexto.split('$').pop().trim()) || 0;

      // Recorrer todos los inputs con la clase 'monto-pago-input'
      document.querySelectorAll('.monto-pago-input').forEach(input => {
        // El parseFloat() es crucial para sumar como números y no como strings
        montoPagadoTotal += parseFloat(input.value) || 0;
      });

      const montoRestante = totalFactura - montoPagadoTotal;

      // Actualizar la interfaz
      document.getElementById('montoPagado').textContent = `Pagado: $${montoPagadoTotal.toFixed(2)}`;

      const restanteElement = document.getElementById('montoRestante').querySelector('span');
      // Cambia el color si hay saldo pendiente o de más
      const color = montoRestante > 0 ? '#dc3545' : (montoRestante < 0 ? '#ffc107' : '#28a745');

      restanteElement.textContent = `$${montoRestante.toFixed(2)}`;
      restanteElement.style.color = color;

      return {
        totalFactura: totalFactura,
        pagado: montoPagadoTotal,
        restante: montoRestante
      };
    }

    // ============================================
    // === FUNCIONES EXISTENTES (INTEGRADAS) ======
    // ============================================

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
            document.getElementById("personaNombre").value =
              data.persona_nombre + " " + data.persona_apellido;
            id = data.persona_documento; // Asumo que ID es el documento para usarlo en insertarFactura
            console.log("Cliente encontrado:", id);
          }
        }
      });
    }

    function insertarFactura() {
      // Usar el ID de persona ya que se usará en la BD
      const RELA_persona = id;

      if (RELA_persona === 0) {
        alert("ERROR: Primero debe buscar y seleccionar un cliente.");
        return;
      }

      $.ajax({
        url: '../../controllers/ventas/iniciar_factura.php',
        method: 'POST',
        data: {
          RELA_persona: RELA_persona
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

    // Lógica de Autocompletado (jQuery UI)
    $(function() {
      $("#nombreProductoBuscador").autocomplete({
        source: '../../controllers/ventas/buscar_sugerencias_producto.php',
        minLength: 2,
        select: function(event, ui) {
          $("#idProductoSeleccionado").val(ui.item.id);
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
      const idProducto = document.getElementById("idProductoSeleccionado").value;
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
      if (idFactura === 0) {
        alert("ERROR: Primero debe iniciar la factura.");
        return;
      }

      $.ajax({
        url: '../../controllers/ventas/buscar_producto.php',
        method: 'POST',
        data: {
          idProducto: idProducto
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
            actualizarCalculos(); // Recalcula el IVA y Totales

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

            // Limpiar campos después de la operación
            document.getElementById("idProductoSeleccionado").value = '';
            document.getElementById("nombreProductoBuscador").value = '';
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

    // ============================================
    // === FUNCIÓN FINALIZAR VENTA (PARA PHP) =====
    // ============================================

    /**
     * Recolecta todos los datos de la venta y los envía a PHP.
     */
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
        // Validación estricta: el restante debe ser 0.
        alert(`El monto pagado ($${resumenPagos.pagado.toFixed(2)}) no coincide con el Total Factura ($${resumenPagos.totalFactura.toFixed(2)}). Restante: $${resumenPagos.restante.toFixed(2)}`);
        return;
      }

      // 1. Recolectar datos de las formas de pago
      const formasPagoData = [];
      document.querySelectorAll('#formasPagoContainer tr').forEach(fila => {
        const id_pago = fila.querySelector('select[name="formaPagoId[]"]').value;
        const interes = fila.querySelector('input[name="interes[]"]').value;
        const monto = fila.querySelector('input[name="monto[]"]').value;

        formasPagoData.push({
          id_pago: id_pago,
          interes: interes,
          monto: monto
        });
      });

      // 2. Recolectar datos de Totales
      const selectorIVA = document.getElementById('selectorIVA');
      const tasaIVA = selectorIVA ? parseFloat(selectorIVA.value) : 0;
      const montoIVA = SubTotalGeneral * (tasaIVA / 100);
      const TotalGeneral = SubTotalGeneral + montoIVA;

      // 3. Preparar el objeto final para PHP
      const datosFinales = {
        idFactura: idFactura,
        subTotal: SubTotalGeneral.toFixed(2),
        tasaIVA: tasaIVA,
        montoIVA: montoIVA.toFixed(2),
        totalFactura: TotalGeneral.toFixed(2),
        pagos: formasPagoData
      };

      console.log("Datos a enviar a PHP:", datosFinales);

      // 4. Enviar a un controlador PHP para la finalización y guardado
      $.ajax({
        url: '../../controllers/ventas/finalizar_venta.php', // ⬅️ DEBES CREAR ESTE CONTROLADOR PHP
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

    // ============================================
    // === INICIALIZACIÓN =========================
    // ============================================
    window.onload = function() {
      // Inicializa los cálculos de la factura (IVA, Subtotal)
      actualizarCalculos();
      // Carga las opciones de pago de la BD y agrega la primera fila
      cargarFormasPagoSelect();
    };

  </script>
</body>

</html>