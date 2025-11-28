<?php
require_once("../../config/conexion.php");
include("../../includes/sidebar.php");
require_once "../../includes/navegacion.php";


$pdo = getConexion();
$query = "SELECT me.id_material_extra, me.material_extra_nombre, 
                me.material_extra_descri, me.material_extra_precio,
                e.estado_insumo_descripcion 
          FROM material_extra me 
          LEFT JOIN estado_insumos e ON me.rela_estado_insumos = e.id_estado_insumo
          ORDER BY me.id_material_extra ASC";
$stmt = $pdo->query($query);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Items: Material Extra</h1>
<p class="description">Listado de todos los materiales registrados.</p>

<a href="form_alta_materialExtra.php" class="btn-add">➕ Agregar nuevo material </a>

<div class="admin-table">
    <h2>Listado de Materiales</h2>
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
                    <td><?php echo $row["id_material_extra"]; ?></td>
                    <td><?php echo htmlspecialchars($row["material_extra_nombre"]); ?></td>
                    <td><?php echo htmlspecialchars($row["material_extra_descri"]); ?></td>
                    <td><?php echo htmlspecialchars($row["material_extra_precio"])?></td>
                    <td><?php echo $row["estado_insumo_descripcion"]; ?></td>
                    <td>
                        <a class="btn-action btn-edit" href="form_modificar_materialExtra.php?id_material_extra=<?php echo $row['id_material_extra']; ?>"> ✏️ Editar </a> 

                        <form action="../../controllers/insumos/baja_logica_materialExtra.php" method="post" style="display:inline;">
                            <input type="hidden" name="id_material_extra" value="<?php echo $row['id_material_extra']; ?>">
                            <button class="btn-action btn-baja" type="submit" onclick="return confirmarBaja('¿Dar de baja este material extra?');">🚫 Dar de baja</button>
                        </form>

                        <form action="../../controllers/insumos/baja_fisica_materialExtra.php" method="post" style="display:inline;">
                            <input type="hidden" name="id_material_extra" value="<?php echo $row['id_material_extra']; ?>">
                            <button class="btn-action btn-delete" type="submit" onclick="return confirmarEliminacion('¿Eliminar definitivamente este material extra?');">❌ Eliminar</button>
                        </form>

                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
