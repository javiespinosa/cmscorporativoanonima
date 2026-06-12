<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';

// Obtener filtros
$filtro_leido = isset($_GET['leido']) ? (int)$_GET['leido'] : null;
$buscar = trim($_GET['buscar'] ?? '');

// Construir consulta
$sql = "SELECT * FROM mensajes_contacto WHERE 1=1";
$params = [];

if ($filtro_leido !== null) {
    $sql .= " AND leido = ?";
    $params[] = $filtro_leido;
}

if (!empty($buscar)) {
    $sql .= " AND (nombre LIKE ? OR email LIKE ? OR mensaje LIKE ?)";
    $busqueda = "%$buscar%";
    $params[] = $busqueda;
    $params[] = $busqueda;
    $params[] = $busqueda;
}

$sql .= " ORDER BY fecha_creacion DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contadores
$totalNuevos = $pdo->query("SELECT COUNT(*) FROM mensajes_contacto WHERE leido = 0")->fetchColumn();
$totalTodos = $pdo->query("SELECT COUNT(*) FROM mensajes_contacto")->fetchColumn();

// Marcar como leído si se solicita
if (isset($_GET['marcar_leido']) && isset($_GET['id'])) {
    $stmt = $pdo->prepare("UPDATE mensajes_contacto SET leido = 1 WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    header("Location: mensajes.php" . (isset($_GET['leido']) ? "?leido=" . $_GET['leido'] : ""));
    exit;
}

// Eliminar mensaje
if (isset($_GET['eliminar']) && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM mensajes_contacto WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    header("Location: mensajes.php");
    exit;
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
                    <h1><i class="fas fa-envelope"></i> Mensajes de Contacto</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Mensajes</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Contadores -->
            <div class="row mb-3">
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                    <a href="mensajes.php" class="info-box bg-light shadow-sm text-decoration-none">
                        <span class="info-box-icon bg-primary"><i class="fas fa-inbox"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Todos</span>
                            <span class="info-box-number"><?= $totalTodos ?></span>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                    <a href="mensajes.php?leido=0" class="info-box <?= $filtro_leido === 0 ? 'bg-warning' : 'bg-light' ?> shadow-sm text-decoration-none">
                        <span class="info-box-icon <?= $filtro_leido === 0 ? 'bg-warning' : 'bg-primary' ?>">
                            <i class="fas fa-envelope-open"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Nuevos</span>
                            <span class="info-box-number"><?= $totalNuevos ?></span>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 col-sm-6 col-12 mb-2">
                    <a href="mensajes.php?leido=1" class="info-box <?= $filtro_leido === 1 ? 'bg-success' : 'bg-light' ?> shadow-sm text-decoration-none">
                        <span class="info-box-icon <?= $filtro_leido === 1 ? 'bg-success' : 'bg-primary' ?>">
                            <i class="fas fa-check"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Leídos</span>
                            <span class="info-box-number"><?= $totalTodos - $totalNuevos ?></span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Filtros y búsqueda -->
            <div class="card mb-3">
                <div class="card-body py-2">
                    <form method="GET" class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="buscar" class="form-control" 
                                       placeholder="Buscar por nombre, email o mensaje..." 
                                       value="<?= htmlspecialchars($buscar) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="mensajes.php" class="btn btn-default">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de mensajes -->
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> 
                        Mensajes Recibidos
                        <?php if ($filtro_leido === 0): ?>
                            <span class="badge bg-warning ms-2">Nuevos</span>
                        <?php endif; ?>
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th>Estado</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Asunto</th>
                                    <th style="width: 150px;">Fecha</th>
                                    <th style="width: 180px;" class="text-center">Acciones</th>
                                </tr>
                            </thead>


                                                        <tbody>
                                <?php if (empty($mensajes)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                            No hay mensajes recibidos
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($mensajes as $m): ?>
                                        <tr class="<?= $m['leido'] ? '' : 'table-warning' ?>">
                                            <td><strong>#<?= $m['id'] ?></strong></td>
                                            <td>
                                                <?php if ($m['leido']): ?>
                                                    <span class="badge badge-success">Leído</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Nuevo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($m['nombre']) ?></strong>
                                                <?php if (!empty($m['empresa'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($m['empresa']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="mailto:<?= htmlspecialchars($m['email']) ?>">
                                                    <?= htmlspecialchars($m['email']) ?>
                                                </a>
                                                <?php if (!empty($m['telefono'])): ?>
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-phone"></i> <?= htmlspecialchars($m['telefono']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $asuntos = [
                                                    'cotizacion' => 'Solicitar cotización',
                                                    'informacion' => 'Información de productos',
                                                    'soporte' => 'Soporte técnico',
                                                    'otro' => 'Otro'
                                                ];
                                                echo $asuntos[$m['asunto']] ?? htmlspecialchars($m['asunto']);
                                                ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($m['fecha_creacion'])) ?></td>
                                            <td class="text-center">
                                                <!-- ✅ BOTÓN QUE ABRE EL MODAL (Sintaxis Bootstrap 4) -->
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        data-toggle="modal" 
                                                        data-target="#modalVer<?= $m['id'] ?>"
                                                        title="Ver mensaje">
                                                    <i class="fas fa-eye"></i> Ver
                                                </button>
                                                
                                                <?php if (!$m['leido']): ?>
                                                    <a href="mensajes.php?marcar_leido=1&id=<?= $m['id'] ?>" 
                                                       class="btn btn-sm btn-success"
                                                       title="Marcar como leído">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="mensajes.php?eliminar=1&id=<?= $m['id'] ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('¿Estás seguro de eliminar este mensaje?')"
                                                   title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- ✅ MODAL POPUP PARA VER EL MENSAJE (Bootstrap 4) -->
                                        <div class="modal fade" id="modalVer<?= $m['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-envelope-open-text mr-2"></i> 
                                                            Mensaje #<?= $m['id'] ?>
                                                        </h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <p class="mb-1 text-muted small">Remitente</p>
                                                                <strong class="text-lg"><?= htmlspecialchars($m['nombre']) ?></strong>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p class="mb-1 text-muted small">Correo electrónico</p>
                                                                <a href="mailto:<?= htmlspecialchars($m['email']) ?>" class="text-lg">
                                                                    <?= htmlspecialchars($m['email']) ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        
                                                        <?php if (!empty($m['telefono']) || !empty($m['empresa'])): ?>
                                                            <div class="row mb-3">
                                                                <?php if (!empty($m['telefono'])): ?>
                                                                <div class="col-md-6">
                                                                    <p class="mb-1 text-muted small">Teléfono</p>
                                                                    <strong><?= htmlspecialchars($m['telefono']) ?></strong>
                                                                </div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($m['empresa'])): ?>
                                                                <div class="col-md-6">
                                                                    <p class="mb-1 text-muted small">Empresa</p>
                                                                    <strong><?= htmlspecialchars($m['empresa']) ?></strong>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <div class="mb-3">
                                                            <p class="mb-1 text-muted small">Asunto</p>
                                                            <span class="badge badge-primary p-2">
                                                                <?= $asuntos[$m['asunto']] ?? htmlspecialchars($m['asunto']) ?>
                                                            </span>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <p class="mb-1 text-muted small">Fecha de recepción</p>
                                                            <strong><?= date('d/m/Y H:i:s', strtotime($m['fecha_creacion'])) ?></strong>
                                                        </div>
                                                        
                                                        <hr>
                                                        
                                                        <div>
                                                            <p class="mb-2 text-muted small font-weight-bold">Contenido del mensaje:</p>
                                                            <div class="bg-light p-3 rounded border" style="white-space: pre-wrap; font-size: 1.05rem; line-height: 1.6;">
                                                                <?= nl2br(htmlspecialchars($m['mensaje'])) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <a href="mailto:<?= htmlspecialchars($m['email']) ?>?subject=Re: <?= urlencode($m['asunto']) ?>" 
                                                           class="btn btn-success">
                                                            <i class="fas fa-reply mr-1"></i> Responder por email
                                                        </a>
                                                        <?php if (!$m['leido']): ?>
                                                            <a href="mensajes.php?marcar_leido=1&id=<?= $m['id'] ?>" 
                                                               class="btn btn-warning">
                                                                <i class="fas fa-check mr-1"></i> Marcar como leído
                                                            </a>
                                                        <?php endif; ?>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- ✅ FIN DEL MODAL -->

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

<?php include '../../includes/footer.php'; ?>