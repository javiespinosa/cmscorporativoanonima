<?php

require_once '../../includes/auth.php';
require_once '../../config/database.php';

$cotizaciones = $pdo->query("
SELECT *
FROM cotizaciones
ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<div class="content-wrapper">

<section class="content-header">

<div class="container-fluid">

<h1>Cotizaciones</h1>

</div>

</section>

<section class="content">

<div class="container-fluid">

<div class="card">

<div class="card-header bg-success">

<h3 class="card-title">

Solicitudes Recibidas

</h3>

</div>

<div class="card-body">

<table id="tablaCotizaciones"
class="table table-bordered table-striped">

<thead>

<tr>

<th>Folio</th>
<th>Fecha</th>
<th>Cliente</th>
<th>Empresa</th>
<th>Teléfono</th>
<th>Estatus</th>
<th>Acción</th>

</tr>

</thead>

<tbody>

<?php foreach($cotizaciones as $c): ?>

<tr>

<td>#<?= $c['id'] ?></td>

<td>

<?= date('d/m/Y', strtotime($c['fecha_registro'])) ?>

</td>

<td><?= htmlspecialchars($c['nombre']) ?></td>

<td><?= htmlspecialchars($c['empresa']) ?></td>

<td><?= htmlspecialchars($c['telefono']) ?></td>

<td>

<span class="badge badge-success">

<?= $c['estatus'] ?>

</span>

</td>

<td>

<a
href="ver.php?id=<?= $c['id'] ?>"
class="btn btn-primary btn-sm">

Ver Detalle

</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div>

</section>

</div>

<script>

$(function(){

$('#tablaCotizaciones').DataTable({

responsive:true,
autoWidth:false,
language:{
url:'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
}

});

});

</script>

<?php include '../../includes/footer.php'; ?>