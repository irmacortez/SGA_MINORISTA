<?php

require_once __DIR__ . "/Conexion.php";

class NotaCreditoModel {

    /*=============================================
    EMITIR NOTA DE CRÉDITO EN BD
    =============================================*/
    public static function emitirNotaCreditoModel($datos) {
        $link = Conexion::conectar();

        try {
            $link->beginTransaction();

            // 1. Registrar Encabezado de la Nota de Crédito
            $stmt = $link->prepare("INSERT INTO notas_credito (id_factura_origen, tipo_ajuste, numero_nc, motivo, total) VALUES (:id_factura, :tipo_ajuste, :numero_nc, :motivo, :total)");
            $stmt->bindParam(":id_factura", $datos["id_factura_origen"], PDO::PARAM_INT);
            $stmt->bindParam(":tipo_ajuste", $datos["tipo_ajuste"], PDO::PARAM_STR);
            $stmt->bindParam(":numero_nc", $datos["numero_nc"], PDO::PARAM_STR);
            $stmt->bindParam(":motivo", $datos["motivo"], PDO::PARAM_STR);
            $stmt->bindParam(":total", $datos["total_nc"], PDO::PARAM_STR);
            $stmt->execute();

            // 2. Si hay artículos físicos a devolver, REINGRESAR AL STOCK
            $productos = is_array($datos["productos"]) ? $datos["productos"] : json_decode($datos["productos"], true);

            if (!empty($productos) && $datos["tipo_ajuste"] != "diferencia_precio") {
                foreach ($productos as $prod) {
                    if ($prod["cantidad"] > 0) {
                        $updateStock = $link->prepare("UPDATE productos SET stock_actual = stock_actual + :cantidad WHERE id = :id_producto");
                        $updateStock->bindParam(":cantidad", $prod["cantidad"], PDO::PARAM_INT);
                        $updateStock->bindParam(":id_producto", $prod["id_producto"], PDO::PARAM_INT);
                        $updateStock->execute();
                    }
                }
            }

            $link->commit();
            return "ok";

        } catch (Exception $e) {
            $link->rollBack();
            return "error: " . $e->getMessage();
        }
    }

    /*=============================================
    EMITIR NOTA DE DÉBITO EN BD
    =============================================*/
    public static function emitirNotaDebitoModel($datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO notas_debito (id_factura_origen, numero_nd, motivo, total) VALUES (:id_factura, :numero_nd, :motivo, :total)");
        $stmt->bindParam(":id_factura", $datos["id_factura_origen"], PDO::PARAM_INT);
        $stmt->bindParam(":numero_nd", $datos["numero_nd"], PDO::PARAM_STR);
        $stmt->bindParam(":motivo", $datos["motivo"], PDO::PARAM_STR);
        $stmt->bindParam(":total", $datos["total_nd"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

    /*=============================================
    LISTAR AJUSTES (NC Y ND)
    =============================================*/
    public static function listarAjustesModel() {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM notas_credito ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
