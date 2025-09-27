<?php include('../../includes/navegacion.php') ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos</title>
    <style>
        /* Estilos básicos para la simulación */
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

        /* Estilo del card de pedido */
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

        /* Estilos para el estado del pedido */
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

        /* Estilos para los botones */
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
            /* Necesario si se usa como enlace */
        }

        .btn-pagar {
            background-color: #0275d8;
        }

        .btn-cancelar {
            background-color: #de0505ff;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .pedido-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-pedido {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .acciones {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>

    <div class="contenedor">
        <h1>Mis Pedidos</h1>

        <div class="pedido-card">
            <div class="info-pedido">
                <p><strong>Pedido #1054</strong></p>
                <p>Fecha: 2025-09-26</p>
                <p>Dirección: Av. Principal 123, Centro</p>
                <p>Estado: <span class="estado pendiente">Pendiente</span></p>
            </div>

            <div class="acciones">
                <button type="button" class="btn btn-cancelar" onclick="alert('Simulación: Confirmación de cancelación para #1054');">Cancelar</button>
                <button type="button" class="btn btn-pagar">Ir a Pagar</button>
            </div>
        </div>

        <div class="pedido-card">
            <div class="info-pedido">
                <p><strong>Pedido #1053</strong></p>
                <p>Fecha: 2025-09-24</p>
                <p>Dirección: Calle Secundaria 45, Barrio Norte</p>
                <p>Estado: <span class="estado en-proceso">En Proceso</span></p>
            </div>

            <div class="acciones">
                <button type="button" class="btn btn-cancelar" onclick="alert('Simulación: Confirmación de cancelación para #1053');">Cancelar</button>
            </div>
        </div>

        <div class="pedido-card">
            <div class="info-pedido">
                <p><strong>Pedido #1052</strong></p>
                <p>Fecha: 2025-09-20</p>
                <p>Dirección: Urb. Las Flores, Casa 8</p>
                <p>Estado: <span class="estado enviado">Enviado</span></p>
            </div>

            <div class="acciones">
                <button type="button" class="btn" style="background-color: #ccc; cursor: default;">Ver Detalles</button>
            </div>
        </div>

        <div class="pedido-card">
            <div class="info-pedido">
                <p><strong>Pedido #1051</strong></p>
                <p>Fecha: 2025-09-18</p>
                <p>Dirección: Av. Histórica 300, Casco Viejo</p>
                <p>Estado: <span class="estado cancelado">Cancelado</span></p>
            </div>

            <div class="acciones">
                <button type="button" class="btn" style="background-color: #ccc; cursor: default;">Pedido Inactivo</button>
            </div>
        </div>
        <style>
            .btn-cake {
                display: inline-block;
                padding: 10px 18px;
                margin: 10px;
                font-size: 14px;
                font-weight: bold;
                border-radius: 8px;
                border: none;
                background-color: #e91e63;
                /* Rosa Cake Party */
                color: #fff;
                text-decoration: none;
                /* ✅ Quita el subrayado */
                text-align: center;
                transition: background 0.3s ease, transform 0.2s ease;
            }

            .btn-cake:hover {
                background-color: #d81b60;
                /* Un poco más oscuro al pasar el mouse */
                transform: translateY(-2px);
            }

            .btn-cake:active {
                background-color: #ad1457;
                transform: translateY(0);
            }

            /* Para alinear los botones en fila */
            .botones-container {
                text-align: center;
                margin-top: 20px;
            }
        </style>

        <div class="botones-container">
            <a href="../../views/cliente/interfaz.php" class="btn-cake">← Volver al Inicio</a>
            <a href="../../controllers/usuario/logout.php" class="btn-cake">Cerrar Sesión</a>
        </div>

    </div>

</body>

</html>