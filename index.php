<?php
// Configuración y Conexión
require_once "config/conexion.php";

// Requerir todos los Modelos
require_once "models/Producto.php";
require_once "models/Categoria.php";
require_once "models/Proveedor.php";
require_once "models/Venta.php";

// Requerir todos los Controladores
require_once "controllers/ProductoController.php";
require_once "controllers/CategoriaController.php";
require_once "controllers/ProveedorController.php";
require_once "controllers/VentaController.php";

// Capturamos la acción desde la URL (por defecto carga "login")
$action = isset($_GET["action"]) ? $_GET["action"] : "login";

// Enrutador principal
switch ($action) {
    case "inventario":
        include "views/modules/inventario.php";
        break;

    case "categorias":
        include "views/modules/categorias.php";
        break;

    case "proveedores":
        include "views/modules/proveedores.php";
        break;

    case "ventas":
        include "views/modules/ventas.php";
        break;

    case "crear-venta":
        include "views/modules/crear-venta.php";
        break;

    case "ver-venta":
        include "views/modules/ver-venta.php";
        break; 

    case "informes":
        include "views/modules/informes.php";
        break;

    case "login":
        include "views/modules/login.php";
        break;

    default:
        include "views/modules/inicio.php";
        break; 
}