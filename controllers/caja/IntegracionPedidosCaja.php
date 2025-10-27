<?php
/**
 * Integración con sistema de pedidos para ingresos automáticos
 * Sistema de gestión de caja para Cake Party
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/CajaController.php';

class IntegracionPedidosCaja {
    private $cajaController;
    private $pdo;

    public function __construct() {
        $this->cajaController = new CajaController();
        $this->pdo = getConexion();
    }

    /**
     * Registrar ingreso automático cuando se confirma un pedido
     */
    public function registrarIngresoPorPedido($pedido_id) {
        try {
            $this->pdo->beginTransaction();

            // Obtener datos del pedido
            $sql_pedido = "SELECT 
                p.ID_pedido,
                p.pedido_total,
                p.RELA_usuario,
                p.RELA_estado,
                e.estado_descri,
                u.usuario_nombre,
                per.persona_nombre,
                per.persona_apellido
                FROM pedido p
                JOIN estado e ON p.RELA_estado = e.ID_estado
                JOIN usuarios u ON p.RELA_usuario = u.ID_usuario
                JOIN persona per ON u.RELA_persona = per.ID_persona
                WHERE p.ID_pedido = ?";
            
            $pedido = $this->pdo->prepare($sql_pedido);
            $pedido->execute([$pedido_id]);
            $pedido_data = $pedido->fetch(PDO::FETCH_ASSOC);

            if (!$pedido_data) {
                throw new Exception("Pedido no encontrado");
            }

            // Solo procesar si el pedido está en estado "Finalizado"
            if ($pedido_data['estado_descri'] !== 'Finalizado') {
                throw new Exception("El pedido debe estar finalizado para registrar el ingreso");
            }

            // Verificar que el monto sea mayor a 0
            if ($pedido_data['pedido_total'] <= 0) {
                throw new Exception("El monto del pedido debe ser mayor a 0");
            }

            // Verificar que el usuario tenga una caja abierta
            $caja_abierta = $this->cajaController->obtenerCajaAbierta($pedido_data['RELA_usuario']);
            
            if (!$caja_abierta) {
                throw new Exception("El usuario " . $pedido_data['persona_nombre'] . " " . $pedido_data['persona_apellido'] . " no tiene una caja abierta");
            }

            // Registrar el ingreso en la caja
            $resultado = $this->cajaController->registrarMovimiento(
                $caja_abierta['ID_caja'],
                $pedido_data['RELA_usuario'],
                'ingreso',
                $pedido_data['pedido_total'],
                "Venta - Pedido #{$pedido_id} - {$pedido_data['persona_nombre']} {$pedido_data['persona_apellido']}"
            );

            if (!$resultado['success']) {
                throw new Exception($resultado['error']);
            }

            $this->pdo->commit();
            return [
                'success' => true,
                'mensaje' => "Ingreso de $" . number_format($pedido_data['pedido_total'], 2) . " registrado en la caja",
                'caja_id' => $caja_abierta['ID_caja'],
                'movimiento_id' => $resultado['movimiento_id']
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Procesar múltiples pedidos pendientes
     */
    public function procesarPedidosPendientes() {
        try {
            // Obtener pedidos finalizados que no han sido procesados en caja
            $sql_pendientes = "SELECT 
                p.ID_pedido,
                p.pedido_total,
                p.RELA_usuario,
                u.usuario_nombre,
                per.persona_nombre,
                per.persona_apellido
                FROM pedido p
                JOIN estado e ON p.RELA_estado = e.ID_estado
                JOIN usuarios u ON p.RELA_usuario = u.ID_usuario
                JOIN persona per ON u.RELA_persona = per.ID_persona
                WHERE e.estado_descri = 'Finalizado'
                AND p.pedido_total > 0
                ORDER BY p.ID_pedido ASC";
            
            $pedidos = $this->pdo->query($sql_pendientes)->fetchAll(PDO::FETCH_ASSOC);
            
            $procesados = 0;
            $errores = [];
            
            foreach ($pedidos as $pedido) {
                $resultado = $this->registrarIngresoPorPedido($pedido['ID_pedido']);
                
                if ($resultado['success']) {
                    $procesados++;
                } else {
                    $errores[] = "Pedido #{$pedido['ID_pedido']}: " . $resultado['error'];
                }
            }
            
            return [
                'success' => true,
                'procesados' => $procesados,
                'total' => count($pedidos),
                'errores' => $errores
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener estadísticas de ventas por caja
     */
    public function obtenerEstadisticasVentas($caja_id) {
        try {
            $sql = "SELECT 
                COUNT(*) as total_ventas,
                SUM(movimiento_monto) as total_ventas_monto,
                AVG(movimiento_monto) as promedio_venta,
                MIN(movimiento_monto) as venta_minima,
                MAX(movimiento_monto) as venta_maxima
                FROM movimiento_caja 
                WHERE RELA_caja = ? 
                AND movimiento_tipo = 'ingreso'
                AND movimiento_descripcion LIKE 'Venta - Pedido%'";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$caja_id]);
            $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'estadisticas' => $estadisticas
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener ventas del día por usuario
     */
    public function obtenerVentasDelDia($usuario_id = null) {
        try {
            $sql = "SELECT 
                m.RELA_caja,
                m.movimiento_monto,
                m.movimiento_descripcion,
                m.movimiento_fecha,
                c.caja_fecha_apertura,
                u.usuario_nombre,
                p.persona_nombre,
                p.persona_apellido
                FROM movimiento_caja m
                JOIN caja c ON m.RELA_caja = c.ID_caja
                JOIN usuarios u ON c.RELA_usuario = u.ID_usuario
                JOIN persona p ON u.RELA_persona = p.ID_persona
                WHERE m.movimiento_tipo = 'ingreso'
                AND m.movimiento_descripcion LIKE 'Venta - Pedido%'
                AND DATE(m.movimiento_fecha) = CURDATE()";
            
            $params = [];
            if ($usuario_id) {
                $sql .= " AND c.RELA_usuario = ?";
                $params[] = $usuario_id;
            }
            
            $sql .= " ORDER BY m.movimiento_fecha DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'ventas' => $ventas
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar si un pedido ya fue procesado en caja
     */
    public function verificarPedidoProcesado($pedido_id) {
        try {
            $sql = "SELECT COUNT(*) as procesado
                    FROM movimiento_caja 
                    WHERE movimiento_descripcion LIKE ?";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(["Venta - Pedido #{$pedido_id}%"]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['procesado'] > 0;
            
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
