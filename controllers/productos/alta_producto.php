<?php
// Incluir la conexión a la base de datos
require_once '../../config/conexion.php';

// Inicializa una variable de mensaje y estado (para la URL de redirección)
$mensaje = '';
$status = '';

try {
    $pdo_conn = getConexion(); 
    
    // RECOMENDADO: Asegurar que PDO lance excepciones en caso de error SQL.
    // Si ya está configurado en 'conexion.php', puedes omitir esta línea.
    $pdo_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    
} catch (Exception $e) {
    // Si la conexión falla, se establece un mensaje de error y se detiene la ejecución.
    $mensaje = "Error al obtener conexión: " . $e->getMessage();
    header("Location: alta_producto.php?status=error&msg=" . urlencode($mensaje));
    exit();
}

// =========================================================================
// 1. PROCESAR INGRESO DE PRODUCTO
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ingresar_producto']) && $pdo_conn) {
    // Recolección y saneamiento básico de datos
    $nombre = $_POST['producto_finalizado_nombre'];
    $descripcion = $_POST['producto_finalizado_descri'];
    $precio = (float)$_POST['producto_finalizado_precio']; 
    $stock = (int)$_POST['stock_actual'];
    $disponible_web = isset($_POST['disponible_web']) ? 1 : 0;
    $imagen_url = isset($_POST['imagen_url']) ? $_POST['imagen_url'] : 'ruta/por/defecto.jpg';

    // Se incluye la columna obligatoria RELA_tematica (asumiendo valor fijo 1)
    $relacion_tematica_default = 1; 
    
    $query = "INSERT INTO producto_finalizado 
                (producto_finalizado_nombre, producto_finalizado_descri, producto_finalizado_precio, stock_actual, disponible_web, imagen_url, RELA_tematica) 
              VALUES (?, ?, ?, ?, ?, ?, ?)"; // 7 placeholders
    
    try {
        // 1. PREPARACIÓN: Crea el objeto $stmt
        $stmt = $pdo_conn->prepare($query); 
        
        // 2. EJECUCIÓN: Se pasan 7 variables (producto, desc, precio, stock, web, imagen, tematica)
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $disponible_web, $imagen_url, $relacion_tematica_default]); 
        
        $titulo = urlencode("Producto Creado");
            $mensaje = urlencode("El producto '{$nombre}' se ha ingresado con éxito.");
            header("Location: ../../includes/mensaje.php?tipo=exito&titulo={$titulo}&mensaje={$mensaje}&redirect_to=../views/productos/productos_finalizados.php&delay=2");
            exit();
    } catch (PDOException $e) {
        $mensaje = "Error al ingresar producto: " . $e->getMessage();
        $status = "error";
    }

    // Redirigir de vuelta a la página de listado (gestion_productos.php)
    header("Location: productos_finalizados.php?status=" . $status . "&msg=" . urlencode($mensaje));
    exit();
}

// =========================================================================
// 2. PROCESAR CAMBIO RÁPIDO DE DISPONIBILIDAD
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_disponibilidad']) && $pdo_conn) {
    // Recolección y saneamiento de datos
    $id_producto = (int)$_POST['id_producto'];
    $nuevo_estado = (int)$_POST['estado']; // 1 (Disponible) o 0 (No Disponible)
    
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
    
    // Redirigir de vuelta a la página de listado (gestion_productos.php)
    header("Location: productos_finalizados.php?status=" . $status . "&msg=" . urlencode($mensaje));
    exit();
}

// Si no se recibió ninguna acción POST conocida, redirigir a la página de gestión.
header("Location: productos_finalizados.php");
exit();
?>