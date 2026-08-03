<?php

class CategoriaController {

    /*=============================================
    MOSTRAR / LISTAR CATEGORÍAS
    =============================================*/
    public static function listarCategoriasController() {
        return Categoria::listarCategoriasModel();
    }

    /*=============================================
    GUARDAR CATEGORÍA
    =============================================*/
    public static function guardarCategoriaController() {
        if (isset($_POST["nuevoNombreCategoria"])) {

            $datos = array(
                "nombre_categoria" => trim($_POST["nuevoNombreCategoria"])
            );

            $respuesta = Categoria::guardarCategoriaModel($datos);

            if ($respuesta === "ok") {
                echo '<script>
                    window.location = "index.php?action=categorias&status=success";
                </script>';
                exit();
            }
        }
    }

    /*=============================================
    ACTUALIZAR CATEGORÍA
    =============================================*/
    public static function actualizarCategoriaController() {
        if (isset($_POST["editarIdCategoria"]) && isset($_POST["editarNombreCategoria"])) {

            $datos = array(
                "id_categoria"     => $_POST["editarIdCategoria"],
                "nombre_categoria" => trim($_POST["editarNombreCategoria"])
            );

            $respuesta = Categoria::actualizarCategoriaModel($datos);

            if ($respuesta === "ok") {
                echo '<script>
                    window.location = "index.php?action=categorias&status=updated";
                </script>';
                exit();
            }
        }
    }

    /*=============================================
    ELIMINAR CATEGORÍA
    =============================================*/
    public static function eliminarCategoriaController() {
        if (isset($_GET["idEliminarCategoria"])) {

            $id = $_GET["idEliminarCategoria"];

            $respuesta = Categoria::eliminarCategoriaModel($id);

            if ($respuesta === "ok") {
                echo '<script>
                    window.location = "index.php?action=categorias&status=deleted";
                </script>';
                exit();
            }
        }
    }
}