<?php
require_once '../config/conexion.php';
$pdo = getConexion();

// 📌 Capturar búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$fecha_desde = $_GET['fecha_desde'] ?? '';
$fecha_hasta = $_GET['fecha_hasta'] ?? '';
$usuario_filtro = $_GET['usuario'] ?? '';

// 📌 Configuración de paginación
$limite = 10; // registros por página
$pagina = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($pagina - 1) * $limite;
$whereSQL = '';
$params = [];

$condiciones = [];

if ($busqueda !== '') {
    $condiciones[] = "(a.auditoria_accion LIKE :b1
                 OR a.auditoria_tabla_afectada LIKE :b2
                 OR a.auditoria_descripcion LIKE :b3
                 OR u.usuario_nombre LIKE :b4
                 OR a.registro_id LIKE :b5)";
    for ($i = 1; $i <= 5; $i++) {
        $params[":b$i"] = "%$busqueda%";
    }
}

if ($fecha_desde != '') {
    $condiciones[] = "DATE(a.auditoria_fecha) >= :fecha_desde";
    $params[':fecha_desde'] = $fecha_desde;
}

if ($fecha_hasta != '') {
    $condiciones[] = "DATE(a.auditoria_fecha) <= :fecha_hasta";
    $params[':fecha_hasta'] = $fecha_hasta;
}

if ($usuario_filtro != '') {
    $condiciones[] = "u.ID_usuario = :usuario";
    $params[':usuario'] = $usuario_filtro;
}

if (count($condiciones) > 0) {
    $whereSQL = " WHERE " . implode(" AND ", $condiciones);
}


// 📌 Contar total de registros
$totalStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM auditoria a
    LEFT JOIN usuarios u ON a.RELA_usuario = u.ID_usuario
    $whereSQL
");
$totalStmt->execute($params);
$totalRegistros = $totalStmt->fetchColumn();
$totalPaginas = ceil($totalRegistros / $limite);

$usuariosStmt = $pdo->query("SELECT ID_usuario, usuario_nombre FROM usuarios ORDER BY usuario_nombre");
$usuarios = $usuariosStmt->fetchAll(PDO::FETCH_ASSOC);

// 📌 Traer datos con límite y offset
$query = "SELECT 
        a.auditoria_fecha,
        a.auditoria_accion,
        a.auditoria_tabla_afectada,
        a.registro_id,
        a.auditoria_descripcion,
        a.auditoria_ip,
        u.usuario_nombre AS usuario
    FROM auditoria a
    LEFT JOIN usuarios u ON a.RELA_usuario = u.ID_usuario
    $whereSQL
    ORDER BY a.auditoria_fecha DESC
    LIMIT :offset, :limite
";

$stmt = $pdo->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val, PDO::PARAM_STR);
}
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
$stmt->execute();
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="../public/css/auditoria.css">
<?php include("../includes/navegacion.php"); ?>
<h1 class="cp-title">Auditoría de Usuarios</h1>


<div class="cp-toolbar">
    <!-- Formulario de búsqueda -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <div class="cp-toolbar">
        <form method="get" class="cp-filtros">

            <div class="cp-campo">
                <label>Buscar</label>
                <input class="cp-input" type="text" name="busqueda"
                    value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar...">
            </div>

            <div class="cp-campo">
                <label>Desde</label>
                <input class="cp-input" type="date" name="fecha_desde"
                    value="<?= htmlspecialchars($fecha_desde) ?>">
            </div>

            <div class="cp-campo">
                <label>Hasta</label>
                <input class="cp-input" type="date" name="fecha_hasta"
                    value="<?= htmlspecialchars($fecha_hasta) ?>">
            </div>

            <div class="cp-campo">
                <label>Usuario</label>
                <select class="cp-input" name="usuario">
                    <option value="">Todos</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['ID_usuario'] ?>"
                            <?= ($usuario_filtro == $u['ID_usuario']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['usuario_nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="cp-btn"><i class="fa-solid fa-filter"></i> Filtrar</button>

        </form>
    </div>

</div>

<table class="cp-table">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Acción</th>
            <th>Tabla afectada</th>
            <th>ID Registro</th>
            <th>Usuario que realizó</th>
            <th>IP</th>
            <th>Descripción</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($registros) > 0): ?>
            <?php foreach ($registros as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['auditoria_fecha']) ?></td>
                    <td>
                        <?php
                        $accion = strtolower($r['auditoria_accion']);
                        if (str_contains($accion, 'insert') || str_contains($accion, 'alta')) {
                            echo "<i class='fa-solid fa-circle-plus cp-i cp-create'></i>";
                        } elseif (str_contains($accion, 'update') || str_contains($accion, 'edit')) {
                            echo "<i class='fa-solid fa-pen cp-i cp-update'></i>";
                        } elseif (str_contains($accion, 'delete') || str_contains($accion, 'baja')) {
                            echo "<i class='fa-solid fa-trash cp-i cp-delete'></i>";
                        } elseif (str_contains($accion, 'login')) {
                            echo "<i class='fa-solid fa-right-to-bracket cp-i cp-login'></i>";
                        } elseif (str_contains($accion, 'logout')) {
                            echo "<i class='fa-solid fa-right-from-bracket cp-i cp-logout'></i>";
                        } else {
                            echo "<i class='fa-solid fa-eye cp-i'></i>";
                        }
                        ?>
                        <?= htmlspecialchars($r['auditoria_accion']) ?>
                    </td>

                    <td><?= htmlspecialchars($r['auditoria_tabla_afectada']) ?></td>
                    <td><?= !empty($r['registro_id']) ? htmlspecialchars($r['registro_id']) : '-' ?></td>

                    <td><?= htmlspecialchars($r['usuario'] ?? 'Sistema') ?></td>
                    <td><?= htmlspecialchars($r['auditoria_ip']) ?></td>
                    <td><?= htmlspecialchars($r['auditoria_descripcion']) ?></td>

                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="cp-empty">⚠️ No hay registros de auditoría.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- 🔹 Paginación -->
<?php if ($totalPaginas > 1): ?>
    <div class="cp-pagination">
        <?php if ($pagina > 1): ?>
            <a href="?page=<?= $pagina - 1 ?>&busqueda=<?= urlencode($busqueda) ?>">&laquo; Anterior</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <?php if ($i == $pagina): ?>
                <strong><?= $i ?></strong>
            <?php else: ?>
                <a href="?page=<?= $i ?>&busqueda=<?= urlencode($busqueda) ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($pagina < $totalPaginas): ?>
            <a href="?page=<?= $pagina + 1 ?>&busqueda=<?= urlencode($busqueda) ?>">Siguiente &raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>