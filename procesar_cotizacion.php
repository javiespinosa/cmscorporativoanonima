<?php
session_start();
require_once 'config/database.php';
require_once 'config/database_sativa.php';

// 1. Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cotizacion.php");
    exit;
}

// 2. Verificar carrito
if (empty($_SESSION['cotizacion']) || !is_array($_SESSION['cotizacion'])) {
    header("Location: productos.php?error=carrito_vacio");
    exit;
}

// 3. Sanitizar datos del formulario
$nombre      = trim($_POST['nombre'] ?? '');
$empresa     = trim($_POST['empresa'] ?? '');
$telefono    = trim($_POST['telefono'] ?? '');
$correo      = filter_var(trim($_POST['correo'] ?? ''), FILTER_SANITIZE_EMAIL);
$comentarios = trim($_POST['comentarios'] ?? '');

// Validaciones
if (empty($nombre) || empty($telefono) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    header("Location: cotizacion.php?error=datos_incompletos");
    exit;
}

// 4. Validar productos en Sativa
$cart_ids = array_keys($_SESSION['cotizacion']);
$placeholders = implode(',', array_fill(0, count($cart_ids), '?'));

$stmtValidacion = $pdo_sativa->prepare("
    SELECT id FROM producto 
    WHERE id IN ($placeholders) AND Activo = 1 AND Deleted = 0
");
$stmtValidacion->execute($cart_ids);
$productos_validos = $stmtValidacion->fetchAll(PDO::FETCH_COLUMN);

if (empty($productos_validos)) {
    unset($_SESSION['cotizacion']);
    header("Location: productos.php?error=productos_no_disponibles");
    exit;
}

// 5. Guardar en BD (Transacción segura)
try {
    $pdo->beginTransaction();

    // Insertar cabecera de cotización
    $stmtCotizacion = $pdo->prepare("
        INSERT INTO cotizaciones (nombre, empresa, telefono, correo, comentarios, estatus) 
        VALUES (?, ?, ?, ?, ?, 'NUEVA')
    ");
    $stmtCotizacion->execute([$nombre, $empresa, $telefono, $correo, $comentarios]);
    
    $id_cotizacion = $pdo->lastInsertId();

    // Insertar detalles
    $stmtDetalle = $pdo->prepare("
        INSERT INTO cotizacion_detalle (cotizacion_id, producto_id, cantidad) 
        VALUES (?, ?, ?)
    ");

    foreach ($productos_validos as $id_producto) {
        $cantidad = (int)$_SESSION['cotizacion'][$id_producto];
        if ($cantidad > 0) {
            $stmtDetalle->execute([$id_cotizacion, $id_producto, $cantidad]);
        }
    }

    $pdo->commit();
    unset($_SESSION['cotizacion']);
    
    header("Location: index.php?mensaje=cotizacion_enviada");
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Error al guardar cotización: " . $e->getMessage());
    header("Location: cotizacion.php?error=error_servidor");
    exit;
}