<?php
/**
 * Controlador para alta de insumos - Módulo de Stock
 * Sistema de gestión de stock para Cake Party
 */

require_once __DIR__ . '/../../config/conexion.php';
session_start();

// Verificar permisos (solo administradores y gerentes)
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['perfil_id'], [1, 4])) {
    header('Location: ../../index.php?error=not_authorized');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar datos recibidos
        $insumo_nombre = isset($_POST['insumo_nombre']) ? trim($_POST['insumo_nombre']) : '';
        $RELA_unidad_medida = isset($_POST['RELA_unidad_medida']) ? (int)$_POST['RELA_unidad_medida'] : 0;
        $insumo_stock_minimo = isset($_POST['insumo_stock_minimo']) ? (float)$_POST['insumo_stock_minimo'] : 0;
        $RELA_categoria_insumos = isset($_POST['RELA_categoria_insumos']) ? (int)$_POST['RELA_categoria_insumos'] : 0;
        $RELA_proveedor = isset($_POST['RELA_proveedor']) ? (int)$_POST['RELA_proveedor'] : 0;
        $insumo_precio_costo = isset($_POST['insumo_precio_costo']) ? (float)$_POST['insumo_precio_costo'] : null;

        // Validaciones
        if (empty($insumo_nombre)) {
            throw new Exception("El nombre del insumo es obligatorio");
        }

        if (strlen($insumo_nombre) > 50) {
            throw new Exception("El nombre del insumo no puede exceder 50 caracteres");
        }

        if (!$RELA_unidad_medida) {
            throw new Exception("Debe seleccionar una unidad de medida");
        }

        if ($insumo_stock_minimo < 0) {
            throw new Exception("El stock mínimo no puede ser negativo");
        }

        if (!$RELA_categoria_insumos) {
            throw new Exception("Debe seleccionar una categoría");
        }

        if (!$RELA_proveedor) {
            throw new Exception("Debe seleccionar un proveedor");
        }

        // Conectar a la base de datos
        $pdo = getConexion();

        // Verificar que la unidad de medida existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM unidad_medida WHERE ID_unidad_medida = ?");
        $stmt->execute([$RELA_unidad_medida]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception("La unidad de medida seleccionada no existe");
        }

        // Verificar que la categoría existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categoria_insumos WHERE ID_categoria_insumo = ?");
        $stmt->execute([$RELA_categoria_insumos]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception("La categoría seleccionada no existe");
        }

        // Verificar que el proveedor existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM proveedor WHERE ID_proveedor = ?");
        $stmt->execute([$RELA_proveedor]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception("El proveedor seleccionado no existe");
        }

        // Verificar que no existe un insumo con el mismo nombre
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM insumos WHERE insumo_nombre = ? AND RELA_estado_insumo = 1");
        $stmt->execute([$insumo_nombre]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Ya existe un insumo con ese nombre");
        }

        // Insertar el nuevo insumo
        $sql = "INSERT INTO insumos 
                (insumo_nombre, insumo_stock_actual, insumo_stock_minimo, insumo_precio_costo, 
                 RELA_unidad_medida, RELA_categoria_insumos, RELA_proveedor, RELA_estado_insumo) 
                VALUES (?, 0, ?, ?, ?, ?, ?, 1)";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $insumo_nombre,
            $insumo_stock_minimo,
            $insumo_precio_costo,
            $RELA_unidad_medida,
            $RELA_categoria_insumos,
            $RELA_proveedor
        ]);

        if ($result) {
            // Redirigir con mensaje de éxito
            $mensaje = "El insumo '$insumo_nombre' fue creado exitosamente";
            header("Location: ../../views/stock/listado_insumos.php?success=1&mensaje=" . urlencode($mensaje));
            exit();
        } else {
            throw new Exception("Error al insertar el insumo en la base de datos");
        }

    } catch (Exception $e) {
        // Redirigir con mensaje de error
        header("Location: ../../views/stock/form_alta_insumo.php?error=1&mensaje=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Si no es POST, redirigir al formulario
    header("Location: ../../views/stock/form_alta_insumo.php");
    exit();
}
?>
