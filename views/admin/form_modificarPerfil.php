<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cake Party - Editar Perfil</title>
    <link rel="stylesheet" href="../../public/css/sidebar.css" />
</head>

<body>
<?php include("../../includes/sidebar.php"); 
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";
?>
    <div class="admin-form">
        <h1>Editar Perfil</h1>
        <hr>

        <?php
        require_once("../../config/conexion.php");

        if (!isset($_GET["ID_perfil"])) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20el%20ID%20del%20perfil.");
            exit();
        }

        $ID_perfil = intval($_GET["ID_perfil"]);

        $pdo = getConexion();
        $stmt = $pdo->prepare("SELECT * FROM perfiles WHERE ID_perfil = :ID_perfil");
        $stmt->execute(['ID_perfil' => $ID_perfil]);
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$perfil) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Perfil%20no%20encontrado.");
            exit();
        }

        $perfil_rol = $perfil["perfil_rol"];
        ?>

        <form action="../../controllers/admin/modificar_perfiles.php" method="post">
            <label for="perfil_rol">Rol del Perfil: </label>
            <input type="text" name="perfil_rol" id="perfil_rol"
                value="<?php echo htmlspecialchars($perfil_rol); ?>" required>
            <br><br>

            <input type="hidden" name="ID_perfil" value="<?php echo $ID_perfil; ?>">
            <br><br>

            <button type="submit">Guardar</button>
        </form>
    </div>
</body>

</html>
