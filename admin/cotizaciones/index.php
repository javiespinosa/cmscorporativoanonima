<?php
require_once '../../includes/auth.php';
// Ya no necesitas database.php aquí porque auth.php ya lo carga

// Filtro opcional por estatus desde la URL
$filtro_estatus = isset($_GET['estatus']) ? $_GET['estatus'] : '';

// Construir consulta con filtro
$sql = "SELECT * FROM cotizaciones";
$params = [];

if (!empty($filtro_estatus) && in_array($filtro_estatus, ['NUEVA', 'EN_PROCESO', 'ATENDIDA'])) {
    $sql .= " WHERE estatus = ?";
    $params[] = $filtro_estatus;
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cotizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contadores para los badges superiores
$totalNuevas = $pdo->query("SELECT COUNT(*) FROM cotizaciones WHERE estatus = 'NUEVA'")->fetchColumn();
$totalEnProceso = $pdo->query("SELECT COUNT(*) FROM cotizaciones WHERE estatus = 'EN_PROCESO'")->fetchColumn();
$totalAtendidas = $pdo->query("SELECT COUNT(*) FROM cotizaciones WHERE estatus = 'ATENDIDA'")->fetchColumn();

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Cotizaciones</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Cotizaciones</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <!-- 🏷️ FILTROS RÁPIDOS POR ESTATUS -->
            <div class="row mb-3">
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                    <a href="index.php<?= $filtro_estatus === 'NUEVA' ? '' : '?estatus=NUEVA' ?>" class="info-box <?= $filtro_estatus === 'NUEVA' ? 'bg-warning' : 'bg-light' ?> shadow-sm text-decoration-none">
                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text <?= $filtro_estatus === 'NUEVA' ? '' : 'text-dark' ?>">Nuevas</span>
                            <span class="info-box-number <?= $filtro_estatus === 'NUEVA' ? '' : 'text-dark' ?>"><?= $totalNuevas ?></span>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                    <a href="index.php<?= $filtro_estatus === 'EN_PROCESO' ? '' : '?estatus=EN_PROCESO' ?>" class="info-box <?= $filtro_estatus === 'EN_PROCESO' ? 'bg-info' : 'bg-light' ?> shadow-sm text-decoration-none">
                        <span class="info-box-icon"><i class="fas fa-spinner"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text <?= $filtro_estatus === 'EN_PROCESO' ? '' : 'text-dark' ?>">En Proceso</span>
                            <span class="info-box-number <?= $filtro_estatus === 'EN_PROCESO' ? '' : 'text-dark' ?>"><?= $totalEnProceso ?></span>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                    <a href="index.php<?= $filtro_estatus === 'ATENDIDA' ? '' : '?estatus=ATENDIDA' ?>" class="info-box <?= $filtro_estatus === 'ATENDIDA' ? 'bg-success' : 'bg-light' ?> shadow-sm text-decoration-none">
                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text <?= $filtro_estatus === 'ATENDIDA' ? '' : 'text-dark' ?>">Atendidas</span>
                            <span class="info-box-number <?= $filtro_estatus === 'ATENDIDA' ? '' : 'text-dark' ?>"><?= $totalAtendidas ?></span>
                        </div>
                    </a>
                </div>
            </div>

            <?php if (!empty($filtro_estatus)): ?>
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-filter"></i> Mostrando cotizaciones con estatus: <strong><?= $filtro_estatus ?></strong>
                    <a href="index.php" class="ml-2">Ver todas</a>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-1"></i> Solicitudes Recibidas (<?= count($cotizaciones) ?>)
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaCotizaciones" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Empresa</th>
                                    <th>Teléfono</th>
                                    <th>Estatus</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($cotizaciones)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No hay cotizaciones registradas.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($cotizaciones as $c): ?>
                                        <tr>
                                            <td><strong>#<?= $c['id'] ?></strong></td>
                                            <td><?= date('d/m/Y H:i', strtotime($c['fecha_registro'])) ?></td>
                                            <td><?= htmlspecialchars($c['nombre']) ?></td>
                                            <td><?= htmlspecialchars($c['empresa'] ?: 'N/A') ?></td>
                                            <td><?= htmlspecialchars($c['telefono']) ?></td>
                                            <td>
                                                <?php
                                                // Badge dinámico según el estatus
                                                $badge_class = 'badge-secondary';
                                                if ($c['estatus'] === 'NUEVA') $badge_class = 'badge-warning';
                                                elseif ($c['estatus'] === 'EN_PROCESO') $badge_class = 'badge-info';
                                                elseif ($c['estatus'] === 'ATENDIDA') $badge_class = 'badge-success';
                                                ?>
                                                <span class="badge <?= $badge_class ?>"><?= $c['estatus'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <a href="ver.php?id=<?= $c['id'] ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i> Ver Detalle
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
    </section>
</div>

<script>
$(function () {
    $('#tablaCotizaciones').DataTable({
        responsive: true,
        autoWidth: false,
        order: [[0, 'desc']], // Ordenar por folio descendente por defecto
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>