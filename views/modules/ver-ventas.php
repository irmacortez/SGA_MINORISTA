<?php
// FORZAR MUESTRA DE ERRORES DE PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Carga segura de archivos evitando errores de inclusión
$baseDir = dirname(__DIR__, 2);

if (file_exists($baseDir . "/config/conexion.php")) {
    require_once $baseDir . "/config/conexion.php";
}
if (file_exists($baseDir . "/models/Venta.php")) {
    require_once $baseDir . "/models/Venta.php";
}
if (file_exists($baseDir . "/controllers/VentaController.php")) {
    require_once $baseDir . "/controllers/VentaController.php";
}

// Obtener las ventas de forma segura
$ventas = array();
if (class_exists('VentaController') && method_exists('VentaController', 'ctrListarVentas')) {
    $ventas = VentaController::ctrListarVentas();
} else {
    echo "<div class='alert alert-danger m-3'>Error: No se encontró la clase o método VentaController::ctrListarVentas().</div>";
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-white">
                <i class="fas fa-list-alt me-2"></i>Historial de Ventas Realizadas
            </h5>
            <a href="index.php?action=crear-venta" class="btn btn-success btn-sm">
                <i class="fas fa-plus me-1"></i> Nueva Venta
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" class="ps-3">#</th>
                            <th scope="col">Nº Factura</th>
                            <th scope="col">Fecha / Hora</th>
                            <th scope="col">Total ($)</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ventas) || !is_array($ventas)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    No hay ventas registradas o la consulta devolvió un valor nulo.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ventas as $index => $v): ?>
                                <tr>
                                    <td class="ps-3"><?php echo $index + 1; ?></td>
                                    <td><strong>#<?php echo htmlspecialchars($v["codigo_factura"]); ?></strong></td>
                                    <td><?php echo isset($v["fecha_hora"]) ? date("d/m/Y H:i", strtotime($v["fecha_hora"])) : 'S/F'; ?></td>
                                    <td class="text-success fw-bold">
                                        $ <?php echo number_format($v["total"], 2, ',', '.'); ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn btn-outline-info btn-sm btnImprimirFactura" 
                                                data-id="<?php echo $v["id_venta"]; ?>"
                                                title="Imprimir Comprobante">
                                            <i class="fas fa-print me-1"></i> Imprimir
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btnImprimirFactura').forEach(btn => {
    btn.addEventListener('click', function() {
        const idVenta = this.getAttribute('data-id');
        
        // Ruta absoluta desde la raíz del servidor web
        const url = `/SGA_MINORISTA/views/modules/imprimir-factura.php?idVenta=${idVenta}`;
        
        window.open(url, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
    });
});
</script>