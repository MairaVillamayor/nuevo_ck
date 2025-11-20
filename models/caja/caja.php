<?php
require_once __DIR__ . '/../../config/conexion.php';

class Caja
{

    public function abrirCaja($RELA_usuario, $monto_efectivo, $monto_transferencia, $fecha, $observaciones)
    {
        $pdo = getConexion();
        $sql = "INSERT INTO caja (
                    RELA_usuario, 
                    caja_monto_inicial_efectivo, 
                    caja_monto_inicial_transferencia,
                    caja_fecha_apertura, 
                    caja_observaciones,
                    caja_estado)
                VALUES (:usuario, :monto_efectivo, :monto_transferencia, :fecha, :observaciones, 'abierta')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':usuario' => $RELA_usuario,
            ':monto_efectivo' => $monto_efectivo,
            ':monto_transferencia' => $monto_transferencia,
            ':fecha' => $fecha,
            ':observaciones' => $observaciones
        ]);
    }

    public function getCajaAbierta()
    {
        $pdo = getConexion();
        $sql = "SELECT ID_caja,
                    RELA_usuario,
                    caja_monto_inicial_efectivo AS monto_inicial_efectivo,
                    caja_monto_inicial_transferencia AS monto_inicial_transferencia, 
                    caja_fecha_apertura AS fecha_apertura,
                    caja_observaciones AS observaciones,
                    caja_estado AS estado 
                FROM caja 
                WHERE caja_estado = 'abierta' LIMIT 1";
        $stmt = $pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cerrarCaja(
        $ID_caja,
        $cierre_efectivo,
        $cierre_transferencia,
        $diferencia_efectivo,
        $diferencia_transferencia,
        $usuario_cierre,
        $fecha_cierre,
        $observaciones_cierre,
        $total_ingresos_sistema,
        $total_egresos_sistema,
        $saldo_final_sistema
    ) {
        $pdo = getConexion();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "UPDATE caja
                SET caja_fecha_cierre = :fecha_cierre,
                    caja_monto_final_efectivo = :cierre_efectivo,
                    caja_monto_final_transferencia = :cierre_transferencia,
                    caja_diferencia_efectivo = :diff_efectivo,
                    caja_diferencia_transferencia = :diff_transferencia,
                    usuario_cierre = :usuario_cierre,
                    caja_observaciones_cierre = :observaciones,
                    caja_total_ingresos = :ingresos_sistema,
                    caja_total_egresos = :egresos_sistema,
                    caja_saldo_final = :saldo_sistema,
                    caja_estado = 'cerrada'
                WHERE ID_caja = :idCaja";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':idCaja' => $ID_caja,
            ':cierre_efectivo' => $cierre_efectivo ?? 0.00,
            ':cierre_transferencia' => $cierre_transferencia ?? 0.00,
            ':diff_efectivo' => $diferencia_efectivo ?? 0.00,
            ':diff_transferencia' => $diferencia_transferencia ?? 0.00,
            ':usuario_cierre' => $usuario_cierre,
            ':fecha_cierre' => $fecha_cierre,
            ':observaciones' => $observaciones_cierre,
            ':ingresos_sistema' => $total_ingresos_sistema ?? 0.00,
            ':egresos_sistema' => $total_egresos_sistema ?? 0.00,
            ':saldo_sistema' => $saldo_final_sistema ?? 0.00,
        ]);
    }

    public function obtenerCajas()
    {
        $pdo = getConexion();

        $sql = "SELECT 
                    c.ID_caja,
                    CONCAT(p.persona_nombre, ' ', p.persona_apellido) AS persona,
                    c.caja_fecha_apertura,
                    c.caja_fecha_cierre,
                    c.caja_monto_inicial_efectivo,
                    c.caja_monto_inicial_transferencia,
                    
                    COALESCE(SUM(CASE WHEN mc.movimiento_tipo = 'ingreso' AND mc.RELA_metodo_pago = 1 THEN mc.movimiento_monto ELSE 0 END), 0) AS ingresos_efectivo,
                    COALESCE(SUM(CASE WHEN mc.movimiento_tipo = 'ingreso' AND mc.RELA_metodo_pago = 2 THEN mc.movimiento_monto ELSE 0 END), 0) AS ingresos_transferencia,
                    COALESCE(SUM(CASE WHEN mc.movimiento_tipo = 'egreso' AND mc.RELA_metodo_pago = 1 THEN mc.movimiento_monto ELSE 0 END), 0) AS egresos_efectivo,
                    COALESCE(SUM(CASE WHEN mc.movimiento_tipo = 'egreso' AND mc.RELA_metodo_pago = 2 THEN mc.movimiento_monto ELSE 0 END), 0) AS egresos_transferencia,
                    
                    c.caja_monto_final_efectivo,
                    c.caja_monto_final_transferencia,
                    c.caja_estado
                FROM caja c
                LEFT JOIN movimiento_caja mc ON c.ID_caja = mc.RELA_caja
                LEFT JOIN usuarios u ON c.RELA_usuario = u.ID_usuario
                LEFT JOIN persona p ON u.RELA_persona = p.ID_persona
                GROUP BY c.ID_caja, p.persona_nombre, p.persona_apellido, c.caja_fecha_apertura, c.caja_fecha_cierre, c.caja_monto_inicial_efectivo, c.caja_monto_inicial_transferencia, c.caja_monto_final_efectivo, c.caja_monto_final_transferencia, c.caja_estado
                ORDER BY c.ID_caja DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Totales de gastos (por método de pago)
    public function obtenerTotalesGastosCaja($ID_caja)
    {
        $pdo = getConexion();
        $sql = "SELECT 
                    metodo_pago.metodo_pago_descri AS metodo_nombre, 
                    SUM(gastos.gasto_monto) AS gasto_monto
                FROM gastos
                INNER JOIN metodo_pago ON gastos.RELA_metodo_pago = metodo_pago.id_metodo_pago
                WHERE gastos.RELA_caja = ?
                GROUP BY metodo_pago.metodo_pago_descri";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$ID_caja]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Totales de ingresos (por método de pago)
    public function obtenerTotalesIngresosCaja($caja_id)
    {
        $pdo = getConexion();
        $sql = "SELECT 
                    metodo_pago.metodo_pago_descri AS metodo_nombre, 
                    SUM(movimiento_caja.movimiento_monto) AS ingreso_monto
                FROM movimiento_caja
                INNER JOIN metodo_pago ON movimiento_caja.RELA_metodo_pago = metodo_pago.ID_metodo_pago
                WHERE movimiento_caja.RELA_caja = ?
                GROUP BY metodo_pago.metodo_pago_descri";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$caja_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodas()
    {
        $pdo = getConexion();
        $sql = "SELECT c.*, u.usuario_nombre AS nombre_usuario
                FROM caja c
                LEFT JOIN usuarios u ON c.RELA_usuario = u.ID_usuario
                ORDER BY c.caja_fecha_apertura DESC";
        $stmt = $pdo->query($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function abrir($usuario_id, $monto_inicial)
    {
        $pdo = getConexion();
        $sql = "INSERT INTO caja (RELA_usuario, caja_monto_inicial, caja_fecha_apertura, caja_estado)
                VALUES (?, ?, NOW(), 'abierta')";
        $stmt = $pdo->query($sql);
        return $stmt->execute([$usuario_id, $monto_inicial]);
    }

    public function obtenerCajaAbierta($usuario_id)
    {
        $pdo = getConexion();
        $sql = "SELECT * FROM caja 
            WHERE caja_estado = 'abierta'
            ORDER BY ID_caja DESC
            LIMIT 1";
        $stmt = $pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function obtenerEgresosPorMetodo($id_caja)
    {
        $pdo = getConexion();
        $sql = "SELECT 
            SUM(CASE WHEN RELA_metodo_pago = 1 THEN movimiento_monto ELSE 0 END) AS egreso_efectivo,
            SUM(CASE WHEN RELA_metodo_pago = 2 THEN movimiento_monto ELSE 0 END) AS egreso_transferencia
        FROM movimiento_caja
        WHERE RELA_caja = :idCaja AND movimiento_tipo = 'egreso'
    ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':idCaja' => $id_caja]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerEgresosIngresosPorMetodo($id_caja)
    {
        $pdo = getConexion();
        $sql = "  SELECT 
            SUM(CASE WHEN movimiento_tipo='ingreso'  AND RELA_metodo_pago=1 THEN movimiento_monto ELSE 0 END) AS ingreso_efectivo,
            SUM(CASE WHEN movimiento_tipo='ingreso'  AND RELA_metodo_pago=2 THEN movimiento_monto ELSE 0 END) AS ingreso_transferencia,
            SUM(CASE WHEN movimiento_tipo='egreso'   AND RELA_metodo_pago=1 THEN movimiento_monto ELSE 0 END) AS egreso_efectivo,
            SUM(CASE WHEN movimiento_tipo='egreso'   AND RELA_metodo_pago=2 THEN movimiento_monto ELSE 0 END) AS egreso_transferencia
        FROM movimiento_caja
        WHERE RELA_caja = :idCaja
    ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':idCaja' => $id_caja]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

class MovimientoCaja
{

    public function registrarMovimiento($RELA_caja, $RELA_usuario, $tipo, $monto, $descripcion, $RELA_metodo_pago)
    {
        $pdo = getConexion();
        $sql = "INSERT INTO movimiento_caja 
                (RELA_caja, RELA_usuario, movimiento_tipo, movimiento_monto, movimiento_descripcion, RELA_metodo_pago)
                VALUES (:caja, :usuario, :tipo, :monto, :descripcion, :metodo_pago)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':caja' => $RELA_caja,
            ':usuario' => $RELA_usuario,
            ':tipo' => $tipo,
            ':monto' => $monto,
            ':descripcion' => $descripcion,
            ':metodo_pago' => $RELA_metodo_pago // Se añadió el método de pago
        ]);

        return ['success' => true];
    }

    public function listarPorCaja($RELA_caja)
    {
        $pdo = getConexion();
        $sql = "SELECT m.*, u.usuario_nombre 
                FROM movimiento_caja m
                INNER JOIN usuario u ON m.RELA_usuario = u.ID_usuario
                WHERE m.RELA_caja = :caja
                ORDER BY m.movimiento_fecha DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':caja' => $RELA_caja]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
