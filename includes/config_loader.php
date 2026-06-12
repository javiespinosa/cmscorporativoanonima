<?php
// Cargar configuración global (con caché para no hacer múltiples queries)
if (!isset($config)) {
    require_once __DIR__ . '/../config/database.php';
    $config = $pdo->query("SELECT * FROM configuracion WHERE id=1")->fetch(PDO::FETCH_ASSOC);
    
    // Si no existe configuración, crear valores por defecto
    if (!$config) {
        $pdo->exec("INSERT INTO configuracion (empresa) VALUES ('Mi Empresa')");
        $config = $pdo->query("SELECT * FROM configuracion WHERE id=1")->fetch(PDO::FETCH_ASSOC);
    }
}
?>