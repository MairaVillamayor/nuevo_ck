<?php
require_once '../../config/conexion.php';
include("../../includes/navegacion.php");

session_start();

// Conexión
try {
    $pdo_conn = getConexion();
} catch (Exception $e) {
    $_SESSION['message'] = "Error al obtener conexión: " . $e->getMessage();
    $_SESSION['status'] = "danger";
    $pdo_conn = null;
}

// --- ALTA DE PRODUCTO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ingresar_producto']) && $pdo_conn) {

    $nombre = $_POST['producto_finalizado_nombre'];
    $descripcion = $_POST['producto_finalizado_descri'];
    $precio = $_POST['producto_finalizado_precio'];
    $stock = $_POST['stock_actual'];
    $disponible_web = isset($_POST['disponible_web']) ? 1 : 0;
echo "<pre>";
print_r($_FILES);
echo "</pre>";

    //  $imagen_url = isset($_POST['imagen_url']) ? $_POST['imagen_url'] : 'default.jpg';
    $imagen_url = "default.jpg";

    if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] === 0) {

        $nombreImagen = time() . "_" . basename($_FILES['imagen_producto']['name']);
        $rutaDestino = $_SERVER['DOCUMENT_ROOT'] . "/nuevo_ck/assets/upgrades" . $nombreImagen;


        $extension = strtolower(pathinfo($rutaDestino, PATHINFO_EXTENSION));
        $permitidas = ["jpg", "jpeg", "png", "gif", "webp"];

        if (in_array($extension, $permitidas)) {

            if (move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $rutaDestino)) {
                $imagen_url = $nombreImagen;
            }
        }
    }


    $query = "INSERT INTO producto_finalizado 
        (producto_finalizado_nombre, producto_finalizado_descri, producto_finalizado_precio, stock_actual, disponible_web, imagen_url) 
        VALUES (?, ?, ?, ?, ?, ?)";

    try {
        $stmt = $pdo_conn->prepare($query);
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $disponible_web, $imagen_url]);

        $_SESSION['message'] = "Producto ingresado con éxito.";
        $_SESSION['status'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error al ingresar producto: " . $e->getMessage();
        $_SESSION['status'] = "danger";
    }
}

// --- CAMBIO RÁPIDO DE DISPONIBILIDAD ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_disponibilidad']) && $pdo_conn) {

    $id = $_POST['id_producto'];
    $estado = $_POST['estado'];

    $query = "UPDATE producto_finalizado SET disponible_web = ? WHERE ID_producto_finalizado = ?";

    try {
        $stmt = $pdo_conn->prepare($query);
        $stmt->execute([$estado, $id]);

        $_SESSION['message'] = "Disponibilidad actualizada.";
        $_SESSION['status'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error al actualizar disponibilidad: " . $e->getMessage();
        $_SESSION['status'] = "danger";
    }
}

// --- LISTADO ---
// --- PAGINACIÓN ---
$por_pagina = 6; // productos por página
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;

$inicio = ($pagina - 1) * $por_pagina;

// Total de productos
$total_productos = 0;
$total_paginas = 1;

$productos = [];

