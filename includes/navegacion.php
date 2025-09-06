<?php
// includes/navegacion.php
?>
<?php
// includes/navegacion.php
?>
<?php
// includes/navegacion.php
?>

<div style="
    position: fixed;
    top: 10px;
    right: 20px;
    z-index: 9999;
    display: flex;
    gap: 8px; /* espacio entre botones */
">
    <!-- Botón Atrás -->
    <button onclick="history.back()" 
            style="
                padding:8px 15px;
                border:none;
                border-radius:8px;
                background-color: rgba(233,30,99,0.85);
                color:white;
                cursor:pointer;
                box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                transition: background-color 0.3s;
            "
            onmouseover="this.style.backgroundColor='rgba(233,30,99,1)';"
            onmouseout="this.style.backgroundColor='rgba(233,30,99,0.85)';"
    >
        ⬅
    </button>

    <!-- Botón Adelante -->
    <button onclick="history.forward()" 
            style="
                padding:8px 15px;
                border:none;
                border-radius:8px;
                background-color: rgba(233,30,99,0.85);
                color:white;
                cursor:pointer;
                box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                transition: background-color 0.3s;
            "
            onmouseover="this.style.backgroundColor='rgba(233,30,99,1)';"
            onmouseout="this.style.backgroundColor='rgba(233,30,99,0.85)';"
    >
        ➡
    </button>
</div>


<!-- Para que el contenido no quede debajo de la barra fija -->
<div style="height:50px;"></div>
