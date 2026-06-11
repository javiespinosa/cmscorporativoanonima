<?php

require_once 'config/database.php';

$config = $pdo->query(
"SELECT * FROM configuracion WHERE id=1"
)->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>
<?= htmlspecialchars($config['empresa']) ?>
</title>

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
}

</style>

</head>

<body>