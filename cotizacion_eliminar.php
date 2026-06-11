<?php

session_start();

$id = (int)$_GET['id'];

if(isset($_SESSION['cotizacion'][$id]))
{
    unset($_SESSION['cotizacion'][$id]);
}

header("Location: cotizacion.php");
exit;