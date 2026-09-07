<?php

require_once __DIR__ . "/../config/conexion.php";

class VentaModel {

    /*=============================================
    OBTENER ÚLTIMA VENTA
    =============================================*/
    public static function obtenerUltimaVentaModel() {
        try {
            $stmt = Conexion::conectar()->prepare("SELECT codigo_factura FROM ventas ORDER BY id_venta DESC LIMIT 1");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    /*=============================================
    LISTAR VENTAS
    =============================================*/
    public static function listarVentaModel() {
        try {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM ventas ORDER BY id_venta DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return array();
        }
    }

    /*=============================================
    OBTENER VENTA POR ID
    =============================================*/
    public static function obtenerVentaPorIdModel($idVenta) {
        try {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM ventas WHERE id_venta = :id");
            $stmt->bindParam(":id", $idVenta, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    /*=============================================
    REGISTRAR VENTA Y DESCONTAR STOCK
    =============================================*/
    public static function registrarVentaModel($datosVenta, $listaProductos) {
        $link = Conexion::conectar();

        try {
            $link->beginTransaction();

            // 1. Insertar Cabecera de la Venta
            $stmt = $link->prepare("INSERT INTO ventas (codigo_factura, total, fecha_hora) VALUES (:codigo, :total, NOW())");
            $stmt->bindParam(":codigo", $datosVenta["codigo_factura"], PDO::PARAM_INT);
            $stmt->bindParam(":total", $datosVenta["total"], PDO::PARAM_STR);
            $stmt->execute();

            $idVenta = $link->lastInsertId();

            // 2. Insertar Detalle de Productos y Actualizar Stock
            foreach ($listaProductos as $producto) {

                $idProducto = $producto["id_producto"] ?? $producto["id"] ?? $producto["idProducto"];
                $cantidad   = $producto["cantidad"] ?? $producto["cant"] ?? 1;
                $precio     = $producto["preciounitario"] ?? $producto["precio"] ?? $producto["precio_unitario"] ?? 0;
                $subtotal   = $producto["subtotal"] ?? ($cantidad * $precio);

                // Guardar ítem en detalle_ventas usando la columna preciounitario
                $stmtDetalle = $link->prepare("
                    INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, preciounitario, subtotal) 
                    VALUES (:id_venta, :id_prod, :cant, :precio, :sub)
                ");
                $stmtDetalle->bindParam(":id_venta", $idVenta, PDO::PARAM_INT);
                $stmtDetalle->bindParam(":id_prod", $idProducto, PDO::PARAM_INT);
                $stmtDetalle->bindParam(":cant", $cantidad, PDO::PARAM_INT);
                $stmtDetalle->bindParam(":precio", $precio, PDO::PARAM_STR);
                $stmtDetalle->bindParam(":sub", $subtotal, PDO::PARAM_STR);
                $stmtDetalle->execute();

                // Actualizar Stock en la tabla productos (stock_actual)
                $stmtStock = $link->prepare("UPDATE productos SET stock_actual = stock_actual - :cant WHERE id_producto = :id_prod");
                $stmtStock->bindParam(":cant", $cantidad, PDO::PARAM_INT);
                $stmtStock->bindParam(":id_prod", $idProducto, PDO::PARAM_INT);
                $stmtStock->execute();
            }

            $link->commit();
            return "ok";

        } catch (Exception $e) {
            if ($link->inTransaction()) {
                $link->rollBack();
            }
            return "Error DB: " . $e->getMessage();
        }
    }
}