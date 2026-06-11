<?php

require_once '../../includes/auth.php';
require_once '../../config/database.php';

if(isset($_POST['guardar']))
{
    $titulo = $_POST['titulo'];
    $subtitulo = $_POST['subtitulo'];
    $enlace = $_POST['enlace'];

    $imagen='';

    if(!empty($_FILES['imagen']['name']))
    {
        $archivo=time().'_'.$_FILES['imagen']['name'];

        move_uploaded_file(
            $_FILES['imagen']['tmp_name'],
            '../../uploads/banners/'.$archivo
        );

        $imagen=$archivo;
    }

    $stmt=$pdo->prepare(
    "INSERT INTO banners
    (
        titulo,
        subtitulo,
        imagen,
        enlace
    )
    VALUES
    (
        ?,?,?,?
    )");

    $stmt->execute([
        $titulo,
        $subtitulo,
        $imagen,
        $enlace
    ]);
}

$banners=$pdo->query(
"SELECT * FROM banners
ORDER BY orden_banner,id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<div class="content-wrapper">

<section class="content p-3">

<div class="row">

<div class="col-md-4">

<div class="card">

<div class="card-header bg-success">

Nuevo Banner

</div>

<div class="card-body">

<form method="post" enctype="multipart/form-data">

<div class="mb-3">
<label>Título</label>
<input type="text"
name="titulo"
class="form-control">
</div>

<div class="mb-3">
<label>Subtítulo</label>
<input type="text"
name="subtitulo"
class="form-control">
</div>

<div class="mb-3">
<label>Enlace</label>
<input type="text"
name="enlace"
class="form-control">
</div>

<div class="mb-3">
<label>Imagen</label>
<input type="file"
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

Listado

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>
<th>Imagen</th>
<th>Título</th>
</tr>

<?php foreach($banners as $banner): ?>

<tr>

<td>

<?php if($banner['imagen']): ?>

<img
src="<?= BASE_URL ?>uploads/banners/<?= $banner['imagen'] ?>"
width="150">

<?php endif; ?>

</td>

<td>

<?= htmlspecialchars($banner['titulo']) ?>

</td>

</tr>

<?php endforeach; ?>

</table>

</div>

</div>

</div>

</div>

</section>

</div>

<?php include '../../includes/footer.php'; ?>