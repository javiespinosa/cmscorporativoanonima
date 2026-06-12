<?php
require_once '../../includes/auth.php';
require_once '../../config/database_sativa.php'; // ← Conexión a Sativa

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Procesar cambio de estatus
if (isset($_POST['guardar_estatus'])) {
    $stmt = $pdo->prepare("UPDATE cotizaciones SET estatus = ? WHERE id = ?");
    $stmt->execute([$_POST['estatus'], $id]);
    
    header("Location: ver.php?id=" . $id . "&msg=estatus_actualizado");
    exit;
}

// 1. Obtener cotización desde CMS
$stmt = $pdo->prepare("SELECT * FROM cotizaciones WHERE id = ?");
$stmt->execute([$id]);
$cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cotizacion) {
    die('Cotización no encontrada');
}

// 2. Obtener detalles (IDs y cantidades) desde CMS
$stmtDetalles = $pdo->prepare("SELECT producto_id, cantidad FROM cotizacion_detalle WHERE cotizacion_id = ?");
$stmtDetalles->execute([$id]);
$detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

// 3. Obtener productos desde SATIVA
$productos = [];
if (!empty($detalles)) {
    $ids = array_column($detalles, 'producto_id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $stmtProd = $pdo_sativa->prepare("
        SELECT 
            p.id,
            p.Codigo,
            p.Descripcion,
            p.Precio1,
            pi.Path1 as imagen
        FROM producto p
        LEFT JOIN producto_imagenes pi ON p.id = pi.idProducto AND pi.Deleted = 0
        WHERE p.id IN ($placeholders)
    ");
    $stmtProd->execute($ids);
    $productosSat = $stmtProd->fetchAll(PDO::FETCH_ASSOC);
    
    // Combinar: agregar la cantidad del detalle a cada producto
    $cantidades = array_column($detalles, 'cantidad', 'producto_id');
    foreach ($productosSat as $prod) {
        $prod['cantidad'] = $cantidades[$prod['id']] ?? 1;
        $productos[] = $prod;
    }
}

// Calcular totales
$subtotal = 0;
foreach ($productos as $p) {
    $subtotal += ($p['Precio1'] * $p['cantidad']);
}
$iva = $subtotal * 0.16;
$total = $subtotal + $iva;

// Función para construir URL de imagen
function getUrlImagen($path) {
    if (empty($path)) return '';
    if (strpos($path, 'http') === 0 || strpos($path, '//') === 0) return $path;
    return (defined('BASE_URL') ? BASE_URL : '/') . ltrim($path, '/');
}

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Cotización #<?= $cotizacion['id'] ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Cotizaciones</a></li>
                        <li class="breadcrumb-item active">#<?= $cotizacion['id'] ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'estatus_actualizado'): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-check-circle"></i> El estatus se actualizó correctamente.
                </div>
            <?php endif; ?>

            <!-- BOTONES DE ACCIÓN -->
            <div class="mb-3">
                <a href="pdf.php?id=<?= $cotizacion['id'] ?>" target="_blank" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Generar PDF
                </a>
                
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                
                <a target="_blank" 
                   href="https://wa.me/52<?= preg_replace('/[^0-9]/', '', $cotizacion['telefono']) ?>" 
                   class="btn btn-success">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                
                <a href="index.php" class="btn btn-default float-right">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="row">
                <!-- COLUMNA IZQUIERDA: Cliente y Estatus -->
                <div class="col-md-4">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user mr-1"></i> Datos del Cliente</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Nombre:</b> <span class="float-right"><?= htmlspecialchars($cotizacion['nombre']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Empresa:</b> <span class="float-right"><?= htmlspecialchars($cotizacion['empresa'] ?: 'N/A') ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Teléfono:</b> <span class="float-right"><?= htmlspecialchars($cotizacion['telefono']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Correo:</b> <span class="float-right text-break"><?= htmlspecialchars($cotizacion['correo']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Fecha:</b> <span class="float-right"><?= date('d/m/Y H:i', strtotime($cotizacion['fecha_registro'])) ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-cogs mr-1"></i> Estatus</h3>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="form-group">
                                    <label>Estatus actual:</label>
                                    <select name="estatus" class="form-control">
                                        <option value="NUEVA" <?= $cotizacion['estatus'] == 'NUEVA' ? 'selected' : '' ?>>NUEVA</option>
                                        <option value="EN_PROCESO" <?= $cotizacion['estatus'] == 'EN_PROCESO' ? 'selected' : '' ?>>EN PROCESO</option>
                                        <option value="ATENDIDA" <?= $cotizacion['estatus'] == 'ATENDIDA' ? 'selected' : '' ?>>ATENDIDA</option>
                                    </select>
                                </div>
                                <button name="guardar_estatus" class="btn btn-warning btn-block">
                                    <i class="fas fa-save"></i> Guardar Estatus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- COLUMNA DERECHA: Productos y Comentarios -->
                <div class="col-md-8">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-shopping-cart mr-1"></i> Productos Solicitados</h3>
                            <div class="card-tools">
                                <span class="badge badge-light"><?= count($productos) ?> productos</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 80px;">Imagen</th>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th class="text-right" style="width: 100px;">P. Unit.</th>
                                            <th class="text-center" style="width: 80px;">Cant.</th>
                                            <th class="text-right" style="width: 110px;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($productos)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">No hay productos en esta cotización.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($productos as $p): 
                                                $imgUrl = getUrlImagen($p['imagen']);
                                                $subtotal_linea = $p['Precio1'] * $p['cantidad'];
                                            ?>
                                                <tr>
                                                    <td class="text-center align-middle">
                                                        <?php if (!empty($imgUrl)): ?>
                                                            <img src="<?= htmlspecialchars($imgUrl) ?>" 
                                                                 class="img-thumbnail" 
                                                                 style="width: 60px; height: 60px; object-fit: contain;"
                                                                 alt="Producto"
                                                                 onerror="this.src='https://placehold.co/60x60?text=Sin+Img'">
                                                        <?php else: ?>
                                                            <div class="img-thumbnail bg-light d-flex align-items-center justify-content-center" 
                                                                 style="width: 60px; height: 60px;">
                                                                <i class="fas fa-image text-muted"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <strong><?= htmlspecialchars($p['Codigo']) ?></strong>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?= htmlspecialchars($p['Descripcion']) ?>
                                                    </td>
                                                    <td class="text-right align-middle">
                                                        $<?= number_format($p['Precio1'], 2) ?>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span class="badge badge-primary badge-pill">
                                                            <?= $p['cantidad'] ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-right align-middle font-weight-bold">
                                                        $<?= number_format($subtotal_linea, 2) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            
                                            <!-- FILAS DE TOTALES -->
                                            <tr class="bg-light">
                                                <td colspan="5" class="text-right font-weight-bold">SUBTOTAL:</td>
                                                <td class="text-right">$<?= number_format($subtotal, 2) ?></td>
                                            </tr>
                                            <tr class="bg-light">
                                                <td colspan="5" class="text-right font-weight-bold">IVA (16%):</td>
                                                <td class="text-right">$<?= number_format($iva, 2) ?></td>
                                            </tr>
                                            <tr class="bg-success text-white">
                                                <td colspan="5" class="text-right font-weight-bold h5 mb-0">TOTAL:</td>
                                                <td class="text-right h5 mb-0">$<?= number_format($total, 2) ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-comment mr-1"></i> Comentarios del Cliente</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($cotizacion['comentarios'])): ?>
                                <div class="bg-light p-3 rounded">
                                    <?= nl2br(htmlspecialchars($cotizacion['comentarios'])) ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0"><em>El cliente no dejó comentarios.</em></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php include '../../includes/footer.php'; ?>