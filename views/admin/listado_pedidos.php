<?php
require_once __DIR__ . '/../../config/conexion.php';
include ("../../includes/navegacion.php");
session_start();

// ✅ Solo administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil_id'] != 1) {
    header('Location: ../../views/usuario/login.php?error=not_logged');
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
    pe.pedido_direccion_envio,
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
ORDER BY pe.ID_pedido DESC; ";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar pedidos por ID de estado para evitar la clave indefinida
$pedidos_por_estado = [];
foreach ($pedidos as $p) {
    // Si la descripción del estado no existe, se usa 'Sin Estado' para la agrupación.
    $estado_descri = $p['estado_descri'] ?? 'Sin Estado';
    $pedidos_por_estado[$estado_descri][] = $p;
}

// Agregar un "estado" ficticio para los pedidos sin estado
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
body { font-family: Arial, sans-serif; background: #fff5f8; padding: 20px; }
h2 { text-align: center; color: #333; }
h3 { margin-top: 40px; color: #555; }
.container { display: flex; flex-wrap: wrap; gap: 20px; }
.card { background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); padding: 20px; flex: 1 1 300px; min-width: 250px; position: relative; }
.card h4 { margin-top: 0; color: #e91e63; }
.card p { margin: 5px 0; color: #333; }
.estado { padding: 5px 10px; border-radius: 5px; color: white; font-weight: bold; display: inline-block; }
/* Colores según estado */
.estado-pendiente { background: #f44336; } 
.estado-en-proceso { background: #ff9800; } 
.estado-finalizado { background: #4caf50; } 
.estado-sin-estado { background: #9e9e9e; } 
.card select { margin-top: 10px; padding: 5px; border-radius: 5px; border: 1px solid #ccc; width: 100%; }
</style>
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
            alert('Estado actualizado a "' + data.estado + '"');
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(err => alert('Error de conexión'));
}
</script>
</head>
<body>
<h2>📋 Listado de Pedidos por Estado</h2>
<!-- ✅ Botón de exportar a Excel -->
                    <p style="margin-top:10px; text-align:center;">
                        <a href="../../excel/excel_pedidos.php?id_usuario=<?= urlencode($p['ID_pedido']) ?>" 
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
                    <p><strong>Dirección:</strong> <?= htmlspecialchars($p['pedido_direccion_envio']) ?></p>
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