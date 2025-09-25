<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Archivo de confirmaciones -->
<script src="../../public/js/confirmaciones.js"></script>

<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";

$pdo = getConexion();
$query = "SELECT s.id_sabor, s.sabor_nombre, 
                s.sabor_descripcion, s.sabor_precio,
                e.estado_decoraciones_descri 
            FROM sabor s
            JOIN estado_decoraciones e 
            ON s.RELA_estado_decoraciones = e.ID_estado_decoraciones";
$stmt = $pdo->query($query);
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
                        
                        <!-- Formulario para baja lógica -->
                        <form id="form-baja-<?= $row['id_sabor']; ?>" method="post" action="../../controllers/pastel/baja_logica_sabor.php" style="display:inline;">
                            <input type="hidden" name="id_sabor" value="<?= $row['id_sabor']; ?>">
                            <button type="button" class="btn-action btn-baja" onclick="confirmarBaja('<?= $row['id_sabor']; ?>', 'form-baja')">🚫 Dar de baja</button>
                        </form>


                        <!-- Formulario para baja física -->

                        <form id="form-eliminar-<?= $row['id_sabor']; ?>" method="post" action="../../controllers/pastel/baja_fisica_sabor.php" style="display:inline;">
                            <input type="hidden" name="id_sabor" value="<?= $row['id_sabor']; ?>">
                            <button type="button" class="btn-action btn-delete" onclick="confirmarEliminacion('<?= $row['id_sabor']; ?>', 'form-eliminar')">❌ Eliminar</button>
                        </form>

                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>