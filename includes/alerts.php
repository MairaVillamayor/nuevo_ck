<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mostrar alerta solo si existe mensaje
if (isset($_SESSION['message']) && isset($_SESSION['status'])) {

    $msg = htmlspecialchars($_SESSION['message']);
    $status = $_SESSION['status'];
    $redirect = $_SESSION['redirect'] ?? $_SERVER["PHP_SELF"];
    $timeout = $_SESSION['timeout'] ?? 0; // ms (0 = sin autoredirección)

    switch ($status) {
        case "success":
            $titulo = "✔ ¡Éxito!";
            $color = "#28a745";
            break;

        case "danger":
            $titulo = "❌ Error";
            $color = "#dc3545";
            break;

        case "warning":
            $titulo = "⚠ Advertencia";
            $color = "#ffc107";
            break;

        default:
            $titulo = "ℹ Información";
            $color = "#0dcaf0";
            break;
    }

    echo "
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            showCakeAlert('$titulo', '$msg', '$redirect', '$color', $timeout);
        });
    </script>
    ";

    unset($_SESSION['message']);
    unset($_SESSION['status']);
    unset($_SESSION['redirect']);
    unset($_SESSION['timeout']);
}
?>

<!-- ALERTA CAKE PARTY -->
<div id="cakePartyAlert" class="cakeparty-overlay">
    <div class="cakeparty-box">
        <h3 id="cpTitle">Título</h3>
        <p id="cpText">Mensaje</p>
        <button id="cpButton" class="btn-cake">Aceptar</button>
    </div>
</div>

<style>
    .cakeparty-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, .45);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 99999;
    }

    .cakeparty-box {
        background: #ffe4ef;
        padding: 30px;
        width: 350px;
        border-radius: 18px;
        text-align: center;
        border: 2px solid #f8a1c4;
        animation: popin .25s ease-out;
        color: #c2185b;
    }

    @keyframes popin {
        from {
            transform: scale(.5);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .btn-cake {
        display: inline-block;
        background: #ff66b2;
        color: white;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: bold;
        text-decoration: none;
        transition: .2s;
        border: none;
    }

    .btn-cake:hover {
        background: #ff4da6;
    }

    .btn-outline-primary {
        color: #e91e63;
        border-color: #e91e63;
    }

    .btn-outline-primary:hover {
        background-color: #e91e63;
        color: #fff;

    }

    
</style>


<script>
    function showCakeAlert(titulo, mensaje, redirectUrl, color, timeout = 0) {
        let overlay = document.getElementById("cakePartyAlert");
        let box = document.querySelector(".cakeparty-box");
        let button = document.getElementById("cpButton");

        document.getElementById("cpTitle").textContent = titulo;
        document.getElementById("cpText").textContent = mensaje;

        box.style.borderColor = color;
        box.style.color = color;

        overlay.style.display = "flex";

        // Cerrar manualmente
        button.onclick = function() {
            overlay.style.display = "none";
            window.location.href = redirectUrl;
        };

        // Autoredirección opcional
        if (timeout > 0) {
            setTimeout(() => {
                overlay.style.display = "none";
                window.location.href = redirectUrl;
            }, timeout);
        }
    }
</script>