<?php
$host_sativa = "173.201.253.34";
$dbname_sativa = "sativa";
$user_sativa = "javimtz";
$pass_sativa = '_J_Martinez$_9008';

try {
    $pdo_sativa = new PDO(
        "mysql:host=$host_sativa;dbname=$dbname_sativa;charset=utf8mb4",
        $user_sativa,
        $pass_sativa
    );
    $pdo_sativa->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo_sativa->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error de conexión a Sativa: " . $e->getMessage());
}
?>