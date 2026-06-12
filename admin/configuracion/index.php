<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';

$mensaje = '';
$tipo_mensaje = 'success';

// 1. OBTENER CONFIGURACIÓN ACTUAL
$config = $pdo->query("SELECT * FROM configuracion WHERE id=1")->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger todos los datos
    $empresa       = $_POST['empresa'] ?? '';
    $fecha_fundacion   = $_POST['fecha_fundacion'] ?? '';
    $giro          = $_POST['giro'] ?? '';
    $slogan        = $_POST['slogan'] ?? '';
    $quienes_somos = $_POST['quienes_somos'] ?? '';
    $mision        = $_POST['mision'] ?? '';
    $vision        = $_POST['vision'] ?? '';
    $valores       = $_POST['valores'] ?? '';
    $direccion     = $_POST['direccion'] ?? '';
    $telefono      = $_POST['telefono'] ?? '';
    $whatsapp      = $_POST['whatsapp'] ?? '';
    $correo        = $_POST['correo'] ?? '';
    $facebook      = $_POST['facebook'] ?? '';
    $instagram     = $_POST['instagram'] ?? '';
    $tiktok        = $_POST['tiktok'] ?? '';
    $youtube       = $_POST['youtube'] ?? '';
    $linkedin      = $_POST['linkedin'] ?? '';
    $google_maps   = $_POST['google_maps'] ?? '';
    $historia        = trim($_POST['historia'] ?? '');
    $objetivos       = trim($_POST['objetivos'] ?? '');
    $imagen_nosotros = $config['imagen_nosotros'] ?? '';

    // Horarios
    $horario_lunes     = $_POST['horario_lunes'] ?? '';
    $horario_martes    = $_POST['horario_martes'] ?? '';
    $horario_miercoles = $_POST['horario_miercoles'] ?? '';
    $horario_jueves    = $_POST['horario_jueves'] ?? '';
    $horario_viernes   = $_POST['horario_viernes'] ?? '';
    $horario_sabado    = $_POST['horario_sabado'] ?? '';
    $horario_domingo   = $_POST['horario_domingo'] ?? '';

    // 2. MANTENER LOGOS ACTUALES POR DEFECTO
    $logo    = $config['logo'];
    $favicon = $config['favicon'];

    // 3. FUNCIÓN PARA PROCESAR SUBIDA DE IMAGEN (Reutilizable para logo y favicon)
    $procesarImagen = function($campo, $directorio, $prefijo) use (&$mensaje, &$tipo_mensaje) {
        if (!empty($_FILES[$campo]['name']) && $_FILES[$campo]['error'] === UPLOAD_ERR_OK) {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'ico', 'svg'];
            $ext = strtolower(pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed_ext)) {
                $mensaje = "Extensión no permitida para $campo. Solo: " . implode(', ', $allowed_ext);
                $tipo_mensaje = 'danger';
                return null;
            }

            // Validación MIME
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES[$campo]['tmp_name']);
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/x-icon', 'image/svg+xml'];
            
            if (!in_array($mime, $allowed_mimes)) {
                $mensaje = "El tipo de archivo para $campo no es una imagen válida.";
                $tipo_mensaje = 'danger';
                return null;
            }

            $upload_dir = __DIR__ . '/../../' . $directorio;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $new_filename = time() . '_' . $prefijo . '.' . $ext;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES[$campo]['tmp_name'], $upload_path)) {
                return $directorio . $new_filename;
            } else {
                $mensaje = "Error al guardar $campo en el servidor.";
                $tipo_mensaje = 'danger';
                return null;
            }
        }
        return null;
    };

    // Procesar logo
    $nuevo_logo = $procesarImagen('logo', 'uploads/logos/', 'logo');
    if ($nuevo_logo) $logo = $nuevo_logo;

    // Procesar favicon
    $nuevo_favicon = $procesarImagen('favicon', 'uploads/logos/', 'favicon');
    if ($nuevo_favicon) $favicon = $nuevo_favicon;

    // Procesar imagen de "Quiénes Somos"
    $nueva_img_nosotros = $procesarImagen('imagen_nosotros', 'uploads/nosotros/', 'nosotros');
    if ($nueva_img_nosotros) $imagen_nosotros = $nueva_img_nosotros;

    // 4. GUARDAR EN BASE DE DATOS
    if (empty($mensaje)) {
        try {
                        $sql = "UPDATE configuracion SET
                        empresa=?,fecha_fundacion=?, giro=?, slogan=?, quienes_somos=?, mision=?, 
                        vision=?, valores=?, historia=?, objetivos=?,
                        direccion=?, telefono=?, whatsapp=?, 
                        correo=?, logo=?, favicon=?, facebook=?, instagram=?, 
                        tiktok=?, youtube=?, linkedin=?, google_maps=?,
                        imagen_nosotros=?,
                        horario_lunes=?, horario_martes=?, horario_miercoles=?,
                        horario_jueves=?, horario_viernes=?, horario_sabado=?,
                        horario_domingo=?, actualizado=NOW()
                    WHERE id=1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $empresa, $fecha_fundacion, $giro, $slogan, $quienes_somos, $mision, $vision, $valores,
                $historia, $objetivos,
                $direccion, $telefono, $whatsapp, $correo, $logo, $favicon,
                $facebook, $instagram, $tiktok, $youtube, $linkedin, $google_maps,
                $imagen_nosotros,
                $horario_lunes, $horario_martes, $horario_miercoles,
                $horario_jueves, $horario_viernes, $horario_sabado, $horario_domingo
            ]);
            
            $mensaje = "Configuración actualizada correctamente.";
            $tipo_mensaje = 'success';
            
            // Recargar datos actualizados
            $config = $pdo->query("SELECT * FROM configuracion WHERE id=1")->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $mensaje = "Error en base de datos: " . $e->getMessage();
            $tipo_mensaje = 'danger';
        }
    }
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
                    <h1><i class="fas fa-cogs"></i> Configuración del Sitio</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Configuración</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <?php if(!empty($mensaje)): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="card card-primary card-outline">
                    <div class="card-header p-0">
                        <!-- PESTAÑAS -->
                        <ul class="nav nav-pills" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="pill" href="#tab-general">
                                    <i class="fas fa-building"></i> General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#tab-identidad">
                                    <i class="fas fa-bullseye"></i> Identidad
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#tab-nosotros-img">
                                    <i class="fas fa-users"></i> Imagen Nosotros
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#tab-contacto">
                                    <i class="fas fa-phone"></i> Contacto
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#tab-redes">
                                    <i class="fas fa-share-alt"></i> Redes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#tab-ubicacion">
                                    <i class="fas fa-map-marker-alt"></i> Ubicación
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#tab-horario">
                                    <i class="fas fa-clock"></i> Horario
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            
                            <!-- TAB 1: GENERAL -->
                            <div class="tab-pane fade show active" id="tab-general">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label>Empresa <span class="text-danger">*</span></label>
                                                <input type="text" name="empresa" class="form-control" 
                                                       value="<?= htmlspecialchars($config['empresa'] ?? '') ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Fecha de Fundación</label>
                                                <input type="date" name="fecha_fundacion" class="form-control" 
                                                    value="<?= htmlspecialchars($config['fecha_fundacion'] ?? '') ?>">
                                                <small class="form-text text-muted">Se usará para calcular los años de experiencia automáticamente</small>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Giro / Sector</label>
                                                <input type="text" name="giro" class="form-control" 
                                                       value="<?= htmlspecialchars($config['giro'] ?? '') ?>"
                                                       placeholder="Ej. Construcción, Tecnología">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label>Slogan</label>
                                            <input type="text" name="slogan" class="form-control" 
                                                   value="<?= htmlspecialchars($config['slogan'] ?? '') ?>"
                                                   placeholder="Frase corta que identifique a la empresa">
                                        </div>

                                        <div class="mb-3">
                                            <label>Quiénes Somos</label>
                                            <textarea name="quienes_somos" class="form-control" rows="4"><?= htmlspecialchars($config['quienes_somos'] ?? '') ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <!-- LOGO -->
                                        <div class="card card-secondary">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-image"></i> Logotipo</h3>
                                            </div>
                                            <div class="card-body text-center">
                                                <?php if (!empty($config['logo'])): ?>
                                                    <img src="../../<?= htmlspecialchars($config['logo']) ?>" 
                                                         alt="Logo" class="img-thumbnail mb-2" style="max-height: 120px;">
                                                <?php else: ?>
                                                    <div class="py-3 text-muted">
                                                        <i class="fas fa-image fa-3x mb-2"></i>
                                                        <p class="small">Sin logotipo</p>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="custom-file">
                                                    <input type="file" name="logo" class="custom-file-input" id="logo" accept="image/*">
                                                    <label class="custom-file-label" for="logo">Cambiar logo</label>
                                                </div>
                                                <small class="text-muted d-block mt-2">JPG, PNG, GIF, WEBP</small>
                                            </div>
                                        </div>

                                        <!-- FAVICON -->
                                        <div class="card card-secondary">
                                            <div class="card-header">
                                                <h3 class="card-title"><i class="fas fa-star"></i> Favicon</h3>
                                            </div>
                                            <div class="card-body text-center">
                                                <?php if (!empty($config['favicon'])): ?>
                                                    <img src="../../<?= htmlspecialchars($config['favicon']) ?>" 
                                                         alt="Favicon" class="img-thumbnail mb-2" style="max-height: 50px;">
                                                <?php else: ?>
                                                    <div class="py-2 text-muted">
                                                        <i class="fas fa-star fa-2x mb-2"></i>
                                                        <p class="small">Sin favicon</p>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="custom-file">
                                                    <input type="file" name="favicon" class="custom-file-input" id="favicon" accept="image/*">
                                                    <label class="custom-file-label" for="favicon">Cambiar</label>
                                                </div>
                                                <small class="text-muted d-block mt-2">ICO, PNG, SVG</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: IDENTIDAD -->
                             <!-- TAB 2: IDENTIDAD CORPORATIVA -->
                            <div class="tab-pane fade" id="tab-identidad">
                                
                                <div class="callout callout-info">
                                    <h5><i class="fas fa-info-circle"></i> Sección "Quiénes Somos"</h5>
                                    <p class="mb-0">Esta información se mostrará en la página <code>nosotros.php</code>. Completa cada campo con cuidado, ya que refleja la identidad de tu empresa.</p>
                                </div>

                                <!-- HISTORIA -->
                                <div class="card card-outline card-secondary mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-book text-primary"></i> Historia de la Empresa</h3>
                                    </div>
                                    <div class="card-body">
                                        <textarea name="historia" class="form-control" rows="6" 
                                                  placeholder="Cuenta cómo inició la empresa, los hitos importantes y la evolución..."><?= htmlspecialchars($config['historia'] ?? '') ?></textarea>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-lightbulb"></i> Usa párrafos cortos y un tono cercano. Los saltos de línea se respetarán.
                                        </small>
                                    </div>
                                </div>

                                <!-- MISIÓN -->
                                <div class="card card-outline card-danger mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-bullseye text-danger"></i> Misión</h3>
                                    </div>
                                    <div class="card-body">
                                        <textarea name="mision" class="form-control" rows="3" 
                                                  placeholder="¿Cuál es el propósito de la empresa?"><?= htmlspecialchars($config['mision'] ?? '') ?></textarea>
                                        <small class="form-text text-muted">Razón de ser. Ej: "Ofrecer soluciones innovadoras que transformen la vida de nuestros clientes."</small>
                                    </div>
                                </div>

                                <!-- VISIÓN -->
                                <div class="card card-outline card-primary mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-eye text-primary"></i> Visión</h3>
                                    </div>
                                    <div class="card-body">
                                        <textarea name="vision" class="form-control" rows="3" 
                                                  placeholder="¿Hacia dónde se dirige la empresa?"><?= htmlspecialchars($config['vision'] ?? '') ?></textarea>
                                        <small class="form-text text-muted">Hacia dónde quiere llegar. Ej: "Ser líderes nacionales en nuestro sector para el 2030."</small>
                                    </div>
                                </div>

                                <!-- VALORES -->
                                <div class="card card-outline card-warning mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-heart text-warning"></i> Valores</h3>
                                    </div>
                                    <div class="card-body">
                                        <textarea name="valores" class="form-control" rows="4" 
                                                  placeholder="Honestidad, Innovación, Calidad, Compromiso, Trabajo en equipo"><?= htmlspecialchars($config['valores'] ?? '') ?></textarea>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Separa los valores con comas o saltos de línea. Se mostrarán como tarjetas individuales.
                                        </small>
                                    </div>
                                </div>

                                <!-- OBJETIVOS -->
                                <div class="card card-outline card-success">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-flag text-success"></i> Objetivos Estratégicos</h3>
                                    </div>
                                    <div class="card-body">
                                        <textarea name="objetivos" class="form-control" rows="6" 
                                                  placeholder="- Ser referentes en el mercado nacional&#10;- Innovar constantemente en nuestros productos&#10;- Brindar la mejor experiencia al cliente"><?= htmlspecialchars($config['objetivos'] ?? '') ?></textarea>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Usa el símbolo <code>-</code> al inicio de cada línea para crear viñetas.
                                        </small>
                                    </div>
                                </div>

                            </div>

                                                        <!-- TAB: IMAGEN QUIÉNES SOMOS -->
                            <div class="tab-pane fade" id="tab-nosotros-img">
                                <div class="callout callout-info">
                                    <h5><i class="fas fa-image"></i> Imagen de Portada</h5>
                                    <p class="mb-0">Esta imagen se usará como fondo del encabezado en la página <code>nosotros.php</code>. Se recomienda una imagen panorámica de alta calidad.</p>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Imagen de Portada</label>
                                            <div class="custom-file">
                                                <input type="file" name="imagen_nosotros" class="custom-file-input" id="imgNosotros" accept="image/*">
                                                <label class="custom-file-label" for="imgNosotros">Seleccionar imagen...</label>
                                            </div>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-ruler-combined"></i> Recomendado: 1920x600px o superior. JPG, PNG o WEBP. Máx. 5MB.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Vista Previa Actual</label>
                                        <div class="border rounded p-2 bg-light">
                                            <?php if (!empty($config['imagen_nosotros'])): ?>
                                                <img src="<?= BASE_URL . htmlspecialchars($config['imagen_nosotros']) ?>" 
                                                     class="img-fluid rounded" alt="Portada actual">
                                            <?php else: ?>
                                                <div class="text-center py-4 text-muted">
                                                    <i class="fas fa-image fa-3x mb-2"></i>
                                                    <p class="mb-0">Sin imagen de portada</p>
                                                    <small>Se usará un fondo de color por defecto</small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: CONTACTO -->
                            <div class="tab-pane fade" id="tab-contacto">
                                <div class="mb-3">
                                    <label><i class="fas fa-map-marker-alt text-danger"></i> Dirección</label>
                                    <textarea name="direccion" class="form-control" rows="2"
                                              placeholder="Calle, Número, Colonia, Ciudad, Estado, CP"><?= htmlspecialchars($config['direccion'] ?? '') ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label><i class="fas fa-phone text-success"></i> Teléfono</label>
                                        <input type="text" name="telefono" class="form-control" 
                                               value="<?= htmlspecialchars($config['telefono'] ?? '') ?>"
                                               placeholder="55 1234 5678">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label><i class="fab fa-whatsapp text-success"></i> WhatsApp</label>
                                        <input type="text" name="whatsapp" class="form-control" 
                                               value="<?= htmlspecialchars($config['whatsapp'] ?? '') ?>"
                                               placeholder="52 1 55 1234 5678">
                                        <small class="form-text text-muted">Incluir código de país (52 para México)</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label><i class="fas fa-envelope text-info"></i> Correo</label>
                                        <input type="email" name="correo" class="form-control" 
                                               value="<?= htmlspecialchars($config['correo'] ?? '') ?>"
                                               placeholder="contacto@empresa.com">
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 4: REDES SOCIALES -->
                            <div class="tab-pane fade" id="tab-redes">
                                <div class="callout callout-info">
                                    <h5><i class="fas fa-info-circle"></i> Instrucciones</h5>
                                    <p class="mb-0">Pega las URLs completas de tus perfiles. Ejemplo: <code>https://facebook.com/tuempresa</code></p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label><i class="fab fa-facebook text-primary"></i> Facebook</label>
                                        <input type="url" name="facebook" class="form-control" 
                                               value="<?= htmlspecialchars($config['facebook'] ?? '') ?>"
                                               placeholder="https://facebook.com/tuempresa">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label><i class="fab fa-instagram text-danger"></i> Instagram</label>
                                        <input type="url" name="instagram" class="form-control" 
                                               value="<?= htmlspecialchars($config['instagram'] ?? '') ?>"
                                               placeholder="https://instagram.com/tuempresa">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label><i class="fab fa-tiktok"></i> TikTok</label>
                                        <input type="url" name="tiktok" class="form-control" 
                                               value="<?= htmlspecialchars($config['tiktok'] ?? '') ?>"
                                               placeholder="https://tiktok.com/@tuempresa">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label><i class="fab fa-youtube text-danger"></i> YouTube</label>
                                        <input type="url" name="youtube" class="form-control" 
                                               value="<?= htmlspecialchars($config['youtube'] ?? '') ?>"
                                               placeholder="https://youtube.com/@tucanal">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label><i class="fab fa-linkedin text-primary"></i> LinkedIn</label>
                                        <input type="url" name="linkedin" class="form-control" 
                                               value="<?= htmlspecialchars($config['linkedin'] ?? '') ?>"
                                               placeholder="https://linkedin.com/company/tuempresa">
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 5: UBICACIÓN (Google Maps) -->
                            <div class="tab-pane fade" id="tab-ubicacion">
                                <div class="callout callout-warning">
                                    <h5><i class="fab fa-google"></i> Cómo obtener el código de Google Maps</h5>
                                    <ol class="mb-0">
                                        <li>Ve a <a href="https://www.google.com/maps" target="_blank">Google Maps</a></li>
                                        <li>Busca la dirección de tu empresa</li>
                                        <li>Haz clic en <strong>"Compartir"</strong> → <strong>"Insertar mapa"</strong></li>
                                        <li>Copia el código HTML que aparece (empieza con <code>&lt;iframe</code>)</li>
                                        <li>Pégalo en el campo de abajo</li>
                                    </ol>
                                </div>

                                <div class="mb-3">
                                    <label>Código iframe de Google Maps</label>
                                    <textarea name="google_maps" class="form-control" rows="5" 
                                              placeholder='<iframe src="https://www.google.com/maps/embed?..." width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'><?= htmlspecialchars($config['google_maps'] ?? '') ?></textarea>
                                </div>

                                <?php if (!empty($config['google_maps'])): ?>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6>Vista previa actual:</h6>
                                            <div class="ratio ratio-16x9">
                                                <?= $config['google_maps'] ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- TAB 6: HORARIO -->
                            <div class="tab-pane fade" id="tab-horario">
                                <div class="callout callout-info">
                                    <h5><i class="fas fa-info-circle"></i> Formato</h5>
                                    <p class="mb-0">Usa el formato <code>9:00 - 18:00</code> o escribe <code>Cerrado</code> para días sin atención.</p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Lunes</label>
                                            <input type="text" name="horario_lunes" class="form-control" 
                                                   value="<?= htmlspecialchars($config['horario_lunes'] ?? '') ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Martes</label>
                                            <input type="text" name="horario_martes" class="form-control" 
                                                   value="<?= htmlspecialchars($config['horario_martes'] ?? '') ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Miércoles</label>
                                            <input type="text" name="horario_miercoles" class="form-control" 
                                                   value="<?= htmlspecialchars($config['horario_miercoles'] ?? '') ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Jueves</label>
                                            <input type="text" name="horario_jueves" class="form-control" 
                                                   value="<?= htmlspecialchars($config['horario_jueves'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Viernes</label>
                                            <input type="text" name="horario_viernes" class="form-control" 
                                                   value="<?= htmlspecialchars($config['horario_viernes'] ?? '') ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Sábado</label>
                                            <input type="text" name="horario_sabado" class="form-control" 
                                                   value="<?= htmlspecialchars($config['horario_sabado'] ?? '') ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Domingo</label>
                                            <input type="text" name="horario_domingo" class="form-control" 
                                                   value="<?= htmlspecialchars($config['horario_domingo'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-footer bg-white">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Guardar Configuración
                        </button>
                        <a href="../dashboard.php" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <?php if (!empty($config['actualizado'])): ?>
                            <span class="float-right text-muted mt-2">
                                <i class="fas fa-clock"></i> 
                                Última actualización: <?= date('d/m/Y H:i', strtotime($config['actualizado'])) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

        </div>
    </section>
</div>

<script>
// Mostrar nombre del archivo al seleccionar
document.querySelectorAll('.custom-file-input').forEach(input => {
    input.addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : 'Seleccionar archivo';
        e.target.nextElementSibling.textContent = fileName;
    });
});
</script>

<?php include '../../includes/footer.php'; ?>