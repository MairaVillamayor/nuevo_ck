<?php
session_start();

require_once __DIR__ . '/../../models/caja/gastos.php';
require_once __DIR__ . '/../../config/conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $pdo = getConexion();

        if (!isset($_POST['RELA_caja']) || empty($_POST['RELA_caja'])) {
            die("Error: Falta la referencia a la caja.");
        }

        $categoria_nombre = $_POST['categoria_custom'];

        $stmt = $pdo->prepare("SELECT ID_categoria FROM categoria WHERE categoria_nombre = :nombre");
        $stmt->execute([':nombre' => $categoria_nombre]);
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$categoria) {
            $insert = $pdo->prepare("INSERT INTO categoria (categoria_nombre) VALUES (:nombre)");
            $insert->execute([':nombre' => $categoria_nombre]);
            $categoria_id = $pdo->lastInsertId();
        } else {
            $categoria_id = $categoria['ID_categoria'];
        }

        if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {

            if (!isset($_POST['ID_gasto'])) {
                die("Error: no se recibió el ID del gasto");
            }

            $ID_gasto = $_POST['ID_gasto'];

            $gastoModel = new Gastos();

            $gastoModel->actualizar(
                $ID_gasto,
                $_POST['gasto_monto'],
                $_POST['gasto_descripcion'],
                $categoria_id,
                $_POST['RELA_metodo_pago']
            );

            $_SESSION['message'] = "Gasto actualizado correctamente.";
            $_SESSION['status'] = "success";

            header("Location: ../../views/caja/listado_gastos.php");
            exit;
        }


        $gasto = new Gastos([
            'RELA_caja'         => $_POST['RELA_caja'],
            'RELA_categoria'    => $categoria_id,
            'RELA_metodo_pago'  => $_POST['RELA_metodo_pago'],
            'gasto_fecha'        => date("Y-m-d H:i:s"),
            'gasto_monto'        => $_POST['gasto_monto'],
            'gasto_descripcion'  => $_POST['gasto_descripcion']
        ]);

        $ultimoID = $gasto->guardar();

        $insertMov = $pdo->prepare("INSERT INTO movimiento_caja 
            (RELA_caja, RELA_usuario, RELA_metodo_pago, movimiento_tipo, movimiento_monto, movimiento_descripcion)
            VALUES 
            (:id_caja, :usuario, :metodo, 'egreso', :monto, :descripcion)
        ");

        $insertMov->execute([
            ':id_caja'     => $_POST['RELA_caja'],
            ':usuario'     => $_SESSION['usuario_id'],
            ':metodo'      => $_POST['RELA_metodo_pago'],
            ':monto'       => $_POST['gasto_monto'],
            ':descripcion' => $_POST['gasto_descripcion']
        ]);

        $updateCaja = $pdo->prepare("
            UPDATE caja 
            SET caja_total_egresos = caja_total_egresos + :monto
            WHERE ID_caja = :id_caja
        ");

        $updateCaja->execute([
            ':monto'    => $_POST['gasto_monto'],
            ':id_caja'  => $_POST['RELA_caja']
        ]);

        $_SESSION['message'] = "Gasto registrado correctamente (ID $ultimoID).";
        $_SESSION['status'] = "success";

        header("Location: ../../views/caja/listado_gastos.php");
        exit;

    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
        exit;
    }
}

