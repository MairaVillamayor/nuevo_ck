<?php
require_once("../../models/caja/caja.php");
 
include ("../../includes/sidebar.php");
include ("../../includes/navegacion.php");

$caja = new Caja();
$caja_abierta = $caja->getCajaAbierta();
?>

<div class="container py-4 cake-container">

    <h2 class="section-title text-center mb-4">
        <i class="bi bi-cash-coin text-rosa"></i> Gestión de Caja
    </h2>

    <ul class="nav nav-tabs justify-content-center mb-4 cake-tabs">
        <li class="nav-item">
            <a class="nav-link active" id="btn-listado" href="#" onclick="mostrarSeccion('listado', event)">
                <i class="bi bi-list-ul"></i> Listado
            </a>
        </li>

        <?php if (!$caja_abierta) { ?>
        <li class="nav-item">
            <a class="nav-link" id="btn-apertura" href="apertura.php" onclick="mostrarSeccion('apertura', event)">
                <i class="bi bi-box-arrow-in-right"></i> Abrir Caja
            </a>
        </li>
        <?php } ?>

        <?php if ($caja_abierta) { ?>
        <li class="nav-item">
            <a class="nav-link" id="btn-gastos" href="listado_gastos.php" onclick="mostrarSeccion('gastos', event)">
                <i class="bi bi-wallet2"></i> Gastos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="btn-arqueo" href="arqueo_caja.php" onclick="mostrarSeccion('arqueo', event)">
                <i class="bi bi-calculator"></i> Arqueo / Cierre
            </a>
        </li>
        <?php } ?>
    </ul>

    <div id="seccion-listado" class="cake-section" style="display: block;">
        <?php include 'listado_caja.php'; ?>
    </div>

    <div id="seccion-apertura" class="cake-section" style="display: none;">
        <?php include 'apertura.php'; ?>
    </div>

    <div id="seccion-gastos" class="cake-section" style="display: none;">
        <?php include 'listado_gastos.php'; ?>
    </div>

    <div id="seccion-arqueo" class="cake-section" style="display: none;">
        <?php include 'arqueo_caja.php'; ?>
    </div>
</div>

<script>
function mostrarSeccion(seccion, event) {
    if (event) event.preventDefault();
    const secciones = ['listado', 'apertura', 'gastos', 'arqueo'];

    secciones.forEach(s => {
        const el = document.getElementById(`seccion-${s}`);
        const btn = document.getElementById(`btn-${s}`);
        if (el) el.style.display = (s === seccion) ? 'block' : 'none';
        if (btn) btn.classList.toggle('active', s === seccion);
    });
}
</script>
