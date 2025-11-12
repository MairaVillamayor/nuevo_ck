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
// 2) Consultar pedidos del usuario
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
LEFT JOIN usuarios u ON pe.RELA_usuario = u.ID_usuario
LEFT JOIN pedido_detalle pd ON pd.RELA_pedido = pe.ID_pedido
LEFT JOIN pastel_personalizado pp ON pp.ID_pastel_personalizado = pd.RELA_pastel_personalizado
LEFT JOIN metodo_pago mp ON pe.RELA_metodo_pago = mp.ID_metodo_pago
LEFT JOIN estado e ON pe.RELA_estado = e.ID_estado
LEFT JOIN pedido_envio p_envio ON pe.RELA_pedido_envio = p_envio.ID_pedido_envio
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
    <title>Mis Pedidos - Cake Party</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Estilo Cake Party -->
    <style>
        body {
            background-color: #fff6fa;
            font-family: 'Poppins', sans-serif;
        }

        h1 {
            color: #e85d9e;
            font-weight: 700;
            text-shadow: 1px 1px #ffd6ea;
        }

        .card {
            border: none;
            border-radius: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 15px rgba(232, 93, 158, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(232, 93, 158, 0.2);
        }

        .card-title {
            color: #e85d9e;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #e85d9e;
            border-color: #e85d9e;
            color: white;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #ff7fbf;
            border-color: #ff7fbf;
        }

        .btn-outline-danger {
            border-radius: 30px;
            color: #e85d9e;
            border-color: #e85d9e;
        }

        .btn-outline-danger:hover {
            background-color: #e85d9e;
            color: #fff;
        }

        .badge {
            font-size: 0.9rem;
            border-radius: 10px;
            padding: 6px 10px;
        }

        .alert-info {
            background-color: #ffe4ef;
            color: #e85d9e;
            border: none;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <h1 class="text-center mb-4">🎂​ Mis Pedidos 🎂​</h1>

        <?php if (count($pedidos) > 0): ?>
            <?php foreach ($pedidos as $pedido):
                $estado_clase = strtolower(str_replace(" ", "-", $pedido['estado_descri']));
                $estado = strtolower(trim($pedido['estado_descri']));
                $id = (int)$pedido['ID_pedido'];
                $url_cancelar = "../../controllers/cliente/cancelar_pedido.php?id=" . $id;
            ?>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">🍰​ Pedido #<?= htmlspecialchars($pedido['ID_pedido']) ?></h5>
                        <p class="card-text mb-1"><strong>Cliente:</strong> <?= htmlspecialchars($pedido['usuario_nombre']) ?></p>
                        <p class="card-text mb-1"><strong>Fecha:</strong> <?= htmlspecialchars($pedido['pedido_fecha']) ?></p>
                        <p class="card-text mb-1"><strong>Descripción:</strong> <?= htmlspecialchars($pedido['pastel_personalizado_descripcion'] ?? 'Sin descripción') ?></p>
                        <p class="card-text mb-1"><strong>Entrega:</strong> <?= htmlspecialchars($pedido['envio_fecha_hora_entrega'] ?? 'N/A') ?></p>
                        <p class="card-text mb-1"><strong>Dirección:</strong>
                            <?= htmlspecialchars($pedido['envio_calle_numero'] ?? 'No disponible') ?>
                            <?= !empty($pedido['envio_barrio']) ? ' (Barrio: ' . htmlspecialchars($pedido['envio_barrio']) . ')' : '' ?>
                        </p>
                        <p class="card-text mb-1"><strong>Localidad:</strong> <?= htmlspecialchars($pedido['envio_localidad'] ?? 'N/A') ?></p>
                        <p class="card-text mb-1"><strong>Método de pago:</strong> <?= htmlspecialchars($pedido['metodo_pago'] ?? 'No especificado') ?></p>

                        <?php
                        $badgeClass = match ($estado) {
                            'pendiente' => 'bg-warning text-dark',
                            'en proceso', 'en-proceso' => 'bg-info text-dark',
                            'enviado' => 'bg-success',
                            'cancelado' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                        ?>
                        <p class="card-text mt-2">
                            <strong>Estado:</strong>
                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($pedido['estado_descri']) ?></span>
                        </p>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <?php if ($estado === 'pendiente'): ?>
                                <form id="form-cancelar-<?= $id ?>" action="../../controllers/cliente/cancelar_pedido.php" method="POST" class="d-inline">
                                    <input type="hidden" name="ID_pedido" value="<?= $id ?>">
                                    <button type="button" class="btn btn-outline-danger"
                                        onclick="return mostrarConfirmacionForm('form-cancelar-<?= $id ?>',
                                        '⚠️ ¿Seguro que querés cancelar el pedido #<?= $id ?>? Esta acción no se puede deshacer.')">
                                        Cancelar
                                    </button>
                                </form>


                                <a href="../../controllers/pago.php?id_pedido=<?= $id ?>"
                                    class="btn btn-primary"
                                    onclick="return confirmarBaja('💳 Vas a proceder al pago del pedido #<?= $id ?>. ¿Deseás continuar?')">
                                    Ir a Pagar
                                </a>

                            <?php elseif ($estado === 'en proceso' || $estado === 'en-proceso'): ?>
                                <a href="<?= $url_cancelar ?>" class="btn btn-outline-danger"
                                    onclick="return mostrarConfirmacion(
                           '<?= $url_cancelar ?>',
                           '⚠️ El pedido #<?= $id ?> está en proceso. ¿Confirmás que querés cancelarlo?'
                           )">Cancelar</a>

                            <?php elseif ($estado === 'enviado'): ?>
                                <a href="../../views/cliente/detalle_pedido.php?id=<?= $id ?> "
                                    class="btn btn-primary">
                                    Ver Detalles
                                </a>

                            <?php else: ?>
                                <button type="button" class="btn btn-secondary" disabled>Pedido Inactivo</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="alert alert-info text-center">
                🍰 No tenés pedidos realizados aún. ¡Hacé tu primer pedido y endulzá tu día!
            </div>
        <?php endif; ?>
    </div>

    <script>
        function confirmarBaja(mensaje) {
            return confirm(mensaje);
        }

        function mostrarConfirmacion(url, mensaje) {
            Swal.fire({
                title: mensaje,
                text: "Esta acción es irreversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e85d9e',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No, volver',
                background: '#fff6fa'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
            return false;
        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>