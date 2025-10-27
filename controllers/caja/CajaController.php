<?php
/**
 * Controlador de Caja
 * Sistema de gestión de caja para Cake Party
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../core/DatabaseHelper.php';

class CajaController {
    private $db;
    private $pdo;

    public function __construct() {
        $this->db = new DatabaseHelper();
        $this->pdo = getConexion();
    }

    /**
     * Abrir una nueva caja
     */
    public function abrirCaja($usuario_id, $monto_inicial) {
        try {
            $this->pdo->beginTransaction();

            // Verificar si ya hay una caja abierta para este usuario
            $caja_abierta = $this->obtenerCajaAbierta($usuario_id);
            if ($caja_abierta) {
                throw new Exception("Ya tienes una caja abierta desde " . $caja_abierta['caja_fecha_apertura']);
            }

            // Crear nueva caja
            $sql = "INSERT INTO caja (RELA_usuario, caja_monto_inicial, caja_fecha_apertura, caja_estado) 
                    VALUES (?, ?, NOW(), 'abierta')";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$usuario_id, $monto_inicial]);
            $caja_id = $this->pdo->lastInsertId();

            $this->pdo->commit();
            return ['success' => true, 'caja_id' => $caja_id, 'mensaje' => 'Caja abierta exitosamente'];

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Cerrar una caja
     */
    public function cerrarCaja($caja_id, $usuario_id) {
        try {
            $this->pdo->beginTransaction();

            // Obtener datos de la caja
            $caja = $this->obtenerCajaPorId($caja_id);
            if (!$caja) {
                throw new Exception("Caja no encontrada");
            }

            if ($caja['caja_estado'] !== 'abierta') {
                throw new Exception("La caja ya está cerrada");
            }

            // Calcular totales
            $totales = $this->calcularTotalesCaja($caja_id);
            
            // Calcular saldo final
            $saldo_final = $caja['caja_monto_inicial'] + $totales['total_ingresos'] - $totales['total_egresos'];

            // Actualizar caja
            $sql = "UPDATE caja SET 
                    caja_fecha_cierre = NOW(),
                    caja_estado = 'cerrada',
                    caja_total_ingresos = ?,
                    caja_total_egresos = ?,
                    caja_saldo_final = ?
                    WHERE ID_caja = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $totales['total_ingresos'],
                $totales['total_egresos'],
                $saldo_final,
                $caja_id
            ]);

            $this->pdo->commit();
            return [
                'success' => true, 
                'mensaje' => 'Caja cerrada exitosamente',
                'resumen' => [
                    'monto_inicial' => $caja['caja_monto_inicial'],
                    'total_ingresos' => $totales['total_ingresos'],
                    'total_egresos' => $totales['total_egresos'],
                    'saldo_final' => $saldo_final
                ]
            ];

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Registrar movimiento de caja
     */
    public function registrarMovimiento($caja_id, $usuario_id, $tipo, $monto, $descripcion, $manejar_transaccion = true) {
        try {
            if ($manejar_transaccion) {
                $this->pdo->beginTransaction();
            }

            // Verificar que la caja esté abierta
            $caja = $this->obtenerCajaPorId($caja_id);
            if (!$caja || $caja['caja_estado'] !== 'abierta') {
                throw new Exception("No se puede registrar movimientos en una caja cerrada");
            }

            // Registrar movimiento
            $sql = "INSERT INTO movimiento_caja (RELA_caja, RELA_usuario, movimiento_tipo, movimiento_monto, movimiento_descripcion, movimiento_fecha) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$caja_id, $usuario_id, $tipo, $monto, $descripcion]);
            $movimiento_id = $this->pdo->lastInsertId();

            if ($manejar_transaccion) {
                $this->pdo->commit();
            }
            return ['success' => true, 'movimiento_id' => $movimiento_id, 'mensaje' => 'Movimiento registrado exitosamente'];

        } catch (Exception $e) {
            if ($manejar_transaccion && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Registrar ingreso automático por venta
     */
    public function registrarIngresoVenta($pedido_id, $monto) {
        try {
            $this->pdo->beginTransaction();

            // Obtener la caja abierta del usuario que procesó el pedido
            $sql_usuario = "SELECT RELA_usuario FROM pedido WHERE ID_pedido = ?";
            $stmt = $this->pdo->prepare($sql_usuario);
            $stmt->execute([$pedido_id]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$pedido) {
                throw new Exception("Pedido no encontrado");
            }

            $caja_abierta = $this->obtenerCajaAbierta($pedido['RELA_usuario']);
            if (!$caja_abierta) {
                throw new Exception("No hay caja abierta para registrar la venta");
            }

            // Registrar el ingreso
            $resultado = $this->registrarMovimiento(
                $caja_abierta['ID_caja'],
                $pedido['RELA_usuario'],
                'ingreso',
                $monto,
                "Venta - Pedido #{$pedido_id}",
                false
            );

            $this->pdo->commit();
            return $resultado;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Obtener caja abierta de un usuario
     */
    public function obtenerCajaAbierta($usuario_id) {
        $sql = "SELECT * FROM caja WHERE RELA_usuario = ? AND caja_estado = 'abierta' ORDER BY caja_fecha_apertura DESC LIMIT 1";
        return $this->db->selectOne($sql, [$usuario_id]);
    }

    /**
     * Obtener caja por ID
     */
    public function obtenerCajaPorId($caja_id) {
        $sql = "SELECT c.*, u.usuario_nombre, p.persona_nombre, p.persona_apellido 
                FROM caja c
                JOIN usuarios u ON c.RELA_usuario = u.ID_usuario
                JOIN persona p ON u.RELA_persona = p.ID_persona
                WHERE c.ID_caja = ?";
        return $this->db->selectOne($sql, [$caja_id]);
    }

    /**
     * Obtener todas las cajas de un usuario
     */
    public function obtenerCajasUsuario($usuario_id, $limite = 50) {
        $sql = "SELECT c.*, 
                DATE_FORMAT(c.caja_fecha_apertura, '%d/%m/%Y %H:%i') as fecha_apertura_formateada,
                DATE_FORMAT(c.caja_fecha_cierre, '%d/%m/%Y %H:%i') as fecha_cierre_formateada
                FROM caja c
                WHERE c.RELA_usuario = ?
                ORDER BY c.caja_fecha_apertura DESC
                LIMIT ?";
        return $this->db->select($sql, [$usuario_id, $limite]);
    }

    /**
     * Obtener todas las cajas (para admin/gerente)
     */
    public function obtenerTodasLasCajas($limite = 100) {
        $sql = "SELECT c.*, 
                u.usuario_nombre,
                p.persona_nombre,
                p.persona_apellido,
                DATE_FORMAT(c.caja_fecha_apertura, '%d/%m/%Y %H:%i') as fecha_apertura_formateada,
                DATE_FORMAT(c.caja_fecha_cierre, '%d/%m/%Y %H:%i') as fecha_cierre_formateada
                FROM caja c
                JOIN usuarios u ON c.RELA_usuario = u.ID_usuario
                JOIN persona p ON u.RELA_persona = p.ID_persona
                ORDER BY c.caja_fecha_apertura DESC
                LIMIT ?";
        return $this->db->select($sql, [$limite]);
    }

    /**
     * Obtener movimientos de una caja
     */
    public function obtenerMovimientosCaja($caja_id) {
        $sql = "SELECT m.*, 
                u.usuario_nombre,
                p.persona_nombre,
                p.persona_apellido,
                DATE_FORMAT(m.movimiento_fecha, '%d/%m/%Y %H:%i') as fecha_formateada
                FROM movimiento_caja m
                JOIN usuarios u ON m.RELA_usuario = u.ID_usuario
                JOIN persona p ON u.RELA_persona = p.ID_persona
                WHERE m.RELA_caja = ?
                ORDER BY m.movimiento_fecha DESC";
        return $this->db->select($sql, [$caja_id]);
    }

    /**
     * Calcular totales de una caja
     */
    public function calcularTotalesCaja($caja_id) {
        $sql = "SELECT 
                SUM(CASE WHEN movimiento_tipo = 'ingreso' THEN movimiento_monto ELSE 0 END) as total_ingresos,
                SUM(CASE WHEN movimiento_tipo = 'egreso' THEN movimiento_monto ELSE 0 END) as total_egresos
                FROM movimiento_caja 
                WHERE RELA_caja = ?";
        
        $resultado = $this->db->selectOne($sql, [$caja_id]);
        
        return [
            'total_ingresos' => $resultado['total_ingresos'] ?? 0,
            'total_egresos' => $resultado['total_egresos'] ?? 0
        ];
    }

    /**
     * Obtener arqueo de caja actual
     */
    public function obtenerArqueoCaja($caja_id) {
        $caja = $this->obtenerCajaPorId($caja_id);
        if (!$caja) {
            return ['success' => false, 'error' => 'Caja no encontrada'];
        }

        $totales = $this->calcularTotalesCaja($caja_id);
        $movimientos = $this->obtenerMovimientosCaja($caja_id);

        $saldo_actual = $caja['caja_monto_inicial'] + $totales['total_ingresos'] - $totales['total_egresos'];

        return [
            'success' => true,
            'caja' => $caja,
            'totales' => $totales,
            'saldo_actual' => $saldo_actual,
            'movimientos' => $movimientos
        ];
    }

    /**
     * Obtener estadísticas de caja por fecha
     */
    public function obtenerEstadisticasCaja($fecha_inicio, $fecha_fin = null) {
        if (!$fecha_fin) {
            $fecha_fin = date('Y-m-d');
        }

        $sql = "SELECT 
                COUNT(*) as total_cajas,
                SUM(caja_total_ingresos) as total_ingresos_general,
                SUM(caja_total_egresos) as total_egresos_general,
                SUM(caja_saldo_final) as saldo_total_general
                FROM caja 
                WHERE DATE(caja_fecha_apertura) BETWEEN ? AND ?
                AND caja_estado = 'cerrada'";
        
        return $this->db->selectOne($sql, [$fecha_inicio, $fecha_fin]);
    }

    /**
     * Verificar permisos de usuario
     */
    public function verificarPermisos($usuario_id, $perfil_id) {
        // Empleado: solo puede ver sus propias cajas
        // Admin/Gerente: pueden ver todas las cajas
        return in_array($perfil_id, [1, 4]) || $perfil_id == 2;
    }
}
?>
