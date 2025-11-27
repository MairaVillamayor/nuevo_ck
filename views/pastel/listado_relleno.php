<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once "../../includes/navegacion.php";

$pdo = getConexion();

$por_pagina = 10; 
$pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

$total_query = "SELECT COUNT(*) AS total FROM relleno";
$total_stmt = $pdo->query($total_query);
$total_registros = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];

$total_paginas = ceil($total_registros / $por_pagina);

$query = "SELECT r.id_relleno, 
                r.relleno_nombre, 
                r.relleno_descripcion, 
                r.relleno_precio, 
                e.estado_decoraciones_descri
          FROM relleno r
          JOIN estado_decoraciones e 
               ON r.RELA_estado_decoraciones = e.ID_estado_decoraciones
          ORDER BY r.id_relleno ASC
          LIMIT :limite OFFSET :offset";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':limite', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../public/js/confirmaciones.js"></script>
<h1>Items: Rellenos</h1>
<p class="description">Listado de todos los rellenos registrados.</p>

<a href="form_alta_relleno.php" class="btn-add">➕ Agregar nuevo relleno</a>

<div class="admin-table">
    <h2>Listado de Rellenos</h2>
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
                    <td><?= $row["id_relleno"]; ?></td>
                    <td><?= htmlspecialchars($row["relleno_nombre"]); ?></td>
                    <td><?= htmlspecialchars($row["relleno_descripcion"]); ?></td>
                    <td><?= htmlspecialchars($row["relleno_precio"]); ?></td>
                    <td><?= $row["estado_decoraciones_descri"]; ?></td>
                    <td>
                        <a class="btn-action btn-edit" 
                           href="form_modificar_relleno.php?id_relleno=<?= $row['id_relleno']; ?>">
                           ✏️ Editar
                        </a>

                        <form id="form-baja-<?= $row['id_relleno']; ?>" 
                              method="post" 
                              action="../../controllers/pastel/baja_logica_relleno.php" 
                              style="display:inline;">
                            <input type="hidden" name="id_relleno" value="<?= $row['id_relleno']; ?>">
                            <button type="button" class="btn-action btn-baja" 
                                    onclick="confirmarBaja('<?= $row['id_relleno']; ?>', 'form-baja')">
                                🚫 Dar de baja
                            </button>
                        </form>

                        <form id="form-eliminar-<?= $row['id_relleno']; ?>" 
                              method="post" 
                              action="../../controllers/pastel/baja_fisica_relleno.php" 
                              style="display:inline;">
                            <input type="hidden" name="id_relleno" value="<?= $row['id_relleno']; ?>">
                            <button type="button" class="btn-action btn-delete" 
                                    onclick="confirmarEliminacion('<?= $row['id_relleno']; ?>', 'form-eliminar')">
                                ❌ Eliminar
                            </button>
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
