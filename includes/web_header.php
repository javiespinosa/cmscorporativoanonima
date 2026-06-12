<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/config_loader.php'; // ← Cargar configuración
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title><?= htmlspecialchars($config['empresa']) ?> - <?= htmlspecialchars($config['slogan'] ?? '') ?></title>
    <meta name="description" content="<?= htmlspecialchars($config['quienes_somos'] ?? '') ?>">
    
    <!-- Open Graph para redes sociales -->
    <meta property="og:title" content="<?= htmlspecialchars($config['empresa']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($config['slogan'] ?? '') ?>">
    <meta property="og:type" content="website">
    <?php if (!empty($config['logo'])): ?>
        <meta property="og:image" content="<?= BASE_URL . htmlspecialchars($config['logo']) ?>">
    <?php endif; ?>
    
    <!-- Favicon -->
    <?php if (!empty($config['favicon'])): ?>
        <link rel="icon" type="image/x-icon" href="<?= BASE_URL . htmlspecialchars($config['favicon']) ?>">
    <?php endif; ?>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    
    <style>
        .hero{
            background:#2E7D32;
            color:white;
            padding:80px 0;
        }
        .producto-card{
            transition:0.3s;
        }
        .producto-card:hover{
            transform:translateY(-5px);
        }
        .whatsapp{
            position:fixed;
            right:20px;
            bottom:20px;
            z-index:999;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #25D366;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        .whatsapp:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 18px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>