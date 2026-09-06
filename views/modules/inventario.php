<?php
// Procesar guardado si viene por POST
if (method_exists('ProductoController', 'guardarProductoController')) {
    ProductoController::guardarProductoController();
}

// Cargar listado de productos
$productos = ProductoController::listarProductosController();
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-boxes"></i> Control de Inventario</h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Listado de Productos en Stock</h3>
                <a href="index.php?action=crear-venta" class="btn btn-success pull-right">
                    <i class="fa fa-shopping-cart"></i> Ir a Nueva Venta
                </a>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Producto</th>
                            <th>Stock Actual</th>
                            <th>Precio Venta</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $index => $p): ?>
                            <tr>
                                <td><?php echo ($index + 1); ?></td>
                                <td><strong><?php echo htmlspecialchars($p['nombre_producto'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                <td>
                                    <?php if ($p['stock_actual'] <= 5): ?>
                                        <span class="badge bg-red"><?php echo $p['stock_actual']; ?> (Bajo)</span>
                                    <?php else: ?>
                                        <span class="badge bg-green"><?php echo $p['stock_actual']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>$<?php echo number_format($p['precio_venta'], 2); ?></td>
                                <td>
                                    <?php if ($p['stock_actual'] > 0): ?>
                                        <span class="label label-success">Disponible</span>
                                    <?php else: ?>
                                        <span class="label label-danger">Agotado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($productos)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No hay productos registrados en la base de datos.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>