<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["decoracion_nombre"]) && isset($_POST["decoracion_descripcion"])) {
	$decoracion_nombre = trim($_POST["decoracion_nombre"]);
	$decoracion_descripcion = trim($_POST["decoracion_descripcion"]);

	if ($decoracion_nombre != "" && $decoracion_descripcion != "") {
		$pdo = getConexion();
		$stmt = $pdo->prepare("INSERT INTO decoracion (decoracion_nombre, decoracion_descripcion, RELA_estado_decoraciones) VALUES (?, ?, 1)");
		if ($stmt->execute([$decoracion_nombre, $decoracion_descripcion])) {
			header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Decoraci%C3%B3n%20creada&mensaje=La%20nueva%20decoraci%C3%B3n%20se%20dio%20de%20alta%20correctamente&redirect_to=../views/pastel/listado_decoracion.php&delay=2");
			exit();
		} else {
			header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20crear%20la%20decoraci%C3%B3n&redirect_to=../views/pastel/listado_decoracion.php&delay=2");
			exit();
		}
	} else {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20valores%20requeridos&redirect_to=../views/pastel/listado_decoracion.php&delay=2");
		exit();
	}
}
header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20recibieron%20datos");
exit();
?>