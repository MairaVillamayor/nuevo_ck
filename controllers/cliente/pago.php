<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar Pedido [ID_PEDIDO]</title>
    <style>
        /* Estilos Generales y Base Rosa/Blanca */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff5f8; /* Fondo Rosa muy claro */
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .container {
            max-width: 550px;
            width: 100%;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(255, 105, 180, 0.1); /* Sombra rosada sutil */
            margin-top: 20px;
        }

        h1 {
            color: #e91e63; /* Rosa fuerte */
            text-align: center;
            margin-bottom: 5px;
            font-size: 2em;
        }
        
        h2 {
            font-size: 1.2em;
            color: #444;
            border-bottom: 2px solid #ffc0cb; /* Línea de división rosa claro */
            padding-bottom: 8px;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        /* Sección de Monto Total */
        .total-box {
            background-color: #ffc0cb; /* Rosa claro para destacar */
            color: #333;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-size: 1.2em;
            margin-bottom: 25px;
        }
        .total-box strong {
            font-size: 1.6em;
            color: #d81b60;
            display: block;
            margin-top: 5px;
        }

        /* Opciones de Pago */
        .opcion-pago {
            border: 2px solid #ffc0cb;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }
        .opcion-pago:hover {
            background-color: #fff5f8;
            border-color: #e91e63;
        }
        
        .opcion-pago input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        
        .opcion-pago label {
            display: flex;
            align-items: center;
            font-weight: bold;
            color: #333;
            cursor: pointer;
        }
        
        .opcion-pago input[type="radio"]:checked + label {
            color: #e91e63; /* Resaltar texto si está seleccionado */
        }
        
        .opcion-pago input[type="radio"]:checked + label::before {
            border-color: #e91e63;
            background-color: #ffc0cb;
        }

        /* Estilo para el ícono de radio custom */
        .opcion-pago label::before {
            content: '';
            width: 18px;
            height: 18px;
            border: 2px solid #ccc;
            border-radius: 50%;
            margin-right: 15px;
            transition: all 0.2s ease;
        }

        /* Campos de Datos Adicionales (Ocultos por defecto) */
        .datos-adicionales {
            border-top: 1px dashed #ffc0cb;
            padding-top: 15px;
            margin-top: 10px;
            /* Controlado por JavaScript, pero configurado para empezar oculto */
            display: none; 
        }
        
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ffc0cb;
            border-radius: 6px;
            box-sizing: border-box;
        }

        /* Botón de Confirmación */
        .btn-confirmar {
            background-color: #e91e63;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: block;
            width: 100%;
            font-size: 1.1em;
            font-weight: bold;
            margin-top: 25px;
            transition: background-color 0.3s ease;
        }
        .btn-confirmar:hover {
            background-color: #d81b60;
        }
        
        /* Enlace de Volver */
        .volver-link {
            text-align: center;
            margin-top: 20px;
        }
        .volver-link a {
            color: #666;
            text-decoration: none;
            font-size: 0.9em;
        }

    </style>
</head>
<body>

<div class="container">
    <h1>Pago de Pedido # [ID_PEDIDO]</h1>

    <div class="mensaje-placeholder" style="/* Estilos para alertas */">
        </div>
    
    <div class="total-box">
        Monto Total a Pagar:
        <strong>$ [MONTO_TOTAL]</strong>
    </div>

    <h2>Elige un método de pago</h2>

    <form action="[RUTA_DEL_CONTROLADOR_PAGO]" method="POST" id="pagoForm">
        <input type="hidden" name="id_pedido" value="[ID_PEDIDO]">

        <div class="opcion-pago" onclick="selectOption('efectivo')">
            <label for="pago_efectivo">
                <input type="radio" name="metodo_pago" id="pago_efectivo" value="efectivo" checked>
                Efectivo al Retiro/Entrega 💰
            </label>
            <p style="font-size:0.85em; margin-top:5px; color:#666;">Pagas al momento de la entrega. ¡Fácil y rápido!</p>
        </div>

        <div class="opcion-pago" onclick="selectOption('mp')">
            <label for="pago_mp">
                <input type="radio" name="metodo_pago" id="pago_mp" value="mercadopago">
                Mercado Pago / Transferencia 📱
            </label>
            <div class="datos-adicionales" id="datos_mp">
                <p style="color:#118c11; font-weight: bold;">(Serás redirigido a la plataforma de Mercado Pago para finalizar.)</p>
            </div>
        </div>

        <div class="opcion-pago" onclick="selectOption('tarjeta')">
            <label for="pago_tarjeta">
                <input type="radio" name="metodo_pago" id="pago_tarjeta" value="tarjeta">
                Tarjeta de Crédito o Débito 💳
            </label>
            <div class="datos-adicionales" id="datos_tarjeta">
                <input type="text" name="numero_tarjeta" placeholder="Número de Tarjeta">
                <div style="display:flex; gap: 10px;">
                    <input type="text" name="vencimiento" placeholder="MM/AA" style="width: 50%;">
                    <input type="text" name="cvc" placeholder="CVC" style="width: 50%;">
                </div>
                <input type="text" name="nombre_tarjeta" placeholder="Nombre en la Tarjeta">
            </div>
        </div>
        
        <button type="submit" class="btn-confirmar">Confirmar y Pagar</button>
    </form>
    
    <div class="volver-link">
        <a href="../../views/cliente/mis_pedidos.php">← Volver a Mis Pedidos</a>
    </div>

</div>

<script>
    // Script simple para controlar la visualización de los campos
    function selectOption(metodo) {
        // Marca el radio button correspondiente
        document.getElementById('pago_' + metodo).checked = true;

        const mpDiv = document.getElementById('datos_mp');
        const tarjetaDiv = document.getElementById('datos_tarjeta');
        
        // Oculta todos
        mpDiv.style.display = 'none';
        tarjetaDiv.style.display = 'none';

        // Muestra solo el seleccionado
        if (metodo === 'mp') {
            mpDiv.style.display = 'block';
        } else if (metodo === 'tarjeta') {
            tarjetaDiv.style.display = 'block';
        }
    }

    // Inicializa la selección al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
        selectOption('efectivo'); // Selecciona 'Efectivo' por defecto
    });
</script>

</body>
</html>