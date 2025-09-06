<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";

$pdo = getConexion();
$query = "SELECT * FROM modulos";
$stmt = $pdo->query($query);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Módulos</h1>
<p class="description">Listado de todos los módulos dentro de la app.</p>

<a href="form_alta_modulos.php" class="btn-add">➕ Agregar nuevo módulo</a>

<div class="admin-table">
    <h2>Listado de Módulos</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del módulo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultado as $row) { ?>
                <tr>
                    <td><?php echo $row["ID_modulos"]; ?></td>
                    <td><?php echo htmlspecialchars($row["modulos_nombre"]); ?></td>
                    <td>
                        <a class="btn-action btn-edit" href="form_modificarModulo.php?ID_modulos=<?php echo $row['ID_modulos']; ?>"> ✏️ Editar </a> 
                        <form action="../../controllers/modulos/baja_modulos.php" method="post" style="display:inline;">
                            <input type="hidden" name="ID_modulos" value="<?php echo $row['ID_modulos']; ?>">
                            <button class="btn-action btn-delete" type="submit" onclick="return confirm('¿Eliminar definitivamente este módulo?');">❌ Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>