<?php

require_once '../../includes/auth.php';
require_once '../../config/database.php';

$id = (int)($_GET['id'] ?? 0);

if(isset($_POST['guardar_estatus']))
{
    $stmt = $pdo->prepare(
    "UPDATE cotizaciones
     SET estatus=?
     WHERE id=?");

    $stmt->execute([
        $_POST['estatus'],
        $id
    ]);
}

$stmt = $pdo->prepare(
"SELECT *
 FROM cotizaciones
 WHERE id=?");

$stmt->execute([$id]);

$cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$cotizacion)
{
    die('Cotización no encontrada');
}

$stmt = $pdo->prepare(
"SELECT
d.cantidad,
p.nombre,
p.descripcion_corta,
p.imagen
FROM cotizacion_detalle d
INNER JOIN productos p
ON p.id=d.producto_id
WHERE d.cotizacion_id=?");

$stmt->execute([$id]);

$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';

?>

<div class="content-wrapper">

<section class="content-header">

<div class="container-fluid">

<h1>

Cotización #<?= $cotizacion['id'] ?>

</h1>

<a
href="pdf.php?id=<?= $cotizacion['id'] ?>"
target="_blank"
class="btn btn-danger mb-3">
<i class="fas fa-file-pdf"></i>
Generar PDF
</a>
<a
<button
onclick="window.print()"
class="btn btn-secondary">

<i class="fas fa-print"></i>

Imprimir

</button>
</a>
<a
target="_blank"
href="https://wa.me/52<?= preg_replace('/[^0-9]/','',$cotizacion['telefono']) ?>"
class="btn btn-success">

<i class="fab fa-whatsapp"></i>

WhatsApp

</a>

</div>

</section>

<section class="content">

<div class="container-fluid">

<div class="row">

<div class="col-md-4">

<div class="card card-success">

<div class="card-header">

<h3 class="card-title">

Datos del Cliente

</h3>

</div>

<div class="card-body">

<p>
<strong>Nombre:</strong><br>
<?= htmlspecialchars($cotizacion['nombre']) ?>
</p>

<p>
<strong>Empresa:</strong><br>
<?= htmlspecialchars($cotizacion['empresa']) ?>
</p>

<p>
<strong>Teléfono:</strong><br>
<?= htmlspecialchars($cotizacion['telefono']) ?>
</p>

<p>
<strong>Correo:</strong><br>
<?= htmlspecialchars($cotizacion['correo']) ?>
</p>

<p>
<strong>Fecha:</strong><br>
<?= date('d/m/Y H:i', strtotime($cotizacion['fecha_registro'])) ?>
</p>

</div>

</div>

<div class="card">

<div class="card-header">

Estatus

</div>

<div class="card-body">

<form method="post">

<select
name="estatus"
class="form-control mb-3">

<option value="NUEVA"
<?= $cotizacion['estatus']=='NUEVA'?'selected':'' ?>>
NUEVA
</option>

<option value="EN_PROCESO"
<?= $cotizacion['estatus']=='EN_PROCESO'?'selected':'' ?>>
EN PROCESO
</option>

<option value="ATENDIDA"
<?= $cotizacion['estatus']=='ATENDIDA'?'selected':'' ?>>
ATENDIDA
</option>

</select>

<button
name="guardar_estatus"
class="btn btn-success btn-block">

Guardar Estatus

</button>

</form>

</div>

</div>

</div>

<div class="col-md-8">

<div class="card">

<div class="card-header bg-success">

<h3 class="card-title">

Productos Solicitados

</h3>

</div>

<div class="card-body">

<table class="table table-bordered table-hover">

<thead>

<tr>
<th>Imagen</th>
<th>Producto</th>
<th>Cantidad</th>
</tr>

</thead>

<tbody>

<?php foreach($productos as $p): ?>

<tr>

<td width="120">

<?php if($p['imagen']): ?>

<img
src="<?= BASE_URL ?>uploads/productos/<?= $p['imagen'] ?>"
class="img-fluid rounded">

<?php endif; ?>

</td>

<td>

<strong>

<?= htmlspecialchars($p['nombre']) ?>

</strong>

<br>

<?= htmlspecialchars($p['descripcion_corta']) ?>

</td>

<td>

<?= $p['cantidad'] ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

<div class="card">

<div class="card-header">

Comentarios del Cliente

</div>

<div class="card-body">

<?= nl2br(htmlspecialchars($cotizacion['comentarios'])) ?>

</div>

</div>

</div>

</div>

</div>

</section>

</div>

<?php include '../../includes/footer.php'; ?>