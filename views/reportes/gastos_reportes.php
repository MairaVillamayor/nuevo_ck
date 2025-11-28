<?php
require_once __DIR__ . '/../../models/caja/gastos.php';

$gasto = new Gastos();

$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;
$anio  = $_GET['anio'] ?? date('Y');

/* ===== POR FECHA ===== */
$porFecha = $gasto->reportePorFecha($desde, $hasta);
$fechas = [];
$montosFecha = [];
$total = 0;

foreach($porFecha as $f){
    $fechas[] = $f['fecha'];
    $montosFecha[] = $f['total'];
    $total += $f['total'];
}

/* ===== POR CATEGORÍA ===== */
$porCategoria = $gasto->reportePorCategoria($desde, $hasta);
$categorias = [];
$montosCategorias = [];

foreach($porCategoria as $c){
    $categorias[] = $c['categoria'];
    $montosCategorias[] = $c['total'];
}

/* ===== POR MÉTODO ===== */
$porMetodo = $gasto->reportePorMetodo($desde, $hasta);
$metodos = [];
$montosMetodos = [];

foreach($porMetodo as $m){
    $metodos[] = $m['metodo'];
    $montosMetodos[] = $m['total'];
}

/* ===== POR MES ===== */
$porMes = $gasto->reportePorMes($anio);
$meses = [];
$montosMes = [];

$mesesNombres = [1=>"Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"];

foreach($porMes as $m){
    $meses[] = $mesesNombres[$m['mes']];
    $montosMes[] = $m['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reportes de Gastos</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
  background:#fff5f8;
  font-family:'Segoe UI',sans-serif;
  padding:20px;
}

h2{
  text-align:center;
  color:#ff2d8f;
}

h3{
  margin-top:40px;
  text-align:center;
  color:#6b2d5c;
}

.filtros-reportes{
  display:flex;
  justify-content:center;
  gap:15px;
  margin-bottom:25px;
  flex-wrap:wrap;
}

.filtros-reportes input,
.filtros-reportes select,
.filtros-reportes button{
  padding:8px 12px;
  border-radius:20px;
  border:1px solid #ffb6d3;
}

.btn-reporte{
  background:#ff4f9a;
  color:white;
  border:none;
  cursor:pointer;
}

.panel{
  display:flex;
  justify-content:center;
  flex-wrap:wrap;
  gap:40px;
}

.grafico-box{
  width:400px;
  height:250px;
  background:white;
  padding:15px;
  border-radius:20px;
  box-shadow:0 5px 10px rgba(0,0,0,0.1);
}

.resumen{
  text-align:center;
  margin-top:20px;
  font-size:18px;
  color:#c2185b;
  font-weight:bold;
}

.btn-imprimir{
  display:block;
  margin:25px auto;
  background:#6b2d5c;
  color:white;
  padding:10px 25px;
  border:none;
  border-radius:25px;
  cursor:pointer;
}
</style>
</head>

<body>

<h2>📊 Reportes de Gastos</h2>

<form method="GET" class="filtros-reportes">
    <input type="date" name="desde" value="<?= $desde ?>">
    <input type="date" name="hasta" value="<?= $hasta ?>">

    <select name="anio">
        <?php for($a = date("Y"); $a >= 2022; $a--): ?>
            <option value="<?= $a ?>" <?= $a == $anio ? 'selected' : '' ?>><?= $a ?></option>
        <?php endfor ?>
    </select>

    <button class="btn-reporte">Filtrar</button>
</form>

<div class="resumen">
   Total de gastos: $<?= number_format($total,2) ?>
</div>

<!-- ================== GRAFICOS =================== -->
<div class="panel">

    <div class="grafico-box">
        <canvas id="fechaChart"></canvas>
    </div>

    <div class="grafico-box">
        <canvas id="catChart"></canvas>
    </div>

    <div class="grafico-box">
        <canvas id="metodoChart"></canvas>
    </div>

    <div class="grafico-box">
        <canvas id="mesChart"></canvas>
    </div>

</div>

<button class="btn-imprimir" onclick="window.print()">🖨 Imprimir</button>

<script>

// POR FECHA
new Chart(document.getElementById('fechaChart'), {
    type:'bar',
    data:{
        labels: <?=json_encode($fechas)?>,
        datasets:[{
            label:'Gastos por día',
            data: <?=json_encode($montosFecha)?>,
            backgroundColor:'#ff5fa2'
        }]
    }
});

// POR CATEGORIA
new Chart(document.getElementById('catChart'), {
    type:'pie',
    data:{
        labels: <?=json_encode($categorias)?>,
        datasets:[{
            data: <?=json_encode($montosCategorias)?>,
            backgroundColor:['#ff80ab','#f06292','#ec407a','#f48fb1','#ad1457']
        }]
    }
});

// POR METODO
new Chart(document.getElementById('metodoChart'), {
    type:'doughnut',
    data:{
        labels: <?=json_encode($metodos)?>,
        datasets:[{
            data: <?=json_encode($montosMetodos)?>,
            backgroundColor:['#ce93d8','#ba68c8','#ab47bc','#9c27b0']
        }]
    }
});

// POR MES
new Chart(document.getElementById('mesChart'), {
    type:'line',
    data:{
        labels: <?=json_encode($meses)?>,
        datasets:[{
            label:'Gastos por mes ' + <?= $anio ?>,
            data: <?=json_encode($montosMes)?>,
            borderColor:'#ff2d8f',
            backgroundColor:'#ffd5e5',
            fill:true,
            tension:0.3
        }]
    }
});

</script>

</body>
</html>
