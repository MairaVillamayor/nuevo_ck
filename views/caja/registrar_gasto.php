<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php?error=not_logged');
    exit;
}

require_once __DIR__ . '/../../models/caja/caja.php';
include ("../../includes/sidebar.php");
include ("../../includes/navegacion.php");

$pdo = getConexion();

$metodos = $pdo->query(" SELECT ID_metodo_pago, metodo_pago_descri 
    FROM metodo_pago
")->fetchAll(PDO::FETCH_ASSOC);

$cajaModel = new Caja();

$caja_abierta = $cajaModel->obtenerCajaAbierta($_SESSION['usuario_id']);

if (!$caja_abierta) {
    echo "
    <script>
    Swal.fire({
        title: '🍰 Caja cerrada',
        text: 'No hay ninguna caja abierta en este momento.',
        icon: 'warning',
        confirmButtonText: 'Volver al listado',
        background: '#fff0f6',
        color: '#c2185b',
        confirmButtonColor: '#ff66b2',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'listado_caja.php';
        }
    });
    </script>
    ";
    exit;
}


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
    <title>Registrar Gasto | Cake Party</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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

        .btn-cake {
            background-color: #f48fb1;
            color: #fff;
            border-radius: 10px;
            border: none;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .btn-cake:hover {
            background-color: #ec407a;
            transform: scale(1.01);
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

        .form-label {
            font-weight: 600;
            color: #c2185b;
        }

        input,
        select,
        textarea {
            border-radius: 10px !important;
            border: 1px solid #f8bbd0 !important;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #ec407a !important;
            box-shadow: 0 0 4px rgba(236, 64, 122, 0.4) !important;
        }
    </style>
</head>

<body class="f">

    <div class="container mt-5">
        <h2 class="mb-4 text-pink fw-bold">🍰 Registrar Nuevo Gasto</h2>

        <a href="listado_gastos.php" class="btn btn-secondary mb-3">⬅ Volver al Listado</a>

        <div class="card p-4 rounded-cake">

            <form action="../../controllers/caja/gasto_controller.php" method="POST">


                <input type="hidden" name="RELA_caja" value="<?= $caja_abierta['ID_caja'] ?>">

                <div class="mb-3">
                    <label class="form-label">Caja:</label>
                    <input type="text" class="form-control" value="Caja N° <?= $caja_abierta['ID_caja'] ?>" readonly>
                </div>


                <div class="mb-3">
                    <label class="form-label">Categoría:</label>
                    <select name="categoria_custom" class="form-select rounded-cake" required>
                        <option value="" disabled selected>Seleccioná una Categoría</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat ?>"><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Método de Pago:</label>
                    <select name="RELA_metodo_pago" class="form-select rounded-cake" required>
                        <option value="" disabled selected>Seleccioná un Método</option>
                        <?php foreach ($metodos as $m): ?>
                            <option value="<?= $m['ID_metodo_pago'] ?>"><?= $m['metodo_pago_descri'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Monto:</label>
                    <input type="number" step="0.01" name="gasto_monto" class="form-control rounded-cake" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción:</label>
                    <textarea name="gasto_descripcion" class="form-control rounded-cake" rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-cake">💾 Guardar Gasto</button>

            </form>
        </div>
    </div>

</body>

</html>