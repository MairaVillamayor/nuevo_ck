<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once "../../includes/navegacion.php";

$pdo = getConexion();
$query = "SELECT * FROM tamaño";
$stmt = $pdo->query($query);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../public/js/confirmaciones.js"></script>

<h1>Items: Tamaño</h1>
<p class="description">Listado de todos los tamaños (medidas) registrados.</p>

<a href="form_alta_tamaño.php" class="btn-add">➕ Agregar nuevo tamaño</a>

<div class="admin-table">
    <h2>Listado de tamaños</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Medidas</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultado as $row) { ?>
                <tr>
                    <td><?php echo $row["ID_tamaño"]; ?></td>
                    <td><?php echo htmlspecialchars($row["tamaño_nombre"]); ?></td>
                    <td><?php echo htmlspecialchars($row["tamaño_medidas"]); ?></td>
                    <td><?php echo htmlspecialchars($row["tamaño_precio"]); ?></td>
                    <td>
                        <a class="btn-action btn-edit" href="form_modificar_tamaño.php?ID_tamaño=<?= $row['ID_tamaño']; ?>">✏️ Editar</a>

                        <form id="form-eliminar-<?= $row['ID_tamaño']; ?>" method="post" action="../../controllers/pastel/baja_tamaño.php" style="display:inline;">
                            <input type="hidden" name="ID_tamaño" value="<?= $row['ID_tamaño']; ?>">
                            <button type="button" class="btn-action btn-delete" onclick="confirmarEliminacion('<?= $row['ID_tamaño']; ?>', 'form-eliminar')">❌ Eliminar</button>
                        </form>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>