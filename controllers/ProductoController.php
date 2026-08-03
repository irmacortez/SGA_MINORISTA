<?php
require_once "models/Producto.php";
require_once "models/Categoria.php";
require_once "models/Proveedor.php";  

class ProductoController {

    public function mostrarInventario() {
        // 1. Traemos los productos usando la función del JOIN
        $productos = ProductoModel::listarProductosModel(); 
        
        // 2. Traemos todas las categorías y proveedores para llenar los <select> del modal
        $categorias  = Categoria::listarCategoriasModel();
        $proveedores = Proveedor::listarProveedoresModel(); 
        
        include "views/modules/inventario.php";
    }

    // Guardar nuevo
    public static function guardarProductoController() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["nuevoNombre"])) {
            
            $datos = array(
                "nombre_producto" => $_POST["nuevoNombre"],
                "precio_venta"    => $_POST["nuevoPrecioVenta"],
                "stock_actual"    => $_POST["nuevoStockActual"],
                "stock_minimo"    => $_POST["nuevoStockMinimo"],
                "id_categoria"    => $_POST["nuevoIdCategoria"],
                "id_proveedor"    => $_POST["nuevoIdProveedor"]
            );

            $respuesta = ProductoModel::guardarProductoModel($datos);

            if ($respuesta === "ok") {
                header("Location: index.php?action=inventario&status=success");
                exit();
            }
        }
    }

    // Actualizar existente
    public static function actualizarProductoController() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["editarId"])) {
            
            $datos = array(
                "id_producto"     => $_POST["editarId"],
                "nombre_producto" => $_POST["editarNombre"],
                "precio_venta"    => $_POST["editarPrecioVenta"],
                "stock_actual"    => $_POST["editarStockActual"],
                "stock_minimo"    => $_POST["editarStockMinimo"],
                "id_categoria"    => $_POST["editarIdCategoria"],
                "id_proveedor"    => $_POST["editarIdProveedor"]
            );

            $respuesta = ProductoModel::actualizarProductoModel($datos);

            if ($respuesta === "ok") {
                header("Location: index.php?action=inventario&status=updated");
                exit();
            }
        }
    }

    // Eliminar
    public static function eliminarProductoController() {
        if (isset($_GET["idEliminar"])) {
            $id = $_GET["idEliminar"];
            $respuesta = ProductoModel::eliminarProductoModel($id);

            if ($respuesta === "ok") {
                echo '<script>
                    window.location = "index.php?action=inventario&status=deleted";
                </script>';
                exit();
            }
        }
    }