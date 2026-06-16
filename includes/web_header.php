<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/config_loader.php';

// ============================================
// FUNCIÓN PARA CONVERTIR HEX A RGB
// ============================================
if (!function_exists('hexToRgb')) {
    function hexToRgb($hex) {
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return "$r, $g, $b";
    }
}

// Obtener colores de la configuración
$color_primario = $config['color_primario'] ?? '#28a745';
$color_secundario = $config['color_secundario'] ?? '#20c997';
$color_texto = $config['color_texto'] ?? '#333333';
$color_fondo = $config['color_fondo'] ?? '#ffffff';

$rgb_primario = hexToRgb($color_primario);
$rgb_secundario = hexToRgb($color_secundario);
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
    
    <!-- ============================================ -->
    <!-- ESTILOS DINÁMICOS DEL TEMA -->
    <!-- ============================================ -->
    <style>
        /* 1. VARIABLES GLOBALES DEL TEMA */
        :root {
            --color-primario: <?= $color_primario ?>;
            --color-secundario: <?= $color_secundario ?>;
            --color-texto: <?= $color_texto ?>;
            --color-fondo: <?= $color_fondo ?>;
            
            /* Versiones RGB para sombras y degradados */
            --rgb-primario: <?= $rgb_primario ?>;
            --rgb-secundario: <?= $rgb_secundario ?>;
        }

        /* 2. SOBRESCRITURA GLOBAL DE BOOTSTRAP */
        .bg-success { background-color: var(--color-primario) !important; }
        .text-success { color: var(--color-primario) !important; }
        
        .btn-success { 
            background-color: var(--color-primario) !important; 
            border-color: var(--color-primario) !important; 
            color: white !important;
            transition: all 0.3s ease;
        }
        .btn-success:hover { 
            background-color: var(--color-secundario) !important; 
            border-color: var(--color-secundario) !important; 
        }
        
        .btn-outline-success {
            color: var(--color-primario) !important;
            border-color: var(--color-primario) !important;
        }
        .btn-outline-success:hover {
            background-color: var(--color-primario) !important;
            color: white !important;
        }

        .badge-success { background-color: var(--color-primario) !important; }

        /* 3. ESTILOS BASE DEL SITIO */
        body {
            color: var(--color-texto);
            background-color: var(--color-fondo);
        }
        
        a { color: var(--color-primario); }
        a:hover { color: var(--color-secundario); }

        /* 4. COMPONENTES ESPECÍFICOS (AHORA CON VARIABLES) */
        .hero {
            background: var(--color-primario); /* ← CAMBIADO de #2E7D32 */
            color: white;
            padding: 80px 0;
        }
        
        .producto-card {
            transition: 0.3s;
        }
        .producto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(var(--rgb-primario), 0.2); /* ← Sombra dinámica */
        }
        
        /* El botón de WhatsApp mantiene su color oficial */
        .whatsapp {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 999;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #25D366; /* ← Verde oficial de WhatsApp (NO cambiar) */
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