if ($pdo_conn) {
    try {

        // TOTAL DE REGISTROS
        $totalQuery = $pdo_conn->query("SELECT COUNT(*) FROM producto_finalizado");
        $total_productos = $totalQuery->fetchColumn();

        $total_paginas = ceil($total_productos / $por_pagina);

        // DATOS CON LIMITE
        $query = "SELECT 
                    ID_producto_finalizado,
                    producto_finalizado_nombre,
                    producto_finalizado_precio,
                    stock_actual,
                    imagen_url,
                    disponible_web
                  FROM producto_finalizado
                  ORDER BY ID_producto_finalizado DESC
                  LIMIT $inicio, $por_pagina";

        $stmt = $pdo_conn->query($query);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error al cargar listado.";
        $_SESSION['status'] = "danger";
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos Finalizados</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="../../public/css/productos.css">

    <style>
        /* --- ESTILO CAKE PARTY GLOBAL --- */
        body {
            background-color: #fff7f9;
            font-family: 'Poppins', sans-serif;
            margin: 0;
        }

        .container {
            width: 95%;
            max-width: 1100px;
            margin: 40px auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        /* --- TÍTULOS --- */
        h1 {
            text-align: center;
            color: #e91e63;
            font-weight: 700;
            margin-bottom: 10px;
        }

        h2 {
            color: #d81b60;
            border-left: 6px solid #f8bbd0;
            padding-left: 10px;
            margin-top: 40px;
        }

        /* --- FORMULARIO --- */
        input,
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            border: 2px solid #f8bbd0;
            font-size: 15px;
            margin-bottom: 15px;
        }

        label {
            font-weight: 600;
            color: #c2185b;
        }

        .btn-cake {
            width: 100%;
            background: #f48fb1;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-cake:hover {
            background: #ec407a;
        }

        /* --- TABLA --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        thead {
            background-color: #f8bbd0;
        }

        thead th {
            padding: 12px;
            font-size: 15px;
            color: #5a4a4a;
        }

        tbody td {
            padding: 12px;
            text-align: center;
        }

        tbody tr:hover {
            background-color: #fde4ef;
        }

        /* --- BOTONES --- */
        .btn-cake-primary {
            padding: 7px 14px;
            background-color: #f8bbd0;
            border: 2px solid #f48fb1;
            color: #d81b60;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-cake-primary:hover {
            background-color: #f48fb1;
            color: white;
        }

        .btn-cake-danger {
            padding: 7px 14px;
            background-color: #fce4ec;
            border: 2px solid #ec407a;
            color: #c2185b;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-cake-danger:hover {
            background-color: #ec407a;
            color: white;
        }

        /* --- ESTADOS --- */
        .agotado {
            color: #d32f2f;
            font-weight: bold;
        }

        .disponible-si {
            color: #43a047;
            font-weight: bold;
        }

        .disponible-no {
            color: #e91e63;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include("../../includes/header.php"); ?>
    <div class="container">

        <h1>🍰 Gestión de Productos Finalizados</h1>

        <!-- ALTA -->
        <h2>Ingresar Producto</h2>

        <form method="POST">
            <label>Nombre:</label>
            <input type="text" name="producto_finalizado_nombre" required>

            <label>Precio:</label>
            <input type="number" name="producto_finalizado_precio" required>

            <label>Stock Inicial:</label>
            <input type="number" name="stock_actual" required>

            <label>Descripción:</label>
            <textarea name="producto_finalizado_descri"></textarea>

            <label>Imagen del producto:</label>
            <input type="file" name="imagen_producto" accept="image/*">


            <label>
                <input type="checkbox" name="disponible_web" checked>
                Mostrar en Web
            </label>

            <button class="btn-cake" type="submit" name="ingresar_producto">
                Guardar Producto
            </button>
        </form>

        <hr>

        <h2>Listado de Productos</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Web</th>
                    <th>Acción</th>
                    <th>Opciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td><?= $p['ID_producto_finalizado'] ?></td>
                        <td>
                            <img src="../../assets/upgrades?= $p['imagen_url'] ?>"
                                style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                        </td>

                        <td><?= htmlspecialchars($p['producto_finalizado_nombre']) ?></td>
                        <td>$<?= number_format($p['producto_finalizado_precio'], 2) ?></td>

                        <td>
                            <?php if ($p['stock_actual'] <= 0): ?>
                                <span class="agotado">AGOTADO</span>
                            <?php else: ?>
                                <strong><?= $p['stock_actual'] ?></strong>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= $p['disponible_web']
                                ? '<span class="disponible-si">Visible</span>'
                                : '<span class="disponible-no">Oculto</span>' ?>
                        </td>

                        <td>
                            <form method="POST">
                                <input type="hidden" name="id_producto" value="<?= $p['ID_producto_finalizado'] ?>">
                                <input type="hidden" name="cambiar_disponibilidad" value="1">
                                <input type="hidden" name="estado" value="<?= $p['disponible_web'] ? 0 : 1 ?>">
                                <button class="btn-cake-primary">
                                    <?= $p['disponible_web'] ? 'Ocultar' : 'Mostrar' ?>
                                </button>
                            </form>
                        </td>

                        <td>
                            <a href="../../controllers/productos/editar_productos.php?id=<?= $p['ID_producto_finalizado'] ?>"
                                class="btn-cake-primary">Editar</a>

                            <button class="btn-cake-danger"
                                onclick="confirmarEliminacion(<?= $p['ID_producto_finalizado'] ?>)">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- PAGINACIÓN -->
        <div style="margin-top:30px; text-align:center;">

            <?php if ($pagina > 1): ?>
                <a class="btn-cake-primary"
                    href="?pagina=<?= $pagina - 1 ?>">
                    « Anterior
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a class="btn-cake-primary"
                    style="<?= $i == $pagina ? 'background:#ec407a;color:white;' : '' ?>"
                    href="?pagina=<?= $i ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($pagina < $total_paginas): ?>
                <a class="btn-cake-primary"
                    href="?pagina=<?= $pagina + 1 ?>">
                    Siguiente »
                </a>
            <?php endif; ?>

        </div>

    </div>

    <script>
        function confirmarEliminacion(id) {
            Swal.fire({
                title: "¿Eliminar producto?",
                text: "Esta acción no se puede deshacer.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#e91e63",
            }).then((result) => {
                if (result.isConfirmed) {

                    const form = document.createElement("form");
                    form.method = "POST";
                    form.action = "../../controllers/productos/eliminarProducto.php";

                    const input = document.createElement("input");
                    input.type = "hidden";
                    input.name = "id_producto_finalizado";
                    input.value = id;

                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

</body>

</html>