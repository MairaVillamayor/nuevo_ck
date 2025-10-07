<?php
// mensaje.php

$tipo      = $_GET['tipo']      ?? 'info';   // exito, error, warning, info
$titulo    = $_GET['titulo']    ?? 'Mensaje';
$mensaje   = $_GET['mensaje']   ?? '';
$detalle   = $_GET['detalle']   ?? '';
$redirect  = $_GET['redirect_to'] ?? '';  // URL a la que volver
$delay     = isset($_GET['delay']) ? intval($_GET['delay']) : 0; // segundos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($titulo); ?> - Cake Party</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .msg-container {
            max-width: 450px;
            margin: 80px auto;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            font-family: Arial, sans-serif;
        }
        .msg-container h2 { margin-bottom: 15px; }
        .msg-container p { margin-bottom: 20px; font-size: 16px; color: #444; }
        .msg-container .detalle { font-size: 13px; color: #888; margin-top: 10px; }

        /* Colores según tipo */
        .exito   { background: #fce4ec; color: #e91e63; box-shadow: 0 0 15px rgba(255,182,193,0.4); }
        .error   { background: #ffebee; color: #d32f2f; box-shadow: 0 0 15px rgba(244,67,54,0.3); }
        .warning { background: #fff3e0; color: #ef6c00; box-shadow: 0 0 15px rgba(255,152,0,0.3); }
        .info    { background: #e3f2fd; color: #1976d2; box-shadow: 0 0 15px rgba(33,150,243,0.3); }
    </style>

    <?php if ($redirect && $delay >= 0): ?>
        <meta http-equiv="refresh" content="<?php echo $delay; ?>;url=<?php echo htmlspecialchars($redirect); ?>">
    <?php endif; ?>
</head>
<body>
    <div class="msg-container <?php echo htmlspecialchars($tipo); ?>">
        <h2><?php echo htmlspecialchars($titulo); ?></h2>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
        <?php if (!empty($detalle)): ?>
            <div class="detalle"><?php echo htmlspecialchars($detalle); ?></div>
        <?php endif; ?>

        <?php if ($redirect && $delay > 0): ?>
            <p>Redirigiendo en <?php echo $delay; ?> segundos...</p>
        <?php elseif ($redirect): ?>
            <p><a href="<?php echo htmlspecialchars($redirect); ?>">Continuar</a></p>
        <?php endif; ?>
    </div>
</body>
</html>