<?php
$result_gastos = $result_gastos ?? [];
$result_categoria = $result_categoria ?? [];
$result_metodo = $result_metodo ?? [];
$caja_abierta = $caja_abierta ?? ['id' => 0];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-semibold text-rosa">
        <i class="bi bi-wallet2 me-2"></i>Gestión de Gastos
    </h3>
    <div>
        <button class="btn btn-rosa me-2" onclick="abrirModal()">
            <i class="bi bi-plus-lg me-1"></i> Registrar Gasto
        </button>
        <button class="btn btn-outline-rosa" onclick="abrirCategoria()">
            <i class="bi bi-folder-plus me-1"></i> Nueva Categoría
        </button>
    </div>
</div>

<div class="table-container p-4 shadow-sm rounded-4 bg-white">
    <div class="table-responsive">
        <table class="table align-middle text-center mb-0">
            <thead class="table-header text-white">
                <tr>
                    <th class="rounded-start">Categoría</th>
                    <th>Fecha</th>
                    <th>Forma de Pago</th>
                    <th>Monto</th>
                    <th class="rounded-end">Descripción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($result_gastos)) { ?>
                    <?php foreach ($result_gastos as $g) { ?>
                        <tr>
                            <td><?= htmlspecialchars($g['categoria_nombre'] ?? '—'); ?></td>
                            <td><?= htmlspecialchars($g['fecha'] ?? '—'); ?></td>
                            <td><?= htmlspecialchars($g['metodo_pago_descri'] ?? '—'); ?></td>
                            <td class="fw-semibold text-success">
                                $<?= number_format($g['monto'] ?? 0, 2, ',', '.'); ?>
                            </td>
                            <td><?= htmlspecialchars($g['descripciones'] ?? ''); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="text-muted py-4">
                            💸 No hay gastos registrados todavía.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalCaja" class="cake-modal">
    <div class="cake-modal-content">
        <span class="cerrar" onclick="cerrarModal()">&times;</span>
        <h4 class="text-center text-rosa mb-3 fw-semibold">Registrar Gasto</h4>

        <form method="POST" action="../controllers/gastos_controlador.php">
            <input type="hidden" name="action" value="guardar">
            <input type="hidden" name="caja_id" value="<?= htmlspecialchars($caja_abierta['id']); ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold">Categoría</label>
                <select name="categoria_id" class="form-select input-cake" required>
                    <option value="">Seleccione una</option>
                    <?php foreach ($result_categoria as $cat) { ?>
                        <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['nombre']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Método de Pago</label>
                <select name="metodo_pago_id" class="form-select input-cake" required>
                    <option value="">Seleccione uno</option>
                    <?php foreach ($result_metodo as $metodo) { ?>
                        <option value="<?= $metodo['id']; ?>"><?= htmlspecialchars($metodo['nombre']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Monto</label>
                <input type="number" step="0.01" name="monto" class="form-control input-cake" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Descripción</label>
                <input type="text" name="descripciones" class="form-control input-cake" required>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-rosa px-4 py-2 fw-semibold">
                    Guardar Gasto
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModal() { document.getElementById('modalCaja').style.display = 'flex'; }
function cerrarModal() { document.getElementById('modalCaja').style.display = 'none'; }
</script>

<style>
:root {
    --rosa: #e83e8c;
    --rosa-claro: #ffb6d9;
    --gris: #f8f9fa;
    --gris-borde: #dee2e6;
}
body { font-family: "Poppins", sans-serif; background-color: var(--gris); }

.table-container {
    border: 1px solid var(--gris-borde);
}
.table-header {
    background-color: var(--rosa);
}
.table td, .table th {
    vertical-align: middle;
    padding: 12px;
}

/* Botones */
.btn-rosa {
    background-color: var(--rosa);
    color: #fff;
    border: none;
    border-radius: 10px;
    transition: 0.2s;
}
.btn-rosa:hover { background-color: #d7337d; color: #fff; }
.btn-outline-rosa {
    border: 2px solid var(--rosa);
    color: var(--rosa);
    border-radius: 10px;
    transition: 0.2s;
}
.btn-outline-rosa:hover {
    background-color: var(--rosa);
    color: #fff;
}

/* Inputs y selects */
.input-cake {
    border: 1.5px solid var(--rosa-claro);
    border-radius: 10px;
    padding: 8px;
    transition: all 0.2s ease;
}
.input-cake:focus {
    border-color: var(--rosa);
    box-shadow: 0 0 0 3px rgba(232, 62, 140, 0.1);
}

/* Modal */
.cake-modal {
    display: none;
    position: fixed;
    z-index: 999;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.4);
    justify-content: center;
    align-items: center;
}
.cake-modal-content {
    background: #fff;
    border-radius: 16px;
    padding: 30px;
    width: 90%;
    max-width: 450px;
    position: relative;
    animation: aparecer 0.3s ease;
}
@keyframes aparecer {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.cerrar {
    position: absolute;
    top: 12px; right: 15px;
    font-size: 22px;
    cursor: pointer;
    color: var(--rosa);
}
.cerrar:hover { color: #d7337d; }
.text-rosa { color: var(--rosa); }
</style>
