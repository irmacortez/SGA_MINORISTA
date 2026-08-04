<?php
require_once __DIR__ . "/../config/conexion.php";

class Venta {

    public static function listarVentasModel() {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM ventas ORDER BY fecha_hora DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function registrarVentaModel($datosVenta, $listaProductos) {
        $db = Conexion::conectar();

        try {
            $db->beginTransaction();

            // 1. Insertar el encabezado de la venta
            $sqlVenta = "INSERT INTO ventas (codigo_factura, fecha_hora, total) VALUES (:codigo, NOW(), :total)";
            $stmtVenta = $db->prepare($sqlVenta);
            $stmtVenta->bindParam(":codigo", $datosVenta["codigo_factura"], PDO::PARAM_STR);
            $stmtVenta->bindParam(":total", $datosVenta["total"], PDO::PARAM_STR);
            $stmtVenta->execute();

            $idVenta = $db->lastInsertId();

            // 2. Preparar consultas para detalle y descuento de stock
            $sqlDetalle = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, preciounitario, subtotal) 
                           VALUES (:id_venta, :id_producto, :cantidad, :preciounitario, :subtotal)";
            $stmtDetalle = $db->prepare($sqlDetalle);

            // ACTUALIZAR STOCK EN TABLA PRODUCTOS
            $sqlStock = "UPDATE productos SET stock_actual = stock_actual - :cantidad WHERE id_producto = :id_producto";
            $stmtStock = $db->prepare($sqlStock);

            // 3. Recorrer los productos del carrito
            foreach ($listaProductos as $prod) {
                $idProducto = $prod["id_producto"];
                $cantidad   = $prod["cantidad"];
                $precio     = $prod["preciounitario"];
                $subtotal   = $prod["subtotal"];

                // Insertar detalle
                $stmtDetalle->bindParam(":id_venta", $idVenta, PDO::PARAM_INT);
                $stmtDetalle->bindParam(":id_producto", $idProducto, PDO::PARAM_INT);
                $stmtDetalle->bindParam(":cantidad", $cantidad, PDO::PARAM_INT);
                $stmtDetalle->bindParam(":preciounitario", $precio, PDO::PARAM_STR);
                $stmtDetalle->bindParam(":subtotal", $subtotal, PDO::PARAM_STR);
                $stmtDetalle->execute();

                // Descontar stock
                $stmtStock->bindParam(":cantidad", $cantidad, PDO::PARAM_INT);
                $stmtStock->bindParam(":id_producto", $idProducto, PDO::PARAM_INT);
                $stmtStock->execute();
            }

            $db->commit();
            return "ok";

        } catch (Exception $e) {
            $db->rollBack();
            return "error";
        }
    }

    public static function obtenerVentaPorIdModel($idVenta) {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM ventas WHERE id_venta = :id");
        $stmt->bindParam(":id", $idVenta, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerDetalleVentaModel($idVenta) {
        $sql = "SELECT d.*, p.nombre_producto 
                FROM detalle_ventas d 
                INNER JOIN productos p ON d.id_producto = p.id_producto 
                WHERE d.id_venta = :id";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(":id", $idVenta, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}