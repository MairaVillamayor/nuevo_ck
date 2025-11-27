<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once "../../includes/navegacion.php";

$pdo = getConexion();
$query = "SELECT * FROM perfiles";
$stmt = $pdo->query($query);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../public/js/confirmaciones.js"></script>

<h1> Perfiles </h1>
<p class="description">Listado de todos los perfiles registrados en la app.</p>

<a href="form_alta_perfiles.php" class="btn-add">➕ Agregar nuevo perfil</a>

<div class="admin-table">
    <h2>Listado de Perfiles</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultado as $row) { ?>
                <tr>
                    <td><?php echo $row["ID_perfil"]; ?></td>
                    <td><?php echo htmlspecialchars($row["perfil_rol"]); ?></td>
                    <td>
                        <a class="btn-action btn-edit" href="form_modificarPerfil.php?ID_perfil=<?php echo $row['ID_perfil']; ?>"> ✏️ Editar </a> 

                        
                        <form id="form-eliminar-<?= $row['ID_perfil']; ?>" method="post" action="../../controllers/admin/baja_perfiles.php" style="display:inline;">
                            <input type="hidden" name="ID_perfil" value="<?= $row['ID_perfil']; ?>">
                            <button type="button" class="btn-action btn-delete" onclick="confirmarEliminacion('<?= $row['ID_perfil']; ?>', 'form-eliminar')">❌ Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>