<?php
require_once __DIR__ . '/../../config/conexion.php';

class Caja
{

    public function abrirCaja($RELA_usuario, $monto_inicial, $fecha)
    {
        $pdo = getConexion();
        $sql = "INSERT INTO caja (RELA_usuario, caja_monto_inicial, caja_fecha_apertura, caja_estado)
                VALUES (:usuario, :monto, :fecha, 'abierta')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':usuario' => $RELA_usuario,
            ':monto' => $monto_inicial,
            ':fecha' => $fecha
        ]);
    }

    public function getCajaAbierta()
    {
        $pdo = getConexion();
        $sql = "SELECT ID_caja AS id, 
                   RELA_usuario,
                   caja_monto_inicial AS monto_inicial_efectivo, -- Asumimos que todo el monto inicial es efectivo
                   caja_monto_inicial AS monto_inicial_transferencia, -- Creamos el segundo campo, asumiendo que vale 0 o lo manejarás después
                   caja_fecha_apertura AS fecha_apertura,
                   caja_fecha_apertura AS hora_apertura, -- Asumiendo que obtienes la hora del mismo campo
                   caja_estado AS estado 
            FROM caja 
            WHERE caja_estado = 'abierta' LIMIT 1";
        $stmt = $pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cerrarCaja($ID_caja, $total_ingresos, $total_egresos, $saldo_final)
    {
        $pdo = getConexion();
        $sql = "UPDATE caja
                SET caja_fecha_cierre = NOW(),
                    caja_total_ingresos = :ingresos,
                    caja_total_egresos = :egresos,
                    caja_saldo_final = :saldo,
                    caja_estado = 'cerrada'
                WHERE ID_caja = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':ingresos' => $total_ingresos,
            ':egresos' => $total_egresos,
            ':saldo' => $saldo_final,
            ':id' => $ID_caja
        ]);
    }

    public function calcularTotales($tipo, $RELA_caja)
    {
        $pdo = getConexion();
        $sql = "SELECT SUM(movimiento_monto) AS total
                FROM movimiento_caja
                WHERE RELA_caja = :caja AND movimiento_tipo = :tipo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':caja' => $RELA_caja, ':tipo' => $tipo]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res['total'] ?? 0;
    }

    public function listarCajas()
    {
        $pdo = getConexion();
        $sql = "SELECT c.*, u.usuario_nombre 
                FROM caja c
                INNER JOIN usuario u ON c.RELA_usuario = u.ID_usuario
                ORDER BY c.caja_fecha_apertura DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Totales de movimientos (por método de pago)
    public function obtenerTotalesCaja($RELA_caja)
    {
        $pdo = getConexion();
        $sql = "SELECT 
                    metodo_pago.metodo_pago_descri AS metodo_nombre, 
                    SUM(movimiento_caja.movimiento_monto) AS total_monto
                FROM movimiento_caja
                INNER JOIN metodo_pago 
                ON movimiento_caja.RELA_metodo_pago = metodo_pago.id_metodo_pago
                WHERE movimiento_caja.RELA_caja = ?
                GROUP BY metodo_pago.metodo_pago_descri";

        $stmt = $pdo->prepare($sql); 
        $stmt->execute([$RELA_caja]); 
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

    public function cerrar($id_caja, $monto_final)
    {
        $pdo = getConexion();
        $sql = "UPDATE caja 
                SET caja_monto_final = ?, caja_fecha_cierre = NOW(), caja_estado = 'cerrada' 
                WHERE ID_caja = ?";
        $stmt = $pdo->query($sql);
        return $stmt->execute([$monto_final, $id_caja]);
    }

    public function obtenerCajaAbierta($usuario_id)
    {
        $pdo = getConexion();
        $sql = "SELECT ID_caja AS id, 
                   RELA_usuario,
                   caja_monto_inicial AS monto_inicial_efectivo,
                   caja_monto_inicial AS monto_inicial_transferencia, 
                   DATE(caja_fecha_apertura) AS fecha_apertura,
                   TIME(caja_fecha_apertura) AS hora_apertura,
                   caja_estado AS estado 
            FROM caja 
            WHERE RELA_usuario = ? AND caja_estado = 'abierta' LIMIT 1";
        $stmt = $pdo->query($sql);
        $stmt->execute([$usuario_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function calcularArqueo($id_caja)
    {
        $pdo = getConexion();
        $sql = "SELECT 
                    SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS total_ingresos,
                    SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) AS total_egresos
                FROM movimiento_caja
                WHERE RELA_caja = ?";
        $stmt = $pdo->query($sql);
        $stmt->execute([$id_caja]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerCajas()
    {
        $pdo = getConexion();
        $sql = "SELECT 
                    c.id_caja AS id_caja,
                    CONCAT(p.persona_nombre, ' ', p.persona_apellido) AS persona,
                    c.caja_fecha_apertura AS fecha_apertura,
                    c.caja_fecha_cierre AS fecha_cierre,
                    c.caja_monto_inicial AS monto_inicial,
                    c.caja_total_ingresos AS ingresos,
                    c.caja_total_egresos AS egresos,
                    c.caja_saldo_final AS saldo_final,
                    c.caja_estado
                FROM caja c
                LEFT JOIN usuarios u ON c.RELA_usuario = u.id_usuario
                LEFT JOIN persona p ON u.RELA_persona = p.id_persona
                ORDER BY c.id_caja DESC";
        $stmt = $pdo->query($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class MovimientoCaja
{

    public function registrarMovimiento($RELA_caja, $RELA_usuario, $tipo, $monto, $descripcion)
    {
        $pdo = getConexion();
        $sql = "INSERT INTO movimiento_caja 
                (RELA_caja, RELA_usuario, movimiento_tipo, movimiento_monto, movimiento_descripcion)
                VALUES (:caja, :usuario, :tipo, :monto, :descripcion)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':caja' => $RELA_caja,
            ':usuario' => $RELA_usuario,
            ':tipo' => $tipo,
            ':monto' => $monto,
            ':descripcion' => $descripcion
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
