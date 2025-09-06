<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_decoracion"])) {
	$id_decoracion = intval($_POST["id_decoracion"]);
	$pdo = getConexion();
	$stmt = $pdo->prepare("UPDATE decoracion SET RELA_estado_decoraciones = 2 WHERE id_decoracion = ?");
	if ($stmt->execute([$id_decoracion])) {
		header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Decoraci%C3%B3n%20dada%20de%20baja%20l%C3%B3gica&mensaje=La%20decoraci%C3%B3n%20fue%20dada%20de%20baja%20correctamente&redirect_to=../views/pastel/listado_decoracion.php&delay=2");
		exit();
	} else {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20dar%20de%20baja%20la%20decoraci%C3%B3n");
		exit();
	}
} else {
	header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20un%20ID%20v%C3%A1lido");
	exit();
}
?>
