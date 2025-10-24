<?php
/**
 * Controlador de Stock
 * Sistema de gestión de stock para Cake Party
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../core/DatabaseHelper.php';

class StockController {
    private $db;
    private $pdo;

    public function __construct() {
        $this->db = new DatabaseHelper();
        $this->pdo = getConexion();
    }

    /**
     * Registrar ingreso de stock
     */
    public function registrarIngreso($insumo_id, $cantidad, $observaciones = '') {
        try {
            $this->pdo->beginTransaction();

            // 1. Registrar la operación de ingreso
            $sql_operacion = "INSERT INTO operacion 
                (operacion_cantidad_de_productos, operacion_fecha_de_actualizacion, RELA_tipo_de_operacion, RELA_insumos) 
                VALUES (?, NOW(), 1, ?)";
            
            $operacion_id = $this->db->insert($sql_operacion, [$cantidad, $insumo_id]);

            // 2. Actualizar stock actual del insumo
            $sql_update = "UPDATE insumos 
                SET insumo_stock_actual = insumo_stock_actual + ? 
                WHERE ID_insumo = ?";
            
            $this->db->update($sql_update, [$cantidad, $insumo_id]);

            $this->pdo->commit();
            return ['success' => true, 'operacion_id' => $operacion_id];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Registrar egreso de stock (consumo por receta)
     */
    public function registrarEgreso($insumo_id, $cantidad, $pastel_id = null, $observaciones = '') {
        try {
            $this->pdo->beginTransaction();

            // Verificar stock disponible
            $stock_actual = $this->obtenerStockActual($insumo_id);
            if ($stock_actual < $cantidad) {
                throw new Exception("Stock insuficiente. Disponible: {$stock_actual}, Requerido: {$cantidad}");
            }

            // 1. Registrar la operación de egreso
            $sql_operacion = "INSERT INTO operacion 
                (operacion_cantidad_de_productos, operacion_fecha_de_actualizacion, RELA_tipo_de_operacion, RELA_insumos) 
                VALUES (?, NOW(), 2, ?)";
            
            $operacion_id = $this->db->insert($sql_operacion, [$cantidad, $insumo_id]);

            // 2. Actualizar stock actual del insumo
            $sql_update = "UPDATE insumos 
                SET insumo_stock_actual = insumo_stock_actual - ? 
                WHERE ID_insumo = ?";
            
            $this->db->update($sql_update, [$cantidad, $insumo_id]);

            // 3. Si es por un pastel, registrar la relación
            if ($pastel_id) {
                $sql_relacion = "INSERT INTO operacion_pastel_personalizado 
                    (RELA_operacion, RELA_pastel_personalizado, operacion_pastel_cantidad_utilizada) 
                    VALUES (?, ?, ?)";
                $this->db->insert($sql_relacion, [$operacion_id, $pastel_id, $cantidad]);
            }

            $this->pdo->commit();
            return ['success' => true, 'operacion_id' => $operacion_id];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Descontar stock automáticamente por pedido
     */
    public function descontarStockPorPedido($pedido_id) {
        try {
            $this->pdo->beginTransaction();

            // 1. Obtener el pastel personalizado del pedido
            $sql_pedido = "SELECT pd.RELA_pastel_personalizado, pp.pastel_personalizado_pisos_total
                FROM pedido_detalle pd
                JOIN pastel_personalizado pp ON pd.RELA_pastel_personalizado = pp.ID_pastel_personalizado
                WHERE pd.RELA_pedido = ?";
            
            $pedido_data = $this->db->selectOne($sql_pedido, [$pedido_id]);
            
            if (!$pedido_data) {
                throw new Exception("No se encontró el pedido o pastel asociado");
            }

            $pastel_id = $pedido_data['RELA_pastel_personalizado'];
            $pisos_total = $pedido_data['pastel_personalizado_pisos_total'];

            // 2. Obtener los ingredientes del pastel por receta
            $sql_receta = "SELECT ri.RELA_insumos, ri.receta_insumo_cantidad, i.insumo_nombre
                FROM receta_insumos ri
                JOIN insumos i ON ri.RELA_insumos = i.ID_insumo
                WHERE ri.RELA_receta = 1"; // Por ahora usamos receta 1 (Pastel Chocolate)
            
            $ingredientes = $this->db->select($sql_receta);

            // 3. Descontar stock por cada ingrediente
            $errores = [];
            foreach ($ingredientes as $ingrediente) {
                $cantidad_necesaria = $ingrediente['receta_insumo_cantidad'] * $pisos_total;
                
                $resultado = $this->registrarEgreso(
                    $ingrediente['RELA_insumos'], 
                    $cantidad_necesaria, 
                    $pastel_id
                );
                
                if (!$resultado['success']) {
                    $errores[] = $ingrediente['insumo_nombre'] . ": " . $resultado['error'];
                }
            }

            if (!empty($errores)) {
                throw new Exception("Errores al descontar stock: " . implode(", ", $errores));
            }

            $this->pdo->commit();
            return ['success' => true, 'mensaje' => 'Stock descontado correctamente'];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Obtener stock actual de un insumo
     */
    public function obtenerStockActual($insumo_id) {
        $sql = "SELECT insumo_stock_actual FROM insumos WHERE ID_insumo = ?";
        $result = $this->db->selectOne($sql, [$insumo_id]);
        return $result ? $result['insumo_stock_actual'] : 0;
    }

    /**
     * Obtener estado de stock de un insumo
     */
    public function obtenerEstadoStock($insumo_id) {
        $sql = "SELECT 
            insumo_stock_actual,
            insumo_stock_minimo,
            CASE
                WHEN insumo_stock_actual = 0 THEN 'Sin stock'
                WHEN insumo_stock_actual <= insumo_stock_minimo THEN 'Bajo stock'
                ELSE 'Stock normal'
            END AS estado_stock
            FROM insumos 
            WHERE ID_insumo = ?";
        
        return $this->db->selectOne($sql, [$insumo_id]);
    }

    /**
     * Obtener todos los insumos con su estado de stock
     */
    public function obtenerTodosInsumosConEstado() {
        $sql = "SELECT 
            i.*,
            c.categoria_insumo_nombre,
            p.proveedor_nombre,
            um.unidad_medida_nombre as insumo_unidad_medida,
            es.estado_insumo_descripcion,
            CASE
                WHEN i.insumo_stock_actual = 0 THEN 'Sin stock'
                WHEN i.insumo_stock_actual <= i.insumo_stock_minimo THEN 'Bajo stock'
                ELSE 'Stock normal'
            END AS estado_stock
            FROM insumos i
            JOIN categoria_insumos c ON i.RELA_categoria_insumos = c.ID_categoria_insumo
            JOIN proveedor p ON i.RELA_proveedor = p.ID_proveedor
            JOIN unidad_medida um ON i.RELA_unidad_medida = um.ID_unidad_medida
            JOIN estado_insumos es ON i.RELA_estado_insumo = es.ID_estado_insumo
            ORDER BY i.insumo_nombre";
        
        return $this->db->select($sql);
    }

    /**
     * Obtener insumos con stock bajo
     */
    public function obtenerInsumosStockBajo() {
        $sql = "SELECT 
            i.*,
            c.categoria_insumo_nombre,
            p.proveedor_nombre,
            um.unidad_medida_nombre as insumo_unidad_medida,
            CASE
                WHEN i.insumo_stock_actual = 0 THEN 'Sin stock'
                ELSE 'Bajo stock'
            END AS estado_stock
            FROM insumos i
            JOIN categoria_insumos c ON i.RELA_categoria_insumos = c.ID_categoria_insumo
            JOIN proveedor p ON i.RELA_proveedor = p.ID_proveedor
            JOIN unidad_medida um ON i.RELA_unidad_medida = um.ID_unidad_medida
            WHERE i.insumo_stock_actual <= i.insumo_stock_minimo
            ORDER BY i.insumo_stock_actual ASC";
        
        return $this->db->select($sql);
    }

    /**
     * Obtener historial de operaciones de un insumo
     */
    public function obtenerHistorialInsumo($insumo_id, $limite = 50) {
        $sql = "SELECT 
            o.*,
            top.tipo_de_operacion_descripcion,
            DATE_FORMAT(o.operacion_fecha_de_actualizacion, '%d/%m/%Y %H:%i') as fecha_formateada,
            CASE 
                WHEN o.RELA_tipo_de_operacion = 1 THEN '+'
                ELSE '-'
            END as signo_operacion
            FROM operacion o
            JOIN tipo_de_operacion top ON o.RELA_tipo_de_operacion = top.ID_tipo_de_operacion
            WHERE o.RELA_insumos = ?
            ORDER BY o.operacion_fecha_de_actualizacion DESC
            LIMIT ?";
        
        return $this->db->select($sql, [$insumo_id, $limite]);
    }

    /**
     * Obtener movimientos de stock por fecha
     */
    public function obtenerMovimientosPorFecha($fecha_inicio, $fecha_fin = null) {
        if (!$fecha_fin) {
            $fecha_fin = date('Y-m-d');
        }
    
        $sql = "SELECT 
                    o.*,
                    i.insumo_nombre,
                    um.unidad_medida_nombre AS unidad_medida,
                    top.tipo_de_operacion_descripcion,
                    DATE_FORMAT(o.operacion_fecha_de_actualizacion, '%d/%m/%Y %H:%i') AS fecha_formateada
                FROM operacion o
                JOIN insumos i ON o.RELA_insumos = i.ID_insumo
                JOIN tipo_de_operacion top ON o.RELA_tipo_de_operacion = top.ID_tipo_de_operacion
                JOIN unidad_medida um ON i.RELA_unidad_medida = um.ID_unidad_medida
                WHERE DATE(o.operacion_fecha_de_actualizacion) BETWEEN ? AND ?
                ORDER BY o.operacion_fecha_de_actualizacion DESC";
        
        return $this->db->select($sql, [$fecha_inicio, $fecha_fin]);
    }
    

    /**
     * Verificar si hay stock suficiente para un pastel
     */
    public function verificarStockSuficiente($pastel_id) {
        try {
            // Obtener los ingredientes necesarios
            $sql_receta = "SELECT ri.RELA_insumos, ri.receta_insumo_cantidad, i.insumo_nombre, i.insumo_stock_actual
                FROM receta_insumos ri
                JOIN insumos i ON ri.RELA_insumos = i.ID_insumo
                WHERE ri.RELA_receta = 1"; // Por ahora receta 1
            
            $ingredientes = $this->db->select($sql_receta);
            
            $insuficientes = [];
            foreach ($ingredientes as $ingrediente) {
                if ($ingrediente['insumo_stock_actual'] < $ingrediente['receta_insumo_cantidad']) {
                    $insuficientes[] = $ingrediente['insumo_nombre'];
                }
            }

            return [
                'suficiente' => empty($insuficientes),
                'insuficientes' => $insuficientes
            ];

        } catch (Exception $e) {
            return ['suficiente' => false, 'error' => $e->getMessage()];
        }
    }
}
?>
