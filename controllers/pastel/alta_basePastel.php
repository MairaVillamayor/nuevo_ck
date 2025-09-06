<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["base_pastel_nombre"]) && isset($_POST["base_pastel_decoracion"])) {
	$base_pastel_nombre = trim($_POST["base_pastel_nombre"]);
	$base_pastel_decoracion = trim($_POST["base_pastel_decoracion"]);

	if ($base_pastel_nombre != "" && $base_pastel_decoracion != "") {
		$pdo = getConexion();
		$stmt = $pdo->prepare("INSERT INTO base_pastel (base_pastel_nombre, base_pastel_descripcion, RELA_estado_decoraciones) VALUES (?, ?, 1)");
		if ($stmt->execute([$base_pastel_nombre, $base_pastel_decoracion])) {
			header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Base%20de%20pastel%20creada&mensaje=La%20nueva%20base%20se%20dio%20de%20alta%20correctamente&redirect_to=../views/pastel/listado_basePastel.php&delay=2");
			exit();
		} else {
			header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20crear%20la%20base%20de%20pastel");
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