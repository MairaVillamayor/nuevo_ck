<?php
require_once("../../config/conexion.php");

class Factura
{

    const ESTADO_PAGADO_ID = 1;
    const ESTADO_CANCELADO_ID = 2;


    public function insertarFactura($idPersona, $subtotal, $ivaMonto, $total, $tasaIva)
    {
        $conexion = Conexion::getInstance();
        $conn = $conexion->getConnection();

        $estadoId = self::ESTADO_PAGADO_ID;

        $sql = "INSERT INTO factura (
                    RELA_persona, factura_subtotal, factura_iva_monto, 
                    factura_total, factura_iva_tasa, RELA_estado_factura, factura_fecha_emision
                ) VALUES (
                    :idPersona, :subtotal, :ivaMonto, 
                    :total, :tasaIva, :estadoId, NOW()
                )";

        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':idPersona', $idPersona, PDO::PARAM_INT);
            $stmt->bindParam(':subtotal', $subtotal);
            $stmt->bindParam(':ivaMonto', $ivaMonto);
            $stmt->bindParam(':total', $total);
            $stmt->bindParam(':tasaIva', $tasaIva);
            $stmt->bindParam(':estadoId', $estadoId, PDO::PARAM_INT);

            $stmt->execute();

            $idFactura = $conn->lastInsertId();
            $conn->commit();

            return $idFactura;
        } catch (PDOException $e) {
            $conn->rollBack();
            error_log("Error al insertar factura: " . $e->getMessage());
            return 0;
        }
    }

    public function insertarDetalle($idFactura, $idProductoFinalizado, $cantidad)
    {
        $conexion = Conexion::getInstance();
        $conn = $conexion->getConnection();

        $sql = "INSERT INTO factura_detalle (
                    RELA_factura, RELA_producto_finalizado, factura_detalle_cantidad
                ) VALUES (
                    :idFactura, :idProductoFinalizado, :cantidad
                )";

        try {
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':idFactura', $idFactura, PDO::PARAM_INT);
            $stmt->bindParam(':idProductoFinalizado', $idProductoFinalizado, PDO::PARAM_INT);
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al insertar detalle de factura: " . $e->getMessage());
            return false;
        }
    }

    public function insertarPago($idFactura, $idMetodoPago, $monto, $interes)
    {
        $conexion = Conexion::getInstance();
        $conn = $conexion->getConnection();

        $sql = "INSERT INTO factura_pagos (
                    RELA_factura, RELA_metodo_pago, pago_monto, pago_interes
                ) VALUES (
                    :idFactura, :idMetodoPago, :monto, :interes
                )";

        try {
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':idFactura', $idFactura, PDO::PARAM_INT);
            $stmt->bindParam(':idMetodoPago', $idMetodoPago, PDO::PARAM_INT);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':interes', $interes);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al insertar pago: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarEstadoFactura($idFactura, $idEstado)
    {
        $conexion = Conexion::getInstance();
        $conn = $conexion->getConnection();

        $sql = "UPDATE factura SET RELA_estado_factura = :idEstado WHERE ID_factura = :idFactura";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':idEstado', $idEstado, PDO::PARAM_INT);
            $stmt->bindParam(':idFactura', $idFactura, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar estado: " . $e->getMessage());
            return false;
        }
    }

    public function get_facturas_con_filtros(
        $cliente = null,
        $fechaDesde = null,
        $fechaHasta = null,
        $limite = null,
        $offset = null
    ) {

        $conexion = Conexion::getInstance()->getConnection();

        $sql = "SELECT 
            f.ID_factura, 
            f.factura_fecha_emision, 
            f.factura_total, 
            ef.estado_factura_descri AS estado,
            CONCAT(p.persona_nombre, ' ', p.persona_apellido) AS cliente,
            p.persona_documento
        FROM factura f
        JOIN persona p ON f.RELA_persona = p.id_persona
        JOIN estado_factura ef ON f.RELA_estado_factura = ef.ID_estado_factura
        WHERE 1=1";

        $params = [];

        // Filtro por cliente
        if (!empty($cliente)) {
            $sql .= " AND (p.persona_nombre LIKE :cliente_nombre 
                      OR p.persona_apellido LIKE :cliente_apellido 
                      OR p.persona_documento LIKE :cliente_doc)";
            $params[':cliente_nombre'] = '%' . $cliente . '%';
            $params[':cliente_apellido'] = '%' . $cliente . '%';
            $params[':cliente_doc'] = '%' . $cliente . '%';
        }

        // Filtro por fechaDesde
        if (!empty($fechaDesde)) {
            $sql .= " AND f.factura_fecha_emision >= :fechaDesde";
            $params[':fechaDesde'] = $fechaDesde;
        }

        // Filtro por fechaHasta
        if (!empty($fechaHasta)) {
            try {
                $fechaHastaObj = new DateTime($fechaHasta);
                $fechaHastaObj->modify('+1 day'); // incluir todo el día final
                $fechaHastaLimite = $fechaHastaObj->format('Y-m-d');
                $sql .= " AND f.factura_fecha_emision < :fechaHastaLimite";
                $params[':fechaHastaLimite'] = $fechaHastaLimite;
            } catch (Exception $e) {
                // Si fechaHasta no es válida, ignoramos el filtro
                error_log("FechaHasta inválida: " . $e->getMessage());
            }
        }

        $sql .= " ORDER BY f.factura_fecha_emision DESC";

        // Paginación
        if ($limite !== null && $offset !== null) {
            $sql .= " LIMIT :offset, :limite";
        }

        try {
            $stmt = $conexion->prepare($sql);

            // Asignar parámetros de filtros
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val, PDO::PARAM_STR);
            }

            // Asignar parámetros de paginación
            if ($limite !== null && $offset !== null) {
                $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
                $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar facturas: " . $e->getMessage());
            return [];
        }
    }

    public function get_total_facturas_con_filtros($cliente = null, $fechaDesde = null, $fechaHasta = null)
    {
        $conexion = Conexion::getInstance()->getConnection();

        $sql = "SELECT COUNT(*) AS total
            FROM factura f
            JOIN persona p ON f.RELA_persona = p.id_persona
            JOIN estado_factura ef ON f.RELA_estado_factura = ef.ID_estado_factura
            WHERE 1=1";

        $params = [];

        if (!empty($cliente)) {
            $sql .= " AND (p.persona_nombre LIKE :cliente_nombre OR p.persona_apellido LIKE :cliente_apellido OR p.persona_documento LIKE :cliente_doc)";
            $params[':cliente_nombre'] = '%' . $cliente . '%';
            $params[':cliente_apellido'] = '%' . $cliente . '%';
            $params[':cliente_doc'] = '%' . $cliente . '%';
        }

        if (!empty($fechaDesde)) {
            $sql .= " AND f.factura_fecha_emision >= :fechaDesde";
            $params[':fechaDesde'] = $fechaDesde;
        }

        if (!empty($fechaHasta)) {
            $fechaHastaObj = new DateTime($fechaHasta);
            $fechaHastaObj->modify('+1 day');
            $fechaHastaLimite = $fechaHastaObj->format('Y-m-d');
            $sql .= " AND f.factura_fecha_emision < :fechaHastaLimite";
            $params[':fechaHastaLimite'] = $fechaHastaLimite;
        }

        $stmt = $conexion->prepare($sql);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val, PDO::PARAM_STR);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }


    public function obtenerProductosDeFactura($idFactura)
    {
        global $conexion;

        $sql = "SELECT * FROM factura_detalle WHERE RELA_factura = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$idFactura]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
