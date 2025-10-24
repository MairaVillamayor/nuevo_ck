<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cake Party - Editar Temática</title>
    <link rel="stylesheet" href="../../public/css/admin_style.css" />
</head>
<body>
<?php 
include("../../includes/header.php"); 
require_once "../../includes/navegacion.php";
?>

<div class="admin-form">
    <h1>Editar Temática</h1>
    <hr>
    <?php
    require_once("../../config/conexion.php");
    if (!isset($_GET["id_tematica"])) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20el%20ID%20de%20la%20tem%C3%A1tica.");
        exit();
    }
    $id_tematica = intval($_GET["id_tematica"]);
    $pdo = getConexion();
    $stmt = $pdo->prepare("SELECT * FROM tematica WHERE id_tematica = :id");
    $stmt->execute(['id' => $id_tematica]);
    $tematica = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$tematica) {
        header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Tem%C3%A1tica%20no%20encontrada.");
        exit();
    }
    $tematica_descripcion = $tematica["tematica_descripcion"];
    $estado_actual = isset($tematica["RELA_estado_decoraciones"]) ? $tematica["RELA_estado_decoraciones"] : null;
    $estados = $pdo->query("SELECT * FROM estado")->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <form action="../../controllers/pastel/modificar_tematica.php" method="post">
        <label for="tematica_descripcion">Descripción: </label>
        <input type="text" name="tematica_descripcion" id="tematica_descripcion" value="<?php echo htmlspecialchars($tematica_descripcion); ?>" required>
        <br><br>
        <label for="rela_estado_decoraciones">Estado:</label>
        <select name="rela_estado_decoraciones" id="rela_estado_decoraciones" required>
            <?php foreach ($estados as $estado) {
                $id_estado = isset($estado['ID_estado_decoraciones']) ? $estado['ID_estado_decoraciones'] : '';
                $nombre_estado = isset($estado['estado_decoraciones_descri']) ? $estado['estado_decoraciones_descri'] : 'Sin descripción';
            ?>
                <option value="<?php echo htmlspecialchars($id_estado); ?>" <?php if ($estado_actual !== null && $id_estado == $estado_actual) echo "selected"; ?>>
                    <?php echo htmlspecialchars($nombre_estado); ?>
                </option>
            <?php } ?>
        </select>
        <input type="hidden" name="id_tematica" value="<?php echo $id_tematica; ?>">
        <br><br>
        <button type="submit">Guardar</button>
    </form>
    </div>
</body>
</html>
