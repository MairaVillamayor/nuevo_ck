<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cake Party - Editar Base Pastel</title>
    <link rel="stylesheet" href="../../public/css/admin_style.css" />
</head>
<body>
<?php include("../../includes/header.php"); 
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";
?>

<div class="admin-form">
        <h1>Editar Base Pastel</h1>
        <hr>
        <?php
        require_once("../../config/conexion.php");
        if (!isset($_GET["id_base_pastel"])) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20el%20ID%20de%20la%20base.");
            exit();
        }
        $id_base_pastel = intval($_GET["id_base_pastel"]);
        $pdo = getConexion();
        $stmt = $pdo->prepare("SELECT * FROM base_pastel WHERE id_base_pastel = :id");
        $stmt->execute(['id' => $id_base_pastel]);
        $base_pastel = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$base_pastel) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Base%20no%20encontrada.");
            exit();
        }
        $base_pastel_nombre = $base_pastel["base_pastel_nombre"];
        $base_pastel_decoracion = $base_pastel["base_pastel_descripcion"];
        $estado_actual = $base_pastel["RELA_estado_decoraciones"];
        $estados = $pdo->query("SELECT * FROM estado_decoraciones")->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <form action="../../controllers/pastel/modificar_basePastel.php" method="post">
            <label for="base_pastel_nombre">Nombre: </label>
            <input type="text" name="base_pastel_nombre" id="base_pastel_nombre"
                value="<?php echo htmlspecialchars($base_pastel_nombre); ?>" required>
            <br><br>
            <label for="base_pastel_decoracion">Descripción: </label>
            <textarea name="base_pastel_decoracion" id="base_pastel_decoracion" rows="4" cols="50" required><?php echo htmlspecialchars($base_pastel_decoracion); ?></textarea>
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
            <input type="hidden" name="id_base_pastel" value="<?php echo $id_base_pastel; ?>">
            <br><br>
            <button type="submit">Guardar</button>
        </form>
    </div>
</body>
</html>