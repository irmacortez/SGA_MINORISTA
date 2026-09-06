<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración y Conexión
require_once "config/conexion.php";

// Incluir Modelos
require_once "models/Venta.php";
if (file_exists("models/Producto.php")) {
    require_once "models/Producto.php";
} elseif (file_exists("models/productos.modelo.php")) {
    require_once "models/productos.modelo.php";
}

// Incluir Controladores
require_once "controllers/VentaController.php";
if (file_exists("controllers/ProductoController.php")) {
    require_once "controllers/ProductoController.php";
} elseif (file_exists("controllers/productos.controlador.php")) {
    require_once "controllers/productos.controlador.php";
}

// Enrutador
$action = $_GET["action"] ?? "ver-ventas";

if ($action == "ver-ventas") {
    include "views/modules/ver-ventas.php";
} elseif ($action == "crear-venta") {
    include "views/modules/crear-venta.php";
} elseif ($action == "imprimir-factura") {
    include "views/modules/imprimir-factura.php";
} else {
    include "views/modules/ver-ventas.php";
}