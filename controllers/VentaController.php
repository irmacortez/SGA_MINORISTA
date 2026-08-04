<?php

class VentaController {


    /*=============================================
    MOSTRAR / LISTAR VENTAS
    =============================================*/
    public static function mostrarVentasController() {
        return Venta::listarVentasModel();
    }

    public static function listarVentasController() {
        return Venta::listarVentasModel();
    }


    /*=============================================
    REGISTRAR Y GUARDAR VENTA
    =============================================*/
    public static function guardarVentaController() {
        if (isset($_POST["totalVenta"]) && isset($_POST["productosCarrito"])) {
            
            $listaProductos = json_decode($_POST["productosCarrito"], true);

            if (empty($listaProductos)) {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "El carrito está vacío",
                        text: "Debe agregar al menos un producto para registrar la venta."
                    });
                </script>';
                return;
            }

            // Generar código único de factura (ej: V-100234)
            $codigoFactura = "V-" . rand(100000, 999999);
            
            $datosVenta = array(
                "codigo_factura" => $codigoFactura,
                "total" => $_POST["totalVenta"]
            );

            // LLAMADA AL MODELO:
            $respuesta = Venta::registrarVentaModel($datosVenta, $listaProductos);

            if ($respuesta == "ok") {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Venta Registrada!",
                        text: "La venta se ha guardado y el stock fue actualizado correctamente.",
                        showConfirmButton: true,
                        confirmButtonText: "Ok"
                    }).then((result) => {
                        if (result.value) {
                            window.location = "index.php?action=ventas";
                        }
                    });
                </script>';
            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "No se pudo registrar la venta. Intente nuevamente."
                    });
                </script>';
            }
        }
    }

    /*=============================================
    OBTENER DETALLE DE UNA VENTA POR ID
    =============================================*/
    public static function mostrarVentaPorIdController($idVenta) {
        return Venta::obtenerVentaPorIdModel($idVenta);
    }

    public static function mostrarDetalleVentaController($idVenta) {
        return Venta::obtenerDetalleVentaModel($idVenta);
    }

} 