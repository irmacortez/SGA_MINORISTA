<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema SGA Minorista</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="views/assets/css/estilos.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <?php if (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok"): ?>

        <!-- ENCABEZADO Y MENÚ DE NAVEGACIÓN -->
        <?php include "views/includes/header.php"; ?>

        <!-- CONTENIDO DINÁMICO DEL MÓDULO SELECCIONADO -->
        <main class="flex-grow-1 py-4">
            <?php Enrutador::cargarPagina(); ?>
        </main>

        <!-- PIE DE PÁGINA -->
        <?php include "views/includes/footer.php"; ?>
         </div> <!-- Cierre de .wrapper -->

         <!-- 1. jQuery PRIMERO -->
             <script src="views/assets/js/jquery.min.js"></script>
             <!-- 2. Bootstrap JS SEGUNDO -->
             <script src="views/assets/js/bootstrap.min.js"></script>
             <!-- 3. AdminLTE JS TERCERO -->
             <script src="views/assets/js/adminlte.min.js"></script>

    <?php else: ?>

        <!-- FORMULARIO DE LOGIN SI NO HAY SESIÓN ACTIVA -->
        <main class="flex-grow-1 d-flex align-items-center justify-content-center">
            <?php include "views/modules/login.php"; ?>
        </main>

    <?php endif; ?>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Scripts JS personalizados -->
    <script src="views/assets/js/main.js"></script>
</body>
</html>