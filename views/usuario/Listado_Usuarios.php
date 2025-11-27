<?php
require_once("../../config/conexion.php");
include("../../includes/header.php");
require_once dirname(__DIR__, 2) . '/includes/navegacion.php';

$pdo = getConexion();

// 📌 Capturar búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// 📌 Configuración de paginación
$limite = 8; // usuarios por página
$pagina = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($pagina - 1) * $limite;
$whereSQL = '';
$params = [];
if ($busqueda !== '') {
    $whereSQL = "WHERE u.usuario_nombre LIKE :b1
                 OR u.usuario_correo_electronico LIKE :b2
                 OR u.usuario_numero_de_celular LIKE :b3
                 OR p.perfil_rol LIKE :b4
                 OR per.persona_nombre LIKE :b5
                 OR per.persona_apellido LIKE :b6
                 OR per.persona_fecha_nacimiento LIKE :b7
                 OR per.persona_direccion LIKE :b8";

    // asignamos cada parámetro
    for ($i = 1; $i <= 8; $i++) {
        $params[":b$i"] = "%$busqueda%";
    }
}

// 📌 Contar total de usuarios filtrados
$totalStmt = $pdo->prepare("SELECT COUNT(*) 
                            FROM usuarios u
                            INNER JOIN perfiles p ON u.RELA_perfil = p.ID_perfil
                            INNER JOIN persona per ON u.RELA_persona = per.ID_persona
                            $whereSQL");
$totalStmt->execute($params);
$totalUsuarios = $totalStmt->fetchColumn();
$totalPaginas = ceil($totalUsuarios / $limite);

// 📌 Traer datos combinados con LIMIT y OFFSET
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
          $whereSQL
          ORDER BY u.ID_usuario ASC
          LIMIT :offset, :limite";

$stmt = $pdo->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val, PDO::PARAM_STR);
}
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
$stmt->execute();
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Usuarios</h1>
<p class="description">Listado de todos los usuarios registrados en la aplicación.</p>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <a href="../usuario/form_AltaUsuarios.php" class="btn-add">➕ Agregar nuevo usuario</a>

    <div style="display: flex; gap: 10px; align-items: center;">
        <!-- Formulario de búsqueda -->
        <form method="get" style="display: flex; gap: 5px;">
            <input type="text" name="busqueda" placeholder="Buscar en todos los campos..." value="<?= htmlspecialchars($busqueda) ?>" 
                   style="padding: 5px 10px; border-radius: 5px; border: 1px solid #ccc;">
            <button type="submit" style="padding: 5px 10px; border-radius: 5px; border: none; background-color: #d63384; color: white; cursor: pointer;">
                Buscar
            </button>
        </form>

        <!-- Botón Exportar a Excel -->
        <a href="../../excel/excel_usuarios.php" class="btn-add" style="padding: 5px 10px; border-radius: 5px; background-color: #ff6b81; color: white; text-decoration: none;">
            ☷ Exportar a Excel
        </a>
    </div>
</div>


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
                        <td><?= $row["ID_usuario"] ?></td>
                        <td><?= htmlspecialchars($row["usuario_nombre"]) ?></td>
                        <td><?= htmlspecialchars($row["usuario_correo_electronico"]) ?></td>
                        <td><?= htmlspecialchars($row["usuario_numero_de_celular"]) ?></td>
                        <td><?= htmlspecialchars($row["persona_nombre"]) ?></td>
                        <td><?= htmlspecialchars($row["persona_apellido"]) ?></td>
                        <td><?= htmlspecialchars($row["persona_fecha_nacimiento"]) ?></td>
                        <td><?= htmlspecialchars($row["persona_direccion"]) ?></td>
                        <td><?= htmlspecialchars($row["perfil_rol"]) ?></td>
                        <td>
                            <a class="btn-action btn-edit"
                               href="form_UpdateUsuarios.php?ID_usuario=<?= $row['ID_usuario'] ?>">✏️ Editar</a>

                            <form id="form-eliminar-<?= $row['ID_usuario'] ?>"
                                  action="../../controllers/usuario/Baja_Usuarios.php"
                                  method="post" style="display:inline;">
                                <input type="hidden" name="ID_usuario" value="<?= $row['ID_usuario'] ?>">
                                <button type="button" class="btn-action btn-delete"
                                        onclick="confirmarEliminacion('<?= $row['ID_usuario'] ?>', 'form-eliminar')">
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

    <!-- 🔹 Paginación -->
    <?php if ($totalPaginas > 1): ?>
        <div class="pagination" style="text-align:center; margin-top:20px;">
            <?php if ($pagina > 1): ?>
                <a href="?page=<?= $pagina - 1 ?>&busqueda=<?= urlencode($busqueda) ?>" class="btn btn-light">⬅️ Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <?php if ($i == $pagina): ?>
                    <span class="btn btn-primary"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?>&busqueda=<?= urlencode($busqueda) ?>" class="btn btn-light"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pagina < $totalPaginas): ?>
                <a href="?page=<?= $pagina + 1 ?>&busqueda=<?= urlencode($busqueda) ?>" class="btn btn-light">Siguiente ➡️</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
