<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpiamos la sesión
session_unset();
session_destroy();

// Redirigimos al inicio / login
echo '<script>
    window.location = "index.php";
</script>';
exit();