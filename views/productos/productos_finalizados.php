<?php
require_once '../../config/conexion.php'; 
include("../../includes/navegacion.php");

$mensaje = '';


try {
    $pdo_conn = getConexion(); 
} catch (Exception $e) {
    $mensaje = "<div class='alert alert-danger'>Error al obtener conexión: " . $e->getMessage() . "</div>";
    $pdo_conn = null;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ingresar_producto']) && $pdo_conn) {
    $nombre = $_POST['producto_finalizado_nombre'];
    $descripcion = $_POST['producto_finalizado_descri'];
    $precio = $_POST['producto_finalizado_precio'];
    $stock = $_POST['stock_actual'];
    $disponible_web = isset($_POST['disponible_web']) ? 1 : 0;
    
    // Simplificamos la URL de imagen por ahora
    $imagen_url = isset($_POST['imagen_url']) ? $_POST['imagen_url'] : 'ruta/por/defecto.jpg';

    $query = "INSERT INTO producto_finalizado 
                (producto_finalizado_nombre, producto_finalizado_descri, producto_finalizado_precio, stock_actual, disponible_web, imagen_url) 
              VALUES (?, ?, ?, ?, ?, ?)";
    
    try {
        $stmt = $pdo_conn->prepare($query);
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $disponible_web, $imagen_url]);
        $mensaje = "<div class='alert alert-success'>Producto ingresado con éxito.</div>";
    } catch (PDOException $e) {
        $mensaje = "<div class='alert alert-danger'>Error al ingresar producto: " . $e->getMessage() . "</div>";
    }
}

// --- 2. PROCESAR CAMBIO RÁPIDO DE DISPONIBILIDAD ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_disponibilidad']) && $pdo_conn) {
    $id_producto = $_POST['id_producto'];
    $nuevo_estado = $_POST['estado']; // Viene como 1 (Disponible) o 0 (No Disponible)
    
    $query = "UPDATE producto_finalizado SET disponible_web = ? WHERE ID_producto_finalizado = ?";
    
    try {
        $stmt = $pdo_conn->prepare($query);
        $stmt->execute([$nuevo_estado, $id_producto]);
        $mensaje = "<div class='alert alert-success'>Disponibilidad actualizada.</div>";
    } catch (PDOException $e) {
        $mensaje = "<div class='alert alert-danger'>Error al actualizar disponibilidad: " . $e->getMessage() . "</div>";
    }
}

// --- 3. OBTENER LISTADO DE PRODUCTOS ---
$productos = [];
if ($pdo_conn) {
    try {
        $query = "SELECT ID_producto_finalizado, producto_finalizado_nombre, producto_finalizado_precio, 
                         stock_actual, disponible_web 
                  FROM producto_finalizado 
                  ORDER BY ID_producto_finalizado DESC";
        $stmt = $pdo_conn->query($query);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $mensaje .= "<div class='alert alert-danger'>Error al cargar listado: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos Finalizados | Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
    
    <style>
        body {
            background-color: #fff7f9;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 1100px; /* Aumentado ligeramente para la tabla */
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
        .table thead {
            background-color: #f8bbd0;
            color: #333;
        }
        .table td {
             vertical-align: middle;
        }
        .agotado {
            font-weight: bold;
            color: #d32f2f;
        }
        .disponible-si {
            color: #4CAF50;
            font-weight: bold;
        }
        .disponible-no {
            color: #f48fb1;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-secondary mb-5">🍰 Gestión de Productos Finalizados</h1>

        <?php echo $mensaje; ?>

        <h2 class="mt-4">Ingresar Nuevo Producto</h2>
        <form method="POST" action="../../controllers/productos/alta_producto.php" enctype="multipart/form-data" class="mb-5">
        <label for="imagen" class="form-label">Imagen del producto: </label>
        <input type="file" name="imagen_url" class="form-control" accept="image/*" required>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre del Producto</label>
                    <input type="text" class="form-control" id="nombre" name="producto_finalizado_nombre" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="precio" class="form-label">Precio ($)</label>
                    <input type="number" class="form-control" id="precio" name="producto_finalizado_precio" step="0.01" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="stock" class="form-label">Stock Inicial</label>
                    <input type="number" class="form-control" id="stock" name="stock_actual" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="producto_finalizado_descri" rows="2"></textarea>
            </div>
            

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="disponible_web" name="disponible_web" value="1" checked>
                <label class="form-check-label" for="disponible_web">
                    Mostrar Disponible en el Sitio Web
                </label>
            </div>
            
            <button type="submit" name="ingresar_producto" class="btn btn-cake w-100">
                <i class="fas fa-plus-circle"></i> Guardar Producto
            </button>
        </form>

        <hr>
        <h2 class="mt-5">Listado y Gestión de Productos</h2>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Visibilidad Web</th>
                <th>Acción Rápida</th>
                <th>Acciones</th> </tr>
        </thead>
        <tbody>
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['ID_producto_finalizado']); ?></td>
                        <td><?php echo htmlspecialchars($producto['producto_finalizado_nombre']); ?></td>
                        <td>$<?php echo number_format($producto['producto_finalizado_precio'], 2); ?></td>
                        <td>
                            <?php if ($producto['stock_actual'] <= 0): ?>
                                <span class="agotado">0 (AGOTADO)</span>
                            <?php else: ?>
                                <strong><?php echo htmlspecialchars($producto['stock_actual']); ?></strong>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $estado_web = $producto['disponible_web'];
                            $clase = $estado_web == 1 ? 'disponible-si' : 'disponible-no';
                            $texto = $estado_web == 1 ? 'SÍ (Visible)' : 'NO (Oculto)';
                            echo "<span class='{$clase}'>{$texto}</span>"; 
                            ?>
                        </td>
                        <td>
                            <form method="POST" action="" style="display:inline;"> <input type="hidden" name="id_producto" value="<?php echo $producto['ID_producto_finalizado']; ?>">
                                <input type="hidden" name="cambiar_disponibilidad" value="1">
                                <?php if ($producto['disponible_web'] == 1): ?>
                                    <input type="hidden" name="estado" value="0">
                                    <button type="submit" class="btn btn-sm btn-outline-warning">Ocultar de Web</button>
                                <?php else: ?>
                                    <input type="hidden" name="estado" value="1">
                                    <button type="submit" class="btn btn-sm btn-outline-success">Mostrar en Web</button>
                                <?php endif; ?>
                            </form>
                        </td>
                        <td>
                            <a href="../../controllers/productos/editar_productos.php?id=<?php echo $producto['ID_producto_finalizado']; ?>" class="btn btn-sm btn-info btn-cake">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <form action="../../controllers/productos/eliminar.php" method="post" style="display:inline;">
                            <input type="hidden" name="id_producto_finalizado" value="<?php echo $row['id_producto_finalizado']; ?>">
                            <button class="btn-action btn-delete" type="submit" onclick="return confirmarEliminacion('¿Eliminar definitivamente este producto?');">❌ Eliminar</button>
                        </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">No hay productos finalizados registrados.</td> </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/tu_kit_id.js" crossorigin="anonymous"></script> 
</body>
</html>