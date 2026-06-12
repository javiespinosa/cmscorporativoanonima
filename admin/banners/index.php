<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';

$mensaje = '';
$tipo_mensaje = '';

// 1. PROCESAR ELIMINACIÓN
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    
    $stmt = $pdo->prepare("SELECT imagen, tipo FROM banners WHERE id = ?");
    $stmt->execute([$id_eliminar]);
    $banner = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($banner) {
        // Eliminar archivo si es imagen o video local
        if ($banner['tipo'] === 'imagen' && !empty($banner['imagen'])) {
            $ruta = __DIR__ . '/../../uploads/banners/' . $banner['imagen'];
            if (file_exists($ruta)) unlink($ruta);
        } elseif ($banner['tipo'] === 'video' && !empty($banner['imagen'])) {
            $ruta = __DIR__ . '/../../uploads/banners/' . $banner['imagen'];
            if (file_exists($ruta)) unlink($ruta);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM banners WHERE id = ?");
    $stmt->execute([$id_eliminar]);
    
    $mensaje = "Banner eliminado correctamente.";
    $tipo_mensaje = "success";
}

// 2. PROCESAR GUARDADO
if (isset($_POST['guardar'])) {
    $titulo = trim($_POST['titulo']);
    $subtitulo = trim($_POST['subtitulo']);
    $enlace = trim($_POST['enlace']);
    $orden = (int)($_POST['orden'] ?? 0);
    $activo = isset($_POST['activo']) ? 1 : 0;
    $tipo = $_POST['tipo'] ?? 'imagen';
    $video_url = trim($_POST['video_url'] ?? '');
    $imagen = '';

    // Procesar según el tipo
    if ($tipo === 'imagen') {
        // Validación de imagen
        if (!empty($_FILES['imagen']['name'])) {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed_ext)) {
                $mensaje = "Error: Solo se permiten archivos JPG, PNG o WEBP.";
                $tipo_mensaje = "danger";
            } else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
                $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
                
                if (!in_array($mime, $allowed_mimes)) {
                    $mensaje = "Error: El archivo no es una imagen válida.";
                    $tipo_mensaje = "danger";
                } else {
                    if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
                        $mensaje = "Error: La imagen no debe superar los 5MB.";
                        $tipo_mensaje = "danger";
                    } else {
                        $upload_dir = __DIR__ . '/../../uploads/banners/';
                        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                        
                        $archivo = time() . '_' . uniqid() . '.' . $ext;
                        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_dir . $archivo)) {
                            $imagen = $archivo;
                        } else {
                            $mensaje = "Error al guardar la imagen.";
                            $tipo_mensaje = "danger";
                        }
                    }
                }
            }
        }
    } elseif ($tipo === 'video') {
        // Validación de video local
        if (!empty($_FILES['video']['name'])) {
            $allowed_ext = ['mp4', 'webm'];
            $ext = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed_ext)) {
                $mensaje = "Error: Solo se permiten archivos MP4 o WEBM.";
                $tipo_mensaje = "danger";
            } else {
                if ($_FILES['video']['size'] > 50 * 1024 * 1024) { // 50MB máximo
                    $mensaje = "Error: El video no debe superar los 50MB.";
                    $tipo_mensaje = "danger";
                } else {
                    $upload_dir = __DIR__ . '/../../uploads/banners/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    
                    $archivo = time() . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($_FILES['video']['tmp_name'], $upload_dir . $archivo)) {
                        $imagen = $archivo; // Guardamos el nombre del video en el campo imagen
                    } else {
                        $mensaje = "Error al guardar el video.";
                        $tipo_mensaje = "danger";
                    }
                }
            }
        }
    } elseif ($tipo === 'youtube') {
        // Validar URL de YouTube/Vimeo
        if (empty($video_url)) {
            $mensaje = "Error: Debes ingresar una URL de YouTube o Vimeo.";
            $tipo_mensaje = "danger";
        } else {
            // Extraer ID del video
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\s]+)/', $video_url, $matches)) {
                $video_url = 'https://www.youtube.com/embed/' . $matches[1] . '?autoplay=1&mute=1&loop=1&controls=0&showinfo=0&rel=0&playlist=' . $matches[1];
            } elseif (preg_match('/vimeo\.com\/(\d+)/', $video_url, $matches)) {
                $video_url = 'https://player.vimeo.com/video/' . $matches[1] . '?autoplay=1&muted=1&loop=1&title=0&byline=0&portrait=0';
            } else {
                $mensaje = "Error: URL de YouTube o Vimeo no válida.";
                $tipo_mensaje = "danger";
            }
        }
    }

    // Guardar en BD si no hubo errores
    if (empty($tipo_mensaje) || $tipo_mensaje !== 'danger') {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO banners (titulo, subtitulo, enlace, imagen, orden_banner, activo, tipo, video_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$titulo, $subtitulo, $enlace, $imagen, $orden, $activo, $tipo, $video_url]);
            
            $mensaje = "Banner agregado correctamente.";
            $tipo_mensaje = "success";
        } catch (PDOException $e) {
            $mensaje = "Error en la base de datos: " . $e->getMessage();
            $tipo_mensaje = "danger";
        }
    }
}

