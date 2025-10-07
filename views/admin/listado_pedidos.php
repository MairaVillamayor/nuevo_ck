<?php
require_once __DIR__ . '/../../config/conexion.php';
include ("../../includes/navegacion.php");
session_start();

// ✅ Solo administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil_id'] != 1) {
    header('Location: ../../index.php?error=not_logged');
    exit;
}

$pdo = getConexion();

// -----------------
// Obtener todos los estados
// -----------------
$estados_stmt = $pdo->query("SELECT ID_estado, estado_descri FROM estado ORDER BY ID_estado ASC");
$estados = $estados_stmt->fetchAll(PDO::FETCH_ASSOC);

// -----------------
// Obtener todos los pedidos
// -----------------
$sql = " SELECT 
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
LEFT JOIN usuarios u 
    ON pe.RELA_usuario = u.ID_usuario
LEFT JOIN pedido_detalle pd 
    ON pd.RELA_pedido = pe.ID_pedido
LEFT JOIN pastel_personalizado pp 
    ON pp.ID_pastel_personalizado = pd.RELA_pastel_personalizado
LEFT JOIN metodo_pago mp 
    ON pe.RELA_metodo_pago = mp.ID_metodo_pago
LEFT JOIN estado e 
    ON pe.RELA_estado = e.ID_estado
LEFT JOIN pedido_envio p_envio
    ON pe.RELA_pedido_envio = p_envio.ID_pedido_envio
ORDER BY pe.ID_pedido DESC; ";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

.container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content:left;
}

.card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    padding: 20px;
    width: 320px;
    transition: transform 0.2s ease;
    position: relative;
}

.card:hover {
    transform: translateY(-5px);
}

.card h4 {
    margin: 0 0 10px;
    color: #d81b60;
    font-size: 1.2rem;
}

.card p {
    margin: 5px 0;
    font-size: 0.95rem;
}

.estado {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: bold;
    color: #fff;
    display: inline-block;
}


/* Select */
.card select {
    margin-top: 12px;
    padding: 8px;
    border-radius: 8px;
    border: 1px solid #ccc;
    width: 100%;
    font-size: 0.9rem;
}

/* Botón para refrescar (opcional) */
.refresh-btn {
    display: block;
    margin: 20px auto;
    padding: 10px 18px;
    background: #e91e63;
    color: #fff;
    border: none;
    border-radius: 25px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s ease;
}

.refresh-btn:hover {
    background: #c2185b;
}
.estado-pendiente { background: #f44336; } 
.estado-en-proceso { background: #ff9800; } 
.estado-finalizado { background: #4caf50; } 
.estado-sin-estado { background: #9e9e9e; } 
.estado-cancelado { background: #607d8b; }
</style>
<!-- Importa SweetAlert2 (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function cambiarEstado(pedidoId, select) {
    const nuevoEstado = select.value;

    fetch('../../controllers/admin/estado_pedido.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({pedido_id: pedidoId, RELA_estado: nuevoEstado})
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            Swal.fire({
                icon: 'success',
                title: 'Estado actualizado 🎉',
                text: 'El pedido ahora está en: ' + data.estado,
                confirmButtonColor: '#e91e63'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error ⚠️',
                text: data.error,
                confirmButtonColor: '#f44336'
            });
        }
    })
    .catch(err => {
        Swal.fire({
            icon: 'warning',
            title: '❌ Error de conexión',
            text: 'No se pudo contactar con el servidor',
            confirmButtonColor: '#ff9800'
        });
    });
}
</script>

</head>
<body>
<h2>📋 Listado de Pedidos por Estado</h2>
<p style="margin-top:10px; text-align:center;">
        <a href="../../excel/excel_pedidos.php?id_usuario=<?= urlencode($p['ID_pedido'] ?? 0) ?>" 
        class="btn-add" style="display:inline-block; padding:8px 12px; background:#4caf50; color:#fff; border-radius:6px; text-decoration:none; font-weight:bold;">
        ☷ Exportar a Excel
        </a>
    </p>
<?php foreach($estados as $estado): ?>
    <h3><?= htmlspecialchars($estado['estado_descri']) ?></h3>
    <div class="container">
        <?php 
        $current_estado_descri = $estado['estado_descri'];
        if (isset($pedidos_por_estado[$current_estado_descri])): 
        ?>
            <?php foreach($pedidos_por_estado[$current_estado_descri] as $p): ?>
                <?php 
                $estado_descri = $p['estado_descri'] ?? 'Sin Estado';
                $clase_estado = strtolower(str_replace(' ', '-', $estado_descri)); 
                ?>
                
                <div class="card">
                    <h4>Pedido #<?= htmlspecialchars($p['ID_pedido']) ?></h4>
                    <p><strong>Usuario:</strong> <?= htmlspecialchars($p['usuario_nombre']) ?></p>
                    <p><strong>Fecha:</strong> <?= htmlspecialchars($p['pedido_fecha']) ?></p>
                    <p><strong>Descripción:</strong> <?= htmlspecialchars($p['pastel_personalizado_descripcion']) ?></p>
                    
                    <p><strong>Entrega:</strong> <?= htmlspecialchars($p['envio_fecha_hora_entrega'] ?? 'N/A') ?></p>
                    <p>
                        <strong>Dirección:</strong> 
                        <?= htmlspecialchars($p['envio_calle_numero'] ?? 'Dirección no disponible') ?>
                        <?= !empty($p['envio_barrio']) ? ' (Barrio: ' . htmlspecialchars($p['envio_barrio']) . ')' : '' ?>
                    </p>
                    <p><strong>Localidad:</strong> <?= htmlspecialchars($p['envio_localidad'] ?? 'N/A') ?></p>
                    
                    <p><strong>Método de pago:</strong> <?= htmlspecialchars($p['metodo_pago']) ?></p>
                    <p><span class="estado estado-<?= $clase_estado ?>"><?= htmlspecialchars($estado_descri) ?></span></p>
                    <select onchange="cambiarEstado(<?= $p['ID_pedido'] ?>, this)">
                        <?php foreach($estados as $e): ?>
                            <option value="<?= $e['ID_estado'] ?>" <?= ($e['ID_estado'] == $p['ID_estado']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['estado_descri']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay pedidos en este estado.</p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

</body>
</html>