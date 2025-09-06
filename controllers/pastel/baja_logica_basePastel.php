<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_base_pastel"])) {
	$id_base_pastel = intval($_POST["id_base_pastel"]);
	$pdo = getConexion();
	$stmt = $pdo->prepare("UPDATE base_pastel SET RELA_estado_decoraciones = 2 WHERE id_base_pastel = ?");
	if ($stmt->execute([$id_base_pastel])) {
		header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Base%20pastel%20dada%20de%20baja%20l%C3%B3gica&mensaje=La%20base%20de%20pastel%20fue%20dada%20de%20baja%20correctamente&redirect_to=../views/pastel/listado_basePastel.php&delay=2");
		exit();
	} else {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20dar%20de%20baja%20la%20base%20de%20pastel");
		exit();
	}
} else {
	header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20un%20ID%20v%C3%A1lido");
	exit();
}
?>
