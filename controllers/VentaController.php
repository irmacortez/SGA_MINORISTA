<?php

class VentaController {

    /*=============================================
    OBTENER ÚLTIMO CÓDIGO DE FACTURA
    =============================================*/
    public static function ctrObtenerUltimoCodigoFactura() {
        $tabla = "ventas";
        $respuesta = VentaModel::mdlObtenerUltimoCodigoFactura($tabla);

        if (!$respuesta || empty($respuesta["codigo_factura"])) {
            return "1";
        } else {
            return intval($respuesta["codigo_factura"]) + 1;
        }
    }

    /*=============================================
    GUARDAR VENTA
    =============================================*/
    public function guardarVentaController() {
        if (isset($_POST["codigoFactura"]) && isset($_POST["totalVenta"])) {

            // Validar que se hayan enviado productos en el JSON
            $productos = isset($_POST["productosCarrito"]) ? $_POST["productosCarrito"] : "[]";
            $listaProductos = json_decode($productos, true);

            if (empty($listaProductos) || !is_array($listaProductos)) {
                echo '<script>
                    alert("⚠️ El carrito está vacío. Agregá al menos un producto antes de facturar.");
                </script>';
                return;
            }

            $datosVenta = array(
                "codigo_factura" => $_POST["codigoFactura"],
                "total"          => $_POST["totalVenta"],
                "productos"      => $productos
            );

            // Registrar la venta en la base de datos
            $respuesta = VentaModel::registrarVentaModel("ventas", $datosVenta, $listaProductos);

            if ($respuesta == "ok") {
                echo '<script>
                    alert("✅ Venta registrada con éxito.");
                    window.location = "index.php?action=ventas";
                </script>';
            } else {
                echo '<script>
                    alert("❌ Ocurrió un error al guardar la venta.");
                </script>';
            }
        }
    }
}