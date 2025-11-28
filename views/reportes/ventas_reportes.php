<?php
require_once("../../config/conexion.php");
require_once("../../models/ventas/factura.php");

session_start();
if(!isset($_SESSION['usuario_id'])){
  exit('Acceso denegado');
}

$factura = new Factura();

$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;

$ventasPorDia = $factura->getVentasPorDia($desde, $hasta);
$porEstado = $factura->getFacturasPorEstado();
$porCliente = $factura->getTotalPorCliente();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reportes</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body{
  background:#fff0f5;
  font-family:Segoe UI;
  padding:20px;
}
.card{
  max-width: 480px;
  margin: 20px auto;
  background:white;
  border-radius:16px;
  padding:15px;
  box-shadow:0 4px 10px rgba(0,0,0,.05)
}
canvas{
  max-height: 220px;
}
h3{
  color:#ff69b4;
  text-align:center;
}
.btn-pink{
  background:#ff69b4;
  border:none;
  padding:8px 15px;
  color:white;
  border-radius:10px;
}

</style>
</head>
<body>

<h3>📊 Reportes Cake Party</h3>

<form method="GET" style="display:flex; gap:10px; margin-bottom:20px;">
  <input type="date" name="desde" value="<?= $desde ?>">
  <input type="date" name="hasta" value="<?= $hasta ?>">
  <button class="btn-pink">Filtrar</button>
</form>

<div class="card">
  <h3>Ventas por día</h3>
  <canvas id="ventasChart"></canvas>
</div>

<div class="card">
  <h3>Estado de facturas</h3>
  <canvas id="estadoChart"></canvas>
</div>

<div class="card">
  <h3>Mejores clientes</h3>
  <canvas id="clienteChart"></canvas>
</div>

<script>

const ventas = <?= json_encode($ventasPorDia) ?>;
const estados = <?= json_encode($porEstado) ?>;
const clientes = <?= json_encode($porCliente) ?>;

new Chart(document.getElementById('ventasChart'),{
  type:'line',
  data:{
    labels: ventas.map(v=>v.dia),
    datasets:[{ label:'Total $', data: ventas.map(v=>v.total), tension:0.4 }]
  }
})

new Chart(document.getElementById('estadoChart'),{
  type:'pie',
  data:{
    labels: estados.map(e=>e.estado),
    datasets:[{ data: estados.map(e=>e.cantidad) }]
  }
})

new Chart(document.getElementById('clienteChart'),{
  type:'bar',
  data:{
    labels: clientes.map(c=>c.cliente),
    datasets:[{ label:'Total $', data: clientes.map(c=>c.total) }]
  }
})

</script>

</body>
</html>
