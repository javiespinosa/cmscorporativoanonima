<?php
session_start();

// 1. Validar que el ID exista y sea un número mayor a 0
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: cotizacion.php?error=invalido");
    exit;
}

// 2. Eliminar el producto del carrito si existe
if (isset($_SESSION['cotizacion'][$id])) {
    unset($_SESSION['cotizacion'][$id]);
    
    // 3. Limpieza: Si el carrito queda vacío, eliminamos la variable de sesión
    if (empty($_SESSION['cotizacion'])) {
        unset($_SESSION['cotizacion']);
    }
}

// 4. Redirigir a la página de cotización con mensaje de éxito
header("Location: cotizacion.php?mensaje=eliminado");
exit;
?>