<?php
require_once __DIR__ . '/../../config/conexion.php';


class Gastos
{
    private $ID_gasto;
    private $RELA_caja;
    private $RELA_categoria;
    private $RELA_metodo_pago;
    private $gasto_fecha;
    private $gasto_monto;
    private $gasto_descripcion;

    public function __construct($data = [])
    {
        $this->ID_gasto = $data['ID_gasto'] ?? null;
        $this->RELA_caja = $data['RELA_caja'] ?? null;
        $this->RELA_categoria = $data['RELA_categoria'] ?? null;
        $this->RELA_metodo_pago = $data['RELA_metodo_pago'] ?? null;
        $this->gasto_fecha = $data['gasto_fecha'] ?? date('Y-m-d H:i:s');
        $this->gasto_monto = $data['gasto_monto'] ?? 0;
        $this->gasto_descripcion = $data['gasto_descripcion'] ?? '';
    }

    public function guardar()
    {
        $pdo = getConexion();
        $sql = "INSERT INTO gastos (RELA_caja, RELA_categoria, RELA_metodo_pago, gasto_fecha, gasto_monto, gasto_descripcion)
                VALUES (:id_caja, :categoria, :metodo, :fecha, :monto, :descripcion)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_caja' => $this->RELA_caja,
            ':categoria' => $this->RELA_categoria,
            ':metodo' => $this->RELA_metodo_pago,
            ':fecha' => $this->gasto_fecha,
            ':monto' => $this->gasto_monto,
            ':descripcion' => $this->gasto_descripcion
        ]);
        return $pdo->lastInsertId();
    }

    public function traerGastos()
    {
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

    public function traerPorCaja($RELA_caja)
    {
        $pdo = getConexion();
        $sql = "SELECT g.*, c.categoria_nombre, m.metodo_pago_descri
                FROM gastos g
                INNER JOIN categoria c ON g.RELA_categoria = c.ID_categoria
                INNER JOIN metodo_pago m ON g.RELA_metodo_pago = m.ID_metodo_pago
                WHERE g.RELA_caja = :id_caja
                ORDER BY g.gasto_fecha DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_caja' => $RELA_caja]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminar($ID_gasto)
    {
        $pdo = getConexion();
        $stmt = $pdo->prepare("DELETE FROM gastos WHERE ID_gasto = :id_gasto");
        return $stmt->execute([':id_gasto' => $ID_gasto]);
    }

    public function traerPorId($ID_gasto)
    {
        $pdo = getConexion();

        $sql = "SELECT g.*, c.categoria_nombre, m.metodo_pago_descri
            FROM gastos g
            INNER JOIN categoria c ON g.RELA_categoria = c.ID_categoria
            INNER JOIN metodo_pago m ON g.RELA_metodo_pago = m.ID_metodo_pago
            WHERE g.ID_gasto = :id_gasto";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_gasto' => $ID_gasto]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function actualizar($ID_gasto, $monto, $descripcion, $categoria, $metodo)
    {
        $pdo = getConexion();

        $sql = "UPDATE gastos 
            SET gasto_monto = :monto,
                gasto_descripcion = :descripcion,
                RELA_categoria = :categoria,
                RELA_metodo_pago = :metodo
            WHERE ID_gasto = :id_gasto";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            ':monto'        => $monto,
            ':descripcion'  => $descripcion,
            ':categoria'    => $categoria,
            ':metodo'       => $metodo,
            ':id_gasto'     => $ID_gasto
        ]);
    }
    public function traerCategorias()
    {
        $pdo = getConexion();

        $sql = "SELECT * FROM categoria ORDER BY categoria_nombre ASC";
        $stmt = $pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function traerMetodosPago()
    {
        $pdo = getConexion();

        $sql = "SELECT * FROM metodo_pago ORDER BY metodo_pago_descri ASC";
        $stmt = $pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // para el buscador 
    public function filtrar($filtros = [])
    {
        $pdo = getConexion();

        $sql = "SELECT g.*, c.categoria_nombre, m.metodo_pago_descri
            FROM gastos g
            INNER JOIN categoria c ON g.RELA_categoria = c.ID_categoria
            INNER JOIN metodo_pago m ON g.RELA_metodo_pago = m.ID_metodo_pago
            WHERE 1 = 1";

        $params = [];

        if (!empty($filtros['categoria'])) {
            $sql .= " AND g.RELA_categoria = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }

        if (!empty($filtros['metodo'])) {
            $sql .= " AND g.RELA_metodo_pago = :metodo";
            $params[':metodo'] = $filtros['metodo'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(g.gasto_fecha) >= :desde";
            $params[':desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(g.gasto_fecha) <= :hasta";
            $params[':hasta'] = $filtros['fecha_hasta'];
        }

        if (!empty($filtros['texto'])) {
            $sql .= " AND g.gasto_descripcion LIKE :texto";
            $params[':texto'] = '%' . $filtros['texto'] . '%';
        }

        $sql .= " ORDER BY g.gasto_fecha DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function contarFiltrados($filtros = [])
    {
        $pdo = getConexion();

        $sql = "SELECT COUNT(*) FROM gastos g WHERE 1=1";
        $params = [];

        if (!empty($filtros['categoria'])) {
            $sql .= " AND g.RELA_categoria = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }

        if (!empty($filtros['metodo'])) {
            $sql .= " AND g.RELA_metodo_pago = :metodo";
            $params[':metodo'] = $filtros['metodo'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(g.gasto_fecha) >= :desde";
            $params[':desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(g.gasto_fecha) <= :hasta";
            $params[':hasta'] = $filtros['fecha_hasta'];
        }

        if (!empty($filtros['texto'])) {
            $sql .= " AND g.gasto_descripcion LIKE :texto";
            $params[':texto'] = '%' . $filtros['texto'] . '%';
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn();
    }


    public function filtrarConPaginacion($filtros, $offset, $limite)
    {
        $pdo = getConexion();

        $sql = "SELECT g.*, c.categoria_nombre, m.metodo_pago_descri
            FROM gastos g
            INNER JOIN categoria c ON g.RELA_categoria = c.ID_categoria
            INNER JOIN metodo_pago m ON g.RELA_metodo_pago = m.ID_metodo_pago
            WHERE 1=1";

        $params = [];

        if (!empty($filtros['categoria'])) {
            $sql .= " AND g.RELA_categoria = :categoria";
            $params[':categoria'] = $filtros['categoria'];
        }

        if (!empty($filtros['metodo'])) {
            $sql .= " AND g.RELA_metodo_pago = :metodo";
            $params[':metodo'] = $filtros['metodo'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(g.gasto_fecha) >= :desde";
            $params[':desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(g.gasto_fecha) <= :hasta";
            $params[':hasta'] = $filtros['fecha_hasta'];
        }

        if (!empty($filtros['texto'])) {
            $sql .= " AND g.gasto_descripcion LIKE :texto";
            $params[':texto'] = '%' . $filtros['texto'] . '%';
        }

        $sql .= " ORDER BY g.gasto_fecha DESC LIMIT :offset, :limite";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => &$value) {
            $stmt->bindParam($key, $value);
        }

        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
