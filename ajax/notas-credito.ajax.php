<?php
require_once "../config/conexion.php";
require_once "../controllers/NotaCreditoController.php";
require_once "../models/NotaCredito.php";

class AjaxNotasCredito {

    /*=============================================
    BUSCAR FACTURA POR NÚMERO O CLIENTE
    =============================================*/
    public $idFactura;

    public function ajaxObtenerFactura() {
        $item = "id";
        $valor = $this->idFactura;

        // Llamada al método correspondiente en la capa Controller/Model
        $respuesta = NotaCreditoController::ctrMostrarFacturaParaNC($item, $valor);

        echo json_encode($respuesta);
    }
}

/*=============================================
RECEPCIÓN DE PETICIONES AJAX
=============================================*/
if (isset($_POST["idFactura"])) {
    $obtenerFactura = new AjaxNotasCredito();
    $obtenerFactura->idFactura = $_POST["idFactura"];
    $obtenerFactura->ajaxObtenerFactura();
}