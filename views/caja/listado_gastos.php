<?php
require_once __DIR__ . '/../../models/caja/Gastos.php';

include("../../includes/header.php");
include("../../includes/navegacion.php");

$gastosModel = new Gastos();
$gastos = $gastosModel->traerGastos();

if (isset($_GET['eliminar_id'])) {
    $id_a_eliminar = $_GET['eliminar_id'];


    if ($gastosModel->eliminar($id_a_eliminar)) {
        session_start();
        $_SESSION['message'] = "El gasto N° $id_a_eliminar ha sido eliminado correctamente.";
        $_SESSION['status'] = "success";
    } else {
        session_start();
        $_SESSION['message'] = "Error al intentar eliminar el gasto N° $id_a_eliminar.";
        $_SESSION['status'] = "danger";
    }
    header('Location: listado_gastos.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Gastos | Cake Party</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        thead.table-cakeparty {
            background: #ff79c6 !important;
            color: white !important;
        }

        .table thead th {
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .table tbody td {
            font-size: 0.9rem;
        }

        .text-pink {
            color: #d63384;
        }
         .btn-primary {
            background-color: #ff6b81;
            border-color: #ff6b81;
            
        }
        .btn-primary:hover {
            background-color: #fabec7ff;
            border-color: #fabec7ff;
        }
        .btn-outline-primary {
            color: #ff6b81;
            border-color: #ff6b81;
        }
        .btn-outline-primary:hover {
            background-color: #ff6b81;
            color: #ff6b81;
        }
    </style>
</head>

<body>
    <!-- ALERTA CAKE PARTY -->
    <div id="cakePartyAlert" class="cakeparty-overlay">
        <div class="cakeparty-box">
            <h3 id="cpTitle">Título</h3>
            <p id="cpText">Mensaje</p>
            <a id="cpButton" href="listado_gastos.php" class="btn-cake">Aceptar</a>
        </div>
    </div>

    <style>
        .cakeparty-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, .45);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 99999;
        }

        .cakeparty-box {
            background: #ffe4ef;
            padding: 30px;
            width: 350px;
            border-radius: 18px;
            text-align: center;
            border: 2px solid #f8a1c4;
            animation: popin .25s ease-out;
            color: #c2185b;
        }

        @keyframes popin {
            from {
                transform: scale(.5);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .btn-cake {
            display: inline-block;
            background: #ff66b2;
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: bold;
            text-decoration: none;
            transition: .2s;
        }

        .btn-cake:hover {
            background: #ff4da6;
        }
    </style>

    <script>
        function showCakeAlert(titulo, mensaje, botonUrl = "listado_gastos.php") {
            document.getElementById("cpTitle").textContent = titulo;
            document.getElementById("cpText").textContent = mensaje;
            document.getElementById("cpButton").href = botonUrl;

            document.getElementById("cakePartyAlert").style.display = "flex";

            // 🔥 AUTO-REDIRECCIÓN A LOS 2.2 SEGUNDOS
            setTimeout(() => {
                window.location.href = botonUrl;
            }, 2200);
        }
    </script>


    <?php

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }



    if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
        $msg = htmlspecialchars($_SESSION['message']);
        $status = $_SESSION['status'];

        $titulo = ($status === "success") ? "✔ ¡Éxito!" : "❌ Error";

        echo "
    <script>
        showCakeAlert('$titulo', '$msg');
    </script>
    ";

        unset($_SESSION['message']);
        unset($_SESSION['status']);
    }
    ?>
    <div class="container-fluid mt-5">
        <h2 class="mb-4 text-pink">💸 Listado de Gastos</h2>

        <div class="mb-3 d-flex gap-2">
            <a href="listado_caja.php" class="btn btn-secondary">Volver a Cajas</a>
            <a href="registrar_gasto.php" class="btn btn-primary">Registrar Nuevo Gasto</a>
        </div>

        <div class="card shadow-sm p-3 bg-white rounded-4">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-cakeparty text-center">
                        <tr>
                            <th>ID</th>
                            <th>Fecha y Hora</th>
                            <th>Monto</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th>Método de Pago</th>
                            <th>Caja N°</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($gastos)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <span class="text-muted fs-5">🚫 No hay gastos registrados aún.</span>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($gastos as $gasto): ?>
                                <tr class="text-center">
                                    <td><?= htmlspecialchars($gasto['ID_gasto'] ?? '-') ?></td>
                                    <td><?= date('d-m-Y H:i', strtotime($gasto['gasto_fecha'] ?? '')) ?></td>
                                    <td class="text-danger fw-bold">$<?= number_format($gasto['gasto_monto'] ?? 0, 2) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($gasto['gasto_descripcion'] ?? '') ?></td>

                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($gasto['categoria_nombre'] ?? 'N/A') ?></span></td>
                                    <td><?= htmlspecialchars($gasto['metodo_pago_descri'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($gasto['RELA_caja'] ?? 'N/A') ?></td>

                                    
                                    <td class="d-flex justify-content-center gap-2">

                                        <a href="editar_gasto.php?id=<?= $gasto['ID_gasto'] ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            ✏️ Editar
                                        </a>

                                        <a href="listado_gastos.php?eliminar_id=<?= $gasto['ID_gasto'] ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar el gasto N° <?= $gasto['ID_gasto'] ?>? Esta acción no se puede deshacer.')">
                                            🗑️ Eliminar
                                        </a>

                                    </td>

                                </tr>

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>