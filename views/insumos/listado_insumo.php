<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Archivo de confirmaciones -->
<script src="../../public/js/confirmaciones.js"></script>


<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once "../../includes/navegacion.php";

$pdo = getConexion();
$query = "SELECT i.ID_insumo, i.insumo_nombre, i.insumo_unidad_medida, e.estado_insumo_descripcion 
        FROM insumos i 
        LEFT JOIN estado_insumos e ON i.RELA_estado_insumo = e.ID_estado_insumo";
$stmt = $pdo->query($query);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Items: Insumos</h1>
<p class="description">Listado de todos los insumos registrados.</p>

<a href="form_alta_insumo.php" class="btn-add">➕ Agregar nuevo insumo</a>

<div class="admin-table">
    <h2>Listado de Insumos</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Unidad</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultado as $row) { ?>
                <tr>
                    <td><?php echo $row["ID_insumo"]; ?></td>
                    <td><?php echo htmlspecialchars($row["insumo_nombre"]); ?></td>
                    <td><?php echo htmlspecialchars($row["insumo_unidad_medida"]); ?></td>
                    <td><?php echo $row["estado_insumo_descripcion"]; ?></td>
                    <td>
                        <a class="btn-action btn-edit" href="form_modificar_insumo.php?ID_insumo=<?php echo $row['ID_insumo']; ?>">✏️ Editar</a>

                        <!-- Formulario para baja lógica -->
                        <form id="form-baja-<?= $row['ID_insumo']; ?>" method="post" action="../../controllers/insumos/baja_logica_insumo.php" style="display:inline;">
                            <input type="hidden" name="ID_insumo" value="<?= $row['ID_insumo']; ?>">
                            <button type="button" class="btn-action btn-baja" onclick="confirmarBaja('<?= $row['ID_insumo']; ?>', 'form-baja')">🚫 Dar de baja</button>
                        </form>


                        <!-- Formulario para baja física -->

                        <form id="form-eliminar-<?= $row['ID_insumo']; ?>" method="post" action="../../controllers/insumos/baja_fisica_insumo.php" style="display:inline;">
                            <input type="hidden" name="ID_insumo" value="<?= $row['ID_insumo']; ?>">
                            <button type="button" class="btn-action btn-delete" onclick="confirmarEliminacion('<?= $row['ID_insumo']; ?>', 'form-eliminar')">❌ Eliminar</button>
                        </form>


                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>