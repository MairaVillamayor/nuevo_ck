<?php
require_once '../../config/conexion.php';
$pdo = getConexion();

$stmt = $pdo->query("SELECT 
        a.*,
        u.usuario_nombre AS afectado,
        ua.usuario_nombre AS accion
    FROM auditoria_usuarios a
    LEFT JOIN usuarios u ON a.usuario_afectado = u.ID_usuario
    LEFT JOIN usuarios ua ON a.usuario_accion = ua.ID_usuario
    ORDER BY a.fecha DESC
");

$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Auditoría de Usuarios</h1>

<table>
<tr>
    <th>Fecha</th>
    <th>Acción</th>
    <th>Usuario afectado</th>
    <th>Usuario que realizó</th>
    <th>IP</th>
    <th>Descripción</th>
</tr>

<?php foreach ($registros as $r): ?>
<tr>
    <td><?= $r['fecha'] ?></td>
    <td><?= $r['accion'] ?></td>
    <td><?= $r['afectado'] ?></td>
    <td><?= $r['accion'] ?></td>
    <td><?= $r['ip'] ?></td>
    <td><?= $r['descripcion'] ?></td>
</tr>
<?php endforeach; ?>
</table>
