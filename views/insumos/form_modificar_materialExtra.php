<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cake Party - Editar Material</title>
    <link rel="stylesheet" href="../../public/css/admin_style.css" />
</head>

<body>
<?php include("../../includes/header.php"); 
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";
?>
    <div class="admin-form">
        <h1>Editar Materiales</h1>
        <hr>

        <?php
        require_once("../../config/conexion.php");
        if (!isset($_GET["id_material_extra"])) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20el%20ID%20del%20material.");
            exit();
        }
        $id_material_extra = intval($_GET["id_material_extra"]);
        $pdo = getConexion();
        $stmt = $pdo->prepare("SELECT * FROM material_extra WHERE id_material_extra = :id");
        $stmt->execute(['id' => $id_material_extra]);
        $material_extra = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$material_extra) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Material%20no%20encontrado.");
            exit();
        }
        $material_extra_nombre = $material_extra["material_extra_nombre"];
        $material_extra_descripcion = $material_extra["material_extra_descripcion"];
        $estado_actual = $material_extra["RELA_estado"];
        $estados = $pdo->query("SELECT * FROM estado")->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <form action="../../controllers/insumos/modificar_materialExtra.php" method="post">
            <label for="material_extra_nombre">Nombre: </label>
            <input type="text" name="material_extra_nombre" id="material_extra_nombre"
                value="<?php echo htmlspecialchars($material_extra_nombre); ?>" required>
            <br><br>

            <label for="material_extra_descripcion">Descripción: </label>
            <input type="text" name="material_extra_descripcion" id="material_extra_descripcion"
                value="<?php echo htmlspecialchars($material_extra_descripcion); ?>" required>
            <br><br>

            <label for="rela_estado">Estado:</label>
            <select name="rela_estado" id="rela_estado" required>
                <?php foreach ($estados as $estado) {
                    $id_estado = isset($estado['id_estado']) ? $estado['id_estado'] : (isset($estado['ID_estado']) ? $estado['ID_estado'] : '');
                    $nombre_estado = isset($estado['estado_descripcion']) ? $estado['estado_descripcion'] : 'Sin descripción';
                ?>
                    <option value="<?php echo htmlspecialchars($id_estado); ?>"
                        <?php if ($id_estado == $estado_actual) echo "selected"; ?>>
                        <?php echo htmlspecialchars($nombre_estado); ?>
                    </option>
                <?php } ?>
            </select>

            <input type="hidden" name="id_material_extra" value="<?php echo $id_material_extra; ?>">
            <br><br>

            <button type="submit">Guardar material</button>
        </form>
    </div>
</body>

</html>