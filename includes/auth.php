<?php

//LÍNEAS AL INICIO DE auth.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['usuario_id'])){
    header("Location: ../login.php");
    exit;
}