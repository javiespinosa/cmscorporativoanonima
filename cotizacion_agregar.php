<?php

session_start();

$id = (int)$_GET['id'];

if(!isset($_SESSION['cotizacion']))
{
    $_SESSION['cotizacion'] = [];
}

if(isset($_SESSION['cotizacion'][$id]))
{
    $_SESSION['cotizacion'][$id]++;
}
else
{
    $_SESSION['cotizacion'][$id] = 1;
}

header("Location: productos.php");
exit;