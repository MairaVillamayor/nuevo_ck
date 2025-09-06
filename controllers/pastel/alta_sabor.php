<?php
require_once __DIR__ . '/../../config/conexion.php';

if (isset($_POST["sabor_nombre"]) && isset($_POST["sabor_descripcion"])) {
	$sabor_nombre = trim($_POST["sabor_nombre"]);
	$sabor_descripcion = trim($_POST["sabor_descripcion"]);

	if ($sabor_nombre != "" && $sabor_descripcion != "") {
		try {
			$pdo = getConexion();
			$stmt = $pdo->prepare("INSERT INTO sabor (sabor_nombre, sabor_descripcion, RELA_estado_decoraciones) VALUES (?, ?, 1)");
			$stmt->execute([$sabor_nombre, $sabor_descripcion]);
			header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Sabor%20creado&mensaje=El%20nuevo%20sabor%20se%20dio%20de%20alta%20correctamente&redirect_to=../views/pastel/listado_sabor.php&delay=2");
			exit();
		} catch (PDOException $e) {
			header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=" . urlencode("No se pudo crear el sabor: " . $e->getMessage()));
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