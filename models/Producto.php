<?php

require_once __DIR__ . "/../config/conexion.php";

class Producto {

    /*=============================================
    LISTAR PRODUCTOS
    =============================================*/
    public static function listarProductosModel() {

        try {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM productos ORDER BY id_producto DESC");
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return array();
        } finally {
            $stmt = null;
        }

    }

    /*=============================================
    GUARDAR O CREAR PRODUCTO
    =============================================*/
    public static function guardarProductoModel($datos) {

        try {
            $stmt = Conexion::conectar()->prepare("INSERT INTO productos (codigo_barras, nombre_producto, precio_venta, stock_actual) VALUES (:codigo_barras, :nombre_producto, :precio_venta, :stock_actual)");

            $stmt->bindParam(":codigo_barras", $datos["codigo_barras"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre_producto", $datos["nombre_producto"], PDO::PARAM_STR);
            $stmt->bindParam(":precio_venta", $datos["precio_venta"], PDO::PARAM_STR);
            $stmt->bindParam(":stock_actual", $datos["stock_actual"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                return "ok";
            } else {
                return "error";
            }

        } catch (Exception $e) {
            return "error";
        } finally {
            $stmt = null;
        }

    }

}