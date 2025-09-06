<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["color_pastel_nombre"]) && isset($_POST["color_pastel_codigo"])) {
	$color_pastel_nombre = trim($_POST["color_pastel_nombre"]);
	$color_pastel_codigo = trim($_POST["color_pastel_codigo"]);

	if ($color_pastel_nombre != "" && $color_pastel_codigo != "") {
		$pdo = getConexion();
		$stmt = $pdo->prepare("INSERT INTO color_pastel (color_pastel_nombre, color_pastel_codigo, RELA_estado_decoraciones) VALUES (?, ?, 1)");
		if ($stmt->execute([$color_pastel_nombre, $color_pastel_codigo])) {
			header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Color%20de%20pastel%20creado&mensaje=El%20nuevo%20color%20se%20dio%20de%20alta%20correctamente&redirect_to=../views/pastel/listado_colorPastel.php&delay=2");
			exit();
		} else {
			header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20crear%20el%20color%20de%20pastel");
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