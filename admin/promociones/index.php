<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';

$mensaje = '';
$tipo_mensaje = '';

// 1. PROCESAR ELIMINACIÓN
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    
    // Obtener imagen para borrarla del servidor
    $stmt = $pdo->prepare("SELECT imagen FROM promociones WHERE id = ?");
    $stmt->execute([$id]);
    $promo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($promo && !empty($promo['imagen'])) {
        $ruta = __DIR__ . '/../../uploads/promociones/' . $promo['imagen'];
        if (file_exists($ruta)) unlink($ruta);
    }
    
    $pdo->prepare("DELETE FROM promociones WHERE id = ?")->execute([$id]);
    $mensaje = "Promoción eliminada correctamente.";
    $tipo_mensaje = "success";
}

// 2. PROCESAR GUARDADO
if (isset($_POST['guardar'])) {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $activo = isset($_POST['activo']) ? 1 : 0;
    $imagen = '';

    // Validar fechas
    if (strtotime($fecha_fin) < strtotime($fecha_inicio)) {
        $mensaje = "Error: La fecha de fin no puede ser anterior a la fecha de inicio.";
        $tipo_mensaje = "danger";
    } else {
        // Validar y subir imagen
        if (!empty($_FILES['imagen']['name'])) {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed_ext)) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
                
                if (in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
                    if ($_FILES['imagen']['size'] <= 5 * 1024 * 1024) { // 5MB
                        $upload_dir = __DIR__ . '/../../uploads/promociones/';
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                        
                        $archivo = time() . '_' . uniqid() . '.' . $ext;
                        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_dir . $archivo)) {
                            $imagen = $archivo;
                        } else {
                            $mensaje = "Error al guardar la imagen.";
                            $tipo_mensaje = "danger";
                        }
                    } else {
                        $mensaje = "La imagen no debe superar los 5MB.";
                        $tipo_mensaje = "danger";
                    }
                } else {
                    $mensaje = "El archivo no es una imagen válida.";
                    $tipo_mensaje = "danger";
                }
            } else {
                $mensaje = "Formato no permitido. Use JPG, PNG o WEBP.";
                $tipo_mensaje = "danger";
            }
        }
    }

    // Guardar en BD si no hubo errores
    if (empty($tipo_mensaje) || $tipo_mensaje !== 'danger') {
        try {
            $stmt = $pdo->prepare("INSERT INTO promociones (titulo, descripcion, imagen, fecha_inicio, fecha_fin, activo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$titulo, $descripcion, $imagen, $fecha_inicio, $fecha_fin, $activo]);
            $mensaje = "Promoción creada exitosamente.";
            $tipo_mensaje = "success";
        } catch (PDOException $e) {
            $mensaje = "Error en la base de datos: " . $e->getMessage();
            $tipo_mensaje = "danger";
        }
    }
}

// 3. OBTENER LISTADO
$promociones = $pdo->query("SELECT * FROM promociones ORDER BY fecha_inicio DESC")->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-tags"></i> Gestión de Promociones</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Promociones</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i> <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- FORMULARIO -->
                <div class="col-md-4">
                    <div class="card card-danger card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-plus-circle"></i> Nueva Promoción</h3>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Título <span class="text-danger">*</span></label>
                                    <input type="text" name="titulo" class="form-control" required placeholder="Ej. 2x1 en Productos Seleccionados">
                                </div>

                                <div class="form-group">
                                    <label>Descripción</label>
                                    <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalles de la promoción..."></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Fecha Inicio <span class="text-danger">*</span></label>
                                            <input type="date" name="fecha_inicio" class="form-control" required value="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Fecha Fin <span class="text-danger">*</span></label>
                                            <input type="date" name="fecha_fin" class="form-control" required value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Imagen Promocional</label>
                                    <div class="custom-file">
                                        <input type="file" name="imagen" class="custom-file-input" id="promoImg" accept="image/*">
                                        <label class="custom-file-label" for="promoImg">Seleccionar imagen...</label>
                                    </div>
                                    <small class="text-muted">JPG, PNG o WEBP. Máx. 5MB.</small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="activo" name="activo" checked>
                                        <label class="custom-control-label" for="activo">Activar promoción</label>
                                    </div>
                                </div>

                                <button type="submit" name="guardar" class="btn btn-danger btn-block">
                                    <i class="fas fa-save"></i> Guardar Promoción
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- LISTADO -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-danger">
                            <h3 class="card-title"><i class="fas fa-list"></i> Promociones Registradas</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Imagen</th>
                                            <th>Título</th>
                                            <th>Vigencia</th>
                                            <th>Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($promociones)): ?>
                                            <tr><td colspan="5" class="text-center text-muted py-4">No hay promociones registradas.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($promociones as $p): 
                                                $hoy = date('Y-m-d');
                                                $es_valida = ($p['activo'] && $hoy >= $p['fecha_inicio'] && $hoy <= $p['fecha_fin']);
                                            ?>
                                                <tr class="<?= $es_valida ? '' : 'table-secondary' ?>">
                                                    <td>
                                                        <?php if (!empty($p['imagen'])): ?>
                                                            <img src="<?= BASE_URL ?>uploads/promociones/<?= htmlspecialchars($p['imagen']) ?>" class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($p['titulo']) ?></strong>
                                                        <br><small class="text-muted"><?= htmlspecialchars(substr($p['descripcion'], 0, 50)) ?>...</small>
                                                    </td>
                                                    <td>
                                                        <small>
                                                            <i class="fas fa-calendar-alt text-danger"></i> 
                                                            <?= date('d/m/Y', strtotime($p['fecha_inicio'])) ?> <br>
                                                            al <?= date('d/m/Y', strtotime($p['fecha_fin'])) ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <?php if ($es_valida): ?>
                                                            <span class="badge badge-success">Vigente</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Inactiva / Vencida</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="?eliminar=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta promoción?')">
                                                            <i class="fas fa-trash"></i>
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
        </div>
    </section>
</div>

<script>
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    e.target.nextElementSibling.textContent = e.target.files[0] ? e.target.files[0].name : 'Seleccionar imagen...';
});
</script>

<?php include '../../includes/footer.php'; ?>