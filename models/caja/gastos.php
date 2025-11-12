<?php
require_once __DIR__ . '/../../config/conexion.php';

class Gastos {
    private $ID_gasto;
    private $RELA_caja;
    private $RELA_categoria;
    private $RELA_metodo_pago;
    private $gasto_fecha;
    private $gasto_monto;
    private $gasto_descripcion;

    public function __construct($data = []) {
        $this->ID_gasto = $data['ID_gasto'] ?? null;
        $this->RELA_caja = $data['RELA_caja'] ?? null;
        $this->RELA_categoria = $data['RELA_categoria'] ?? null;
        $this->RELA_metodo_pago = $data['RELA_metodo_pago'] ?? null;
        $this->gasto_fecha = $data['gasto_fecha'] ?? date('Y-m-d H:i:s');
        $this->gasto_monto = $data['gasto_monto'] ?? 0;
        $this->gasto_descripcion = $data['gasto_descripcion'] ?? '';
    }

    // ================================
    // 🔹 Guardar nuevo gasto
    // ================================
    public function guardar() {
        $pdo = getConexion();
        $sql = "INSERT INTO gastos (RELA_caja, RELA_categoria, RELA_metodo_pago, gasto_fecha, gasto_monto, gasto_descripcion)
                VALUES (:caja, :categoria, :metodo, :fecha, :monto, :descripcion)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':caja' => $this->RELA_caja,
            ':categoria' => $this->RELA_categoria,
            ':metodo' => $this->RELA_metodo_pago,
            ':fecha' => $this->gasto_fecha,
            ':monto' => $this->gasto_monto,
            ':descripcion' => $this->gasto_descripcion
        ]);
        return $pdo->lastInsertId();
    }

    // ================================
    // 🔹 Traer todos los gastos
    // ================================
    public function traerGastos() {
        $pdo = getConexion();
        $sql = "SELECT g.ID_gasto, g.gasto_fecha, g.gasto_monto, g.gasto_descripcion,
                       c.categoria_nombre, m.metodo_pago_descri, g.RELA_caja
                FROM gastos g
                INNER JOIN categoria c ON g.RELA_categoria = c.ID_categoria
                INNER JOIN metodo_pago m ON g.RELA_metodo_pago = m.ID_metodo_pago
                ORDER BY g.gasto_fecha DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================
    // 🔹 Traer gastos por caja
    // ================================
    public function traerPorCaja($RELA_caja) {
        $pdo = getConexion();
        $sql = "SELECT g.*, c.categoria_nombre, m.metodo_pago_descri
                FROM gastos g
                INNER JOIN categoria c ON g.RELA_categoria = c.ID_categoria
                INNER JOIN metodo_pago m ON g.RELA_metodo_pago = m.ID_metodo_pago
                WHERE g.RELA_caja = :caja
                ORDER BY g.gasto_fecha DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':caja' => $RELA_caja]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================
    // 🔹 Eliminar gasto
    // ================================
    public function eliminar($ID_gasto) {
        $pdo = getConexion();
        $stmt = $pdo->prepare("DELETE FROM gastos WHERE ID_gasto = :id");
        return $stmt->execute([':id' => $ID_gasto]);
    }
}
?>
