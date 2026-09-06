<?php
// Obtener historial de ventas ordenadas por fecha
$ventas = VentaController::listarVentasController();
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-file-text-o"></i> Historial de Facturas y Ventas</h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Facturas Registradas (Ordenadas por Fecha)</h3>
                <a href="index.php?action=crear-venta" class="btn btn-success pull-right">
                    <i class="fa fa-plus"></i> Nueva Venta
                </a>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped dt-responsive">
                    <thead>
                        <tr>
                            <th style="width:10px">#</th>
                            <th>Nº Factura</th>
                            <th>Fecha y Hora</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventas as $index => $v): ?>
                            <tr>
                                <td><?php echo ($index + 1); ?></td>
                                <td><strong><?php echo $v["codigo_factura"]; ?></strong></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($v["fecha_hora"])); ?></td>
                                <td>$<?php echo number_format($v["total"], 2); ?></td>
                                <td>
                                    <a href="index.php?action=ver-venta&id=<?php echo $v["id_venta"]; ?>" class="btn btn-info btn-xs">
                                        <i class="fa fa-eye"></i> Ver Detalle
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($ventas)): ?>
                            <tr><td colspan="5" class="text-center">No hay ventas registradas aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>