<?php
require_once __DIR__ . '/../../config/conexion.php';
include("../../includes/navegacion.php");
session_start();

$perfiles_permitidos = [1, 2, 4];

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['perfil_id'], $perfiles_permitidos)) {
    header('Location: ../../index.php?error=not_logged');
    exit;
}

$pdo = getConexion();

// Obtener estados
$estados_stmt = $pdo->query("SELECT ID_estado, estado_descri FROM estado ORDER BY ID_estado ASC");
$estados = $estados_stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener pedidos
$sql = "SELECT 
    pe.ID_pedido,
    pe.pedido_fecha,
    p_envio.envio_fecha_hora_entrega,
    p_envio.envio_calle_numero,
    p_envio.envio_barrio,
    p_envio.envio_localidad,
    u.usuario_nombre,
    pp.pastel_personalizado_descripcion,
    mp.metodo_pago_descri AS metodo_pago,
    e.ID_estado,
    e.estado_descri AS estado_descri
FROM pedido pe
LEFT JOIN usuarios u ON pe.RELA_usuario = u.ID_usuario
LEFT JOIN pedido_detalle pd ON pd.RELA_pedido = pe.ID_pedido
LEFT JOIN pastel_personalizado pp ON pp.ID_pastel_personalizado = pd.RELA_pastel_personalizado
LEFT JOIN metodo_pago mp ON pe.RELA_metodo_pago = mp.ID_metodo_pago
LEFT JOIN estado e ON pe.RELA_estado = e.ID_estado
LEFT JOIN pedido_envio p_envio ON pe.RELA_pedido_envio = p_envio.ID_pedido_envio
ORDER BY pe.ID_pedido DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar pedidos por estado
$pedidos_por_estado = [];
foreach ($pedidos as $p) {
    $estado_descri = $p['estado_descri'] ?? 'Sin Estado';
    $pedidos_por_estado[$estado_descri][] = $p;
}

if (isset($pedidos_por_estado['Sin Estado'])) {
    array_unshift($estados, ['ID_estado' => 0, 'estado_descri' => 'Sin Estado']);
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Listado de Pedidos - Admin</title>

<style>
    body {
        font-family: 'Poppins', Arial, sans-serif;
        background: #fff0f5;
        margin: 0;
        padding: 20px;
        color: #333;
    }

    h2 {
        text-align: center;
        color: #e91e63;
        margin-bottom: 30px;
        font-size: 2rem;
    }

    h3 {
        margin-top: 40px;
        color: #333;
    }

    /* Tarjetas más pequeñas (4 por fila) */
    .cards-container {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        justify-content: start;
    }

    .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 15px;
        width: 22%;
        min-width: 240px;
        box-sizing: border-box;
    }

    .card h4 {
        font-size: 1rem;
        margin-bottom: 6px;
    }

    .card p {
        font-size: .85rem;
        margin: 4px 0;
    }

    /* Estado */
    .estado {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: .8rem;
        font-weight: bold;
        color: #fff;
    }

    .estado-pendiente { background:#f44336; }
    .estado-en-proceso { background:#ff9800; }
    .estado-finalizado { background:#4caf50; }
    .estado-sin-estado { background:#9e9e9e; }
    .estado-cancelado { background:#607d8b; }

    /* Select */
    .card select {
        margin-top: 8px;
        padding: 6px;
        width: 100%;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: .85rem;
    }

    /* Paginador */
    .paginador {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin: 15px 0 35px 0;
    }

    .pagina-btn {
        padding: 7px 14px;
        background: #e91e63;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
    }

    .pagina-btn:hover {
        background: #c2185b;
    }

    .pagina-activa {
        background:#880e4f !important;
        font-weight: bold;
    }

    /* Botón Excel */
    .btn-add {
        padding: 10px 15px;
        background: #2196f3;
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
    }

    .btn-add:hover {
        background:#1976d2;
    }

    /* Responsive */
    @media (max-width:1100px) { .card { width:30%; } }
    @media (max-width:800px) { .card { width:45%; } }
    @media (max-width:500px) { .card { width:100%; } }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function cambiarEstado(pedidoId, select) {
    const nuevoEstado = select.value;

    fetch('../../controllers/admin/estado_pedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            pedido_id: pedidoId,
            RELA_estado: nuevoEstado
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Estado actualizado',
                confirmButtonColor: '#e91e63'
            }).then(() => location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error
            });
        }
    });
}
</script>

</head>

<body>

<h2>📋 Listado de Pedidos por Estado</h2>

<p style="text-align:center;">
    <a href="../../excel/excel_pedidos.php" class="btn-add">☷ Exportar a Excel</a>
</p>

<!-- Pasamos los pedidos al JS -->
<script>
    const pedidosPorEstado = <?= json_encode($pedidos_por_estado, JSON_UNESCAPED_UNICODE) ?>;
    const estadosInfo = <?= json_encode($estados, JSON_UNESCAPED_UNICODE) ?>;
</script>

<?php foreach ($estados as $estado): ?>
<h3><?= htmlspecialchars($estado['estado_descri']) ?></h3>

