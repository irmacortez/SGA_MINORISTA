<?php
// Habilitar la visualización de errores de PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar controladores y modelos necesarios
require_once "controllers/ProductoController.php";
require_once "models/Producto.php";
require_once "config/conexion.php";

// Determinar la acción solicitada por la URL (por defecto 'inventario')
$action = isset($_GET['action']) ? $_GET['action'] : 'inventario';

if ($action === 'inventario') {
    $controller = new ProductoController();
    $controller->guardarProductoController();
    $productos = $controller->listarProductosController();
    
    // Cargar la vista de inventario
    require_once "views/modules/inventario.php";
} else {
    echo "Página no encontrada";
}