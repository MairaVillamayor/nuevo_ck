<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cake Party - Editar Decoración</title>
    <link rel="stylesheet" href="../../public/css/admin_style.css" />
</head>

<body>
<?php include("../../includes/header.php"); 
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";
?>

<div class="admin-form">
        <h1>Editar Decoración</h1>
        <hr>

        <?php
        require_once("../../config/conexion.php");
        if (!isset($_GET["id"])) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20el%20ID%20de%20la%20decoraci%C3%B3n.");
            exit();
        }
        $id_decoracion = intval($_GET["id"]);
        $pdo = getConexion();
        $stmt = $pdo->prepare("SELECT * FROM decoracion WHERE id_decoracion = :id");
        $stmt->execute(['id' => $id_decoracion]);
        $decoracion = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$decoracion) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Decoraci%C3%B3n%20no%20encontrada.");
            exit();
        }
        $decoracion_nombre = $decoracion["decoracion_nombre"];
        $decoracion_descripcion = $decoracion["decoracion_descripcion"];
        $estado_actual = $decoracion["RELA_estado_decoraciones"];
        $estados = $pdo->query("SELECT * FROM estado_decoraciones")->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <form action="../../controllers/pastel/modificar_decoracion.php" method="post">
            <label for="decoracion_nombre">Nombre: </label>
            <input type="text" name="decoracion_nombre" id="decoracion_nombre"
                value="<?php echo htmlspecialchars($decoracion_nombre); ?>" required>
            <br><br>

            <label for="decoracion_descripcion">Descripción: </label>
            <input type="text" name="decoracion_descripcion" id="decoracion_descripcion"
                value="<?php echo htmlspecialchars($decoracion_descripcion); ?>" required>
            <br><br>

            <label for="RELA_estado_decoraciones">Estado:</label>
            <select name="RELA_estado_decoraciones" id="RELA_estado_decoraciones" required>
                <?php foreach ($estados as $estado) {
                    $id_estado = isset($estado['ID_estado_decoraciones']) ? $estado['ID_estado_decoraciones'] : '';
                    $nombre_estado = isset($estado['estado_decoraciones_descri']) ? $estado['estado_decoraciones_descri'] : 'Sin descripción';
                ?>
                    <option value="<?php echo htmlspecialchars($id_estado); ?>"
                        <?php if ($id_estado == $estado_actual) echo "selected"; ?>>
                        <?php echo htmlspecialchars($nombre_estado); ?>
                    </option>
                <?php } ?>
            </select>

            <input type="hidden" name="id_decoracion" value="<?php echo $id_decoracion; ?>">
            <br><br>

            <button type="submit">Guardar decoración</button>
        </form>
    </div>
</body>

</html>