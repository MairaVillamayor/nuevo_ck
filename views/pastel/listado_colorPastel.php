<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Archivo de confirmaciones -->
<script src="../../public/js/confirmaciones.js"></script>

<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";

$pdo = getConexion();
$query = "SELECT cp.id_color_pastel, cp.color_pastel_nombre, cp.color_pastel_codigo, e.estado_decoraciones_descri 
        FROM color_pastel cp 
        JOIN estado_decoraciones e ON cp.RELA_estado_decoraciones = e.ID_estado_decoraciones";
$stmt = $pdo->query($query);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Items: Color de Pastel</h1>
<p class="description">Listado de todos los colores registrados.</p>

<a href="form_alta_colorPastel.php" class="btn-add">➕ Agregar nuevo color</a>

<div class="admin-table">
    <h2>Listado de Colores</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Código</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultado as $row) { ?>
                <tr>
                    <td><?php echo $row["id_color_pastel"]; ?></td>
                    <td><?php echo htmlspecialchars($row["color_pastel_nombre"]); ?></td>
                    <td><?php echo htmlspecialchars($row["color_pastel_codigo"]); ?></td>
                    <td><?php echo $row["estado_decoraciones_descri"]; ?></td>
                    <td>
                        <a class="btn-action btn-edit" href="form_modificar_colorPastel.php?id_color_pastel=<?php echo $row['id_color_pastel']; ?>"> ✏️ Editar </a> 

                        <!-- Formulario para baja lógica -->
                        <form id="form-baja-<?= $row['id_color_pastel']; ?>" method="post" action="../../controllers/pastel/baja_logica_colorPastel.php" style="display:inline;">
                            <input type="hidden" name="id_color_pastel" value="<?= $row['id_color_pastel']; ?>">
                            <button type="button" class="btn-action btn-baja" onclick="confirmarBaja('<?= $row['id_color_pastel']; ?>', 'form-baja')">🚫 Dar de baja</button>
                        </form>


                        <!-- Formulario para baja física -->

                        <form id="form-eliminar-<?= $row['id_color_pastel']; ?>" method="post" action="../../controllers/pastel/baja_fisica_colorPastel.php" style="display:inline;">
                            <input type="hidden" name="id_color_pastel" value="<?= $row['id_color_pastel']; ?>">
                            <button type="button" class="btn-action btn-delete" onclick="confirmarEliminacion('<?= $row['id_color_pastel']; ?>', 'form-eliminar')">❌ Eliminar</button>
                        </form>

                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
