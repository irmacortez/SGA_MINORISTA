<?php
require_once "controllers/VentaController.php";

// Obtener la lista de ventas registradas en la base de datos
$ventas = VentaController::listarVentasController();
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-file-invoice-dollar text-primary"></i> Historial de Ventas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php?action=inicio">Inicio</a></li>
                    <li class="breadcrumb-item active">Ventas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <a href="index.php?action=crear-venta" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Nueva Venta
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tablaVentas">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Código Factura</th>
                                <th>Fecha / Hora</th>
                                <th>Total</th>
                                <th style="width: 100px;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ventas)): ?>
                                <?php foreach ($ventas as $index => $v): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><strong><?php echo htmlspecialchars($v['codigo_factura']); ?></strong></td>
                                        <td><?php echo date("d/m/Y H:i", strtotime($v['fecha_hora'])); ?></td>
                                        <td>$<?php echo number_format($v['total'], 2); ?></td>
                                        <td class="text-center">
                                            <a href="index.php?action=ver-venta&idVenta=<?php echo $v['id_venta']; ?>" class="btn btn-info btn-sm" title="Ver Detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No se encontraron ventas registradas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
