<?php
require_once '../../config/conexion.php'; 
include("../../includes/navegacion.php");

$mensaje = '';
$producto = null; 

try {
    $pdo_conn = getConexion(); 
} catch (Exception $e) {
    $mensaje = "<div class='alert alert-danger'>Error al obtener conexión: " . $e->getMessage() . "</div>";
    $pdo_conn = null;
}

if (!$pdo_conn || !isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $mensaje .= "<div class='alert alert-danger'>ID de producto no válido o no especificado.</div>";
} else {
    $id_producto = (int)$_GET['id'];
    
    try {
        $query_select = "SELECT ID_producto_finalizado, producto_finalizado_nombre, producto_finalizado_descri, 
                         producto_finalizado_precio, stock_actual, disponible_web, imagen_url 
                         FROM producto_finalizado 
                         WHERE ID_producto_finalizado = ?";
        $stmt_select = $pdo_conn->prepare($query_select);
        $stmt_select->execute([$id_producto]);
        $producto = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            $mensaje .= "<div class='alert alert-warning'>Producto no encontrado.</div>";
        }
    } catch (PDOException $e) {
        $mensaje .= "<div class='alert alert-danger'>Error al cargar datos del producto: " . $e->getMessage() . "</div>";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_producto']) && $pdo_conn) {
    
    $id_actualizar = $_POST['id_producto_finalizado'];
    $nombre = $_POST['producto_finalizado_nombre'];
    $descripcion = $_POST['producto_finalizado_descri'];
    $precio = $_POST['producto_finalizado_precio'];
    $stock = $_POST['stock_actual'];
    $disponible_web = isset($_POST['disponible_web']) ? 1 : 0;
    $imagen_url = $_POST['imagen_url'];

    $query_update = "UPDATE producto_finalizado SET 
                        producto_finalizado_nombre = ?, 
                        producto_finalizado_descri = ?, 
                        producto_finalizado_precio = ?, 
                        stock_actual = ?, 
                        disponible_web = ?, 
                        imagen_url = ?
                     WHERE ID_producto_finalizado = ?";
    
    try {
        $stmt_update = $pdo_conn->prepare($query_update);
        $stmt_update->execute([$nombre, $descripcion, $precio, $stock, $disponible_web, $imagen_url, $id_actualizar]);
        
        $mensaje = "<div class='alert alert-success'>Producto actualizado con éxito.</div>";
        
        $query_select_after_update = "SELECT ID_producto_finalizado, producto_finalizado_nombre, producto_finalizado_descri, 
                                      producto_finalizado_precio, stock_actual, disponible_web, imagen_url 
                                      FROM producto_finalizado 
                                      WHERE ID_producto_finalizado = ?";
        $stmt_select_after_update = $pdo_conn->prepare($query_select_after_update);
        $stmt_select_after_update->execute([$id_actualizar]);
        $producto = $stmt_select_after_update->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $mensaje = "<div class='alert alert-danger'>Error al actualizar producto: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto | Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #fff7f9;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
            margin-bottom: 50px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .btn-cake {
            background-color: #f48fb1;
            color: #fff;
            border-radius: 10px;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-cake:hover {
            background-color: #ec407a;
            color: #fff;
        }
        h2 {
            color: #e91e63;
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f8bbd0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-secondary mb-5">✏️ Editar Producto: #<?php echo htmlspecialchars($producto['ID_producto_finalizado'] ?? 'N/A'); ?></h1>

        <?php echo $mensaje; ?>

        <?php if ($producto): ?>
        <h2 class="mt-4"><?php echo htmlspecialchars($producto['producto_finalizado_nombre']); ?></h2>
        
        <form method="POST" action="">
            <input type="hidden" name="id_producto_finalizado" value="<?php echo htmlspecialchars($producto['ID_producto_finalizado']); ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre del Producto</label>
                    <input type="text" class="form-control" id="nombre" name="producto_finalizado_nombre" 
                           value="<?php echo htmlspecialchars($producto['producto_finalizado_nombre']); ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="precio" class="form-label">Precio ($)</label>
                    <input type="number" class="form-control" id="precio" name="producto_finalizado_precio" step="0.01" 
                           value="<?php echo htmlspecialchars($producto['producto_finalizado_precio']); ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="stock" class="form-label">Stock Actual</label>
                    <input type="number" class="form-control" id="stock" name="stock_actual" 
                           value="<?php echo htmlspecialchars($producto['stock_actual']); ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="producto_finalizado_descri" rows="3"><?php echo htmlspecialchars($producto['producto_finalizado_descri']); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="imagen_url" class="form-label">Ruta/URL de Imagen</label>
                <input type="text" class="form-control" id="imagen_url" name="imagen_url" 
                       value="<?php echo htmlspecialchars($producto['imagen_url']); ?>">
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="disponible_web" name="disponible_web" value="1" 
                       <?php echo $producto['disponible_web'] == 1 ? 'checked' : ''; ?>>
                <label class="form-check-label" for="disponible_web">
                    Mostrar Disponible en el Sitio Web
                </label>
            </div>
            
            <button type="submit" name="actualizar_producto" class="btn btn-cake w-100 mb-3">
                <i class="fas fa-save"></i> Actualizar Producto
            </button>
            <a href="../../views/productos/productos_finalizados.php" class="btn btn-outline-secondary w-100">Volver al Listado</a>
        </form>

        <?php elseif (!$mensaje): ?>
            <div class="alert alert-info text-center">Cargando datos...</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>