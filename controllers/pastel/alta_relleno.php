<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["relleno_nombre"]) && isset($_POST["relleno_descripcion"])) {
	$relleno_nombre = trim($_POST["relleno_nombre"]);
	$relleno_descripcion = trim($_POST["relleno_descripcion"]);

	if ($relleno_nombre != "" && $relleno_descripcion != "") {
		$pdo = getConexion();
		$stmt = $pdo->prepare("INSERT INTO relleno (relleno_nombre, relleno_descripcion, RELA_estado_decoraciones) VALUES (?, ?, 1)");
		if ($stmt->execute([$relleno_nombre, $relleno_descripcion])) {
			header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Relleno%20creado&mensaje=El%20nuevo%20relleno%20se%20dio%20de%20alta%20correctamente&redirect_to=../views/pastel/listado_relleno.php&delay=2");
			exit();
		} else {
			header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20crear%20el%20relleno");
			exit();
		}
	} else {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20valores%20requeridos");
		exit();
	}
}
header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibieron%20datos");
exit();
?>