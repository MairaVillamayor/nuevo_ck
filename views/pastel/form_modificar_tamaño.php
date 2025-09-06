<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cake Party - Editar Tamaño</title>
    <link rel="stylesheet" href="../../public/css/admin_style.css" />
</head>

<body>
<?php include("../../includes/header.php"); 
require_once "C:/laragon/www/nuevo_ck/includes/navegacion.php";
?>

    <div class="admin-form">
        <h1>Editar Tamaño</h1>
        <hr>

        <?php
        require_once("../../config/conexion.php");

        if (!isset($_GET["id_tamaño"])) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20el%20ID%20del%20tama%C3%B1o.");
            exit();
        }

        $id_tamaño = intval($_GET["id_tamaño"]);

        $pdo = getConexion();
        $stmt = $pdo->prepare("SELECT * FROM tamaño WHERE id_tamaño = :id");
        $stmt->execute(['id' => $id_tamaño]);
        $tamaño = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tamaño) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Tamaño%20no%20encontrado.");
            exit();
        }

        $tamaño_nombre = $tamaño["tamaño_nombre"];
        $tamaño_medidas = $tamaño["tamaño_medidas"];
        ?>

        <form action="../../controllers/pastel/modificar_tamaño.php" method="post">
            <label for="tamaño_nombre">Nombre: </label>
            <input type="text" name="tamaño_nombre" id="tamaño_nombre"
                value="<?php echo htmlspecialchars($tamaño_nombre); ?>" required>
            <br><br>

            <label for="tamaño_medidas">Medidas: </label>
            <input type="text" name="tamaño_medidas" id="tamaño_medidas"
                value="<?php echo htmlspecialchars($tamaño_medidas); ?>" required>
            <br><br>

            <input type="hidden" name="id_tamaño" value="<?php echo $id_tamaño; ?>">
            <br><br>

            <button type="submit">Guardar</button>
        </form>
    </div>
</body>

</html>