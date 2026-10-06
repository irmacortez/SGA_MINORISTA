<?php

class NotaCreditoController {

    public function ctrCrearNotaCredito() {
        if (isset($_POST["idVentaAnular"])) {

            if (!empty($_POST["listaProductosNC"])) {

                $idVenta   = $_POST["idVentaAnular"];
                $motivo    = $_POST["motivoNC"] ?? "Devolución / Anulación de factura";
                $totalNC   = $_POST["totalNC"] ?? 0;
                $numeroNC  = "NC-" . str_pad(rand(1, 99999), 5, "0", STR_PAD_LEFT);

                $datos = array(
                    "numero_nc"   => $numeroNC,
                    "id_venta"    => $idVenta,
                    "motivo"      => $motivo,
                    "total"       => $totalNC,
                    "productos"   => $_POST["listaProductosNC"]
                );

                $respuesta = NotaCredito::guardarNotaCreditoModel($datos);

                if ($respuesta) {
                    echo '<script>
                        alert("Nota de Crédito emitida correctamente y stock reintegrado.");
                        window.location = "index.php?action=notas-credito";
                    </script>';
                } else {
                    echo '<script>
                        alert("Ocurrió un error al procesar la Nota de Crédito.");
                    </script>';
                }
            }
        }
    }

    public static function ctrListarNotasCredito() {
        return NotaCredito::listarNotasCreditoModel();
    }
}