// 3. OBTENER LISTADO
$banners = $pdo->query("SELECT * FROM banners ORDER BY orden_banner ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<style>
    .spec-card {
        background: #f8f9fa;
        border-left: 4px solid #28a745;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
    }
    .spec-card h6 {
        color: #28a745;
        margin-bottom: 10px;
        font-weight: 700;
    }
    .spec-card ul {
        margin: 0;
        padding-left: 20px;
        font-size: 0.9rem;
        color: #6c757d;
    }
    .spec-card li {
        margin-bottom: 5px;
    }
    .help-toggle {
        cursor: pointer;
        color: #28a745;
        font-weight: 600;
    }
    .help-toggle:hover {
        text-decoration: underline;
    }
    .badge-spec {
        font-size: 0.75rem;
        padding: 3px 8px;
        margin-left: 5px;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-images"></i> Gestión de Banners</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Banners</li>
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
                    <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <div class="row">

                <!-- FORMULARIO -->
                <div class="col-md-4">
                    <div class="card card-success card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="fas fa-plus-circle"></i> Nuevo Banner</h3>
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="toggleEspecificaciones()">
                                <i class="fas fa-info-circle"></i> Ver Especificaciones
                            </button>
                        </div>
                        <div class="card-body">
                            
                            <!-- SECCIÓN DE ESPECIFICACIONES (Colapsable) -->
                            <div id="especificaciones" class="collapse mb-3">
                                <div class="spec-card">
                                    <h6><i class="fas fa-image"></i> Imágenes</h6>
                                    <ul>
                                        <li><strong>Formatos:</strong> JPG, PNG, WEBP</li>
                                        <li><strong>Tamaño máximo:</strong> 5MB</li>
                                        <li><strong>Resolución recomendada:</strong> 1920x1080px</li>
                                        <li><strong>Proporción:</strong> 16:9 (horizontal)</li>
                                    </ul>
                                </div>
                                
                                <div class="spec-card" style="border-left-color: #ffc107;">
                                    <h6 style="color: #ffc107;"><i class="fas fa-video"></i> Videos Locales</h6>
                                    <ul>
                                        <li><strong>Formatos:</strong> MP4, WEBM</li>
                                        <li><strong>Tamaño máximo:</strong> 50MB</li>
                                        <li><strong>Duración:</strong> 10-15 segundos</li>
                                        <li><strong>Resolución:</strong> 1920x1080px (1080p)</li>
                                        <li><strong>Codec:</strong> H.264 (para MP4)</li>
                                        <li><strong>Sin audio</strong> o audio muteado</li>
                                        <li><strong>FPS:</strong> 24 o 30 fps</li>
                                    </ul>
                                </div>
                                
                                <div class="spec-card" style="border-left-color: #dc3545;">
                                    <h6 style="color: #dc3545;"><i class="fab fa-youtube"></i> YouTube / Vimeo</h6>
                                    <ul>
                                        <li><strong>URL completa</strong> del video</li>
                                        <li><strong>Debe permitir</strong> embed/insertar</li>
                                        <li><strong>Debe permitir</strong> autoplay</li>
                                        <li>El video se reproducirá en loop</li>
                                        <li>Sin controles visibles</li>
                                    </ul>
                                </div>
                                
                                <div class="alert alert-info mb-0">
                                    <strong><i class="fas fa-lightbulb"></i> Consejos:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Usa contenido de alta calidad</li>
                                        <li>Evita texto pequeño en imágenes</li>
                                        <li>Los videos deben ser livianos</li>
                                        <li>Prueba en móvil antes de publicar</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <form method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Tipo de Banner <span class="text-danger">*</span></label>
                                    <select name="tipo" id="tipoBanner" class="form-control" required>
                                        <option value="imagen">🖼️ Imagen Estática</option>
                                        <option value="video">🎥 Video Local (MP4)</option>
                                        <option value="youtube">📺 YouTube / Vimeo</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Selecciona el tipo de contenido que subirás
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>Título del Banner <span class="text-danger">*</span></label>
                                    <input type="text" name="titulo" class="form-control" required placeholder="Ej. Oferta de Verano 2024" maxlength="100">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Texto principal que se mostrará en el banner
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>Subtítulo / Badge</label>
                                    <input type="text" name="subtitulo" class="form-control" placeholder="Ej. Hasta 50% de descuento" maxlength="100">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-tag"></i> Aparece como etiqueta superior (opcional)
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>Enlace del Botón</label>
                                    <input type="text" name="enlace" class="form-control" placeholder="https://... o #productos">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-link"></i> URL a la que redirigirá el botón "Más Información"
                                    </small>
                                </div>

                                <!-- Campo para Imagen -->
                                <div class="form-group campo-tipo" data-tipo="imagen">
                                    <label>
                                        Imagen del Banner <span class="text-danger">*</span>
                                        <span class="badge badge-info badge-spec">JPG, PNG, WEBP</span>
                                        <span class="badge badge-secondary badge-spec">Máx. 5MB</span>
                                    </label>
                                    <div class="custom-file">
                                        <input type="file" name="imagen" class="custom-file-input" id="imagenInput" accept="image/jpeg, image/png, image/webp">
                                        <label class="custom-file-label" for="imagenInput">Seleccionar imagen...</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-ruler-combined"></i> Recomendado: 1920x1080px (16:9)
                                    </small>
                                    <div class="mt-2 p-2 bg-light rounded">
                                        <small class="text-muted">
                                            <i class="fas fa-check-circle text-success"></i> 
                                            Se aplicará efecto Ken Burns (zoom suave)
                                        </small>
                                    </div>
                                </div>

                                <!-- Campo para Video Local -->
                                <div class="form-group campo-tipo d-none" data-tipo="video">
                                    <label>
                                        Video MP4 <span class="text-danger">*</span>
                                        <span class="badge badge-warning badge-spec">MP4, WEBM</span>
                                        <span class="badge badge-secondary badge-spec">Máx. 50MB</span>
                                    </label>
                                    <div class="custom-file">
                                        <input type="file" name="video" class="custom-file-input" id="videoInput" accept="video/mp4, video/webm">
                                        <label class="custom-file-label" for="videoInput">Seleccionar video...</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-clock"></i> Duración ideal: 10-15 segundos
                                    </small>
                                    <div class="mt-2 p-2 bg-light rounded">
                                        <small class="text-muted">
                                            <i class="fas fa-volume-mute text-warning"></i> 
                                            Sin audio (se reproducirá muteado automáticamente)
                                        </small>
                                    </div>
                                    <div class="mt-2 p-2 bg-light rounded">
                                        <small class="text-muted">
                                            <i class="fas fa-sync text-info"></i> 
                                            Se reproducirá en loop continuo
                                        </small>
                                    </div>
                                </div>

                                <!-- Campo para YouTube/Vimeo -->
                                <div class="form-group campo-tipo d-none" data-tipo="youtube">
                                    <label>
                                        URL de YouTube o Vimeo <span class="text-danger">*</span>
                                        <span class="badge badge-danger badge-spec">EMBED</span>
                                    </label>
                                    <input type="url" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-link"></i> Pega el enlace completo del video
                                    </small>
                                    <div class="mt-2 p-2 bg-light rounded">
                                        <small class="text-muted">
                                            <strong>Ejemplos válidos:</strong><br>
                                            <code>https://www.youtube.com/watch?v=CODIGO</code><br>
                                            <code>https://youtu.be/CODIGO</code><br>
                                            <code>https://vimeo.com/12345678</code>
                                        </small>
                                    </div>
                                    <div class="alert alert-warning mt-2 mb-0 py-2">
                                        <small>
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            El video debe permitir inserción (embed) y autoplay
                                        </small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>
                                        Orden de Aparición
                                        <span class="badge badge-secondary badge-spec">OPCIONAL</span>
                                    </label>
                                    <input type="number" name="orden" class="form-control" value="0" min="0">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-sort-numeric-down"></i> Menor número = aparece primero (0 es el primero)
                                    </small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="activo" name="activo" checked>
                                        <label class="custom-control-label" for="activo">
                                            <strong>Publicar inmediatamente</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-eye"></i> Desmarca para guardar como borrador (no visible en el sitio)
                                    </small>
                                </div>

                                <hr>

                                <button type="submit" name="guardar" class="btn btn-success btn-lg btn-block">
                                    <i class="fas fa-save"></i> Guardar Banner
                                </button>
                                
                                <button type="reset" class="btn btn-secondary btn-block mt-2" onclick="return confirm('¿Limpiar formulario?')">
                                    <i class="fas fa-eraser"></i> Limpiar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>




                <!-- LISTADO -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title"><i class="fas fa-list"></i> Banners Registrados</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 50px;">Orden</th>
                                            <th>Tipo</th>
                                            <th>Vista Previa</th>
                                            <th>Título / Subtítulo</th>
                                            <th style="width: 80px;">Estado</th>
                                            <th style="width: 120px;" class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($banners)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="fas fa-images fa-2x mb-2 d-block"></i>
                                                    No hay banners registrados.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($banners as $banner): ?>
                                                <tr>
                                                    <td class="text-center align-middle">
                                                        <span class="badge badge-secondary"><?= $banner['orden_banner'] ?></span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?php
                                                        $tipos = [
                                                            'imagen' => ['icon' => 'fa-image', 'color' => 'info', 'label' => 'Imagen'],
                                                            'video' => ['icon' => 'fa-video', 'color' => 'warning', 'label' => 'Video'],
                                                            'youtube' => ['icon' => 'fa-youtube', 'color' => 'danger', 'label' => 'YouTube']
                                                        ];
                                                        $t = $tipos[$banner['tipo']] ?? $tipos['imagen'];
                                                        ?>
                                                        <span class="badge badge-<?= $t['color'] ?>">
                                                            <i class="fas <?= $t['icon'] ?>"></i> <?= $t['label'] ?>
                                                        </span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?php if ($banner['tipo'] === 'imagen' && !empty($banner['imagen'])): ?>
                                                            <img src="<?= BASE_URL ?>uploads/banners/<?= htmlspecialchars($banner['imagen']) ?>" 
                                                                 class="img-thumbnail" 
                                                                 style="width: 120px; height: 60px; object-fit: cover;" 
                                                                 alt="Banner">
                                                        <?php elseif ($banner['tipo'] === 'video' && !empty($banner['imagen'])): ?>
                                                            <video src="<?= BASE_URL ?>uploads/banners/<?= htmlspecialchars($banner['imagen']) ?>" 
                                                                   class="img-thumbnail" 
                                                                   style="width: 120px; height: 60px; object-fit: cover;"
                                                                   muted></video>
                                                        <?php elseif ($banner['tipo'] === 'youtube' && !empty($banner['video_url'])): ?>
                                                            <div class="bg-dark text-white text-center p-2" style="width: 120px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fab fa-youtube fa-2x"></i>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted small">Sin contenido</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <strong><?= htmlspecialchars($banner['titulo']) ?></strong>
                                                        <?php if (!empty($banner['subtitulo'])): ?>
                                                            <br><small class="text-muted"><?= htmlspecialchars($banner['subtitulo']) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <?php if ($banner['activo']): ?>
                                                            <span class="badge badge-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Oculto</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <a href="?eliminar=<?= $banner['id'] ?>" 
                                                           class="btn btn-sm btn-danger"
                                                           onclick="return confirm('¿Estás seguro de eliminar este banner?')"
                                                           title="Eliminar">
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
// Mostrar/ocultar campos según el tipo de banner
document.getElementById('tipoBanner').addEventListener('change', function() {
    const tipo = this.value;
    document.querySelectorAll('.campo-tipo').forEach(campo => {
        if (campo.dataset.tipo === tipo) {
            campo.classList.remove('d-none');
        } else {
            campo.classList.add('d-none');
        }
    });
});

// Mostrar nombre del archivo
document.querySelectorAll('.custom-file-input').forEach(input => {
    input.addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : 'Seleccionar archivo';
        e.target.nextElementSibling.textContent = fileName;
    });
});
</script>

