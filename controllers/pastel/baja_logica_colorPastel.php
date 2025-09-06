<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_color_pastel"])) {
	$id_color_pastel = intval($_POST["id_color_pastel"]);
	$pdo = getConexion();
	$stmt = $pdo->prepare("UPDATE color_pastel SET RELA_estado_decoraciones = 2 WHERE id_color_pastel = ?");
	if ($stmt->execute([$id_color_pastel])) {
		header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Color%20pastel%20dado%20de%20baja%20l%C3%B3gica&mensaje=El%20color%20pastel%20fue%20dado%20de%20baja%20correctamente&redirect_to=../views/pastel/listado_colorPastel.php&delay=2");
		exit();
	} else {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20dar%20de%20baja%20el%20color%20pastel");
		exit();
	}
} else {
	header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20un%20ID%20v%C3%A1lido");
	exit();
}
?>
