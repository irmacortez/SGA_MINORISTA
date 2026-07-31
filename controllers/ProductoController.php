<?php
require_once "models/Producto.php";

class ProductoController {

    public function index() {
        return $this->listarProductosController();
    }

    public function listarProductosController() {
        return ProductoModel::listarProductosModel();
    }

    public function guardarProductoController() {
        // Solo ejecuta si realmente presionaste el botón "Guardar Artículo" del formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST["nuevoNombre"])) {
            
            $datos = array(
                "nombre_producto" => $_POST["nuevoNombre"],
                "id_categoria"       => $_POST["nuevaCategoria"],
                "id_proveedor"       => $_POST["nuevoProveedor"],
                "precio_venta"    => $_POST["nuevoPrecioVenta"],
                "stock_actual"    => $_POST["nuevoStockActual"],
                "stock_minimo"    => $_POST["nuevoStockMinimo"]
            );

            $respuesta = ProductoModel::guardarProductoModel($datos);

            if ($respuesta === "ok") {
                header("Location: index.php?action=inventario&status=success");
                exit();
            }
        }
    }
}