<div class="cards-container" id="cards-<?= $estado['ID_estado'] ?>"></div>
<div class="paginador" id="paginador-<?= $estado['ID_estado'] ?>"></div>

<?php endforeach; ?>

<script>
const pedidosPorPagina = 4;
const paginas = {};
estadosInfo.forEach(e => paginas[e.estado_descri] = 0);

function renderPedidos(estadoNombre, estadoId) {
    const contenedor = document.getElementById("cards-" + estadoId);
    const paginador = document.getElementById("paginador-" + estadoId);

    const pedidos = pedidosPorEstado[estadoNombre] || [];
    const paginaActual = paginas[estadoNombre];

    const inicio = paginaActual * pedidosPorPagina;
    const fin = inicio + pedidosPorPagina;
    const pagina = pedidos.slice(inicio, fin);

    contenedor.innerHTML = "";

    if (pedidos.length === 0) {
        contenedor.innerHTML = "<p>No hay pedidos.</p>";
        paginador.innerHTML = "";
        return;
    }

    pagina.forEach(p => {
        const estado = p.estado_descri || "Sin Estado";
        const clase = estado.toLowerCase().replace(/ /g, '-');

        let opciones = "";
        estadosInfo.forEach(e => {
            let estadoActual = (p.estado_descri || "").toUpperCase();
            let nombreEstado = e.estado_descri.toUpperCase();
            let deshabilitar = false;

            if (estadoActual === "CANCELADO" && nombreEstado !== "CANCELADO") deshabilitar = true;
            if (estadoActual === "FINALIZADO" && nombreEstado !== "FINALIZADO") deshabilitar = true;
            if (estadoActual === "PAGADO" && nombreEstado === "CANCELADO") deshabilitar = true;
            if (estadoActual === "EN PROCESO" && nombreEstado !== "FINALIZADO" && nombreEstado !== "EN PROCESO") deshabilitar = true;

            opciones += `
                <option value="${e.ID_estado}"
                    ${p.ID_estado == e.ID_estado ? "selected" : ""} 
                    ${deshabilitar ? "disabled" : ""}>
                    ${e.estado_descri}
                </option>
            `;
        });

        const card = document.createElement("div");
        card.className = "card";

        card.innerHTML = `
            <h4>Pedido #${p.ID_pedido}</h4>
            <p><strong>Usuario:</strong> ${p.usuario_nombre}</p>
            <p><strong>Fecha:</strong> ${p.pedido_fecha}</p>
            <p><strong>Descripción:</strong> ${p.pastel_personalizado_descripcion}</p>
            <p><strong>Entrega:</strong> ${p.envio_fecha_hora_entrega || "N/A"}</p>
            <p><strong>Dirección:</strong> ${p.envio_calle_numero || "N/A"} ${p.envio_barrio ? `(Barrio: ${p.envio_barrio})` : ''}</p>
            <p><strong>Localidad:</strong> ${p.envio_localidad}</p>
            <p><strong>Método de pago:</strong> ${p.metodo_pago}</p>

            <p><span class="estado estado-${clase}">${estado}</span></p>

            <select onchange="cambiarEstado(${p.ID_pedido}, this)">
                ${opciones}
            </select>
        `;

        contenedor.appendChild(card);
    });

    // PAGINADOR
    const totalPaginas = Math.ceil(pedidos.length / pedidosPorPagina);
    paginador.innerHTML = "";

    const btnPrev = document.createElement("button");
    btnPrev.className = "pagina-btn";
    btnPrev.textContent = "⬅";
    btnPrev.onclick = () => cambiarPagina(estadoNombre, estadoId, -1);
    paginador.appendChild(btnPrev);

    for (let i = 0; i < totalPaginas; i++) {
        const btn = document.createElement("button");
        btn.className = "pagina-btn";
        if (i === paginaActual) btn.classList.add("pagina-activa");

        btn.textContent = (i + 1);
        btn.onclick = () => {
            paginas[estadoNombre] = i;
            renderPedidos(estadoNombre, estadoId);
        };
        paginador.appendChild(btn);
    }

    const btnNext = document.createElement("button");
    btnNext.className = "pagina-btn";
    btnNext.textContent = "➡";
    btnNext.onclick = () => cambiarPagina(estadoNombre, estadoId, 1);
    paginador.appendChild(btnNext);
}

function cambiarPagina(estadoNombre, estadoId, dir) {
    const total = pedidosPorEstado[estadoNombre]?.length || 0;
    const totalPaginas = Math.ceil(total / pedidosPorPagina);

    paginas[estadoNombre] += dir;
    if (paginas[estadoNombre] < 0) paginas[estadoNombre] = 0;
    if (paginas[estadoNombre] >= totalPaginas) paginas[estadoNombre] = totalPaginas - 1;

    renderPedidos(estadoNombre, estadoId);
}

estadosInfo.forEach(e => {
    renderPedidos(e.estado_descri, e.ID_estado);
});
</script>

</body>
</html>
