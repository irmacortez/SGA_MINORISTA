<?php

class ProveedorController {

    public static function listarProveedoresController() {
        return Proveedor::listarProveedoresModel();
    }

    public static function guardarProveedorController() {
        if (isset($_POST["nuevoNombreProveedor"])) {
            $datos = array(
                "nombre_proveedor" => trim($_POST["nuevoNombreProveedor"]),
                "telefono"         => trim($_POST["nuevoTelefono"]),
                "email"            => trim($_POST["nuevoEmail"])
            );
            $respuesta = Proveedor::guardarProveedorModel($datos);

            if ($respuesta === "ok") {
                echo '<script>window.location = "index.php?action=proveedores&status=success";</script>';
                exit();
            }
        }
    }

    public static function actualizarProveedorController() {
        if (isset($_POST["editarIdProveedor"]) && isset($_POST["editarNombreProveedor"])) {
            $datos = array(
                "id_proveedor"     => $_POST["editarIdProveedor"],
                "nombre_proveedor" => trim($_POST["editarNombreProveedor"]),
                "telefono"         => trim($_POST["editarTelefono"]),
                "email"            => trim($_POST["editarEmail"])
            );
            $respuesta = Proveedor::actualizarProveedorModel($datos);

            if ($respuesta === "ok") {
                echo '<script>window.location = "index.php?action=proveedores&status=updated";</script>';
                exit();
            }
        }
    }

    public static function eliminarProveedorController() {
        if (isset($_GET["idEliminarProveedor"])) {
            $id = $_GET["idEliminarProveedor"];
            $respuesta = Proveedor::eliminarProveedorModel($id);

            if ($respuesta === "ok") {
                echo '<script>window.location = "index.php?action=proveedores&status=deleted";</script>';
                exit();
            }
        }
    }

    
}