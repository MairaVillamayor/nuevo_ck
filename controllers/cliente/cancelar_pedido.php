<?php
session_start();
require_once "../../config/conexion.php";
include("../../includes/navegacion.php");

if (!isset($_POST['id_pedido'])) {
    die("Pedido no especificado.");
}

$id_pedido = intval($_POST['id_pedido']);

$sql = "UPDATE pedido SET RELA_estado = 4 WHERE pedido_id = $id_pedido";
mysqli_query($conn, $sql);

// Guardamos mensaje
$_SESSION['success'] = "❌ El pedido #$id_pedido fue cancelado correctamente.";

// Redirigimos a pago
header("Location: pago.php?id_pedido=$id_pedido");
exit;
