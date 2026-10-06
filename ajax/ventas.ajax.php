<?php
require_once "../config/conexion.php";
require_once "../controllers/VentaController.php";
require_once "../models/Venta.php";

class AjaxVentas {

    public $idVenta;

    public function ajaxObtenerVenta() {
        $idVenta = $this->idVenta;

        // Obtener encabezado de la venta
        $db = Conexion::conectar();
        $sqlVenta = "SELECT v.*, u.nombre AS vendedor 
                    FROM ventas v 
                    LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario 
                    WHERE v.id_venta = :id";
        $stmtVenta = $db->prepare($sqlVenta);
        $stmtVenta->execute([':id' => $idVenta]);
        $venta = $stmtVenta->fetch(PDO::FETCH_ASSOC);

        // Obtener detalle de productos
        $sqlDetalle = "SELECT dv.*, p.nombre AS descripcion 
                       FROM detalle_ventas dv 
                       INNER JOIN productos p ON dv.id_producto = p.id_producto 
                       WHERE dv.id_venta = :id";
        $stmtDetalle = $db->prepare($sqlDetalle);
        $stmtDetalle->execute([':id' => $idVenta]);
        $productos = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

        $respuesta = [
            "venta"     => $venta,
            "productos" => $productos
        ];

        echo json_encode($respuesta);
    }
}

if (isset($_POST["idVenta"])) {
    $ajax = new AjaxVentas();
    $ajax->idVenta = $_POST["idVenta"];
    $ajax->ajaxObtenerVenta();
}