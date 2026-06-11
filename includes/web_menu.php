<?php

if(session_status() === PHP_SESSION_NONE)
{
    session_start();
}

$cantidadCotizacion = 0;

if(isset($_SESSION['cotizacion']))
{
    $cantidadCotizacion = array_sum($_SESSION['cotizacion']);
}

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success">

<div class="container">

<a class="navbar-brand" href="index.php">

<?= htmlspecialchars($config['empresa']) ?>

</a>

<button
class="navbar-toggler"
data-bs-toggle="collapse"
data-bs-target="#menu">

<span class="navbar-toggler-icon"></span>

</button>

<div class="collapse navbar-collapse" id="menu">

<ul class="navbar-nav ms-auto">

<li class="nav-item">
<a class="nav-link" href="index.php">Inicio</a>
</li>

<li class="nav-item">
<a class="nav-link" href="productos.php">Productos</a>
</li>

<li class="nav-item">
<a class="nav-link" href="contacto.php">Contacto</a>
</li>

<li class="nav-item">

<a class="nav-link" href="cotizacion.php">

Cotización

<?php if($cantidadCotizacion > 0): ?>

<span class="badge bg-warning text-dark">

<?= $cantidadCotizacion ?>

</span>

<?php endif; ?>

</a>

</li>

</ul>

</div>

</div>

</nav>