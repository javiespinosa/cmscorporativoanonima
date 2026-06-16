<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';

$mensaje = '';
$tipo_mensaje = '';

// ==========================================
// PROCESAR ELIMINACIÓN
// ==========================================
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    
    // Obtener imágenes para borrarlas
    $stmt = $pdo->prepare("SELECT imagen_principal FROM causas_sociales WHERE id = ?");
    $stmt->execute([$id]);
    $causa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($causa && !empty($causa['imagen_principal'])) {
        $ruta = __DIR__ . '/../../uploads/causas/' . $causa['imagen_principal'];
        if (file_exists($ruta)) unlink($ruta);
    }
    
    // Eliminar galería
    $stmt = $pdo->prepare("SELECT imagen FROM causas_galeria WHERE causa_id = ?");
    $stmt->execute([$id]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $img) {
        $ruta = __DIR__ . '/../../uploads/causas/' . $img['imagen'];
        if (file_exists($ruta)) unlink($ruta);
    }
    
    // Eliminar causa (CASCADE eliminará galería)
    $pdo->prepare("DELETE FROM causas_sociales WHERE id = ?")->execute([$id]);
    
    $mensaje = "Causa eliminada correctamente.";
    $tipo_mensaje = "success";
}

// ==========================================
// PROCESAR GUARDADO (AGREGAR / EDITAR)
// ==========================================
if (isset($_POST['guardar'])) {
    $id_editar = isset($_POST['id_editar']) ? (int)$_POST['id_editar'] : 0;
    $titulo = trim($_POST['titulo']);
    $subtitulo = trim($_POST['subtitulo'] ?? '');
    $descripcion = trim($_POST['descripcion']);
    $tipo = $_POST['tipo'] ?? 'ANIMAL';
    $fecha_inicio = $_POST['fecha_inicio'] ?: date('Y-m-d');
    $activo = isset($_POST['activo']) ? 1 : 0;
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    
    // Estadísticas como JSON
    $stats = [
        'rescatados' => (int)($_POST['stat_rescatados'] ?? 0),
        'alimentados' => (int)($_POST['stat_alimentados'] ?? 0),
        'voluntarios' => (int)($_POST['stat_voluntarios'] ?? 0),
        'donaciones' => (int)($_POST['stat_donaciones'] ?? 0)
    ];
    $estadisticas = json_encode($stats);
    
    $imagen_actual = '';
    if ($id_editar > 0) {
        $stmt = $pdo->prepare("SELECT imagen_principal FROM causas_sociales WHERE id = ?");
        $stmt->execute([$id_editar]);
        $imagen_actual = $stmt->fetchColumn();
    }
    
    $nueva_imagen = $imagen_actual;
    
    // Procesar imagen principal
    if (!empty($_FILES['imagen_principal']['name'])) {
        $ext = strtolower(pathinfo($_FILES['imagen_principal']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $upload_dir = __DIR__ . '/../../uploads/causas/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            
            $nombreArchivo = time() . '_principal_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $upload_dir . $nombreArchivo)) {
                $nueva_imagen = $nombreArchivo;
                if (!empty($imagen_actual) && $imagen_actual !== $nueva_imagen) {
                    $ruta_vieja = $upload_dir . $imagen_actual;
                    if (file_exists($ruta_vieja)) unlink($ruta_vieja);
                }
            }
        }
    }
    
    try {
        if ($id_editar > 0) {
            $stmt = $pdo->prepare("
                UPDATE causas_sociales SET 
                    titulo=?, subtitulo=?, descripcion=?, tipo=?, imagen_principal=?,
                    estadisticas=?, fecha_inicio=?, activo=?, destacado=?
                WHERE id=?
            ");
            $stmt->execute([$titulo, $subtitulo, $descripcion, $tipo, $nueva_imagen, 
                          $estadisticas, $fecha_inicio, $activo, $destacado, $id_editar]);
            $mensaje = "Causa actualizada correctamente.";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO causas_sociales 
                (titulo, subtitulo, descripcion, tipo, imagen_principal, estadisticas, fecha_inicio, activo, destacado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$titulo, $subtitulo, $descripcion, $tipo, $nueva_imagen, 
                          $estadisticas, $fecha_inicio, $activo, $destacado]);
            $id_editar = $pdo->lastInsertId();
            $mensaje = "Causa agregada correctamente.";
        }
        $tipo_mensaje = "success";
    } catch (PDOException $e) {
        $mensaje = "Error: " . $e->getMessage();
        $tipo_mensaje = "danger";
    }
}

