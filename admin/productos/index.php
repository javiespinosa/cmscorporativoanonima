<?php

require_once '../../includes/auth.php';
require_once '../../config/database.php';

if(isset($_POST['guardar']))
{
    $categoria_id = $_POST['categoria_id'];
    $nombre = trim($_POST['nombre']);
    $descripcion_corta = trim($_POST['descripcion_corta']);
    $descripcion_larga = trim($_POST['descripcion_larga']);

    $imagen = '';

    if(!empty($_FILES['imagen']['name']))
    {
        $nombreArchivo = time().'_'.$_FILES['imagen']['name'];

        move_uploaded_file(
            $_FILES['imagen']['tmp_name'],
            '../../uploads/productos/'.$nombreArchivo
        );

        $imagen = $nombreArchivo;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO productos
        (
            categoria_id,
            nombre,
            descripcion_corta,
            descripcion_larga,
            imagen
        )
        VALUES
        (
            ?,?,?,?,?
        )"
    );

    $stmt->execute([
        $categoria_id,
        $nombre,
        $descripcion_corta,
        $descripcion_larga,
        $imagen
    ]);
}

$categorias = $pdo->query(
    "SELECT * FROM categorias ORDER BY nombre"
)->fetchAll(PDO::FETCH_ASSOC);

$productos = $pdo->query(
    "SELECT p.*, c.nombre categoria
     FROM productos p
     INNER JOIN categorias c
     ON c.id = p.categoria_id
     ORDER BY p.id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<div class="content-wrapper">

<section class="content-header">
<div class="container-fluid">
<h1>Productos</h1>
</div>
</section>

<section class="content">

<div class="container-fluid">

<div class="row">

<div class="col-md-4">

<div class="card">

<div class="card-header bg-success">
<h3 class="card-title">
Nuevo Producto
</h3>
</div>

<div class="card-body">

<form method="post" enctype="multipart/form-data">

<div class="mb-3">

<label>Categoría</label>

<select
name="categoria_id"
class="form-control"
required>

<option value="">
Seleccione
</option>

<?php foreach($categorias as $cat): ?>

<option value="<?= $cat['id'] ?>">
<?= htmlspecialchars($cat['nombre']) ?>
</option>

<?php endforeach; ?>

</select>

</div>

<div class="mb-3">

<label>Nombre</label>

<input
type="text"
name="nombre"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Descripción corta</label>

<textarea
name="descripcion_corta"
class="form-control"></textarea>

</div>

<div class="mb-3">

<label>Descripción larga</label>

<textarea
name="descripcion_larga"
class="form-control"
rows="5"></textarea>

</div>

<div class="mb-3">

<label>Imagen</label>

<input
type="file"
name="imagen"
class="form-control">

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
Listado de Productos
</div>

<div class="card-body">

<table id="tablaProductos" class="table table-bordered table-striped">

<thead>

<tr>
<th>ID</th>
<th>Imagen</th>
<th>Producto</th>
<th>Categoría</th>
</tr>

</thead>

<tbody>

<?php foreach($productos as $producto): ?>

<tr>

<td><?= $producto['id'] ?></td>

<td>

<?php if($producto['imagen']): ?>

<img
src="<?= BASE_URL ?>uploads/productos/<?= $producto['imagen'] ?>"
width="80">

<?php endif; ?>

</td>

<td><?= htmlspecialchars($producto['nombre']) ?></td>

<td><?= htmlspecialchars($producto['categoria']) ?></td>

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
<script>

$(function(){

    $('#tablaProductos').DataTable({
        pageLength:25,
        responsive:true,
        language:{
            url:'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
        }
    });

});

</script>

<?php include '../../includes/footer.php'; ?>