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
            header('Location: ../../views/caja/listado_caja.php?page=caja/caja&message=Usuario no especificado&status=danger');
            return;
        }

        $usuario = $_POST['RELA_usuario'];
        $fecha = date('Y-m-d H:i:s');
        $observaciones = $_POST['observaciones'] ?? '';

        if ($monto_efectivo < 0 || $monto_transferencia < 0) {
            header('Location: ../../views/caja/listado_caja.php?page=caja/caja&message=Los montos iniciales no pueden ser negativos&status=danger');
            return;
        }

        $caja->abrirCaja($usuario, $monto_efectivo, $monto_transferencia, $fecha, $observaciones);
        header('Location: ../../views/caja/listado_caja.php?page=caja/caja&message=Caja abierta correctamente&status=success');
    }

    public function cerrar()
    {
        $caja = new Caja();
        $caja_abierta = $caja->getCajaAbierta();

        if (!$caja_abierta || !isset($caja_abierta['ID_caja'])) {
             header('Location: ../../views/caja/listado_caja.php?message=No existe caja abierta&status=danger');
             exit;
        }

        $id_caja = $caja_abierta['ID_caja'];

        $cierre_efectivo = floatval($_POST['cierre_efectivo'] ?? 0);
        $cierre_transferencia = floatval($_POST['cierre_transferencia'] ?? 0);

        $usuario_cierre = intval($_POST['usuario_cierre'] ?? ($_SESSION['usuario_id'] ?? 0));
        $fecha_cierre = $_POST['fecha_cierre'] ?? date('Y-m-d H:i:s');
        $observaciones = $_POST['observaciones'] ?? '';

        $movimientos = $caja->obtenerEgresosIngresosPorMetodo($id_caja);

        $ingreso_efectivo       = floatval($movimientos['ingreso_efectivo'] ?? 0);
        $ingreso_transferencia  = floatval($movimientos['ingreso_transferencia'] ?? 0);
        $egreso_efectivo        = floatval($movimientos['egreso_efectivo'] ?? 0);
        $egreso_transferencia   = floatval($movimientos['egreso_transferencia'] ?? 0);

        $total_ingresos = $ingreso_efectivo + $ingreso_transferencia;
        $total_egresos  = $egreso_efectivo + $egreso_transferencia;

        $monto_inicial_efectivo = floatval($caja_abierta['caja_monto_inicial_efectivo'] ?? 0);
        $monto_inicial_transferencia = floatval($caja_abierta['caja_monto_inicial_transferencia'] ?? 0);

        $saldo_esperado_efectivo = $monto_inicial_efectivo + $ingreso_efectivo - $egreso_efectivo;
        $saldo_esperado_transferencia = $monto_inicial_transferencia + $ingreso_transferencia - $egreso_transferencia;

        $saldo_final_sistema = $saldo_esperado_efectivo + $saldo_esperado_transferencia;

        $diferencia_efectivo = $cierre_efectivo - $saldo_esperado_efectivo;
        $diferencia_transferencia = $cierre_transferencia - $saldo_esperado_transferencia;

        try {
            $caja->cerrarCaja(
                $id_caja,
                $cierre_efectivo,
                $cierre_transferencia,
                $diferencia_efectivo, // Usamos la calculada aquí
                $diferencia_transferencia, // Usamos la calculada aquí
                $usuario_cierre,
                $fecha_cierre,
                $observaciones,
                $total_ingresos,
                $total_egresos,
                $saldo_final_sistema
            );

            header('Location: ../../views/caja/listado_caja.php?page=caja/caja&message=Caja cerrada correctamente&status=success');

        } catch (Exception $e) {
            error_log("Error al cerrar caja: " . $e->getMessage()); // Guardar en log del servidor
            header('Location: ../../views/caja/listado_caja.php?message=Error al cerrar la caja: '.$e->getMessage().'&status=danger');
            exit;
        }
    }
}
?>