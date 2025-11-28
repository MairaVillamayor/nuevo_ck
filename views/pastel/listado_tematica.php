<?php
require_once("../../config/conexion.php");
include("../../includes/sidebar.php");
require_once "../../includes/navegacion.php";

$pdo = getConexion();

$por_pagina = 10;
$pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

$count_query = "SELECT COUNT(*) AS total FROM tematica";
$total_stmt = $pdo->query($count_query);
$total_registros = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_registros / $por_pagina);

$query = "SELECT t.id_tematica, 
                 t.tematica_descripcion, 
                 e.estado_decoraciones_descri 
          FROM tematica t
          JOIN estado_decoraciones e 
            ON t.RELA_estado_decoraciones = e.ID_estado_decoraciones
          ORDER BY t.id_tematica ASC
          LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Items: Temáticas</h1>
<p class="description">Listado de todas las temáticas registradas.</p>

<a href="form_alta_tematica.php" class="btn-add">➕ Agregar nueva temática</a>

<div class="admin-table">
    <h2>Listado de Temáticas</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultado as $row) { ?>
                <tr>
                    <td><?php echo $row["id_tematica"]; ?></td>
                    <td><?php echo htmlspecialchars($row["tematica_descripcion"]); ?></td>
                    <td><?php echo $row["estado_decoraciones_descri"]; ?></td>
                    <td>
                        <a class="btn-action btn-edit" href="form_modificar_tematica.php?id_tematica=<?php echo $row['id_tematica']; ?>"> ✏️ Editar </a>

                        <form id="form-baja-<?= $row['id_tematica']; ?>" method="post" action="../../controllers/pastel/baja_logica_tematica.php" style="display:inline;">
                            <input type="hidden" name="id_tematica" value="<?= $row['id_tematica']; ?>">
                            <button type="button" class="btn-action btn-baja" onclick="confirmarBaja('<?= $row['id_tematica']; ?>', 'form-baja')">🚫 Dar de baja</button>
                        </form>

                        <form id="form-eliminar-<?= $row['id_tematica']; ?>" method="post" action="../../controllers/pastel/baja_fisica_tematica.php" style="display:inline;">
                            <input type="hidden" name="id_tematica" value="<?= $row['id_tematica']; ?>">
                            <button type="button" class="btn-action btn-delete" onclick="confirmarEliminacion('<?= $row['id_tematica']; ?>', 'form-eliminar')">❌ Eliminar</button>
                        </form>


                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="pagination">
        <?php if ($pagina_actual > 1): ?>
            <a class="page-btn" href="?page=<?php echo $pagina_actual - 1; ?>">⬅</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <a class="page-btn <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>"
                href="?page=<?php echo $i; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($pagina_actual < $total_paginas): ?>
            <a class="page-btn" href="?page=<?php echo $pagina_actual + 1; ?>">➡</a>
        <?php endif; ?>
    </div>
    <style>
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .page-btn {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 4px;
            background-color: #f0f0f0;
            color: #333;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }

        .page-btn:hover {
            background-color: #dcdcdc;
        }

        .page-btn.active {
            background-color: #e83e8c;
            color: white;
        }
    </style>
</div>