<?php
require_once __DIR__ . '/../../config/conexion.php';
include("../../includes/navegacion.php");

session_start();
$pdo = getConexion();

// -----------------
// 1) Validar sesión
// -----------------
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php?error=not_logged');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// -------------------------
// 2) Consultar pedidos reales SOLO del usuario
// -------------------------
$sql = "SELECT 
    pe.ID_pedido,
    pe.pedido_fecha,
    p_envio.envio_fecha_hora_entrega,
    p_envio.envio_calle_numero,
    p_envio.envio_barrio,
    p_envio.envio_localidad,
    u.usuario_nombre,
    pp.pastel_personalizado_descripcion,
    mp.metodo_pago_descri AS metodo_pago,
    e.estado_descri
FROM pedido pe
LEFT JOIN usuarios u 
    ON pe.RELA_usuario = u.ID_usuario
LEFT JOIN pedido_detalle pd 
    ON pd.RELA_pedido = pe.ID_pedido
LEFT JOIN pastel_personalizado pp 
    ON pp.ID_pastel_personalizado = pd.RELA_pastel_personalizado
LEFT JOIN metodo_pago mp 
    ON pe.RELA_metodo_pago = mp.ID_metodo_pago
LEFT JOIN estado e 
    ON pe.RELA_estado = e.ID_estado
LEFT JOIN pedido_envio p_envio
    ON pe.RELA_pedido_envio = p_envio.ID_pedido_envio
WHERE pe.RELA_usuario = :usuario_id
ORDER BY pe.ID_pedido DESC;";

