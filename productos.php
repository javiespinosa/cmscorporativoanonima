<?php

include 'includes/web_header.php';
include 'includes/web_menu.php';

$productos = $pdo->query(
"SELECT * FROM productos
 ORDER BY nombre"
)->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container py-5">

<h1>Productos</h1>

<div class="row">

<?php foreach($productos as $p): ?>

<div class="col-md-3 mb-4">

<div class="card h-100">

<?php if($p['imagen']): ?>

<img
src="uploads/productos/<?= $p['imagen'] ?>"
class="card-img-top">

<?php endif; ?>

<div class="card-body">

<h5>

<?= htmlspecialchars($p['nombre']) ?>

</h5>

<div class="d-grid gap-2">

<a
href="producto.php?id=<?= $p['id'] ?>"
class="btn btn-outline-success">

Ver detalle

</a>

<a
href="cotizacion_agregar.php?id=<?= $p['id'] ?>"
class="btn btn-success">

<i class="fas fa-cart-plus"></i>
Agregar a Cotización

</a>

</div>

</div>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

<?php include 'includes/web_footer.php'; ?>