// ==========================================
// PROCESAR SUBIDA DE GALERÍA
// ==========================================
if (isset($_POST['agregar_galeria']) && isset($_POST['causa_id'])) {
    $causa_id = (int)$_POST['causa_id'];
    $upload_dir = __DIR__ . '/../../uploads/causas/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
    
    $count = 0;
    foreach ($_FILES['galeria']['tmp_name'] as $key => $tmp) {
        if ($_FILES['galeria']['error'][$key] === UPLOAD_ERR_OK && !empty($_FILES['galeria']['name'][$key])) {
            $ext = strtolower(pathinfo($_FILES['galeria']['name'][$key], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $nombreArchivo = time() . '_gal_' . $key . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($tmp, $upload_dir . $nombreArchivo)) {
                    $descripcion = $_POST['desc_galeria'][$key] ?? '';
                    $stmt = $pdo->prepare("INSERT INTO causas_galeria (causa_id, imagen, descripcion) VALUES (?, ?, ?)");
                    $stmt->execute([$causa_id, $nombreArchivo, $descripcion]);
                    $count++;
                }
            }
        }
    }
    
    $mensaje = "$count imagen(es) agregada(s) a la galería.";
    $tipo_mensaje = "success";
}

// ==========================================
// PROCESAR ELIMINACIÓN DE IMAGEN DE GALERÍA
// ==========================================
if (isset($_GET['eliminar_img']) && is_numeric($_GET['eliminar_img'])) {
    $img_id = (int)$_GET['eliminar_img'];
    $stmt = $pdo->prepare("SELECT imagen FROM causas_galeria WHERE id = ?");
    $stmt->execute([$img_id]);
    $img = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($img) {
        $ruta = __DIR__ . '/../../uploads/causas/' . $img['imagen'];
        if (file_exists($ruta)) unlink($ruta);
        $pdo->prepare("DELETE FROM causas_galeria WHERE id = ?")->execute([$img_id]);
        $mensaje = "Imagen eliminada de la galería.";
        $tipo_mensaje = "success";
    }
}

// ==========================================
// OBTENER DATOS
// ==========================================
$causas = $pdo->query("SELECT * FROM causas_sociales ORDER BY fecha_creacion DESC")->fetchAll(PDO::FETCH_ASSOC);

