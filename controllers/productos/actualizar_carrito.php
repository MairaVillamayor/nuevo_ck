<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'], $_POST['action'])) {
    $id = (int)$_POST['producto_id'];
    $accion = $_POST['action'];

    if ($accion === 'remove' && !empty($_SESSION['carrito'])) {
        foreach ($_SESSION['carrito'] as $key => $item) {
            if ($item['id'] === $id) {
                unset($_SESSION['carrito'][$key]);
                // Reindexar el array para evitar huecos
                $_SESSION['carrito'] = array_values($_SESSION['carrito']);
                break;
            }
        }
    }
}

header("Location: ../views/cliente/carrito.php");
exit;
?>
