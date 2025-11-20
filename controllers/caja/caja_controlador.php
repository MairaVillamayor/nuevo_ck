<?php
session_start();
require_once("../../models/caja/caja.php");
require_once("../../config/conexion.php");

if (!isset($_POST['action'])) {
    header('Location: ../views/caja/listado_caja.php?page=caja/caja&message=Acción no especificada&status=danger');
    exit;
}

$cajaControlador = new CajaControlador();

switch ($_POST['action']) {
    case 'abrir':
        $cajaControlador->abrir();
        break;
    case 'cerrar':
        $cajaControlador->cerrar();
        break;
}

class CajaControlador
{

    public function abrir()
    {
        $caja = new Caja();

        $caja_abierta = $caja->getCajaAbierta();
        if ($caja_abierta) {
            header('Location: ../../views/caja/listado_caja.php?page=caja/caja&message=Ya hay una caja abierta&status=danger');
            return;
        }

        $monto_efectivo = floatval($_POST['monto_inicial_efectivo'] ?? 0);
        $monto_transferencia = floatval($_POST['monto_inicial_transferencia'] ?? 0);

        if (!isset($_POST['RELA_usuario'])) {
            header('Location: ../../views/caja/listado_caja.php?page=caja/caja&message=Usuario no especificado para la apertura de caja&status=danger');
            return;
        }

        $usuario = $_POST['RELA_usuario'];
        $fecha = date('Y-m-d H:i:s');
        $observaciones = $_POST['observaciones'] ?? '';

        if ($monto_efectivo < 0 || $monto_transferencia < 0) {
            header('Location: ../../views/caja/listado_caja.php?page=caja/caja&message=Los montos iniciales no pueden ser negativos&status=danger');
            return;
        }

        $caja->abrirCaja(
            $usuario,
            $monto_efectivo,
            $monto_transferencia,
            $fecha,
            $observaciones
        );
        header('Location: ../../views/caja/listado_caja.php?page=caja/caja&message=Caja abierta correctamente&status=success');
    }


    public function cerrar()
    {
        $caja = new Caja();
        $caja_abierta = $caja->getCajaAbierta();

        if (!$caja_abierta) {
            $_SESSION['message'] = 'No hay caja abierta para cerrar.';
            $_SESSION['status'] = 'danger';
            header('Location: ../../views/caja/listado_caja.php');
            exit;
           
        }

        if (!$caja_abierta || !isset($caja_abierta['ID_caja'])) {
    die("⚠ No existe una caja abierta.");
}

        $id_caja = $caja_abierta['ID_caja'];

        $cierre_efectivo = floatval($_POST['cierre_efectivo'] ?? 0);
        $cierre_transferencia = floatval($_POST['cierre_transferencia'] ?? 0);

        $diferencia_efectivo = floatval($_POST['diferencia_efectivo'] ?? 0);
        $diferencia_transferencia = floatval($_POST['diferencia_transferencia'] ?? 0);

        $usuario_cierre = intval($_POST['usuario_cierre'] ?? ($_SESSION['usuario_id'] ?? 0));
        $fecha_cierre = $_POST['fecha_cierre'] ?? date('Y-m-d H:i:s');
        $observaciones = $_POST['observaciones'] ?? '';

        $movimientos = $caja->obtenerEgresosIngresosPorMetodo($id_caja);

        $ing_efe   = floatval($movimientos['ingreso_efectivo'] ?? 0);
        $ing_trans = floatval($movimientos['ingreso_transferencia'] ?? 0);
        $eg_efe    = floatval($movimientos['egreso_efectivo'] ?? 0);
        $eg_trans  = floatval($movimientos['egreso_transferencia'] ?? 0);

        // Totales generales
        $total_ingresos = $ing_efe + $ing_trans;
        $total_egresos  = $eg_efe + $eg_trans;

        // ======================================================
        // 📌 Saldos esperados según sistema
        // ======================================================
        $monto_inicial_efectivo = floatval($caja_abierta['caja_monto_inicial_efectivo'] ?? 0);
        $monto_inicial_transferencia = floatval($caja_abierta['caja_monto_inicial_transferencia'] ?? 0);

        $saldo_esperado_efectivo =
        $monto_inicial_efectivo + $ing_efe - $eg_efe;

        $saldo_esperado_transferencia =
        $monto_inicial_transferencia + $ing_trans - $eg_trans;

        $saldo_final_sistema = $saldo_esperado_efectivo + $saldo_esperado_transferencia;

        // ======================================================
        // 📌 Cerrar caja
        // ======================================================
        try {
            $caja->cerrarCaja(
                $id_caja,
                $cierre_efectivo,
                $cierre_transferencia,
                $diferencia_efectivo,
                $diferencia_transferencia,
                $usuario_cierre,
                $fecha_cierre,
                $observaciones,
                $total_ingresos,
                $total_egresos,
                $saldo_final_sistema
            );

            header('Location: ../../views/caja/listado_caja.php?page=caja/caja&message=Caja cerrada correctamente&status=success');

        } catch (Exception $e) {
            echo 'Error al cerrar caja: ' . $e->getMessage();
            var_dump($e
            )   ;
            exit;
        }
    }
}