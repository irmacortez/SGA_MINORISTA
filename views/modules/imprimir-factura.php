<?php
require_once __DIR__ . "/../../config/conexion.php";
require_once __DIR__ . "/../../models/Venta.php";

if (!isset($_GET["idVenta"]) || empty($_GET["idVenta"])) {
    die("ID de venta no proporcionado.");
}

$idVenta = intval($_GET["idVenta"]);
$venta = VentaModel::obtenerVentaPorIdModel($idVenta);

if (!$venta) {
    die("La venta solicitada no existe.");
}

// Consulta directa adaptada para garantizar el join con productos
try {
    $stmt = Conexion::conectar()->prepare("
        SELECT d.*, 
               COALESCE(p.nombre, p.descripcion, 'Producto N/A') AS nombre_producto
        FROM detalle_ventas d 
        LEFT JOIN productos p ON d.id_producto = p.id_producto 
        WHERE d.id_venta = :id
    ");
    $stmt->bindParam(":id", $idVenta, PDO::PARAM_INT);
    $stmt->execute();
    $detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $detalle = array();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket Factura #<?php echo $venta["codigo_factura"]; ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 300px;
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .linea { border-top: 1px dashed #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        th { border-bottom: 1px solid #000; padding: 4px 0; }
        td { padding: 4px 0; }
        .total-box { font-size: 14px; font-weight: bold; margin-top: 10px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print text-center" style="margin-bottom: 15px;">
        <button onclick="window.print();" style="padding: 8px 15px; cursor: pointer;">
            🖨️ Imprimir Ticket
        </button>
    </div>

    <div class="text-center">
        <strong>SGA MINORISTA</strong><br>
        Comprobante de Venta<br>
        <strong>Factura Nº: <?php echo $venta["codigo_factura"]; ?></strong><br>
        Fecha: <?php echo date("d/m/Y H:i", strtotime($venta["fecha_hora"])); ?>
    </div>

    <div class="linea"></div>

    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Cant.</th>
                <th style="width: 50%;">Producto</th>
                <th style="width: 30%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($detalle)): ?>
                <tr>
                    <td colspan="3" class="text-center" style="padding: 10px 0;">
                        <em>Sin detalle grabado en DB</em>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($detalle as $item): ?>
                    <tr>
                        <td style="vertical-align: top;"><?php echo $item["cantidad"]; ?>x</td>
                        <td style="vertical-align: top;">
                            <?php 
                                $nombre = !empty($item["nombre_producto"]) ? $item["nombre_producto"] : ($item["descripcion"] ?? 'Producto');
                                echo htmlspecialchars($nombre); 
                            ?>
                        </td>
                        <td class="text-right" style="vertical-align: top;">
                            $<?php echo number_format($item["subtotal"] ?? ($item["precio_unitario"] * $item["cantidad"]), 2, ',', '.'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="linea"></div>

    <div class="text-right total-box">
        TOTAL: $<?php echo number_format($venta["total"], 2, ',', '.'); ?>
    </div>

    <div class="linea"></div>

    <div class="text-center">
        ¡Gracias por su compra!<br>
        --- Conserve este ticket ---
    </div>

</body>
</html>