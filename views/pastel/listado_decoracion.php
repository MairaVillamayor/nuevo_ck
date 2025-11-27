
<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once "../../includes/navegacion.php";

$pdo = getConexion();
$por_pagina = 10; 
$pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

$total_query = "SELECT COUNT(*) AS total FROM decoracion";
$total_stmt = $pdo->query($total_query);
$total_registros = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];

$total_paginas = ceil($total_registros / $por_pagina);

$query = "SELECT d.id_decoracion,
                d.decoracion_nombre,
                d.decoracion_descripcion,
                d.decoracion_precio,
                e.estado_decoraciones_descri
          FROM decoracion d
          LEFT JOIN estado_decoraciones e 
                 ON d.RELA_estado_decoraciones = e.ID_estado_decoraciones
          ORDER BY d.id_decoracion ASC
          LIMIT :limite OFFSET :offset";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':limite', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../public/js/confirmaciones.js"></script>

<h1>Items: Decoración</h1>
<p class="description">Listado de todas las decoraciones registradas.</p>

<a href="form_alta_decoracion.php" class="btn-add">➕ Agregar nueva decoración</a>

<div class="admin-table">
    <h2>Listado de Decoraciones</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultado as $row): ?>
                <tr>
                    <td><?= $row["id_decoracion"]; ?></td>
                    <td><?= htmlspecialchars($row["decoracion_nombre"]); ?></td>
                    <td><?= htmlspecialchars($row["decoracion_descripcion"]); ?></td>
                    <td><?= htmlspecialchars($row["decoracion_precio"] ?? "—"); ?></td>
                    <td><?= $row["estado_decoraciones_descri"]; ?></td>
                    <td>
                        <a class="btn-action btn-edit" 
                           href="form_modificar_decoracion.php?id=<?= $row['id_decoracion']; ?>">
                           ✏️ Editar
                        </a>

                        <form id="form-baja-<?= $row['id_decoracion']; ?>" 
                              method="post" 
                              action="../../controllers/pastel/baja_logica_decoracion.php" 
                              style="display:inline;">
                            <input type="hidden" name="id_decoracion" value="<?= $row['id_decoracion']; ?>">
                            <button type="button" class="btn-action btn-baja" 
                                    onclick="confirmarBaja('<?= $row['id_decoracion']; ?>', 'form-baja')">🚫 Dar de baja</button>
                        </form>

                   
                        <form id="form-eliminar-<?= $row['id_decoracion']; ?>" 
                              method="post" 
                              action="../../controllers/pastel/baja_fisica_decoracion.php" 
                              style="display:inline;">
                            <input type="hidden" name="id_decoracion" value="<?= $row['id_decoracion']; ?>">
                            <button type="button" class="btn-action btn-delete" 
                                    onclick="confirmarEliminacion('<?= $row['id_decoracion']; ?>', 'form-eliminar')">❌ Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<div class="pagination">
    <?php if ($pagina_actual > 1): ?>
        <a href="?page=<?= $pagina_actual - 1 ?>" class="page-btn">⬅ Anterior</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
        <a href="?page=<?= $i ?>" 
           class="page-number <?= ($i == $pagina_actual) ? 'active' : '' ?>">
           <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($pagina_actual < $total_paginas): ?>
        <a href="?page=<?= $pagina_actual + 1 ?>" class="page-btn">Siguiente ➡</a>
    <?php endif; ?>
</div>

<style>
.pagination {
    margin-top: 20px;
    text-align: center;
}
.page-number, .page-btn {
    margin: 5px;
    padding: 8px 12px;
    background: #eee;
    border-radius: 5px;
    text-decoration: none;
    color: #333;
}
.page-number.active {
    background: #e83e8c;
    color: white;
    font-weight: bold;
}
.page-number:hover, .page-btn:hover {
    background: #ccc;
}
</style>
