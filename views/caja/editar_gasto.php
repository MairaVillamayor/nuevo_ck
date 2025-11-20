<?php 
include ("../../includes/header.php");
include ("../../includes/navegacion.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php?error=not_logged');
    exit;
}

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/caja/gastos.php';

$pdo = getConexion();
$gastosModel = new Gastos();

// Obtener ID del gasto a editar
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: listado_gastos.php?error=no_id");
    exit;
}

$id = intval($_GET['id']);
$gasto = $gastosModel->traerPorId($id);

if (!$gasto) {
    echo "<div class='container mt-5'><div class='alert alert-danger text-center'>
            ❌ No se encontró el gasto solicitado.
            <br><a href='listado_gastos.php' class='btn btn-primary mt-3'>Volver</a>
        </div></div>";
    exit;
}

// Métodos de pago
$metodos = $pdo->query("SELECT ID_metodo_pago, metodo_pago_descri FROM metodo_pago")->fetchAll(PDO::FETCH_ASSOC);

// Categorías (simples como en crear gasto)
$categorias = [
    'Servicios',
    'Insumos',
    'Transporte',
    'Electricidad',
    'Internet',
    'Herramientas',
    'Otros'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Gasto | Cake Party</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #fff7fa;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            max-width: 700px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 35px;
            margin: 40px auto;
        }

        h2 {
            color: #e91e63;
            font-weight: 600;
        }

        .form-label {
            font-weight: 600;
            color: #c2185b;
        }

        input, select, textarea {
            border-radius: 10px !important;
            border: 1px solid #f8bbd0 !important;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #ec407a !important;
            box-shadow: 0 0 4px rgba(236, 64, 122, 0.4) !important;
        }

        .btn-primary {
            background-color: #ff4081;
            border: none;
            border-radius: 10px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #e91e63;
        }

        .btn-outline-secondary {
            color: #e91e63;
            border-color: #f8bbd0;
            border-radius: 10px;
        }

        .btn-outline-secondary:hover {
            background-color: #f8bbd0;
            color: #c2185b;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 class="mb-4 fw-bold">📝 Editar Gasto</h2>

        <a href="listado_gastos.php" class="btn btn-outline-secondary mb-3">⬅ Volver al Listado</a>

        <div class="card p-4">

            <form action="../../controllers/caja/gasto_controller.php" method="POST">

                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="ID_gasto" value="<?= $id ?>">

                <div class="mb-3">
                    <label class="form-label">Categoría:</label>
                    <select name="categoria_custom" class="form-select" required>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat ?>" <?= ($cat == $gasto['categoria_nombre']) ? 'selected' : '' ?>>
                                <?= $cat ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Método de Pago:</label>
                    <select name="RELA_metodo_pago" class="form-select" required>
                        <?php foreach ($metodos as $m): ?>
                            <option value="<?= $m['ID_metodo_pago'] ?>" 
                                <?= ($m['ID_metodo_pago'] == $gasto['RELA_metodo_pago']) ? 'selected' : '' ?>>
                                <?= $m['metodo_pago_descri'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Monto:</label>
                    <input type="number" step="0.01" name="gasto_monto" 
                           value="<?= $gasto['gasto_monto'] ?>" 
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción:</label>
                    <textarea name="gasto_descripcion" class="form-control" rows="3" required><?= $gasto['gasto_descripcion'] ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">💾 Guardar Cambios</button>

            </form>

        </div>
    </div>

</body>
</html>
