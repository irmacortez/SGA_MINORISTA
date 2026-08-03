<?php
require_once "models/Producto.php";
require_once "models/Categoria.php";
require_once "models/Proveedor.php";

class ProductoController {

    public function mostrarInventario() {
        // 1. Traemos los productos usando la clase Producto
        $productos = Producto::listarProductosModel(); 
        
        // 2. Traemos todas las categorías y proveedores para llenar los <select> del modal
        $categorias  = Categoria::listarCategoriasModel();
        $proveedores = Proveedor::listarProveedoresModel(); 
        
        include "views/modules/inventario.php";
    }

    // Listar productos
    public static function listarProductosController() {
        return Producto::listarProductosModel();
    }

    // Guardar nuevo producto
    public static function guardarProductoController() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["nuevoNombreProducto"])) {
            
            $datos = array(
                "nombre_producto" => trim($_POST["nuevoNombreProducto"]),
                "precio_venta"    => trim($_POST["nuevoPrecioVenta"]),
                "stock_actual"    => trim($_POST["nuevoStockActual"]),
                "stock_minimo"    => trim($_POST["nuevoStockMinimo"]),
                "id_categoria"    => $_POST["nuevaCategoria"],
                "id_proveedor"    => $_POST["nuevoProveedor"]
            );

            $respuesta = Producto::guardarProductoModel($datos);

            if ($respuesta === "ok") {
                echo '<script>window.location = "index.php?action=inventario&status=success";</script>';
                exit();
            }
        }
    }

    // Actualizar producto existente
    public static function actualizarProductoController() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["editarIdProducto"])) {
            
            $datos = array(
                "id_producto"     => $_POST["editarIdProducto"],
                "nombre_producto" => trim($_POST["editarNombreProducto"]),
                "precio_venta"    => trim($_POST["editarPrecioVenta"]),
                "stock_actual"    => trim($_POST["editarStockActual"]),
                "stock_minimo"    => trim($_POST["editarStockMinimo"]),
                "id_categoria"    => $_POST["editarCategoria"],
                "id_proveedor"    => $_POST["editarProveedor"]
            );

            $respuesta = Producto::actualizarProductoModel($datos);

            if ($respuesta === "ok") {
                echo '<script>window.location = "index.php?action=inventario&status=updated";</script>';
                exit();
            }
        }
    }

    // Eliminar producto
    public static function eliminarProductoController() {
        if (isset($_GET["idEliminarProducto"])) {
            $id = $_GET["idEliminarProducto"];
            $respuesta = Producto::eliminarProductoModel($id);

            if ($respuesta === "ok") {
                echo '<script>window.location = "index.php?action=inventario&status=deleted";</script>';
                exit();
            }
        }
    }
}