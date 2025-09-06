<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["ID_insumo"])) {
	$id = intval($_POST["ID_insumo"]);
	$pdo = getConexion();
	$stmt = $pdo->prepare("UPDATE insumos SET RELA_estado_insumo = 2 WHERE ID_insumo = ?");
	if ($stmt->execute([$id])) {
		header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Baja%20l%C3%B3gica%20realizada&mensaje=El%20insumo%20fue%20dado%20de%20baja%20correctamente&redirect_to=../views/insumos/listado_insumo.php&delay=2");
		exit();
	} else {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20dar%20de%20baja%20el%20insumo");
		exit();
	}
} else {
	header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20un%20ID%20v%C3%A1lido");
	exit();
}
?>
