<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["insumo_nombre"]) && isset($_POST["insumo_unidad_medida"])) {
	$insumo_nombre = trim($_POST["insumo_nombre"]);
	$insumo_unidad_medida = trim($_POST["insumo_unidad_medida"]);

	if ($insumo_nombre != "" && $insumo_unidad_medida != "") {
		$pdo = getConexion();
		$stmt = $pdo->prepare("INSERT INTO insumos (insumo_nombre, insumo_unidad_medida, RELA_categoria_insumos, RELA_proveedor, RELA_estado_insumo) VALUES (?, ?, 1, 1, 1)");
		if ($stmt->execute([$insumo_nombre, $insumo_unidad_medida])) {
			header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Insumo%20creado&mensaje=El%20nuevo%20insumo%20se%20dio%20de%20alta%20correctamente&redirect_to=../views/insumos/listado_insumo.php&delay=2");
			exit();
		} else {
			header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20crear%20el%20insumo");
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