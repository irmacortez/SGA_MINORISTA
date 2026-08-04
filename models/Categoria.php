<?php
require_once __DIR__ . "/../config/conexion.php";

class Categoria {

    /*=============================================
    LISTAR CATEGORÍAS
    =============================================*/
    public static function listarCategoriasModel() {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM categorias ORDER BY nombre_categoria ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    } // <-- ¡ACÁ FALTABA ESTA LLAVE DE CIERRE!

    /*=============================================
    MOSTRAR UNA CATEGORÍA POR ID
    =============================================*/
    public static function mostrarCategoriaPorIdModel($id) {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM categorias WHERE id_categoria = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /*=============================================
    GUARDAR CATEGORÍA
    =============================================*/
    public static function guardarCategoriaModel($datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO categorias (nombre_categoria) VALUES (:nombre_categoria)");
        $stmt->bindParam(":nombre_categoria", $datos["nombre_categoria"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

    /*=============================================
    ACTUALIZAR CATEGORÍA
    =============================================*/
    public static function actualizarCategoriaModel($datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE categorias SET nombre_categoria = :nombre_categoria WHERE id_categoria = :id_categoria");
        $stmt->bindParam(":nombre_categoria", $datos["nombre_categoria"], PDO::PARAM_STR);
        $stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

    /*=============================================
    ELIMINAR CATEGORÍA
    =============================================*/
    public static function eliminarCategoriaModel($id) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM categorias WHERE id_categoria = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }
}