$stmt = $pdo->prepare($sql);
$stmt->execute(['usuario_id' => $usuario_id]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff5f8;
            margin: 0;
            padding: 20px;
        }

        .contenedor {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 25px;
        }

        .pedido-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .info-pedido {
            flex-grow: 1;
            margin-right: 20px;
        }

        .info-pedido p {
            margin: 5px 0;
        }

        .estado {
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px 8px;
            border-radius: 4px;
            color: #fff;
            font-size: 0.85em;
        }

        .estado.pendiente {
            background-color: #ff9800;
        }

        .estado.en-proceso {
            background-color: #539cb3ff;
        }

        .estado.enviado {
            background-color: #589458ff;
        }

        .estado.cancelado {
            background-color: #de0505ff;
        }

        .acciones {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            display: inline-block;
        }

        .btn-pagar {
            background-color: #0275d8;
        }

        .btn-cancelar {
            background-color: #de0505ff;
        }

        .btn-cake {
            display: inline-block;
            padding: 10px 18px;
            margin: 10px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 8px;
            border: none;
            background-color: #e91e63;
            color: #fff;
            text-decoration: none;
            text-align: center;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-cake:hover {
            background-color: #d81b60;
            transform: translateY(-2px);
        }

        .btn-cake:active {
            background-color: #ad1457;
            transform: translateY(0);
        }

        .botones-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="contenedor">
        <h1>Mis Pedidos</h1>

        <?php if (count($pedidos) > 0): ?>
            <?php foreach ($pedidos as $pedido):
                // Normalizamos estado a clases CSS
                $estado_clase = strtolower(str_replace(" ", "-", $pedido['estado_descri']));
            ?>
                <div class="pedido-card">
                    <div class="info-pedido">
                        <h4>Pedido #<?= htmlspecialchars($pedido['ID_pedido']) ?></h4>
                        <p><strong>Usuario:</strong> <?= htmlspecialchars($pedido['usuario_nombre']) ?></p>
                        <p><strong>Fecha:</strong> <?= htmlspecialchars($pedido['pedido_fecha']) ?></p>
                        <p><strong>Descripción:</strong> <?= htmlspecialchars($pedido['pastel_personalizado_descripcion']) ?></p>
                        <p><strong>Entrega:</strong> <?= htmlspecialchars($pedido['envio_fecha_hora_entrega'] ?? 'N/A') ?></p>
                        <p><strong>Dirección:</strong>
                            <?= htmlspecialchars($pedido['envio_calle_numero'] ?? 'Dirección no disponible') ?>
                            <?= !empty($pedido['envio_barrio']) ? ' (Barrio: ' . htmlspecialchars($pedido['envio_barrio']) . ')' : '' ?>
                        </p>
                        <p><strong>Localidad:</strong> <?= htmlspecialchars($pedido['envio_localidad'] ?? 'N/A') ?></p>
                        <p><strong>Método de pago:</strong> <?= htmlspecialchars($pedido['metodo_pago'] ?? 'No especificado') ?></p>
                        <p>Estado: <span class="estado <?= $estado_clase ?>"><?= htmlspecialchars($pedido['estado_descri']) ?></span></p>
                    </div>

                    <div class="pedido-card">
                         <div class="acciones">
                            <?php
                            $estado = strtolower(trim($pedido['estado_descri']));
                            $id = (int)$pedido['ID_pedido'];
                            $url_cancelar = "../../controllers/cliente/cancelar_pedido.php?id=" . $id; // URL de destino
                            ?>
                            
                            <?php if ($estado === 'pendiente'): ?>
                                <a href="<?= $url_cancelar ?>"
                                class="btn btn-cancelar"
                                onclick="return mostrarConfirmacion(
                                '<?= $url_cancelar ?>',
                                '⚠️ ¿Seguro que querés cancelar el pedido #<?= $id ?>? Esta acción no se puede deshacer.'
                                )">Cancelar
                                </a>
                                
                                <a href="../../controllers/pago.php?id_pedido=<?= $id ?>"
                                class="btn btn-pagar"
                                onclick="return confirmarBaja('💳 Vas a proceder al pago del pedido #<?= $id ?>. ¿Deseás continuar?'
                                )">Ir a Pagar
                                </a>
                                
                                <?php elseif ($estado === 'en proceso' || $estado === 'en-proceso'): ?>
                                    <a href="<?= $url_cancelar ?>"
                                    class="btn btn-cancelar"
                                    onclick="return mostrarConfirmacion(
                                    '<?= $url_cancelar ?>',
                                    '⚠️ El pedido #<?= $id ?> está en proceso. ¿Confirmás que querés cancelarlo de todas formas?'
                                    )">Cancelar
                                    </a>
                                    
                                    <?php elseif ($estado === 'enviado'): ?>
                                        <a href="../../views/cliente/detalle_pedido.php?id=<?= $id ?>"
                                        class="btn"
                                        onclick="return confirmarBaja('📦 Vas a ver los detalles del pedido #<?= $id ?>. ¿Continuar?')">
                                        Ver Detalles
                                        </a>
                                        <?php else: ?>
                                            <button type="button" class="btn" style="background-color: #ccc; cursor: default;">
                                                Pedido Inactivo
                                            </button>
                                            <?php endif; ?>
                         </div>
                </div>
                
                <script>
                function confirmarBaja(mensaje) {
                    return confirm(mensaje);
                    }
                    </script>
                    <script>
        // Función centralizada usando SweetAlert2
        function mostrarConfirmacion(url, mensaje) {
            Swal.fire({
                title: mensaje, // El mensaje que le pasaste desde PHP
                text: "Esta acción es irreversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF69B4', // Rosa fuerte (Hot Pink)
                cancelButtonColor: '#A9A9A9', // Gris oscuro
                confirmButtonText: '¡Sí, Cancelar!',
                cancelButtonText: 'No, Volver',
                background: '#FFFFFF' // Fondo Blanco
            }).then((result) => {
                // Si el usuario confirma, redirigimos a la URL
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
            // Es crucial devolver 'false' aquí para detener la acción nativa del 'a href'
            return false;
        }
    </script>


</body>
</html>                <?php endforeach; ?>
        <?php else: ?>
            <p>No tenés pedidos realizados aún.</p>
        <?php endif; ?> 