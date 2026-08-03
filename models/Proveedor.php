<?php
require_once __DIR__ . "/../config/conexion.php";

class Proveedor {
    public static function listarProveedoresModel() {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM proveedores ORDER BY nombre_proveedor ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}