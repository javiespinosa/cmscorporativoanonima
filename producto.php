<?php

include 'includes/web_header.php';
include 'includes/web_menu.php';

$id = (int)$_GET['id'];

$stmt = $pdo->prepare(
"SELECT * FROM productos WHERE id=?"
);

$stmt->execute([$id]);

$producto = $stmt->fetch();

?>

<div class="container py-5">

<div class="row">

<div class="col-md-5">

<img
src="uploads/productos/<?= $producto['imagen'] ?>"
class="img-fluid">

</div>

<div class="col-md-7">

<h1>

<?= htmlspecialchars($producto['nombre']) ?>

</h1>

<p>

<?= nl2br($producto['descripcion_larga']) ?>

</p>

</div>

</div>

</div>

<?php include 'includes/web_footer.php'; ?>