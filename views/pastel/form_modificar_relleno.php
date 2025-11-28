<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cake Party - Editar Relleno</title>
    <link rel="stylesheet" href="../../public/css/admin_style.css" />
</head>
<body>
<?php include("../../includes/sidebar.php"); 
require_once "../../includes/navegacion.php";
?>

<div class="admin-form">
        <h1>Editar Relleno</h1>
        <hr>
        <?php
        require_once("../../config/conexion.php");
        if (!isset($_GET["id_relleno"])) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20el%20ID%20del%20relleno.");
            exit();
        }
        $id_relleno = intval($_GET["id_relleno"]);
        $pdo = getConexion();
        $stmt = $pdo->prepare("SELECT * FROM relleno WHERE id_relleno = :id");
        $stmt->execute(['id' => $id_relleno]);
        $relleno = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$relleno) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Relleno%20no%20encontrado.");
            exit();
        }
        $relleno_nombre = $relleno["relleno_nombre"];
        $relleno_descripcion = $relleno["relleno_descripcion"];
        $relleno_precio = $relleno["relleno_precio"];
        $estado_actual = $relleno["RELA_estado_decoraciones"];
        $estados = $pdo->query("SELECT * FROM estado_decoraciones")->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <form action="../../controllers/pastel/modificar_relleno.php" method="post">
            
            <label for="relleno_nombre">Nombre: </label>
            <input type="text" name="relleno_nombre" id="relleno_nombre"
                value="<?php echo htmlspecialchars($relleno_nombre); ?>" required>
            <br><br>

            <label for="relleno_descripcion">Descripción: </label>
            <textarea name="relleno_descripcion" id="relleno_descripcion" rows="4" cols="50" required><?php echo htmlspecialchars($relleno_descripcion); ?></textarea>
            <br><br>

            <label for="relleno_precio">Precio: </label>
            <input type="text" name="relleno_precio" id="relleno_precio"
                value="<?php echo htmlspecialchars($relleno_precio); ?>" required>
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
            <input type="hidden" name="id_relleno" value="<?php echo $id_relleno; ?>">
            <br><br>
            <button type="submit">Guardar</button>
        </form>
    </div>
</body>
</html>