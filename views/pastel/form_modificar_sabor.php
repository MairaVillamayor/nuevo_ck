<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cake Party - Editar Sabor</title>
    <link rel="stylesheet" href="../../public/css/admin_style.css" />
</head>

<body>
<?php include("../../includes/header.php"); 
require_once "../../includes/navegacion.php";
?>

    <div class="admin-form">
        <h1>Editar Sabor</h1>
        <hr>

        <?php
        require_once("../../config/conexion.php");
        if (!isset($_GET["id_sabor"])) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20el%20ID%20del%20sabor.");
            exit();
        }
        $id_sabor = intval($_GET["id_sabor"]);
        $pdo = getConexion();
        $stmt = $pdo->prepare("SELECT * FROM sabor WHERE id_sabor = :id");
        $stmt->execute(['id' => $id_sabor]);
        $sabor = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$sabor) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Sabor%20no%20encontrado.");
            exit();
        }
        $sabor_nombre = $sabor["sabor_nombre"];
        $sabor_descripcion = $sabor["sabor_descripcion"];
        $sabor_precio = $sabor["sabor_precio"];
        $estado_actual = $sabor["RELA_estado_decoraciones"];
        $estados = $pdo->query("SELECT * FROM estado_decoraciones")->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <form action="../../controllers/pastel/modificar_sabor.php" method="post">
            <label for="sabor_nombre">Nombre: </label>
            <input type="text" name="sabor_nombre" id="sabor_nombre"
                value="<?php echo htmlspecialchars($sabor_nombre); ?>" required>
            <br><br>

            <label for="sabor_descripcion">Descripción: </label>
            <textarea name="sabor_descripcion" id="sabor_descripcion" rows="4" cols="50" required><?php echo htmlspecialchars($sabor_descripcion); ?></textarea>
            <br><br>

            <label for="sabor_precio">Precio: </label>
            <input type="text" name="sabor_precio" id="sabor_precio"
                value="<?php echo htmlspecialchars($sabor_precio); ?>" required>
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

            <input type="hidden" name="id_sabor" value="<?php echo $id_sabor; ?>">
            <br><br>

            <button type="submit">Guardar</button>
        </form>
    </div>
</body>

</html>