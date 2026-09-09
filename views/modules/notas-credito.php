<?php
// Obtener los listados desde el controlador
$notasCredito = NotaCreditoController::listarNotasCreditoController();
$notasDebito  = NotaCreditoController::listarNotasDebitoController();
?>

<div class="container-fluid px-4 py-3">
    
    <!-- Encabezado y Botón de Nueva NC/ND -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Notas de Crédito y Débito</h1>
            <p class="text-muted small mb-0">Historial de comprobantes emitidos y ajustes de facturación.</p>
        </div>
        <a href="index.php?action=crear-nota-credito" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nueva Nota de Crédito / Débito
        </a>
    </div>

    <!-- Pestañas para alternar entre Crédito y Débito -->
    <ul class="nav nav-tabs mb-3" id="notasTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="nc-tab" data-bs-toggle="tab" data-bs-target="#nc-panel" type="button" role="tab">
                Notas de Crédito (<?= count($notasCredito) ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="nd-tab" data-bs-toggle="tab" data-bs-target="#nd-panel" type="button" role="tab">
                Notas de Débito (<?= count($notasDebito) ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="notasTabContent">

        <!-- =============================================
        PANEL 1: NOTAS DE CRÉDITO
        ============================================= -->
        <div class="tab-pane fade show active" id="nc-panel" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>N° Comprobante</th>
                                    <th>Factura Origen</th>
                                    <th>Tipo Ajuste</th>
                                    <th>Motivo / Observaciones</th>
                                    <th>Monto Total</th>
                                    <th>Fecha Emisión</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($notasCredito)): ?>
                                    <?php foreach ($notasCredito as $nc): ?>
                                        <tr>
                                            <td class="fw-bold text-primary"><?= htmlspecialchars($nc["numero_nc"]) ?></td>
                                            <td>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($nc["numero_factura"]) ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                switch ($nc["tipo_ajuste"]) {
                                                    case 'devolucion_total':
                                                        echo '<span class="badge bg-danger">Devolución Total (+Stock)</span>';
                                                        break;
                                                    case 'devolucion_parcial':
                                                        echo '<span class="badge bg-warning text-dark">Devolución Parcial (+Stock)</span>';
                                                        break;
                                                    case 'diferencia_precio':
                                                        echo '<span class="badge bg-info text-dark">Diferencia de Precio</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge bg-light text-dark">' . $nc["tipo_ajuste"] . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td><?= htmlspecialchars($nc["motivo"] ?? $nc["observaciones"] ?? 'N/A') ?></td>
                                            <td class="fw-bold text-success">$<?= number_format($nc["total"], 2, ',', '.') ?></td>
                                            <td><?= date("d/m/Y H:i", strtotime($nc["fecha_emision"])) ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-info" title="Ver Detalle" onclick="verDetalleNC(<?= $nc['id'] ?>)">
                                                    <i class="bi bi-eye"></i> Ver
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No se han registrado Notas de Crédito todavía.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- =============================================
        PANEL 2: NOTAS DE DÉBITO
        ============================================= -->
        <div class="tab-pane fade" id="nd-panel" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>N° Comprobante</th>
                                    <th>Factura Origen</th>
                                    <th>Motivo / Recargo</th>
                                    <th>Monto Total</th>
                                    <th>Fecha Emisión</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($notasDebito)): ?>
                                    <?php foreach ($notasDebito as $nd): ?>
                                        <tr>
                                            <td class="fw-bold text-danger"><?= htmlspecialchars($nd["numero_nd"]) ?></td>
                                            <td>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($nd["numero_factura"]) ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($nd["motivo"] ?? $nd["observaciones"] ?? 'Recargo / Mora') ?></td>
                                            <td class="fw-bold text-dark">$<?= number_format($nd["total"], 2, ',', '.') ?></td>
                                            <td><?= date("d/m/Y H:i", strtotime($nd["fecha_emision"])) ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-info" title="Ver Detalle" onclick="verDetalleND(<?= $nd['id'] ?>)">
                                                    <i class="bi bi-eye"></i> Ver
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No se han registrado Notas de Débito todavía.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal para Detalle de Comprobante (Opcional vía AJAX o JS) -->
<div class="modal fade" id="modalDetalleComprobante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModalDetalle">Detalle del Comprobante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contenidoModalDetalle">
                <!-- Se carga dinámicamente -->
            </div>
        </div>
    </div>
</div>

<script>
function verDetalleNC(idNC) {
    // Función para abrir modal o consultar AJAX el detalle de los productos devueltos
    alert("Consultando detalle de la Nota de Crédito ID: " + idNC);
}

function verDetalleND(idND) {
    // Función para abrir modal o consultar detalle de la Nota de Débito
    alert("Consultando detalle de la Nota de Débito ID: " + idND);
}
</script>