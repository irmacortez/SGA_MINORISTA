<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (file_exists("../../config/conexion.php")) {
    require_once "../../config/conexion.php";
}
if (file_exists("../../models/Venta.php")) {
    require_once "../../models/Venta.php";
}

$idVenta = $_GET["id"] ?? $_GET["idVenta"] ?? 0;
$venta = false;

// 1. Intentar buscar en Base de Datos
if (class_exists('Venta')) {
    $venta = Venta::obtenerVentaPorIdModel($idVenta);
}

// 2. Si no esta en BD, leer del respaldo de sesion
if (!$venta && isset($_SESSION["ultima_venta_temp"])) {
    $venta = $_SESSION["ultima_venta_temp"];
}

// 3. Si aun no hay datos, crear objeto basico para mostrar la prueba
if (!$venta) {
    $venta = array(
        "codigo_factura" => "FAC-" . rand(10000, 99999),
        "fecha_hora"     => date("Y-m-d H:i:s"),
        "total"          => "0.00",
        "productos"      => "[]"
    );
}

$productos = json_decode($venta["productos"] ?? "[]", true) ?? [];
$codigo = $venta["codigo_factura"] ?? $venta["codigo"] ?? "FAC-" . $idVenta;
$fecha  = $venta["fecha_hora"] ?? $venta["fecha"] ?? date("Y-m-d H:i:s");
$total  = $venta["total"] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura <?php echo $codigo; ?></title>
    <style>
        body { font-family: monospace, sans-serif; width: 300px; margin: 0 auto; padding: 10px; color: #000; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 4px 0; font-size: 12px; }
        .linea { border-top: 1px dashed #000; margin: 8px 0; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print();">

    <div class="text-center">
        <h2 style="margin: 0;">SGA MINORISTA</h2>
        <p style="margin: 5px 0;">Comprobante de Venta</p>
        <p style="margin: 5px 0;"><strong>Nº: <?php echo $codigo; ?></strong></p>
        <p style="margin: 5px 0;">Fecha: <?php echo date("d/m/Y H:i", strtotime($fecha)); ?></p>
    </div>

    <div class="linea"></div>

    <table>
        <thead>
            <tr>
                <th>Cant.</th>
                <th>Producto</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $item): ?>
                    <tr>
                        <td><?php echo $item["cantidad"]; ?></td>
                        <td><?php echo htmlspecialchars($item["descripcion"]); ?></td>
                        <td class="text-right">$<?php echo number_format($item["total"], 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="text-center">Sin items agregados</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="linea"></div>

    <div class="text-right">
        <h3 style="margin: 5px 0;">TOTAL: $<?php echo number_format((float)$total, 2, ',', '.'); ?></h3>
    </div>

    <div class="text-center" style="margin-top:15px;">
        <p style="font-size: 11px;">¡Muchas gracias por su compra!</p>
    </div>

    <div class="text-center no-print" style="margin-top: 20px;">
        <button onclick="window.location.href='../../index.php?action=ventas';" style="padding: 5px 10px; cursor: pointer;">Volver a Ventas</button>
    </div>

</body>
</html>