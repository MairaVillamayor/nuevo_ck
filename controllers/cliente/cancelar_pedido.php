<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["ID_pedido"])) {
	$id = intval($_POST["ID_pedido"]);
	$pdo = getConexion();
	$stmt = $pdo->prepare("UPDATE pedido SET RELA_estado = 4 WHERE ID_pedido = ?");
	if ($stmt->execute([$id])) {
		header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Baja%20l%C3%B3gica%20realizada&mensaje=El%pedido%20fue%20dado%20de%20baja%20correctamente&redirect_to=../views/cliente/mis_pedidos.php&delay=2");
		exit();
	} else {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20dar%20de%20baja%20el%pedido");
		exit();
	}
} else {
	header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibi%C3%B3%20un%20ID%20v%C3%A1lido");
	exit();
}
?>


