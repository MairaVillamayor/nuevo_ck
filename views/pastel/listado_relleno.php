<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Archivo de confirmaciones -->
<script src="../../public/js/confirmaciones.js"></script>

<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once "../../includes/navegacion.php";

$pdo = getConexion();
$query = "SELECT r.id_relleno, r.relleno_nombre, 
                r.relleno_descripcion, r.relleno_precio, 
                e.estado_decoraciones_descri
            FROM relleno r 
            JOIN estado_decoraciones e 
            ON r.RELA_estado_decoraciones = e.ID_estado_decoraciones
            ORDER BY r.id_relleno ASC";
$stmt = $pdo->query($query);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

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
            <?php foreach ($resultado as $row) { ?>
                <tr>
                    <td><?php echo $row["id_relleno"]; ?></td>
                    <td><?php echo htmlspecialchars($row["relleno_nombre"]); ?></td>
                    <td><?php echo htmlspecialchars($row["relleno_descripcion"]); ?></td>
                    <td><?php echo htmlspecialchars($row["relleno_precio"]); ?></td>
                    <td><?php echo $row["estado_decoraciones_descri"]; ?></td>
                    <td>
                        <a class="btn-action btn-edit" href="form_modificar_relleno.php?id_relleno=<?php echo $row['id_relleno']; ?>"> ✏️ Editar </a> 
                        
                        <!-- Formulario para baja lógica -->
                        <form id="form-baja-<?= $row['id_relleno']; ?>" method="post" action="../../controllers/pastel/baja_logica_relleno.php" style="display:inline;">
                            <input type="hidden" name="id_relleno" value="<?= $row['id_relleno']; ?>">
                            <button type="button" class="btn-action btn-baja" onclick="confirmarBaja('<?= $row['id_relleno']; ?>', 'form-baja')">🚫 Dar de baja</button>
                        </form>


                        <!-- Formulario para baja física -->

                        <form id="form-eliminar-<?= $row['id_relleno']; ?>" method="post" action="../../controllers/pastel/baja_fisica_relleno.php" style="display:inline;">
                            <input type="hidden" name="id_relleno" value="<?= $row['id_relleno']; ?>">
                            <button type="button" class="btn-action btn-delete" onclick="confirmarEliminacion('<?= $row['id_relleno']; ?>', 'form-eliminar')">❌ Eliminar</button>
                        </form>

                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>