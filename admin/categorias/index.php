<?php

require_once '../../includes/auth.php';
require_once '../../config/database.php';

if(isset($_POST['guardar']))
{
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    $stmt = $pdo->prepare(
        "INSERT INTO categorias(nombre,descripcion)
         VALUES(?,?)"
    );

    $stmt->execute([
        $nombre,
        $descripcion
    ]);
}

$categorias = $pdo->query(
    "SELECT * FROM categorias
     ORDER BY nombre"
)->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';

?>

<div class="content-wrapper">

<section class="content-header">

<div class="container-fluid">

<h1>Categorías</h1>

</div>

</section>

<section class="content">

<div class="container-fluid">

<div class="row">

<div class="col-md-4">

<div class="card">

<div class="card-header bg-success">

<h3 class="card-title">
Nueva Categoría
</h3>

</div>

<div class="card-body">

<form method="post">

<div class="mb-3">

<label>Nombre</label>

<input
type="text"
name="nombre"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Descripción</label>

<textarea
name="descripcion"
class="form-control">
</textarea>

</div>

<button
name="guardar"
class="btn btn-success">

Guardar

</button>

</form>

</div>

</div>

</div>

<div class="col-md-8">

<div class="card">

<div class="card-header">

<h3 class="card-title">
Listado
</h3>

</div>

<div class="card-body">

<table class="table table-bordered">

<thead>

<tr>
<th>ID</th>
<th>Nombre</th>
<th>Descripción</th>
</tr>

</thead>

<tbody>

<?php foreach($categorias as $cat): ?>

<tr>

<td><?= $cat['id'] ?></td>

<td><?= htmlspecialchars($cat['nombre']) ?></td>

<td><?= htmlspecialchars($cat['descripcion']) ?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

</section>

</div>

<?php include '../../includes/footer.php'; ?>