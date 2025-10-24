<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Archivo de confirmaciones -->
<script src="../../public/js/confirmaciones.js"></script>

<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once "../../includes/navegacion.php";

$pdo = getConexion();
$query = "SELECT t.id_tematica, t.tematica_descripcion, e.estado_decoraciones_descri 
        FROM tematica t 
        JOIN estado_decoraciones e ON t.RELA_estado = e.ID_estado";
$stmt = $pdo->query($query);
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


                         <!-- Formulario para baja lógica -->
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
</div>