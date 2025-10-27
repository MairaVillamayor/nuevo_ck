<?php
/**
 * Controlador para apertura de caja
 * Sistema de gestión de caja para Cake Party
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/CajaController.php';

session_start();

// Verificar permisos (empleado, admin o gerente)
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['perfil_id'], [1, 2, 4])) {
    header('Location: ../../index.php?error=not_authorized');
    exit;
}

$cajaController = new CajaController();
$mensaje = '';
$tipo_mensaje = '';

// Procesar apertura de caja
if ($_POST && isset($_POST['monto_inicial'])) {
    $monto_inicial = floatval($_POST['monto_inicial']);
    
    if ($monto_inicial < 0) {
        $mensaje = 'El monto inicial no puede ser negativo';
        $tipo_mensaje = 'error';
    } else {
        $resultado = $cajaController->abrirCaja($_SESSION['usuario_id'], $monto_inicial);
        
        if ($resultado['success']) {
            header('Location: ../../views/caja/dashboard_caja.php?success=1&mensaje=' . urlencode($resultado['mensaje']));
            exit;
        } else {
            $mensaje = $resultado['error'];
            $tipo_mensaje = 'error';
        }
    }
}

// Verificar si ya tiene una caja abierta
$caja_abierta = $cajaController->obtenerCajaAbierta($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apertura de Caja - Cake Party</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/stock_dashboard.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .form-title {
            color: #e91e63;
            font-size: 2em;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #e91e63;
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.1);
        }

        .btn-submit {
            width: 100%;
            background: #e91e63;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: #c2185b;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .caja-abierta {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #e91e63;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div style="background: #ffe6ef; min-height: 100vh; padding: 20px;">
        <a href="../../views/caja/dashboard_caja.php" class="back-link">← Volver al Dashboard de Caja</a>
        
        <div class="form-container">
            <h1 class="form-title">Apertura de Caja</h1>
            
            <?php if ($caja_abierta): ?>
                <div class="caja-abierta">
                    <h3>Ya tienes una caja abierta</h3>
                    <p><strong>Fecha de apertura:</strong> <?= date('d/m/Y H:i', strtotime($caja_abierta['caja_fecha_apertura'])) ?></p>
                    <p><strong>Monto inicial:</strong> $<?= number_format($caja_abierta['caja_monto_inicial'], 2) ?></p>
                    <a href="../../views/caja/dashboard_caja.php" class="btn-submit" style="margin-top: 15px; text-decoration: none; display: inline-block;">
                        Ir al Dashboard
                    </a>
                </div>
            <?php else: ?>
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?= $tipo_mensaje ?>">
                        <?= htmlspecialchars($mensaje) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="monto_inicial">Monto Inicial ($)</label>
                        <input type="text" 
                               id="monto_inicial" 
                               name="monto_inicial" 
                               required
                               placeholder="0.00"
                               pattern="[0-9]+(\.[0-9]{1,2})?"
                               title="Ingrese un monto válido (ej: 1000.50)">
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        Abrir Caja
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Formatear y validar input de monto
        document.getElementById('monto_inicial')?.addEventListener('input', function(e) {
            let value = e.target.value;
            
            // Permitir solo números y un punto decimal
            value = value.replace(/[^0-9.]/g, '');
            
            // Evitar múltiples puntos decimales
            let parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            
            // Limitar a 2 decimales
            if (parts.length === 2 && parts[1].length > 2) {
                value = parts[0] + '.' + parts[1].substring(0, 2);
            }
            
            e.target.value = value;
        });

        // Validar formulario antes de enviar
        document.querySelector('form')?.addEventListener('submit', function(e) {
            const montoInput = document.getElementById('monto_inicial');
            const monto = parseFloat(montoInput.value);
            
            if (isNaN(monto) || monto < 0) {
                e.preventDefault();
                alert('Por favor ingrese un monto válido mayor o igual a 0');
                montoInput.focus();
                return false;
            }
            
            if (monto > 999999.99) {
                e.preventDefault();
                alert('El monto no puede ser mayor a $999,999.99');
                montoInput.focus();
                return false;
            }
            
            // Formatear el valor antes de enviar
            montoInput.value = monto.toFixed(2);
        });

        // Formatear automáticamente cuando se pierde el foco
        document.getElementById('monto_inicial')?.addEventListener('blur', function(e) {
            let value = parseFloat(e.target.value);
            if (!isNaN(value)) {
                e.target.value = value.toFixed(2);
            }
        });
    </script>
</body>
</html>
