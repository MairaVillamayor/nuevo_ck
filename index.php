<?php

// Incluir los archivos necesarios
// 'PlantillaControlador.php' debe contener la clase PlantillaControlador
require_once('controllers/PlantillaControlador.php'); 

// -----------------------------------------------------------------
// 1. Instanciar el Controlador de Plantilla o Vistas
// -----------------------------------------------------------------
$plantilla = new PlantillaControlador();

// 2. Ejecutar el método que trae la plantilla principal.
// Este método es responsable de decidir qué contenido mostrar.
$plantilla->traer_plantilla(); 

// --- Detalle de lo que haría 'traer_plantilla()' (dentro de la clase) ---
// En este escenario, el método 'traer_plantilla()' internamente verificaría:
// A) ¿Hay una sesión iniciada? -> Mostrar la vista de bienvenida/dashboard.
// B) ¿No hay sesión? -> Cargar automáticamente la vista de login.
// -----------------------------------------------------------------

?>