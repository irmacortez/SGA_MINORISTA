<?php

require_once __DIR__ . "/../models/Venta.php";

class VentaController {

    /*=============================================
    GUARDAR VENTA
    =============================================*/
    public function guardarVentaController() {

        // Captura tanto si el input se llama 'total' o 'totalVenta'
        if ((isset($_POST["total"]) && floatval($_POST["total"]) > 0) || (isset($_POST["totalVenta"]) && floatval($_POST["totalVenta"]) > 0)) {

            $total = $_POST["total"] ?? $_POST["totalVenta"];
            
            // Decodificar el JSON de productos enviado desde el JS
            $productosJson = $_POST["productosCarrito"] ?? "[]";
            $listaProductos = json_decode($productosJson, true);

            if (!empty($listaProductos) && is_array($listaProductos)) {

                $datosVenta = [
                    "codigo_factura" => $_POST["codigoFactura"] ?? 1,
                    "total"          => $total
                ];

                // Invocación directa a tu VentaModel y su método registrarVentaModel
                $respuesta = VentaModel::registrarVentaModel($datosVenta, $listaProductos);

                if ($respuesta === "ok") {
                    echo '<script>
                        alert("¡Venta registrada con éxito y stock actualizado!");
                        window.location = "index.php?action=crear-venta";
                    </script>';
                    exit();
                } else {
                    echo '<script>
                        alert("Error de Base de Datos: ' . addslashes($respuesta) . '");
                    </script>';
                }
            } else {
                echo '<script>
                    alert("El carrito está vacío o el formato de productos es incorrecto.");
                </script>';
            }
        }
    }
}