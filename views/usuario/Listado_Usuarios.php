<!DOCTYPE html>
<html>
<style>
    .admin-table {
        overflow-x: auto;
        max-width: 1200px;
        margin: 40px auto;
        padding: 10px;
        background-color: #fff;
        border-radius: 6px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    }

    .admin-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table th,
    .admin-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .admin-table th {
        background-color: #f4f4f4;
        font-weight: bold;
    }
</style>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Archivo de confirmaciones -->
<script src="../../public/js/confirmaciones.js"></script>

</html>

<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once dirname(__DIR__, 2) . '/includes/navegacion.php';

$pdo = getConexion();

// 📌 Traer datos combinados
$query = "SELECT u.ID_usuario, 
                 u.usuario_nombre, 
                 u.usuario_correo_electronico, 
                 u.usuario_numero_de_celular,
                 p.perfil_rol,
                 per.persona_nombre,
                 per.persona_apellido,
                 per.persona_fecha_nacimiento,
                 per.persona_direccion
          FROM usuarios u
          INNER JOIN perfiles p ON u.RELA_perfil = p.ID_perfil
          INNER JOIN persona per ON u.RELA_persona = per.ID_persona
          ORDER BY u.ID_usuario ASC";

$stmt = $pdo->query($query);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Usuarios</h1>
<p class="description">Listado de todos los usuarios registrados en la aplicación.</p>

<a href="../usuario/form_AltaUsuarios.php" class="btn-add">➕ Agregar nuevo usuario</a>

<td><a href="../../excel/excel_usuarios.php?id_usuario=' . urlencode($reg['id_usuario']) . '" class="btn-add">☷ Exportar a Excel</a></td>

<div class="admin-table">
    <h2>Listado de Usuarios</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Celular</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Fecha Nac.</th>
                <th>Dirección</th>
                <th>Perfil</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($resultado) > 0): ?>
                <?php foreach ($resultado as $row): ?>
                    <tr>
                        <td><?php echo $row["ID_usuario"]; ?></td>
                        <td><?php echo htmlspecialchars($row["usuario_nombre"]); ?></td>
                        <td><?php echo htmlspecialchars($row["usuario_correo_electronico"]); ?></td>
                        <td><?php echo htmlspecialchars($row["usuario_numero_de_celular"]); ?></td>
                        <td><?php echo htmlspecialchars($row["persona_nombre"]); ?></td>
                        <td><?php echo htmlspecialchars($row["persona_apellido"]); ?></td>
                        <td><?php echo htmlspecialchars($row["persona_fecha_nacimiento"]); ?></td>
                        <td><?php echo htmlspecialchars($row["persona_direccion"]); ?></td>
                        <td><?php echo htmlspecialchars($row["perfil_rol"]); ?></td>
                        <td>
                            <!-- Editar -->
                            <a class="btn-action btn-edit"
                                href="form_UpdateUsuarios.php?ID_usuario=<?php echo $row['ID_usuario']; ?>">✏️ Editar</a>

                            <form id="form-eliminar-<?php echo $row['ID_usuario']; ?>"
                                action="../../controllers/usuario/Baja_Usuarios.php"
                                method="post" style="display:inline;">

                                <input type="hidden" name="ID_usuario" value="<?php echo $row['ID_usuario']; ?>">

                                <button type="button"
                                    class="btn-action btn-delete"
                                    onclick="confirmarEliminacion('<?php echo $row['ID_usuario']; ?>', 'form-eliminar')">
                                    ❌ Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10">⚠️ No hay usuarios cargados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>