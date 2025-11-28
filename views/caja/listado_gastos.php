<?php
session_start();
require_once __DIR__ . '/../../models/caja/gastos.php';

include("../../includes/sidebar.php");
include("../../includes/navegacion.php");

$gastosModel = new Gastos();
$categorias = $gastosModel->traerCategorias();
$metodos = $gastosModel->traerMetodosPago();


$filtros = [
    'categoria'  => $_GET['categoria'] ?? null,
    'metodo'      => $_GET['metodo'] ?? null,
    'fecha_desde' => $_GET['fecha_desde'] ?? null,
    'fecha_hasta' => $_GET['fecha_hasta'] ?? null,
    'texto'       => $_GET['texto'] ?? null
];

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = 8;
$offset = ($pagina - 1) * $limite;

$total = $gastosModel->contarFiltrados($filtros);
$total_paginas = ceil($total / $limite);

$gastos = $gastosModel->filtrarConPaginacion($filtros, $offset, $limite);

if (isset($_GET['eliminar_id'])) {
    $ID_gasto = $_GET['eliminar_id'];

    if ($gastosModel->eliminar($ID_gasto)) {
        $_SESSION['message'] = "El gasto N° $ID_gasto ha sido eliminado correctamente.";
        $_SESSION['status'] = "success";
    } else {
        $_SESSION['message'] = "Error al intentar eliminar el gasto N° $ID_gasto.";
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
    <title>Listado de Gastos | Cake Party</title>
    <link rel="stylesheet" href="../../public/css/style.css">

    <style>
        body {
            background: #fff5f8;
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            padding: 40px;
        }

        /* ENCABEZADO */
        .header-gastos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        h2 {
            color: #ff2d8f;
        }

        /* BOTÓN CREAR */
        .btn-cake {
            background: #ff4fa3;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }

        .btn-cake:hover {
            background: #ff2d8f;
        }

        .filtros-contenedor {
            background: white;
            width: 95%;
            margin: 30px auto;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border-left: 6px solid #ff4f9a;
        }

        .filtros-form {
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }

        .campo {
            display: flex;
            flex-direction: column;
            min-width: 180px;
            gap: 5px;
        }

        .campo label {
            font-size: 14px;
            font-weight: 500;
        }

        .campo select,
        .campo input {
            padding: 10px 14px;
            border-radius: 20px;
            border: 1.5px solid #ffb6d3;
            outline: none;
            min-width: 180px;
        }

        .campo-botones {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-buscar {
            background: #ff4f9a;
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 25px;
            cursor: pointer;
        }

        .btn-buscar:hover {
            background: #ff2f85;
        }

        .btn-limpiar {
            background: #9e9e9e;
            color: white;
            text-decoration: none;
            padding: 10px 22px;
            border-radius: 25px;
            display: inline-block;
        }

        .btn-limpiar:hover {
            background: #7e7e7e;
        }

        /* Responsive (cuando la pantalla es chica) */
        @media (max-width: 768px) {
            .filtros-form {
                flex-direction: column;
                align-items: center;
            }

            .campo,
            .campo-botones {
                width: 100%;
                max-width: 300px;
            }

            .campo select,
            .campo input {
                width: 100%;
            }
        }


        /* TABLA */
        .card-cake {
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #ff8fb8;
            color: white;
        }

        thead th,
        tbody td {
            padding: 12px;
            text-align: center;
        }

        tbody tr:hover {
            background: #fff1f6;
        }

        .badge {
            background: #ffd5e5;
            padding: 4px 10px;
            border-radius: 14px;
            font-size: 13px;
        }

        /* BOTONES ACCIÓN */
        .btn-outline {
            border: 2px solid #ff4fa3;
            background: transparent;
            color: #ff4fa3;
            padding: 5px 10px;
            border-radius: 12px;
            cursor: pointer;
        }

        /* PAGINACIÓN */
        .paginacion {
            margin-top: 25px;
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .paginacion a {
            padding: 8px 14px;
            border-radius: 10px;
            text-decoration: none;
            border: 1px solid #ffd5e5;
            color: #ff2d8f;
            font-weight: bold;
        }

        .paginacion a.activo,
        .paginacion a:hover {
            background: #ff2d8f;
            color: white;
        }
    </style>
</head>

<body>

    <div class="container">


        <div class="header-gastos">
            <h2>💸 Listado de Gastos</h2>
            <div>
                <button id="btnReportes" class="btn-cake">📊 Reportes</button>
                <a href="registrar_gasto.php" class="btn-cake">+ Registrar Gasto</a>
            </div>
        </div>

        <div class="filtros-contenedor">
            <form method="GET" class="filtros-form">

                <div class="campo">
                    <label>Categoría</label>
                    <select name="categoria">
                        <option value="">Todas</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['ID_categoria'] ?>">
                                <?= $cat['categoria_nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label>Método de pago</label>
                    <select name="metodo">
                        <option value="">Todos</option>
                        <?php foreach ($metodos as $m): ?>
                            <option value="<?= $m['ID_metodo_pago'] ?>">
                                <?= $m['metodo_pago_descri'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label>Desde</label>
                    <input type="date" name="desde">
                </div>

                <div class="campo">
                    <label>Hasta</label>
                    <input type="date" name="hasta">
                </div>

                <div class="campo-botones">
                    <button type="submit" class="btn-buscar">Buscar</button>
                    <a href="listado_gastos.php" class="btn-limpiar">Limpiar</a>
                </div>

            </form>
        </div>


        <!-- TABLA -->
        <div class="card-cake">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Pago</th>
                        <th>Caja</th>
                        <th>Editar</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($gastos)): ?>
                        <tr>
                            <td colspan="8">No hay gastos</td>
                        </tr>
                        <?php else: foreach ($gastos as $g): ?>
                            <tr>
                                <td><?= $g['ID_gasto'] ?></td>
                                <td><?= date('d/m/Y', strtotime($g['gasto_fecha'])) ?></td>
                                <td style="color:#c2185b; font-weight:bold;">- $<?= number_format($g['gasto_monto'], 2) ?></td>
                                <td><?= $g['gasto_descripcion'] ?></td>
                                <td><span class="badge"><?= $g['categoria_nombre'] ?></span></td>
                                <td><?= $g['metodo_pago_descri'] ?></td>
                                <td><span class="badge">Caja <?= $g['RELA_caja'] ?></span></td>
                                <td>
                                    <a class="btn-outline" href="editar_gasto.php?id_gasto=<?= $g['ID_gasto'] ?>">✏</a>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>

        <div class="paginacion">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a class="<?= $i == $pagina ? 'activo' : '' ?>"
                    href="?pagina=<?= $i ?>&<?= http_build_query($filtros) ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>

    </div>
    <div id="modalReportes" class="modal-reportes">
        <div class="modal-contenido">
            <span class="cerrar">&times;</span>
            <iframe src="../reportes/gastos_reportes.php"></iframe>
        </div>
    </div>

    <style>
        .modal-reportes {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .modal-contenido {
            background: #fff0f6;
            width: 90%;
            max-width: 900px;
            height: 85%;
            border-radius: 20px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .modal-contenido iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 15px;
        }

        .cerrar {
            font-size: 24px;
            color: #ff2d8f;
            font-weight: bold;
            cursor: pointer;
            text-align: right;
        }
    </style>

    <script>
        const btn = document.getElementById('btnReportes');
        const modal = document.getElementById('modalReportes');
        const cerrar = document.querySelector('.cerrar');

        btn.onclick = () => modal.style.display = "flex";
        cerrar.onclick = () => modal.style.display = "none";

        window.onclick = (e) => {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>


</body>

</html>