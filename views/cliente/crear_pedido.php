<?php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/navegacion.php';
session_start();

// ⚠️ Verificamos que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../index.php?error=login_required");
    exit();
}

$conexion = getConexion();

// 🔹 Obtener datos desde la BD
$colores = $conexion->query("SELECT id_color_pastel, color_pastel_nombre FROM color_pastel")->fetchAll(PDO::FETCH_ASSOC);
$decoraciones = $conexion->query("SELECT id_decoracion, decoracion_nombre, decoracion_descripcion FROM decoracion")->fetchAll(PDO::FETCH_ASSOC);
$bases = $conexion->query("SELECT id_base_pastel, base_pastel_nombre FROM base_pastel")->fetchAll(PDO::FETCH_ASSOC);
$tamanos = $conexion->query("SELECT id_tamaño, tamaño_nombre, tamaño_medidas FROM tamaño")->fetchAll(PDO::FETCH_ASSOC);
$sabores = $conexion->query("SELECT id_sabor, sabor_nombre FROM sabor")->fetchAll(PDO::FETCH_ASSOC);
$rellenos = $conexion->query("SELECT id_relleno, relleno_nombre FROM relleno")->fetchAll(PDO::FETCH_ASSOC);
$materiales = $conexion->query("SELECT ID_material_extra, material_extra_nombre, material_extra_descri 
                                FROM material_extra WHERE RELA_estado_insumos = 1")->fetchAll(PDO::FETCH_ASSOC);

$materiales_agrupados = [];
foreach ($materiales as $m) {
    $nombre = $m['material_extra_nombre'];
    if (!isset($materiales_agrupados[$nombre])) {
        $materiales_agrupados[$nombre] = [];
    }
    $materiales_agrupados[$nombre][] = $m;
}

$metodos_pago = $conexion->query("SELECT ID_metodo_pago, metodo_pago_descri 
                                  FROM metodo_pago")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear tu pastel - Cake Party</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
    
    <style>
        /* Estilos base */
        body {
            background-color: #fff7fa; /* Rosa muy claro */
            font-family: 'Poppins', sans-serif;
        }

        .container {
            max-width: 1000px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 35px;
            margin: 40px auto;
        }

        h2, h3, h4, h5 {
            color: #e91e63; /* Rosa vibrante */
            font-weight: 600;
        }

        /* Inputs y Selects */
        .form-control, .form-select {
            border-radius: 10px !important;
            border: 1px solid #f8bbd0 !important; /* Borde rosa claro */
        }

        .form-control:focus, .form-select:focus {
            border-color: #ec407a !important; /* Borde rosa medio en foco */
            box-shadow: 0 0 4px rgba(236, 64, 122, 0.4) !important;
        }

        /* Botones y Cards */
        .btn-primary, .bg-primary {
            background-color: #ff4081 !important; /* Rosa fuerte */
            border: none !important;
            border-radius: 10px !important;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: #e91e63 !important; /* Rosa vibrante oscuro */
        }
        
        /* Botón de Agregar Piso (Success) */
        .btn-success {
            background-color: #e91e63 !important; /* Rosa vibrante */
            border: none !important;
            border-radius: 10px !important;
            color: #fff !important;
        }
        .btn-success:hover {
            background-color: #c2185b !important; /* Rosa oscuro */
        }

        /* Botón de Eliminar Piso (Danger) */
        .btn-danger {
            background-color: #ff4081 !important; /* Usamos el mismo rosa fuerte para consistencia */
            border-color: #ff4081 !important;
            color: #fff !important;
        }
        .btn-danger:hover {
            background-color: #e91e63 !important;
            border-color: #e91e63 !important;
        }

        /* Card Headers */
        .card-header.bg-primary { background-color: #ff4081 !important; }
        .card-header.bg-warning { background-color: #f8bbd0 !important; color: #4a4a4a !important; } /* Rosa claro */
        .card-header.bg-info { background-color: #ff80ab !important; } /* Rosa medio */
        .card-header.bg-secondary { background-color: #c2185b !important; } /* Rosa oscuro */

        /* Alertas y Pisos */
        .alert-info {
            background-color: #fce4ec; /* Fondo muy claro */
            border-color: #f8bbd0;
            color: #c2185b; /* Texto rosa oscuro */
            font-weight: 500;
        }
        
        .piso {
            border: 1px solid #f8bbd0; /* Borde rosa claro */
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            background-color: #fff0f5; /* Fondo del piso */
        }
        
        /* Acordeón (Materiales Extra) */
        .accordion-button:not(.collapsed) {
            color: #e91e63;
            background-color: #ffe6ef;
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.08);
        }
        .accordion-button:focus {
             box-shadow: 0 0 4px rgba(236, 64, 122, 0.4) !important;
             border-color: #ec407a;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <h2 class="text-center mb-4">🎂 Crear pastel personalizado</h2>
        
        <form action="../../controllers/cliente/guardar_pedido.php" method="POST" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="h5 mb-0">🍰 Detalles del Pastel</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="color_pastel" class="form-label">Color del pastel:</label>
                                <select class="form-select" id="color_pastel" name="RELA_color_pastel" required>
                                    <option value="" disabled selected>Seleccioná un color</option>
                                    <?php foreach ($colores as $c): ?>
                                        <option value="<?= $c['id_color_pastel'] ?>"><?= $c['color_pastel_nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, seleccioná un color.</div>
                            </div>

                            <div class="mb-3">
                                <label for="decoracion" class="form-label">Decoración:</label>
                                <select class="form-select" id="decoracion" name="RELA_decoracion" required>
                                    <option value="" disabled selected>Seleccioná una decoración</option>
                                    <?php foreach ($decoraciones as $d): ?>
                                        <option value="<?= $d['id_decoracion'] ?>">
                                            <?= htmlspecialchars($d['decoracion_nombre']) ?> — <?= htmlspecialchars($d['decoracion_descripcion']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, seleccioná una decoración.</div>
                            </div>

                            <div class="mb-3">
                                <label for="base_pastel" class="form-label">Base:</label>
                                <select class="form-select" id="base_pastel" name="RELA_base_pastel" required>
                                    <option value="" disabled selected>Seleccioná una base</option>
                                    <?php foreach ($bases as $b): ?>
                                        <option value="<?= $b['id_base_pastel'] ?>"><?= $b['base_pastel_nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, seleccioná una base.</div>
                            </div>

                            <h3 class="h5 mt-4 mb-3">🎁 Materiales extra</h3>
                            <div class="accordion" id="accordionMateriales">
                                <?php foreach ($materiales_agrupados as $nombre_grupo => $opciones_grupo): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading_<?= str_replace(' ', '_', $nombre_grupo) ?>">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_<?= str_replace(' ', '_', $nombre_grupo) ?>" aria-expanded="false" aria-controls="collapse_<?= str_replace(' ', '_', $nombre_grupo) ?>">
                                                <strong><?= htmlspecialchars($nombre_grupo) ?></strong>
                                            </button>
                                        </h2>
                                        <div id="collapse_<?= str_replace(' ', '_', $nombre_grupo) ?>" class="accordion-collapse collapse" aria-labelledby="heading_<?= str_replace(' ', '_', $nombre_grupo) ?>" data-bs-parent="#accordionMateriales">
                                            <div class="accordion-body">
                                                <?php foreach ($opciones_grupo as $opcion): ?>
                                                    <div class="form-check d-flex align-items-center mb-2">
                                                        <input class="form-check-input" type="checkbox" name="material_extra[]" value="<?= htmlspecialchars($opcion['ID_material_extra']) ?>" id="mat_<?= htmlspecialchars($opcion['ID_material_extra']) ?>">
                                                        <label class="form-check-label me-3" for="mat_<?= htmlspecialchars($opcion['ID_material_extra']) ?>">
                                                            <?= htmlspecialchars($opcion['material_extra_descri']) ?>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            class="form-control form-control-sm"
                                                            name="color_material_extra[<?= htmlspecialchars($opcion['ID_material_extra']) ?>]"
                                                            placeholder="Color (opcional)"
                                                            style="width: 150px;">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning">
                            <h3 class="h5 mb-0">⚡ Pisos</h3>
                        </div>
                        <div class="card-body">
                            <div id="pisos-container"></div>
                            <div id="alerta-limite-pisos" class="alert alert-danger d-none" role="alert">
                                ⚠️ ¡Atención! Solo puedes agregar un máximo de 3 pisos.
                            </div>
                            <button type="button" class="btn btn-success w-100" onclick="agregarPiso()">➕ Agregar Piso</button>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h3 class="h5 mb-0">🚚 Datos de envío</h3>
                        </div>
                        <div class="card-body">
                            <?php
                                // La alerta se estiliza con los colores rosa claro
                                echo '<div class="alert alert-info text-center" role="alert">';
                                echo '🗓️ Recordatorio: Los pedidos personalizados requieren <strong>7 días / 1 semana</strong> de anticipación.';
                                echo '</div>';
                            ?>
                            <div class="mb-3">
                                <label for="envio_fecha_hora_entrega" class="form-label">Fecha y Hora de Entrega:</label>
                                <input
                                    type="datetime-local"
                                    class="form-control"
                                    id="envio_fecha_hora_entrega"
                                    name="envio_fecha_hora_entrega"
                                    required>
                                <div class="invalid-feedback">Por favor, seleccioná la fecha y hora de entrega.</div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label for="envio_calle_numero" class="form-label">Calle y Número:</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="envio_calle_numero"
                                        name="envio_calle_numero"
                                        placeholder="Ej: Av. 25 de Mayo 1234"
                                        required>
                                    <div class="invalid-feedback">Por favor, ingresá la calle y el número.</div>
                                </div>
                                <div class="col-md-2">
                                    <label for="envio_piso" class="form-label">Piso (Opc.):</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="envio_piso"
                                        name="envio_piso"
                                        placeholder="Ej: 5">
                                </div>
                                <div class="col-md-2">
                                    <label for="envio_dpto" class="form-label">Dpto (Opc.):</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="envio_dpto"
                                        name="envio_dpto"
                                        placeholder="Ej: A">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="envio_localidad" class="form-label">Localidad:</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="envio_localidad"
                                        name="envio_localidad"
                                        placeholder="Ej: Formosa"
                                        required>
                                    <div class="invalid-feedback">Por favor, ingresá la localidad.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="envio_barrio" class="form-label">Barrio:</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="envio_barrio"
                                        name="envio_barrio"
                                        placeholder="Ej: Centro"
                                        required>
                                    <div class="invalid-feedback">Por favor, ingresá el barrio.</div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="envio_cp" class="form-label">Código Postal (CP):</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="envio_cp"
                                        name="envio_cp"
                                        placeholder="Ej: 3600"
                                        required>
                                    <div class="invalid-feedback">Por favor, ingresá el código postal.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="envio_provincia" class="form-label">Provincia:</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="envio_provincia"
                                        name="envio_provincia"
                                        placeholder="Ej: Formosa"
                                        required>
                                    <div class="invalid-feedback">Por favor, ingresá la provincia.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="envio_telefono_contacto" class="form-label">Teléfono de Contacto (con código de área):</label>
                                <input
                                    type="tel"
                                    class="form-control"
                                    id="envio_telefono_contacto"
                                    name="envio_telefono_contacto"
                                    placeholder="Ej: 54 3704 1234"
                                    required>
                                <div class="invalid-feedback">Por favor, ingresá tu teléfono de contacto.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="envio_referencias" class="form-label">Referencias para el Repartidor (Máx. 250 caracteres):</label>
                                <textarea
                                    class="form-control"
                                    id="envio_referencias"
                                    name="envio_referencias"
                                    rows="3"
                                    maxlength="250"
                                    placeholder="Ej: Portón verde, casa con rejas blancas, tocar timbre de la izquierda."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h3 class="h5 mb-0">💳 Método de Pago</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="metodo_pago" class="form-label">Método de Pago:</label>
                                <select class="form-select" id="metodo_pago" name="RELA_metodo_pago" required>
                                    <?php foreach ($metodos_pago as $mp): ?>
                                        <option value="<?= $mp['ID_metodo_pago'] ?>"><?= $mp['metodo_pago_descri'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, seleccioná un método de pago.</div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">✅ Finalizar Pedido</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let pisoCount = 0;

        function agregarPiso() {
            const container = document.getElementById("pisos-container");
            const alerta = document.getElementById("alerta-limite-pisos");

            const MAX_PISOS = 3;
            const numPisosActual = container.getElementsByClassName("piso").length;

            alerta.classList.add('d-none'); // Ocultar alerta con Bootstrap

            if (numPisosActual >= MAX_PISOS) {
                alerta.classList.remove('d-none'); // Mostrar alerta
                return;
            }

            pisoCount++;

            const div = document.createElement("div");
            // Se le aplica la clase 'piso' para tomar el estilo de borde y fondo personalizado
            div.classList.add("piso", "p-3", "mb-3"); 
            div.innerHTML = `
                <h4 class="h6 mb-3">Piso ${pisoCount}</h4>

                <div class="mb-3">
                    <label class="form-label">Tamaño:</label>
                    <select class="form-select" name="pisos[${pisoCount}][RELA_tamaño]" required>
                        <option value="" disabled selected>Seleccioná un tamaño</option>
                        <?php foreach ($tamanos as $t): ?>
                            <option value="<?= $t['id_tamaño'] ?>"> <?= $t['tamaño_nombre'] ?> (<?=$t['tamaño_medidas'] ?>) </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sabor:</label>
                    <select class="form-select" name="pisos[${pisoCount}][RELA_sabor]" required>
                        <option value="" disabled selected>Seleccioná un sabor</option>
                        <?php foreach ($sabores as $s): ?>
                            <option value="<?= $s['id_sabor'] ?>"><?= $s['sabor_nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Relleno:</label>
                    <select class="form-select" name="pisos[${pisoCount}][RELA_relleno]" required>
                        <option value="" disabled selected>Seleccioná un relleno</option>
                        <?php foreach ($rellenos as $r): ?>
                            <option value="<?= $r['id_relleno'] ?>"><?= $r['relleno_nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="eliminarPiso(this)">❌ Eliminar Piso</button>
            `;
            container.appendChild(div);
        }

        function eliminarPiso(button) {
            const pisoDiv = button.closest('.piso');
            if (pisoDiv) {
                pisoDiv.remove();
                document.getElementById("alerta-limite-pisos").classList.add('d-none');
            }
        }

        // Script para la fecha mínima de entrega
        document.addEventListener('DOMContentLoaded', () => {
            const now = new Date();
            // Sumamos 7 días (7 * 24 * 60 * 60 * 1000)
            const minDate = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000); 

            // Ajuste de zona horaria para datetime-local
            minDate.setMinutes(minDate.getMinutes() - minDate.getTimezoneOffset());
            const minDateTime = minDate.toISOString().slice(0, 16);

            document.getElementById("envio_fecha_hora_entrega").min = minDateTime;
            
            // Validación de Bootstrap
            (function () {
              'use strict'
              const forms = document.querySelectorAll('.needs-validation')
              Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                  if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                  }
                  form.classList.add('was-validated')
                }, false)
              })
            })()
        });
    </script>
</body>

</html>