<?php

require_once __DIR__ . "/../config/conexion.php";

class NotaCreditoModel {

    /*=============================================
    MOSTRAR FACTURA ORIGEN PARA NC / ND
    =============================================*/
    public static function mdlMostrarFacturaParaNC($tabla, $item, $valor) {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /*=============================================
    EMITIR NOTA DE CRÉDITO
    =============================================*/
    public static function emitirNotaCreditoModel($datos) {
        $db = Conexion::conectar();

        try {
            $db->beginTransaction();

            // Insertar en la tabla real 'notascredito' usando 'fecha_emision'
            $stmt = $db->prepare("INSERT INTO notascredito (id_factura_origen, tipo_ajuste, numero_nc, motivo, total_nc, fecha_emision) 
                                  VALUES (:id_factura_origen, :tipo_ajuste, :numero_nc, :motivo, :total_nc, NOW())");

            $stmt->bindParam(":id_factura_origen", $datos["id_factura_origen"], PDO::PARAM_INT);
            $stmt->bindParam(":tipo_ajuste", $datos["tipo_ajuste"], PDO::PARAM_STR);
            $stmt->bindParam(":numero_nc", $datos["numero_nc"], PDO::PARAM_STR);
            $stmt->bindParam(":motivo", $datos["motivo"], PDO::PARAM_STR);
            $stmt->bindParam(":total_nc", $datos["total_nc"], PDO::PARAM_STR);

            if ($stmt->execute()) {
                $idNC = $db->lastInsertId();

                // Si vienen productos en el JSON, procesamos el detalle y stock
                $productos = json_decode($datos["productos"], true);
                if (!empty($productos) && is_array($productos)) {
                    foreach ($productos as $prod) {
                        // Insertar en detalle_nota_credito si la tenés creada
                        $stmtDetalle = $db->prepare("INSERT INTO detalle_nota_credito (id_nota_credito, id_producto, cantidad, precio_unitario, subtotal) 
                                                     VALUES (:id_nc, :id_prod, :cant, :precio, :subtotal)");
                        $stmtDetalle->bindParam(":id_nc", $idNC, PDO::PARAM_INT);
                        $stmtDetalle->bindParam(":id_prod", $prod["id"], PDO::PARAM_INT);
                        $stmtDetalle->bindParam(":cant", $prod["cantidad"], PDO::PARAM_INT);
                        $stmtDetalle->bindParam(":precio", $prod["precio"], PDO::PARAM_STR);
                        $stmtDetalle->bindParam(":subtotal", $prod["subtotal"], PDO::PARAM_STR);
                        $stmtDetalle->execute();

                        // Devolver stock al inventario
                        if ($prod["cantidad"] > 0) {
                            $stmtStock = $db->prepare("UPDATE productos SET stock = stock + :cant WHERE id = :id_prod");
                            $stmtStock->bindParam(":cant", $prod["cantidad"], PDO::PARAM_INT);
                            $stmtStock->bindParam(":id_prod", $prod["id"], PDO::PARAM_INT);
                            $stmtStock->execute();
                        }
                    }
                }

                $db->commit();
                return "ok";
            } else {
                $db->rollBack();
                return "error";
            }
        } catch (Exception $e) {
            $db->rollBack();
            return "error: " . $e->getMessage();
        }
    }
    /*=============================================
    EMITIR NOTA DE DÉBITO
    =============================================*/
    public static function emitirNotaDebitoModel($datos) {
        $db = Conexion::conectar();

        try {
            $stmt = $db->prepare("INSERT INTO notas_debito (id_factura_origen, numero_nd, motivo, total_nd, fecha_emision) 
                                  VALUES (:id_factura_origen, :numero_nd, :motivo, :total_nd, NOW())");

            $stmt->bindParam(":id_factura_origen", $datos["id_factura_origen"], PDO::PARAM_INT);
            $stmt->bindParam(":numero_nd", $datos["numero_nd"], PDO::PARAM_STR);
            $stmt->bindParam(":motivo", $datos["motivo"], PDO::PARAM_STR);
            $stmt->bindParam(":total_nd", $datos["total_nd"], PDO::PARAM_STR);

            if ($stmt->execute()) {
                return "ok";
            } else {
                return "error";
            }
        } catch (Exception $e) {
            return "error: " . $e->getMessage();
        }
    }

    /*=============================================
    LISTAR COMPROBANTES DE AJUSTE (NC Y ND UNIFICADOS)
    =============================================*/
    public static function listarAjustesModel() {
        // Mapeamos 'fecha_emision' como 'fecha' para que las vistas lean $item["fecha"] sin romper nada
        $stmt = Conexion::conectar()->prepare("
            SELECT id, id_factura_origen, numero_nc AS numero, 'Nota de Crédito' AS tipo, motivo, total_nc AS total, fecha_emision AS fecha 
            FROM notascredito
            UNION ALL
            SELECT id, id_factura_origen, numero_nd AS numero, 'Nota de Débito' AS tipo, motivo, total_nd AS total, fecha_emision AS fecha 
            FROM notas_debito
            ORDER BY fecha DESC
        ");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}