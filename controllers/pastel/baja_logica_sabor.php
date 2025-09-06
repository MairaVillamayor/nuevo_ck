<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["id_sabor"])) {
	$id = intval($_POST["id_sabor"]);
	$pdo = getConexion();
	$stmt = $pdo->prepare("UPDATE sabor SET RELA_estado_decoraciones = 2 WHERE id_sabor = ?");
	if ($stmt->execute([$id])) {
		header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Sabor%20dado%20de%20baja%20l%C3%B3gica&mensaje=El%20sabor%20fue%20dado%20de%20baja%20correctamente&redirect_to=../views/pastel/listado_sabor.php&delay=2");
		exit();
	} else {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20dar%20de%20baja%20el%20sabor");
		exit();
	}
} else {
	header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20un%20ID%20v%C3%A1lido");
	exit();
}
?>
