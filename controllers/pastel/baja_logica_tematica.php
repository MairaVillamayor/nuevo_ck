<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_tematica"])) {
	$id = intval($_POST["id_tematica"]);
	$pdo = getConexion();
	$stmt = $pdo->prepare("UPDATE tematica SET RELA_estado_decoraciones = 2 WHERE id_tematica = ?");
	if ($stmt->execute([$id])) {
		header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Tem%C3%A1tica%20dada%20de%20baja%20l%C3%B3gica&mensaje=La%20tem%C3%A1tica%20fue%20dada%20de%20baja%20correctamente&redirect_to=../views/pastel/listado_tematica.php&delay=2");
		exit();
	} else {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20dar%20de%20baja%20la%20tem%C3%A1tica");
		exit();
	}
} else {
	header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20un%20ID%20v%C3%A1lido");
	exit();
}
?>
