<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";

$pdo = getConexion();

$por_pagina = 10;
$pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pagina_actual - 1) * $por_pagina;

$count_query = "SELECT COUNT(*) AS total FROM sabor";
$total_stmt = $pdo->query($count_query);
$total_registros = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_registros / $por_pagina);

$query = "SELECT s.id_sabor, s.sabor_nombre, 
                s.sabor_descripcion, s.sabor_precio,
                e.estado_decoraciones_descri 
          FROM sabor s
          JOIN estado_decoraciones e 
            ON s.RELA_estado_decoraciones = e.ID_estado_decoraciones
          ORDER BY s.id_sabor ASC
          LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Items: Sabor</h1>
<p class="description">Listado de todos los sabores registrados.</p>

<a href="form_alta_sabor.php" class="btn-add">➕ Agregar nuevo sabor</a>

<div class="admin-table">
    <h2>Listado de Sabores</h2>
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
            <?php foreach ($resultado as $row) { ?>
                <tr>
                    <td><?php echo $row["id_sabor"]; ?></td>
                    <td><?php echo htmlspecialchars($row["sabor_nombre"]); ?></td>
                    <td><?php echo htmlspecialchars($row["sabor_descripcion"]); ?></td>
                    <td><?php echo htmlspecialchars($row["sabor_precio"]); ?></td>
                    <td><?php echo $row["estado_decoraciones_descri"]; ?></td>
                    <td>
                        <a class="btn-action btn-edit" href="form_modificar_sabor.php?id_sabor=<?php echo $row['id_sabor']; ?>"> ✏️ Editar </a>

                        <form id="form-baja-<?= $row['id_sabor']; ?>" method="post" action="../../controllers/pastel/baja_logica_sabor.php" style="display:inline;">
                            <input type="hidden" name="id_sabor" value="<?= $row['id_sabor']; ?>">
                            <button type="button" class="btn-action btn-baja" onclick="confirmarBaja('<?= $row['id_sabor']; ?>', 'form-baja')">🚫 Dar de baja</button>
                        </form>
                        <form id="form-eliminar-<?= $row['id_sabor']; ?>" method="post" action="../../controllers/pastel/baja_fisica_sabor.php" style="display:inline;">
                            <input type="hidden" name="id_sabor" value="<?= $row['id_sabor']; ?>">
                            <button type="button" class="btn-action btn-delete" onclick="confirmarEliminacion('<?= $row['id_sabor']; ?>', 'form-eliminar')">❌ Eliminar</button>
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