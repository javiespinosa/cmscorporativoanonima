<?php
session_start();

// Conexión a Sativa (ajusta la ruta si tu archivo está en la raíz)
require_once 'config/database_sativa.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Validar que el producto exista, esté activo y no eliminado en Sativa
    $stmt = $pdo_sativa->prepare("SELECT id FROM producto WHERE id = ? AND Activo = 1 AND Deleted = 0");
    $stmt->execute([$id]);
    
    if ($stmt->fetch()) {
        // Inicializar carrito si no existe
        if (!isset($_SESSION['cotizacion'])) {
            $_SESSION['cotizacion'] = [];
        }

        // Incrementar cantidad o establecer en 1
        if (isset($_SESSION['cotizacion'][$id])) {
            $_SESSION['cotizacion'][$id]++;
        } else {
            $_SESSION['cotizacion'][$id] = 1;
        }
        
        // Redirigir con mensaje de éxito
        header("Location: productos.php?mensaje=agregado");
        exit;
    }
}

// Si el ID no es válido o el producto no existe/está inactivo
header("Location: productos.php?error=invalido");
exit;