// Si estamos editando, cargar galería
$galeria_actual = [];
$causa_editar = null;
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM causas_sociales WHERE id = ?");
    $stmt->execute([(int)$_GET['editar']]);
    $causa_editar = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT * FROM causas_galeria WHERE causa_id = ? ORDER BY orden");
    $stmt->execute([(int)$_GET['editar']]);
    $galeria_actual = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <h1><i class="fas fa-heart text-danger"></i> Causas Sociales</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Causas Sociales</li>
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
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- FORMULARIO -->
                <div class="col-md-5">
                    <div class="card card-danger card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-<?= $causa_editar ? 'edit' : 'plus-circle' ?>"></i>
                                <?= $causa_editar ? 'Editar Causa' : 'Nueva Causa Social' ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id_editar" value="<?= $causa_editar['id'] ?? 0 ?>">
                                
                                <div class="form-group">
                                    <label>Título <span class="text-danger">*</span></label>
                                    <input type="text" name="titulo" class="form-control" required
                                           value="<?= htmlspecialchars($causa_editar['titulo'] ?? '') ?>"
                                           placeholder="Ej. Rescate de perritos callejeros">
                                </div>

                                <div class="form-group">
                                    <label>Subtítulo</label>
                                    <input type="text" name="subtitulo" class="form-control"
                                           value="<?= htmlspecialchars($causa_editar['subtitulo'] ?? '') ?>"
                                           placeholder="Ej. Dando amor a quien más lo necesita">
                                </div>

                                <div class="form-group">
                                    <label>Tipo de Causa</label>
                                    <select name="tipo" class="form-control">
                                        <?php 
                                        $tipos = ['ANIMAL' => '🐾 Animal', 'AMBIENTAL' => '🌱 Ambiental', 
                                                  'COMUNITARIA' => '🤝 Comunitaria', 'EDUCATIVA' => '📚 Educativa', 'OTRA' => '✨ Otra'];
                                        $tipo_actual = $causa_editar['tipo'] ?? 'ANIMAL';
                                        foreach ($tipos as $val => $label): 
                                        ?>
                                            <option value="<?= $val ?>" <?= $tipo_actual == $val ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Descripción <span class="text-danger">*</span></label>
                                    <textarea name="descripcion" class="form-control" rows="4" required
                                              placeholder="Cuenta la historia de esta causa..."><?= htmlspecialchars($causa_editar['descripcion'] ?? '') ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Fecha de inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control"
                                           value="<?= $causa_editar['fecha_inicio'] ?? date('Y-m-d') ?>">
                                </div>

                                <hr>
                                <h6 class="text-muted mb-3"><i class="fas fa-chart-bar"></i> Estadísticas de Impacto</h6>
                                
                                <?php 
                                $stats = json_decode($causa_editar['estadisticas'] ?? '{}', true) ?: [];
                                ?>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>🐾 Rescatados</label>
                                            <input type="number" name="stat_rescatados" class="form-control" 
                                                   value="<?= $stats['rescatados'] ?? 0 ?>" min="0">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>🍖 Alimentados</label>
                                            <input type="number" name="stat_alimentados" class="form-control" 
                                                   value="<?= $stats['alimentados'] ?? 0 ?>" min="0">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>👥 Voluntarios</label>
                                            <input type="number" name="stat_voluntarios" class="form-control" 
                                                   value="<?= $stats['voluntarios'] ?? 0 ?>" min="0">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>💰 Donaciones</label>
                                            <input type="number" name="stat_donaciones" class="form-control" 
                                                   value="<?= $stats['donaciones'] ?? 0 ?>" min="0">
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <div class="form-group">
                                    <label>Imagen Principal</label>
                                    <?php if (!empty($causa_editar['imagen_principal'])): ?>
                                        <div class="mb-2">
                                            <img src="<?= BASE_URL ?>uploads/causas/<?= htmlspecialchars($causa_editar['imagen_principal']) ?>" 
                                                 class="img-thumbnail" style="max-height: 120px;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="imagen_principal" class="form-control" accept="image/*">
                                    <small class="text-muted">Recomendado: 1200x600px</small>
                                </div>

                                <div class="form-check mb-2">
                                    <input type="checkbox" name="activo" class="form-check-input" id="activo" 
                                           <?= ($causa_editar['activo'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="activo">Causa activa (visible en el sitio)</label>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" name="destacado" class="form-check-input" id="destacado"
                                           <?= ($causa_editar['destacado'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="destacado">⭐ Destacar en página de inicio</label>
                                </div>

                                <button type="submit" name="guardar" class="btn btn-danger btn-block">
                                    <i class="fas fa-save"></i> <?= $causa_editar ? 'Actualizar' : 'Guardar' ?>
                                </button>
                                <?php if ($causa_editar): ?>
                                    <a href="index.php" class="btn btn-secondary btn-block mt-2">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>

                    <?php if ($causa_editar): ?>
                    <!-- GALERÍA -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-images"></i> Galería de Imágenes</h3>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" name="causa_id" value="<?= $causa_editar['id'] ?>">
                                
                                <div id="galeriaInputs">
                                    <div class="galeria-item mb-2">
                                        <input type="file" name="galeria[]" class="form-control mb-1" accept="image/*" multiple>
                                    </div>
                                </div>
                                
                                <button type="submit" name="agregar_galeria" class="btn btn-info btn-sm btn-block mt-2">
                                    <i class="fas fa-plus"></i> Agregar a Galería
                                </button>
                            </form>

                            <hr>
                            <div class="row">
                                <?php foreach ($galeria_actual as $img): ?>
                                    <div class="col-4 mb-2">
                                        <div class="position-relative">
                                            <img src="<?= BASE_URL ?>uploads/causas/<?= htmlspecialchars($img['imagen']) ?>" 
                                                 class="img-thumbnail" style="height: 80px; width: 100%; object-fit: cover;">
                                            <a href="?eliminar_img=<?= $img['id'] ?>&editar=<?= $causa_editar['id'] ?>" 
                                               class="btn btn-danger btn-sm position-absolute" 
                                               style="top: 2px; right: 2px; padding: 2px 6px; font-size: 10px;"
                                               onclick="return confirm('¿Eliminar esta imagen?')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (empty($galeria_actual)): ?>
                                <p class="text-muted text-center small">Sin imágenes en galería</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- LISTADO -->
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header bg-danger">
                            <h3 class="card-title"><i class="fas fa-list"></i> Causas Registradas (<?= count($causas) ?>)</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="tablaCausas" class="table table-bordered table-striped mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Imagen</th>
                                            <th>Título</th>
                                            <th>Tipo</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($causas)): ?>
                                            <tr><td colspan="5" class="text-center text-muted py-4">No hay causas registradas.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($causas as $c): 
                                                $iconos_tipo = ['ANIMAL'=>'🐾','AMBIENTAL'=>'🌱','COMUNITARIA'=>'🤝','EDUCATIVA'=>'📚','OTRA'=>'✨'];
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?php if (!empty($c['imagen_principal'])): ?>
                                                            <img src="<?= BASE_URL ?>uploads/causas/<?= htmlspecialchars($c['imagen_principal']) ?>" 
                                                                 class="img-thumbnail" style="width: 70px; height: 50px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <span class="text-muted small">Sin imagen</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($c['titulo']) ?></strong>
                                                        <?php if ($c['destacado']): ?>
                                                            <span class="badge badge-warning">⭐ Destacada</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= $iconos_tipo[$c['tipo']] ?? '' ?> <?= $c['tipo'] ?></td>
                                                    <td class="text-center">
                                                        <?php if ($c['activo']): ?>
                                                            <span class="badge badge-success">Activa</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Inactiva</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="?editar=<?= $c['id'] ?>" class="btn btn-sm btn-info" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="?eliminar=<?= $c['id'] ?>" class="btn btn-sm btn-danger" 
                                                           onclick="return confirm('¿Eliminar esta causa y todas sus imágenes?')" title="Eliminar">
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
$(function(){
    $('#tablaCausas').DataTable({
        pageLength: 10,
        responsive: true,
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json' },
        order: [[0, 'desc']]
    });
});
</script>

<?php include '../../includes/footer.php'; ?>