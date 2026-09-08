<?php

require_once __DIR__ . "/../models/Producto.php";
require_once __DIR__ . "/../models/Categoria.php";
require_once __DIR__ . "/../models/Proveedor.php";

class ProductoController {

    /*=============================================
    LISTAR PRODUCTOS
    =============================================*/
    public static function listarProductosController() {
        return Producto::listarProductosModel();
    }

    /*=============================================
    MOSTRAR INVENTARIO
    =============================================*/
    public static function mostrarInventario() {
        $productos   = Producto::listarProductosModel(); 
        $categorias  = Categoria::listarCategoriasModel();
        $proveedores = Proveedor::listarProveedoresModel(); 
        
        return [
            "productos"   => $productos,
            "categorias"  => $categorias,
            "proveedores" => $proveedores
        ];
    }

    /*=============================================
    GUARDAR O CREAR PRODUCTO
    =============================================*/
    public static function guardarProductoController() {
        if (isset($_POST["nombre_producto"])) {

            $datos = [
                "codigo_barras"   => $_POST["codigo_barras"] ?? "",
                "nombre_producto" => $_POST["nombre_producto"],
                "precio_venta"    => $_POST["precio_venta"] ?? $_POST["precio_unitario"] ?? 0,
                "stock_actual"    => $_POST["stock_actual"] ?? 0
            ];

            $respuesta = Producto::guardarProductoModel($datos);

            if ($respuesta == "ok") {
                echo '<script>
                    alert("¡Producto guardado correctamente!");
                    window.location = "index.php?action=inventario";
                </script>';
                exit();
            } else {
                echo '<script>
                    alert("Error al guardar el producto en la base de datos.");
                </script>';
            }
        }
    }
}