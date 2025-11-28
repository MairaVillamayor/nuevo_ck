<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cake Party - Editar Insumo</title>
    <link rel="stylesheet" href="../../public/css/admin_style.css" />
</head>

<body>
<?php include("../../includes/sidebar.php"); 
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";
?>
    <div class="admin-form">
        <h1>Editar Insumo</h1>
        <hr>

        <?php
        require_once("../../config/conexion.php");
        if (!isset($_GET["ID_insumo"])) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20el%20ID%20del%20insumo.");
            exit();
        }
        $id_insumo = intval($_GET["ID_insumo"]);
        $pdo = getConexion();
        $stmt = $pdo->prepare("SELECT * FROM insumos WHERE ID_insumo = :id");
        $stmt->execute(['id' => $id_insumo]);
        $insumo = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$insumo) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Insumo%20no%20encontrado.");
            exit();
        }
        $insumo_nombre = $insumo["insumo_nombre"];
        $insumo_unidad_medida = $insumo["insumo_unidad_medida"];
        $estado_actual = $insumo["RELA_estado_insumo"];
        $estados = $pdo->query("SELECT * FROM estado_insumos")->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <form action="../../controllers/insumos/modificar_insumo.php" method="post">
            <label for="insumo_nombre">Nombre: </label>
            <input type="text" name="insumo_nombre" id="insumo_nombre"
                value="<?php echo htmlspecialchars($insumo_nombre); ?>" required>
            <br><br>

            <label for="insumo_unidad_medida">Unidad de medida: </label>
            <input type="text" name="insumo_unidad_medida" id="insumo_unidad_medida"
                value="<?php echo htmlspecialchars($insumo_unidad_medida); ?>" required>
            <br><br>

            <label for="RELA_estado_insumo">Estado:</label>
            <select name="RELA_estado_insumo" id="RELA_estado_insumo" required>
                <?php foreach ($estados as $estado) {
                    $id_estado = isset($estado['ID_estado_insumo']) ? $estado['ID_estado_insumo'] : '';
                    $nombre_estado = isset($estado['estado_insumo_descripcion']) ? $estado['estado_insumo_descripcion'] : 'Sin descripción';
                ?>
                    <option value="<?php echo htmlspecialchars($id_estado); ?>"
                        <?php if ($id_estado == $estado_actual) echo "selected"; ?>>
                        <?php echo htmlspecialchars($nombre_estado); ?>
                    </option>
                <?php } ?>
            </select>

            <input type="hidden" name="ID_insumo" value="<?php echo $id_insumo; ?>">
            <br><br>

            <button type="submit">Guardar insumo</button>
        </form>
    </div>
</body>

</html>