<script>
// Mostrar/ocultar especificaciones
function toggleEspecificaciones() {
    const espec = document.getElementById('especificaciones');
    if (espec.classList.contains('show')) {
        espec.classList.remove('show');
    } else {
        espec.classList.add('show');
    }
}

// Mostrar/ocultar campos según el tipo de banner
document.getElementById('tipoBanner').addEventListener('change', function() {
    const tipo = this.value;
    document.querySelectorAll('.campo-tipo').forEach(campo => {
        if (campo.dataset.tipo === tipo) {
            campo.classList.remove('d-none');
        } else {
            campo.classList.add('d-none');
        }
    });
});

// Mostrar nombre del archivo
document.querySelectorAll('.custom-file-input').forEach(input => {
    input.addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : 'Seleccionar archivo';
        e.target.nextElementSibling.textContent = fileName;
    });
});

// Validación del tamaño de archivo en tiempo real
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const maxSize = this.accept.includes('video') ? 50 * 1024 * 1024 : 5 * 1024 * 1024;
            const maxSizeMB = this.accept.includes('video') ? 50 : 5;
            
            if (file.size > maxSize) {
                alert(`⚠️ El archivo es demasiado grande.\n\nTamaño actual: ${(file.size / 1024 / 1024).toFixed(2)} MB\nTamaño máximo: ${maxSizeMB} MB\n\nPor favor selecciona un archivo más pequeño.`);
                this.value = ''; // Limpiar input
            }
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>