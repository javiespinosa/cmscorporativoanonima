<?php
require_once '../includes/auth.php';
require_once '../config/database_sativa.php'; // ← Conexión a Sativa

// ==========================================
// 1. MÉTRICAS DE SATIVA (Catálogo Principal)
// ==========================================
$totalProductosSativa = $pdo_sativa->query("
    SELECT COUNT(*) FROM producto WHERE Deleted = 0 AND Activo = 1
")->fetchColumn() ?: 0;

$productosSinImagen = $pdo_sativa->query("
    SELECT COUNT(DISTINCT p.id) 
    FROM producto p
    LEFT JOIN producto_imagenes pi ON p.id = pi.idProducto AND pi.Deleted = 0
    WHERE p.Deleted = 0 AND p.Activo = 1 AND pi.id IS NULL
")->fetchColumn() ?: 0;

// ==========================================
// 2. MÉTRICAS DEL CMS LOCAL
// ==========================================
$totalProductosPremium = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn() ?: 0;
$totalCategorias       = $pdo->query("SELECT COUNT(*) FROM categorias")->fetchColumn() ?: 0;
$totalCotizaciones     = $pdo->query("SELECT COUNT(*) FROM cotizaciones")->fetchColumn() ?: 0;
$totalBanners          = $pdo->query("SELECT COUNT(*) FROM banners WHERE activo = 1")->fetchColumn() ?: 0;
$totalPromociones      = $pdo->query("
    SELECT COUNT(*) FROM promociones 
    WHERE activo = 1 AND fecha_inicio <= CURDATE() AND fecha_fin >= CURDATE()
")->fetchColumn() ?: 0;

// Mensajes de contacto
try {
    $mensajesNuevos = $pdo->query("SELECT COUNT(*) FROM mensajes_contacto WHERE leido = 0")->fetchColumn() ?: 0;
} catch (Exception $e) {
    $mensajesNuevos = 0;
}

// Cotizaciones por estatus
$cotizacionesNuevas    = $pdo->query("SELECT COUNT(*) FROM cotizaciones WHERE estatus = 'NUEVA'")->fetchColumn() ?: 0;
$cotizacionesProceso   = $pdo->query("SELECT COUNT(*) FROM cotizaciones WHERE estatus = 'EN_PROCESO'")->fetchColumn() ?: 0;
$cotizacionesAtendidas = $pdo->query("SELECT COUNT(*) FROM cotizaciones WHERE estatus = 'ATENDIDA'")->fetchColumn() ?: 0;

// ==========================================
// 3. GRÁFICA MENSUAL (Últimos 6 meses)
// ==========================================
$stmtChart = $pdo->query("
    SELECT DATE_FORMAT(fecha_registro, '%b') as mes, COUNT(*) as total 
    FROM cotizaciones 
    WHERE fecha_registro >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY mes, DATE_FORMAT(fecha_registro, '%Y-%m')
    ORDER BY MIN(fecha_registro) ASC
");
$chartMeses = [];
$chartTotales = [];
foreach ($stmtChart->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $chartMeses[] = $row['mes'];
    $chartTotales[] = (int)$row['total'];
}

// ==========================================
// 4. ÚLTIMAS 10 COTIZACIONES
// ==========================================
$ultimasCotizaciones = $pdo->query("
    SELECT id, nombre, empresa, correo, estatus, fecha_registro 
    FROM cotizaciones 
    ORDER BY fecha_registro DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// ==========================================
// 5. PRODUCTOS RECIENTES DE SATIVA (Top 5)
// ==========================================
$productosRecientes = $pdo_sativa->query("
    SELECT p.id, p.Codigo, p.Descripcion, pi.Path1 as imagen
    FROM producto p
    LEFT JOIN producto_imagenes pi ON p.id = pi.idProducto AND pi.Deleted = 0
    WHERE p.Deleted = 0 AND p.Activo = 1
    ORDER BY p.id DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// ==========================================
// 6. PROMOCIONES ACTIVAS
// ==========================================
$promocionesActivas = $pdo->query("
    SELECT id, titulo, fecha_fin 
    FROM promociones 
    WHERE activo = 1 AND fecha_inicio <= CURDATE() AND fecha_fin >= CURDATE()
    ORDER BY fecha_fin ASC
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
include '../includes/navbar.php';
include '../includes/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-tachometer-alt"></i> Dashboard Ejecutivo</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <!-- 📊 FILA 1: KPIs PRINCIPALES -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= number_format($totalProductosSativa) ?></h3>
                            <p>Productos en Catálogo</p>
                        </div>
                        <div class="icon"><i class="fas fa-box"></i></div>
                        <a href="productos/index.php" class="small-box-footer">
                            Gestionar imágenes <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= number_format($totalProductosPremium) ?></h3>
                            <p>Productos Premium</p>
                        </div>
                        <div class="icon"><i class="fas fa-crown"></i></div>
                        <a href="productospremium/index.php" class="small-box-footer">
                            Ver todos <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= number_format($totalCotizaciones) ?></h3>
                            <p>Total Cotizaciones</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-invoice"></i></div>
                        <a href="cotizaciones/index.php" class="small-box-footer">
                            Ver todas <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= number_format($cotizacionesNuevas) ?></h3>
                            <p>Cotizaciones Nuevas</p>
                        </div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                        <a href="cotizaciones/index.php?estatus=NUEVA" class="small-box-footer">
                            Atender ahora <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 📊 FILA 2: KPIs SECUNDARIOS -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><?= number_format($cotizacionesAtendidas) ?></h3>
                            <p>Cotizaciones Atendidas</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                        <a href="cotizaciones/index.php?estatus=ATENDIDA" class="small-box-footer">
                            Ver historial <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <h3><?= number_format($mensajesNuevos) ?></h3>
                            <p>Mensajes Nuevos</p>
                        </div>
                        <div class="icon"><i class="fas fa-envelope"></i></div>
                        <a href="contacto/mensajes.php" class="small-box-footer">
                            Ver mensajes <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-pink">
                        <div class="inner">
                            <h3><?= number_format($totalPromociones) ?></h3>
                            <p>Promociones Activas</p>
                        </div>
                        <div class="icon"><i class="fas fa-tags"></i></div>
                        <a href="promociones/index.php" class="small-box-footer">
                            Gestionar <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="small-box bg-teal">
                        <div class="inner">
                            <h3><?= number_format($totalBanners) ?></h3>
                            <p>Banners Activos</p>
                        </div>
                        <div class="icon"><i class="fas fa-images"></i></div>
                        <a href="banners/index.php" class="small-box-footer">
                            Ver banners <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 📈 FILA 3: GRÁFICA Y ALERTAS -->
            <div class="row mt-3">
                <!-- Gráfica Mensual -->
                <div class="col-md-8">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line mr-1"></i> Cotizaciones Mensuales (Últimos 6 meses)
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="chartCotizaciones" style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Alertas y Pendientes -->
                <div class="col-md-4">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Atención Requerida
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="products-list product-list-in-card pl-2 pr-2">
                                <?php if ($cotizacionesNuevas > 0): ?>
                                    <li class="item">
                                        <div class="product-img bg-warning text-white d-flex align-items-center justify-content-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                        <div class="product-info">
                                            <a href="cotizaciones/index.php?estatus=NUEVA" class="product-title">
                                                <?= $cotizacionesNuevas ?> cotizacion(es) nueva(s)
                                            </a>
                                            <span class="product-description">Pendientes de atención</span>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($mensajesNuevos > 0): ?>
                                    <li class="item">
                                        <div class="product-img bg-purple text-white d-flex align-items-center justify-content-center">
                                            <i class="fas fa-envelope fa-2x"></i>
                                        </div>
                                        <div class="product-info">
                                            <a href="contacto/mensajes.php" class="product-title">
                                                <?= $mensajesNuevos ?> mensaje(s) de contacto
                                            </a>
                                            <span class="product-description">Sin leer</span>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($productosSinImagen > 0): ?>
                                    <li class="item">
                                        <div class="product-img bg-info text-white d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image fa-2x"></i>
                                        </div>
                                        <div class="product-info">
                                            <a href="productos/index.php" class="product-title">
                                                <?= $productosSinImagen ?> producto(s) sin imagen
                                            </a>
                                            <span class="product-description">Agregar imágenes para mejor presentación</span>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($cotizacionesNuevas == 0 && $mensajesNuevos == 0 && $productosSinImagen == 0): ?>
                                    <li class="item text-center py-4">
                                        <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                                        <p class="text-muted mb-0">¡Todo al día! No hay pendientes.</p>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 📋 FILA 4: PRODUCTOS RECIENTES Y PROMOCIONES -->
            <div class="row mt-3">
                <!-- Productos Recientes de Sativa -->
                <div class="col-md-8">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-box mr-1"></i> Productos Recientes (Sativa)
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">Imagen</th>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($productosRecientes)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">No hay productos registrados.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($productosRecientes as $prod): ?>
                                                <tr>
                                                    <td>
                                                        <?php if (!empty($prod['imagen'])): ?>
                                                            <img src="<?= htmlspecialchars($prod['imagen']) ?>" 
                                                                 alt="Producto" 
                                                                 class="img-size-50 img-thumbnail"
                                                                 onerror="this.src='https://placehold.co/50x50?text=Sin+Img'">
                                                        <?php else: ?>
                                                            <img src="https://placehold.co/50x50/e9ecef/6c757d?text=Sin+Img" 
                                                                 alt="Sin imagen" class="img-size-50">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><strong><?= htmlspecialchars($prod['Codigo']) ?></strong></td>
                                                    <td><?= htmlspecialchars(substr($prod['Descripcion'], 0, 60)) ?>...</td>
                                                    <td class="text-center">
                                                        <a href="productos/index.php" class="btn btn-xs btn-primary" title="Gestionar imágenes">
                                                            <i class="fas fa-images"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="productos/index.php" class="uppercase">Gestionar todos los productos</a>
                        </div>
                    </div>
                </div>

                <!-- Promociones Activas -->
                <div class="col-md-4">
                    <div class="card card-danger card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tags mr-1"></i> Promociones Activas
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="products-list product-list-in-card pl-2 pr-2">
                                <?php if (empty($promocionesActivas)): ?>
                                    <li class="item text-center py-4 text-muted">No hay promociones activas.</li>
                                <?php else: ?>
                                    <?php foreach ($promocionesActivas as $promo): 
                                        $diasRestantes = floor((strtotime($promo['fecha_fin']) - time()) / 86400);
                                    ?>
                                        <li class="item">
                                            <div class="product-img bg-danger text-white d-flex align-items-center justify-content-center">
                                                <i class="fas fa-percent fa-2x"></i>
                                            </div>
                                            <div class="product-info">
                                                <a href="promociones/index.php" class="product-title">
                                                    <?= htmlspecialchars($promo['titulo']) ?>
                                                </a>
                                                <span class="product-description">
                                                    <?php if ($diasRestantes <= 3): ?>
                                                        <span class="text-danger font-weight-bold">
                                                            <i class="fas fa-exclamation-triangle"></i> 
                                                            Termina en <?= $diasRestantes ?> día(s)
                                                        </span>
                                                    <?php else: ?>
                                                        Vence: <?= date('d/m/Y', strtotime($promo['fecha_fin'])) ?>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <a href="promociones/index.php" class="uppercase">Gestionar promociones</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 📋 FILA 5: ÚLTIMAS COTIZACIONES -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-1"></i> Últimas 10 Cotizaciones Recibidas
                            </h3>
                            <div class="card-tools">
                                <a href="cotizaciones/index.php" class="btn btn-sm btn-primary">
                                    <i class="fas fa-list"></i> Ver todas
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead class="bg-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Empresa</th>
                                        <th>Correo</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($ultimasCotizaciones)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">No hay cotizaciones registradas aún.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($ultimasCotizaciones as $cot): ?>
                                            <tr>
                                                <td><strong>#<?= $cot['id'] ?></strong></td>
                                                <td><?= htmlspecialchars($cot['nombre']) ?></td>
                                                <td><?= htmlspecialchars($cot['empresa'] ?: 'N/A') ?></td>
                                                <td><?= htmlspecialchars($cot['correo']) ?></td>
                                                <td>
                                                    <?php
                                                    $estado = strtoupper($cot['estatus'] ?: 'NUEVA');
                                                    $badge = 'secondary';
                                                    if ($estado === 'NUEVA') $badge = 'warning';
                                                    elseif ($estado === 'ATENDIDA') $badge = 'success';
                                                    elseif ($estado === 'EN_PROCESO') $badge = 'info';
                                                    ?>
                                                    <span class="badge badge-<?= $badge ?>"><?= str_replace('_', ' ', $estado) ?></span>
                                                </td>
                                                <td><?= date('d/m/Y H:i', strtotime($cot['fecha_registro'])) ?></td>
                                                <td class="text-center">
                                                    <a href="cotizaciones/ver.php?id=<?= $cot['id'] ?>" class="btn btn-xs btn-default" title="Ver detalle">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('chartCotizaciones').getContext('2d');
    
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartMeses) ?>,
            datasets: [{
                label: 'Cotizaciones',
                data: <?= json_encode($chartTotales) ?>,
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                pointBackgroundColor: '#fff',
                pointBorderColor: 'rgba(40, 167, 69, 1)',
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1,
                        font: { size: 11 }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleFont: { size: 13 },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 8
                }
            }
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>