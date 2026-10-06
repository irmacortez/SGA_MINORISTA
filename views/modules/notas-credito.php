<?php
require_once "controllers/NotaCreditoController.php";
require_once "models/NotaCredito.php";

$notasCredito = NotaCreditoController::ctrListarNotasCredito();
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Gestión de Notas de Crédito
            <small>Historial de Emisiones</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">Notas de Crédito</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            
            <div class="box-header with-border">
                <a href="index.php?action=emitir-nota-credito" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Nueva Nota de Crédito
                </a>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped tablas">
                        <thead>
                            <tr>
                                <th style="width:10px">#</th>
                                <th>N° Nota Crédito</th>
                                <th>Factura de Origen</th>
                                <th>Motivo / Observación</th>
                                <th>Fecha Emisión</th>
                                <th>Total Devuelto</th>
                                <th>Emitido por</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($notasCredito)): ?>
                                <?php foreach ($notasCredito as $key => $nc): ?>
                                    <tr>
                                        <td><?php echo ($key + 1); ?></td>
                                        <td><strong><?php echo htmlspecialchars($nc['numero_nc']); ?></strong></td>
                                        <td><span class="label label-info"><?php echo htmlspecialchars($nc['factura_origen']); ?></span></td>
                                        <td><?php echo htmlspecialchars($nc['motivo'] ?? 'Anulación de comprobante'); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($nc['fecha'])); ?></td>
                                        <td>$ <?php echo number_format($nc['total'], 2, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars($nc['usuario'] ?? 'Sistema'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No hay Notas de Crédito registradas hasta el momento.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>