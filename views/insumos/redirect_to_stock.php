<?php
/**
 * Redirección para mantener compatibilidad con enlaces antiguos
 * Redirige a los nuevos archivos del módulo de stock
 */

// Redirigir al nuevo listado de insumos en el módulo de stock
header('Location: ../stock/listado_insumos.php');
exit();
?>
