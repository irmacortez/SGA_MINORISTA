<?php

require_once __DIR__ . "/../models/Venta.php";
require_once __DIR__ . "/../models/Producto.php";

class VentaController {

    public static function listarVentasController() {
        return Venta::listarVentasModel();
    }

    public function ctrCrearVenta() {

        if (isset($_POST["listaProductos"]) && !empty($_POST["listaProductos"])) {

            if ($_POST["listaProductos"] == "[]") {
                echo '<script>alert("No se pueden procesar ventas vacías.");</script>';
                return;
            }

            $datos = array(
                "codigo_factura" => "FAC-" . rand(10000, 99999),
                "total"          => $_POST["totalVenta"] ?? 0,
                "productos"      => $_POST["listaProductos"],
                "fecha_hora"     => date("Y-m-d H:i:s")
            );

            // Guardar o simular ID si falla la tabla
            $idVenta = Venta::guardarVentaModel($datos);

            // Guardar temporalmente la ultima venta en SESSION como respaldo para el ticket
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION["ultima_venta_temp"] = array(
                "id_venta"       => $idVenta,
                "codigo_factura" => $datos["codigo_factura"],
                "total"          => $datos["total"],
                "productos"      => $datos["productos"],
                "fecha_hora"     => $datos["fecha_hora"]
            );

            // Redirección forzada e inmediata a imprimir-factura.php
            echo '<script>
                window.location.href = "views/modules/imprimir-factura.php?id=' . $idVenta . '";
            </script>';
            exit();
        }
    }
}