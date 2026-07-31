<?php
require_once "config/conexion.php";

class ProductoModel {

    // Mostrar productos
    public static function listarProductosModel() {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM productos ORDER BY id_producto DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Registrar producto
    public static function guardarProductoModel($datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO productos (nombre_producto, precio_venta, stock_actual, stock_minimo, id_categoria, id_proveedor) VALUES (:nombre_producto, :precio_venta, :stock_actual, :stock_minimo, :id_categoria, :id_proveedor)");

        $stmt->bindParam(":nombre_producto", $datos["nombre_producto"], PDO::PARAM_STR);
        $stmt->bindParam(":precio_venta",    $datos["precio_venta"],    PDO::PARAM_STR);
        $stmt->bindParam(":stock_actual",    $datos["stock_actual"],    PDO::PARAM_INT);
        $stmt->bindParam(":stock_minimo",    $datos["stock_minimo"],    PDO::PARAM_INT);
        $stmt->bindParam(":id_categoria",    $datos["id_categoria"],    PDO::PARAM_INT);
        $stmt->bindParam(":id_proveedor",    $datos["id_proveedor"],    PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }
}