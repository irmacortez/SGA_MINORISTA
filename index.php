<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/config/conexion.php';

$controllerName = isset($_GET['c']) ? ucfirst($_GET['c']) . 'Controller' : 'ProductoController';
$action         = isset($_GET['a']) ? $_GET['a'] : 'index';

$controllerPath = __DIR__ . '/controllers/' . $controllerName . '.php';

if (file_exists($controllerPath)) {
    require_once $controllerPath;
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            die("Error 404: El método '{$action}' no existe.");
        }
    } else {
        die("Error 404: La clase '{$controllerName}' no existe.");
    }
} else {
    die("Error 404: El controlador '{$controllerName}' no existe.");
}