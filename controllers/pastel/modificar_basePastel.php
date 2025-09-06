<?php
require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if (
		!isset($_POST["id_base_pastel"]) ||
		!isset($_POST["base_pastel_nombre"]) ||
		!isset($_POST["base_pastel_decoracion"]) ||
		!isset($_POST["rela_estado"]) 
	) {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20datos%20del%20formulario");
		exit();
	}

	$id_base_pastel = intval($_POST["id_base_pastel"]);
	$base_pastel_nombre = trim($_POST["base_pastel_nombre"]);
	$base_pastel_decoracion = trim($_POST["base_pastel_decoracion"]);
	$rela_estado = intval($_POST["rela_estado"]);

	if ($base_pastel_nombre === "") {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=El%20nombre%20no%20puede%20estar%20vac%C3%ADo");
		exit();
	}

	try {
		$pdo = getConexion();
		$sql = "UPDATE base_pastel 
				SET base_pastel_nombre = :base_pastel_nombre, 
					base_pastel_descripcion = :base_pastel_descripcion, 
					RELA_estado_decoraciones = :rela_estado 
				WHERE id_base_pastel = :id_base_pastel";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([
			'base_pastel_nombre' => $base_pastel_nombre,
			'base_pastel_descripcion' => $base_pastel_decoracion,
			'rela_estado' => $rela_estado,
			'id_base_pastel' => $id_base_pastel
		]);
		header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Base%20modificada&mensaje=La%20base%20de%20pastel%20fue%20modificada%20correctamente&redirect_to=../views/pastel/listado_basePastel.php&delay=2");
		exit();
	} catch (PDOException $e) {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=" . urlencode("No se pudo modificar la base: " . $e->getMessage()));
		exit();
	}
} else {
	header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Acceso%20no%20permitido");
	exit();
}
?> 