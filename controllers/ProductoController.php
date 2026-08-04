<?php

class ProductoController {

    /*=============================================
    LISTAR PRODUCTOS (PARA USAR EN VISTAS / VENTAS)
    =============================================*/
    public static function listarProductosController() {
        return Producto::listarProductosModel();
    }

    public static function mostrarProductosController() {
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

}