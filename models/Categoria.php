<?php
require_once __DIR__ . "/../config/conexion.php";

class Categoria {
    public static function listarCategoriasModel() {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM categorias ORDER BY nombre_categoria ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}