<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cake Party - Editar Color Pastel</title>
    <link rel="stylesheet" href="../../public/css/admin_style.css" />
</head>
<body>
<?php include("../../includes/sidebar.php"); 
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";
?>

<div class="admin-form">
        <h1>Editar Color Pastel</h1>
        <hr>
        <?php
        require_once("../../config/conexion.php");
        if (!isset($_GET["id_color_pastel"])) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20el%20ID%20del%20color.");
            exit();
        }
        $id_color_pastel = intval($_GET["id_color_pastel"]);
        $pdo = getConexion();
        $stmt = $pdo->prepare("SELECT * FROM color_pastel WHERE id_color_pastel = :id");
        $stmt->execute(['id' => $id_color_pastel]);
        $color_pastel = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$color_pastel) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Color%20no%20encontrado.");
            exit();
        }
        $color_pastel_nombre = $color_pastel["color_pastel_nombre"];
        $color_pastel_codigo = $color_pastel["color_pastel_codigo"];
        $estado_actual = $color_pastel["RELA_estado_decoraciones"];
        $estados = $pdo->query("SELECT * FROM estado_decoraciones")->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <form action="../../controllers/pastel/modificar_colorPastel.php" method="post">
            <label for="color_pastel_nombre">Nombre: </label>
            <input type="text" name="color_pastel_nombre" id="color_pastel_nombre"
                value="<?php echo htmlspecialchars($color_pastel_nombre); ?>" required>
            <br><br>
            <label for="color_pastel_codigo">Código: </label>
            <textarea name="color_pastel_codigo" id="color_pastel_codigo" rows="4" cols="50" required><?php echo htmlspecialchars($color_pastel_codigo); ?></textarea>
            <br><br>
            <label for="rela_estado">Estado:</label>
            <select name="rela_estado" id="rela_estado" required>
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
            <input type="hidden" name="id_color_pastel" value="<?php echo $id_color_pastel; ?>">
            <br><br>
            <button type="submit">Guardar</button>
        </form>
    </div>
</body>
</html>