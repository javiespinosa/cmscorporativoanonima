<?php

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once __DIR__ . '/../config/config.php';

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>CMS Corporativo</title>

<link rel="stylesheet"
href="<?= BASE_URL ?>assets/adminlte/plugins/fontawesome-free/css/all.min.css">

<link rel="stylesheet"
href="<?= BASE_URL ?>assets/adminlte/dist/css/adminlte.min.css">

</head>

<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">