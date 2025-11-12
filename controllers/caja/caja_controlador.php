<?php
session_start();
require_once("../../models/caja/caja.php");

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

class CajaControlador {

    public function abrir() {
        $pdo = getConexion();
        $caja = new Caja();

        // Verificar si ya hay una caja abierta
        $caja_abierta = $caja->getCajaAbierta();
        if ($caja_abierta) {
            header('Location: ../views/caja/listado_caja.php?page=caja/caja&message=Ya hay una caja abierta&status=danger');
            return;
        }

        // Validaciones
        if (!isset($_POST['RELA_usuario']) || !isset($_POST['caja_monto_inicial'])) {
            header('Location: ../views/caja/listado_caja.php?page=caja/caja&message=Campos obligatorios faltantes&status=danger');
            return;
        }

        $usuario = $_POST['RELA_usuario'];
        $monto = floatval($_POST['caja_monto_inicial']);
        $fecha = date('Y-m-d H:i:s');

        $caja->abrirCaja($usuario, $monto, $fecha);
        header('Location: ../views/caja/listado_caja.php?page=caja/caja&message=Caja abierta con éxito&status=success');
    }

    public function cerrar() {
        $caja = new Caja();
        $caja_abierta = $caja->getCajaAbierta();

        if (!$caja_abierta) {
            $_SESSION['message'] = 'Caja abierta con éxito';
            $_SESSION['status'] = 'success';
            header('Location: ../views/caja/listado_caja.php');
            exit;
        }

        $id_caja = $caja_abierta['ID_caja'];
        $total_ingresos = $caja->calcularTotales('ingreso', $id_caja);
        $total_egresos = $caja->calcularTotales('egreso', $id_caja);
        $saldo_final = ($caja_abierta['caja_monto_inicial'] + $total_ingresos) - $total_egresos;

        $caja->cerrarCaja($id_caja, $total_ingresos, $total_egresos, $saldo_final);
        header('Location: ../views/caja/listado_caja.php?page=caja/caja&message=Caja cerrada correctamente&status=success');
    }
}
?>
