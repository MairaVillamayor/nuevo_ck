<?php
require_once __DIR__ . '/../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if (
		!isset($_POST["id_color_pastel"]) ||
		!isset($_POST["color_pastel_nombre"]) ||
		!isset($_POST["color_pastel_codigo"]) ||
		!isset($_POST["rela_estado"])
	) {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Faltan%20datos%20del%20formulario.");
		exit();
	}

	$id_color_pastel = intval($_POST["id_color_pastel"]);
	$color_pastel_nombre = trim($_POST["color_pastel_nombre"]);
	$color_pastel_codigo = trim($_POST["color_pastel_codigo"]);
	$rela_estado = intval($_POST["rela_estado"]);

	if ($color_pastel_nombre === "") {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=El%20nombre%20del%20color%20no%20puede%20estar%20vac%C3%ADo.");
		exit();
	}

	try {
		$pdo = getConexion();
		$sql = "UPDATE color_pastel 
				SET color_pastel_nombre = :color_pastel_nombre, 
					color_pastel_codigo = :color_pastel_codigo, 
					RELA_estado_decoraciones = :rela_estado 
				WHERE id_color_pastel = :id_color_pastel";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([
			'color_pastel_nombre' => $color_pastel_nombre,
			'color_pastel_codigo' => $color_pastel_codigo,
			'rela_estado' => $rela_estado,
			'id_color_pastel' => $id_color_pastel
		]);
		header("Location: ../../includes/mensaje.php?tipo=exito&titulo=Color%20modificado&mensaje=El%20color%20pastel%20fue%20modificado%20correctamente&redirect_to=../views/pastel/listado_colorPastel.php&delay=2");
		exit();
	} catch (PDOException $e) {
		header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Error%20al%20actualizar%20el%20color:%20".urlencode($e->getMessage()));
		exit();
	}

} else {
	header("Location: ../../includes/mensaje.php?tipo=error&titulo=Error&mensaje=Acceso%20no%20permitido.");
	exit();
}
?>