<?php

require_once __DIR__ . "/../models/NotaCredito.php";
require_once __DIR__ . "/../models/Producto.php";

class NotaCreditoController {

    /*=============================================
    EMITIR NOTA DE CRÉDITO (TOTAL, PARCIAL O PRECIO)
    =============================================*/
    public static function emitirNotaCreditoController() {
        if (isset($_POST["id_factura_origen"]) && isset($_POST["total_nc"])) {

            $datos = [
                "id_factura_origen" => $_POST["id_factura_origen"],
                "tipo_ajuste"       => $_POST["tipo_ajuste"], // 'devolucion_total', 'devolucion_parcial', 'diferencia_precio'
                "numero_nc"         => $_POST["numero_nc"],
                "motivo"            => $_POST["motivo"] ?? "Ajuste / Devolución de comprobante",
                "total_nc"          => $_POST["total_nc"],
                "productos"         => $_POST["listaProductosNC"] ?? "[]" // JSON con id_producto y cantidad a devolver (0 si es solo precio)
            ];

            $respuesta = NotaCreditoModel::emitirNotaCreditoModel($datos);

            if ($respuesta == "ok") {
                echo '<script>
                    alert("¡Nota de Crédito procesada con éxito!");
                    window.location = "index.php?action=notas-credito";
                </script>';
                exit();
            } else {
                echo '<script>
                    alert("Error al procesar la Nota de Crédito en la base de datos.");
                </script>';
            }
        }
    }

    /*=============================================
    EMITIR NOTA DE DÉBITO (RECARGO O RECUPERO)
    =============================================*/
    public static function emitirNotaDebitoController() {
        if (isset($_POST["id_factura_origen"]) && isset($_POST["total_nd"])) {

            $datos = [
                "id_factura_origen" => $_POST["id_factura_origen"],
                "numero_nd"         => $_POST["numero_nd"],
                "motivo"            => $_POST["motivo"] ?? "Recargo / Gastos administrativos",
                "total_nd"          => $_POST["total_nd"]
            ];

            $respuesta = NotaCreditoModel::emitirNotaDebitoModel($datos);

            if ($respuesta == "ok") {
                echo '<script>
                    alert("¡Nota de Débito emitida con éxito!");
                    window.location = "index.php?action=notas-credito";
                </script>';
                exit();
            } else {
                echo '<script>
                    alert("Error al procesar la Nota de Débito.");
                </script>';
            }
        }
    }

    /*=============================================
    LISTAR COMPROBANTES DE AJUSTE
    =============================================*/
    public static function listarAjustesController() {
        return NotaCreditoModel::listarAjustesModel();
    }
}