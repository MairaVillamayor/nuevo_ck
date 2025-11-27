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

        if (!isset($_GET["ID_tamaño"])) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20el%20ID%20del%20tama%C3%B1o.");
            exit();
        }

        $ID_tamaño = intval($_GET["ID_tamaño"]);

        $pdo = getConexion();
        $stmt = $pdo->prepare("SELECT tamaño_nombre, tamaño_medidas, tamaño_precio FROM tamaño WHERE ID_tamaño = :ID_tamaño");
        $stmt->execute(['ID_tamaño' => $ID_tamaño]);
        $tamaño = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tamaño) {
            header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Tamaño%20no%20encontrado.");
            exit();
        }

        $tamaño_nombre = $tamaño["tamaño_nombre"];
        $tamaño_medidas = $tamaño["tamaño_medidas"];
        $tamaño_precio = $tamaño["tamaño_precio"];
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


            <label for="tamaño_precio">Precio: </label>
            <input type="text" name="tamaño_precio" id="tamaño_precio"
                value="<?php echo htmlspecialchars($tamaño_precio); ?>" required>
            <br><br>    
            
            <input type="hidden" name="ID_tamaño" value="<?php echo $ID_tamaño; ?>">
            <br><br>

            <button type="submit">Guardar</button>
        </form>
    </div>
</body>

</html>