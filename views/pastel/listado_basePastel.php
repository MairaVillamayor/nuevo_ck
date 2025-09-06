<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Archivo de confirmaciones -->
<script src="../../public/js/confirmaciones.js"></script>

<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";

$pdo = getConexion();
$query = "SELECT bp.id_base_pastel, bp.base_pastel_nombre, bp.base_pastel_descripcion, e.estado_decoraciones_descri 
        FROM base_pastel bp 
        JOIN estado_decoraciones e ON bp.RELA_estado_decoraciones = e.ID_estado_decoraciones";
$stmt = $pdo->query($query);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Items: Base del Pastel</h1>
<p class="description">Listado de todas las bases registradas.</p>

<a href="form_alta_basePastel.php" class="btn-add">➕ Agregar nueva base</a>

<div class="admin-table">
    <h2>Listado de Bases</h2>
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
                    <td><?php echo $row["id_base_pastel"]; ?></td>
                    <td><?php echo htmlspecialchars($row["base_pastel_nombre"]); ?></td>
                    <td><?php echo htmlspecialchars($row["base_pastel_descripcion"]); ?></td>
                    <td><?php echo $row["estado_decoraciones_descri"]; ?></td>
                    <td>
                        <a class="btn-action btn-edit" href="form_modificar_basePastel.php?id_base_pastel=<?php echo $row['id_base_pastel']; ?>">✏️ Editar</a>


                        
                        <!-- Formulario para baja lógica -->
                        <form id="form-baja-<?= $row['id_base_pastel']; ?>" method="post" action="../../controllers/pastel/baja_logica_basePastel.php" style="display:inline;">
                            <input type="hidden" name="id_base_pastel" value="<?= $row['id_base_pastel']; ?>">
                            <button type="button" class="btn-action btn-baja" onclick="confirmarBaja('<?= $row['id_base_pastel']; ?>', 'form-baja')">🚫 Dar de baja</button>
                        </form>


                        <!-- Formulario para baja física -->

                        <form id="form-eliminar-<?= $row['id_base_pastel']; ?>" method="post" action="../../controllers/pastel/baja_fisica_basePastel.php" style="display:inline;">
                            <input type="hidden" name="id_base_pastel" value="<?= $row['id_base_pastel']; ?>">
                            <button type="button" class="btn-action btn-delete" onclick="confirmarEliminacion('<?= $row['id_base_pastel']; ?>', 'form-eliminar')">❌ Eliminar</button>
                        </form>


                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
