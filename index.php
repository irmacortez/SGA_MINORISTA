<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "controllers/ProductoController.php";
require_once "models/Producto.php";
require_once "config/conexion.php";

// Escuchar acciones de guardar, editar o eliminar
ProductoController::guardarProductoController();
ProductoController::actualizarProductoController();
ProductoController::eliminarProductoController();

$action = isset($_GET['action']) ? $_GET['action'] : 'inventario';

if ($action === 'inventario') {

   // Instanciamos el controlador
    $inventario = new ProductoController();

    // Procesamos acciones POST/GET si las hay (guardar, editar, eliminar)
    ProductoController::guardarProductoController();
    ProductoController::actualizarProductoController();
    ProductoController::eliminarProductoController();

    // Cargamos la vista con sus datos
    $inventario->mostrarInventario();

} else {
    echo "<h1>Página no encontrada</h1>";
}