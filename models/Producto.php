<?php
require_once __DIR__ . '/../config/conexion.php';

class Producto {
    private $db;

    public function __construct() {
        $conexionObj = new Conexion();
        $this->db = $conexionObj->conectar();
    }

    public function listarProductos() {
        $sql = "SELECT p.*, c.nombre_categoria AS categoria, pr.nombre_proveedor AS proveedor
                FROM productos p 
                INNER JOIN categorias c ON p.id_categoria = c.id_categoria 
                LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
                ORDER BY p.id_producto DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}