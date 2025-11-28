<?php
require_once __DIR__ . '/../../config/conexion.php';

class Caja
{

    public function abrirCaja($RELA_usuario, $caja_monto_inicial_efectivo, $caja_monto_inicial_transferencia, $fecha, $observaciones)
    {
        $pdo = getConexion();
        $sql = "INSERT INTO caja (
                    RELA_usuario, 
                    caja_monto_inicial_efectivo, 
                    caja_monto_inicial_transferencia,
                    caja_fecha_apertura, 
                    caja_observaciones,
                    caja_estado)
                VALUES (:usuario, :caja_monto_inicial_efectivo, :caja_monto_inicial_transferencia, :fecha, :observaciones, 'abierta')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':usuario' => $RELA_usuario,
            ':caja_monto_inicial_efectivo' => $caja_monto_inicial_efectivo,
            ':caja_monto_inicial_transferencia' => $caja_monto_inicial_transferencia,
            ':fecha' => $fecha,
            ':observaciones' => $observaciones
        ]);
    }

    public function getCajaAbierta()
    {
        $pdo = getConexion();
        $sql = "SELECT ID_caja,
                    RELA_usuario,
                    caja_monto_inicial_efectivo,
                    caja_monto_inicial_transferencia, 
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
                WHERE ID_caja = :id_caja";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':id_caja' => $ID_caja,
            ':cierre_efectivo' => $cierre_efectivo,
            ':cierre_transferencia' => $cierre_transferencia,
            ':diff_efectivo' => $diferencia_efectivo,
            ':diff_transferencia' => $diferencia_transferencia,
            ':usuario_cierre' => $usuario_cierre,
            ':fecha_cierre' => $fecha_cierre,
            ':observaciones' => $observaciones_cierre,
            ':ingresos_sistema' => $total_ingresos_sistema,
            ':egresos_sistema' => $total_egresos_sistema,
            ':saldo_sistema' => $saldo_final_sistema,
        ]);
    }

    public function obtenerCajas()
    {
        $pdo = getConexion();

        $sql = "SELECT 
                c.*, 
                CONCAT(p.persona_nombre, ' ', p.persona_apellido) AS persona
            FROM caja c
            LEFT JOIN usuarios u ON c.RELA_usuario = u.ID_usuario
            LEFT JOIN persona p ON u.RELA_persona = p.ID_persona
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
                AND movimiento_caja.movimiento_tipo = 'ingreso'
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

    /**
     * Obtiene la suma total de INGRESOS (VENTAS + MOVIMIENTOS) y EGRESOS (MOVIMIENTOS) por método de pago.
     * @param int $id_caja El ID de la caja abierta.
     * @return array Los totales agregados.
     */
    public function obtenerEgresosIngresosPorMetodo($id_caja)
    {
        $pdo = getConexion();

        $ID_EFECTIVO = 1;
        $ID_TRANSFERENCIA = 2;
        
        // 1. OBTENER INGRESOS POR VENTAS (Desde factura_pagos y factura)
        // La factura se enlaza con la caja a través del campo RELA_caja en la tabla factura (que acabamos de agregar)
        $sql_ventas = "SELECT
            SUM(CASE WHEN fp.RELA_metodo_pago = ? THEN fp.pago_monto ELSE 0 END) AS venta_efectivo,
            SUM(CASE WHEN fp.RELA_metodo_pago = ? THEN fp.pago_monto ELSE 0 END) AS venta_transferencia
            FROM factura_pagos fp
            INNER JOIN factura f ON fp.RELA_factura = f.ID_factura
            WHERE f.RELA_caja = ?"; 
        
        $stmt_ventas = $pdo->prepare($sql_ventas);
        $stmt_ventas->execute([$ID_EFECTIVO, $ID_TRANSFERENCIA, $id_caja]);
        $ventas = $stmt_ventas->fetch(PDO::FETCH_ASSOC);

        // 2. OBTENER MOVIMIENTOS MANUALES (Desde movimiento_caja)
        // Solo sumamos ingresos y egresos que NO son ventas (ej: retiros, depósitos iniciales, gastos)
        $sql_mov = "SELECT 
             SUM(CASE WHEN movimiento_tipo = 'ingreso' AND RELA_metodo_pago = ? THEN movimiento_monto ELSE 0 END) AS ingreso_manual_efectivo,
             SUM(CASE WHEN movimiento_tipo = 'ingreso' AND RELA_metodo_pago = ? THEN movimiento_monto ELSE 0 END) AS ingreso_manual_transferencia,
             SUM(CASE WHEN movimiento_tipo = 'egreso' AND RELA_metodo_pago = ? THEN movimiento_monto ELSE 0 END) AS egreso_efectivo,
             SUM(CASE WHEN movimiento_tipo = 'egreso' AND RELA_metodo_pago = ? THEN movimiento_monto ELSE 0 END) AS egreso_transferencia
             FROM movimiento_caja
             WHERE RELA_caja = ?"; 
        
        $stmt_mov = $pdo->prepare($sql_mov);
        $stmt_mov->execute([$ID_EFECTIVO, $ID_TRANSFERENCIA, $ID_EFECTIVO, $ID_TRANSFERENCIA, $id_caja]);
        $movimientos = $stmt_mov->fetch(PDO::FETCH_ASSOC);

        // 3. CONSOLIDAR RESULTADOS
        $resultado = [
            'ingreso_efectivo'      => (float)($ventas['venta_efectivo'] ?? 0) + (float)($movimientos['ingreso_manual_efectivo'] ?? 0),
            'ingreso_transferencia' => (float)($ventas['venta_transferencia'] ?? 0) + (float)($movimientos['ingreso_manual_transferencia'] ?? 0),
            'egreso_efectivo'       => (float)($movimientos['egreso_efectivo'] ?? 0),
            'egreso_transferencia'  => (float)($movimientos['egreso_transferencia'] ?? 0),
        ];

        return $resultado;
    }

    // para reporte 
    public function obtenerCajaPorId($id_caja)
    {
        $pdo = getConexion();
        $sql = "SELECT c.*,
                   CONCAT(p.persona_nombre,' ',p.persona_apellido) AS usuario_nombre
                FROM caja c
                LEFT JOIN usuarios u ON c.RELA_usuario = u.ID_usuario
                LEFT JOIN persona p ON u.RELA_persona = p.ID_persona
                WHERE c.ID_caja = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_caja]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function obtenerUsuarioPorId($id_usuario)
    {
        $pdo = getConexion();
        $sql = "SELECT CONCAT(p.persona_nombre,' ',p.persona_apellido)
                FROM usuarios u
                INNER JOIN persona p ON u.RELA_persona = p.ID_persona
                WHERE u.ID_usuario = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchColumn();
    }

    public function obtenerMovimientosPorMetodo($id_caja, $metodo)
    {
        $pdo = getConexion();

        switch ($metodo) {
            case 'efectivo':
                $id_metodo = 1;
                break;
            case 'transferencia':
                $id_metodo = 2;
                break;
            case 'mp':
            default:
                return [];
        }

        $sql = "SELECT 
                    m.ID_movimiento,
                    m.movimiento_fecha,
                    m.movimiento_monto AS total,
                    m.movimiento_descripcion,
                    u.usuario_nombre
                FROM movimiento_caja m
                INNER JOIN usuarios u ON m.RELA_usuario = u.ID_usuario
                WHERE m.RELA_caja = ?
                AND m.movimiento_tipo = 'ingreso'
                AND m.RELA_metodo_pago = ?
                ORDER BY m.movimiento_fecha ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_caja, $id_metodo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerVentasPorMetodoPago($id_caja, $metodo)
    {
        $pdo = getConexion();

        $mapa_metodos = [
            'efectivo' => 1,
            'transferencia' => 2,
        ];

        if (!isset($mapa_metodos[$metodo])) return [];

        $id_metodo = $mapa_metodos[$metodo];

        $sql = "SELECT 
                f.ID_factura,
                CONCAT(p.persona_nombre, ' ', p.persona_apellido) AS usuario_nombre,
                f.factura_fecha_emision,
                f.factura_total,
                f.ID_factura AS recibo,
                fp.pago_monto AS total,
                ef.estado_factura_descri
            FROM factura f
            INNER JOIN factura_pagos fp ON f.ID_factura = fp.RELA_factura
            INNER JOIN persona p ON f.RELA_persona = p.ID_persona
            LEFT JOIN estado_factura ef ON f.RELA_estado_factura = ef.ID_estado_factura
            WHERE fp.RELA_metodo_pago = ?
            ORDER BY f.factura_fecha_emision ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_metodo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerTotalVentasPorMetodo($metodo)
    {
        $pdo = getConexion();

        $mapa_metodos = [
            'efectivo' => 1,
            'transferencia' => 2,
        ];

        if (!isset($mapa_metodos[$metodo])) return 0;

        $id_metodo = $mapa_metodos[$metodo];

        $sql = "SELECT SUM(pago_monto)
            FROM factura_pagos
            WHERE RELA_metodo_pago = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_metodo]);
        return (float)$stmt->fetchColumn() ?: 0;
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
            ':metodo_pago' => $RELA_metodo_pago
        ]);

        return ['success' => true];
    }

    public function listarPorCaja($RELA_caja)
    {
        $pdo = getConexion();
        $sql = "SELECT m.*, u.usuario_nombre 
                FROM movimiento_caja m
                INNER JOIN usuarios u ON m.RELA_usuario = u.ID_usuario
                WHERE m.RELA_caja = :caja
                ORDER BY m.movimiento_fecha DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':caja' => $RELA_caja]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
