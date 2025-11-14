<?php
require_once '../../config/conexion.php';
$mensaje = '';
$status = '';

try {
    $pdo_conn = getConexion(); 
    $pdo_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (Exception $e) {
    $mensaje = "Error al obtener conexión: " . $e->getMessage();
    header("Location: alta_producto.php?status=error&msg=" . urlencode($mensaje));
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ingresar_producto']) && $pdo_conn) {
    $nombre = trim($_POST['producto_finalizado_nombre']);
    $descripcion = trim($_POST['producto_finalizado_descri']);
    $precio = (float)$_POST['producto_finalizado_precio'];
    $stock = (int)$_POST['stock_actual'];
    $disponible_web = isset($_POST['disponible_web']) ? 1 : 0;
    $relacion_tematica_default = 1; 

    $imagen_url = 'uploads/por_defecto.jpg'; 


    if (isset($_FILES['imagen_url']) && $_FILES['imagen_url']['error'] === UPLOAD_ERR_OK) {
        $nombreTmp = $_FILES['imagen_url']['tmp_name'];
        $nombreOriginal = basename($_FILES['imagen_url']['name']);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        $nombreNuevo = uniqid('prod_') . '.' . $extension;
        $rutaDestino = '../../uploads/' . $nombreNuevo;

        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
        $tipoArchivo = mime_content_type($nombreTmp);

        if (in_array($tipoArchivo, $tiposPermitidos)) {
         
            if ($_FILES['imagen_url']['size'] <= 3 * 1024 * 1024) {
                if (move_uploaded_file($nombreTmp, $rutaDestino)) {
                   
                    $imagen_url = 'uploads/' . $nombreNuevo;
                } else {
                    $mensaje = "⚠️ No se pudo mover la imagen subida al destino.";
                }
            } else {
                $mensaje = "⚠️ La imagen excede el tamaño máximo permitido (3MB).";
            }
        } else {
            $mensaje = "⚠️ Formato no permitido. Solo JPG, PNG o WEBP.";
        }
    }

    $query = "INSERT INTO producto_finalizado 
              (producto_finalizado_nombre, producto_finalizado_descri, producto_finalizado_precio, stock_actual, disponible_web, imagen_url, RELA_tematica) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    try {
        $stmt = $pdo_conn->prepare($query);
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $disponible_web, $imagen_url, $relacion_tematica_default]);

        $titulo = urlencode("Producto Creado");
        $mensaje = urlencode("El producto '{$nombre}' se ha ingresado con éxito.");
        header("Location: ../../includes/mensaje.php?tipo=exito&titulo={$titulo}&mensaje={$mensaje}&redirect_to=../../views/productos/productos_finalizados.php&delay=2");
        exit();
    } catch (PDOException $e) {
        $mensaje = "Error al ingresar producto: " . $e->getMessage();
        $status = "error";
        header("Location: /../../views/productos/productos_finalizados.php?status={$status}&msg=" . urlencode($mensaje));
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_disponibilidad']) && $pdo_conn) {
    $id_producto = (int)$_POST['id_producto'];
    $nuevo_estado = (int)$_POST['estado']; 
    
    $query = "UPDATE producto_finalizado SET disponible_web = ? WHERE ID_producto_finalizado = ?";
    
    try {
        $stmt = $pdo_conn->prepare($query);
        $stmt->execute([$nuevo_estado, $id_producto]);
        $mensaje = "Disponibilidad actualizada.";
        $status = "success";
    } catch (PDOException $e) {
        $mensaje = "Error al actualizar disponibilidad: " . $e->getMessage();
        $status = "error";
    }
    
    header("Location: ../../views/productos/productos_finnalizados.php?status=" . $status . "&msg=" . urlencode($mensaje));
    exit();
}


header("Location: ../../views/productos/productos_finalizados.php");
exit();
?>
