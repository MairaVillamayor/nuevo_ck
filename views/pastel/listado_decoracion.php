<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Archivo de confirmaciones -->
<script src="../../public/js/confirmaciones.js"></script>

<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";

$pdo = getConexion();
$query = "SELECT d.id_decoracion, d.decoracion_nombre, d.decoracion_descripcion, e.estado_decoraciones_descri 
        FROM decoracion d 
        JOIN estado_decoraciones e ON d.RELA_estado_decoraciones = e.ID_estado_decoraciones";
$stmt = $pdo->query($query);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

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
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultado as $row) { ?>
                <tr>
                    <td><?php echo $row["id_decoracion"]; ?></td>
                    <td><?php echo htmlspecialchars($row["decoracion_nombre"]); ?></td>
                    <td><?php echo htmlspecialchars($row["decoracion_descripcion"]); ?></td>
                    <td><?php echo $row["estado_decoraciones_descri"]; ?></td>
                    <td>
                        <a class="btn-action btn-edit" href="form_modificar_decoracion.php?id=<?php echo $row['id_decoracion']; ?>">✏️ Editar</a>
                        
                        
                        <!-- Formulario para baja lógica -->
                        <form id="form-baja-<?= $row['id_decoracion']; ?>" method="post" action="../../controllers/pastel/baja_logica_decoracion.php" style="display:inline;">
                            <input type="hidden" name="id_decoracion" value="<?= $row['id_decoracion']; ?>">
                            <button type="button" class="btn-action btn-baja" onclick="confirmarBaja('<?= $row['id_decoracion']; ?>', 'form-baja')">🚫 Dar de baja</button>
                        </form>


                        <!-- Formulario para baja física -->

                        <form id="form-eliminar-<?= $row['id_decoracion']; ?>" method="post" action="../../controllers/pastel/baja_fisica_decoracion.php" style="display:inline;">
                            <input type="hidden" name="id_decoracion" value="<?= $row['id_decoracion']; ?>">
                            <button type="button" class="btn-action btn-delete" onclick="confirmarEliminacion('<?= $row['id_decoracion']; ?>', 'form-eliminar')">❌ Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>