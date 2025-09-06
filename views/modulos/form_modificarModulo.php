<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cake Party - Editar Módulo</title>
    <link rel="stylesheet" href="../../public/css/admin_style.css" />
</head>

<body>
<?php include("../../includes/header.php"); 
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";
?>
    <div class="admin-form">
        <h1>Editar Módulo</h1>
        <hr>

        <?php
        require_once("../../config/conexion.php");

        if (!isset($_GET["ID_modulos"])) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20el%20ID%20del%20m%C3%B3dulo.");
            exit();
        }

        $ID_modulos = intval($_GET["ID_modulos"]);

        $pdo = getConexion();
        $stmt = $pdo->prepare("SELECT * FROM modulos WHERE ID_modulos = :ID_modulos");
        $stmt->execute(['ID_modulos' => $ID_modulos]);
        $modulo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$modulo) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Módulo%20no%20encontrado.");
            exit();
        }

        $modulos_nombre = $modulo["modulos_nombre"];
        ?>

        <form action="../../controllers/modulos/modificar_modulos.php" method="post">
            <label for="modulos_nombre">Nombre del Módulo: </label>
            <input type="text" name="modulos_nombre" id="modulos_nombre"
                value="<?php echo htmlspecialchars($modulos_nombre); ?>" required>
            <br><br>

            <input type="hidden" name="ID_modulos" value="<?php echo $ID_modulos; ?>">
            <br><br>

            <button type="submit">Guardar</button>
        </form>
    </div>
</body>

</html>
