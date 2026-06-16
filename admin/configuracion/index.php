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
    $color_primario   = trim($_POST['color_primario'] ?? '#28a745');
    $color_secundario = trim($_POST['color_secundario'] ?? '#20c997');
    $color_texto      = trim($_POST['color_texto'] ?? '#333333');
    $color_fondo      = trim($_POST['color_fondo'] ?? '#ffffff');

    // Procesar datos bancarios como JSON
    $datos_bancarios = [
        'beneficiario' => trim($_POST['banco_beneficiario'] ?? ''),
        'banco' => trim($_POST['banco_nombre'] ?? ''),
        'clabe' => trim($_POST['banco_clabe'] ?? ''),
        'tarjeta' => trim($_POST['banco_tarjeta'] ?? ''),
        'link_pago' => trim($_POST['banco_link_pago'] ?? '')
    ];
    $datos_bancarios_json = json_encode($datos_bancarios);

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

    // 3. FUNCIÓN PARA PROCESAR SUBIDA DE IMAGEN
    $procesarImagen = function($campo, $directorio, $prefijo) use (&$mensaje, &$tipo_mensaje) {
        if (!empty($_FILES[$campo]['name']) && $_FILES[$campo]['error'] === UPLOAD_ERR_OK) {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'ico', 'svg'];
            $ext = strtolower(pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed_ext)) {
                $mensaje = "Extensión no permitida para $campo. Solo: " . implode(', ', $allowed_ext);
                $tipo_mensaje = 'danger';
                return null;
            }

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

    $nuevo_logo = $procesarImagen('logo', 'uploads/logos/', 'logo');
    if ($nuevo_logo) $logo = $nuevo_logo;

    $nuevo_favicon = $procesarImagen('favicon', 'uploads/logos/', 'favicon');
    if ($nuevo_favicon) $favicon = $nuevo_favicon;

    $nueva_img_nosotros = $procesarImagen('imagen_nosotros', 'uploads/nosotros/', 'nosotros');
    if ($nueva_img_nosotros) $imagen_nosotros = $nueva_img_nosotros;

    // 4. GUARDAR EN BASE DE DATOS
    if (empty($mensaje)) {
        try {
            $sql = "UPDATE configuracion SET
                    empresa=?, fecha_fundacion=?, giro=?, slogan=?, quienes_somos=?, mision=?, 
                    vision=?, valores=?, historia=?, objetivos=?,
                    direccion=?, telefono=?, whatsapp=?, 
                    correo=?, logo=?, favicon=?, facebook=?, instagram=?, 
                    tiktok=?, youtube=?, linkedin=?, google_maps=?,
                    imagen_nosotros=?,
                    horario_lunes=?, horario_martes=?, horario_miercoles=?,
                    horario_jueves=?, horario_viernes=?, horario_sabado=?,
                    horario_domingo=?, actualizado=NOW(),
                    color_primario=?, color_secundario=?, color_texto=?, color_fondo=?,
                    datos_bancarios=?  
                WHERE id=1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $empresa, $fecha_fundacion, $giro, $slogan, $quienes_somos, $mision, $vision, $valores,
                $historia, $objetivos,
                $direccion, $telefono, $whatsapp, $correo, $logo, $favicon,
                $facebook, $instagram, $tiktok, $youtube, $linkedin, $google_maps,
                $imagen_nosotros,
                $horario_lunes, $horario_martes, $horario_miercoles,
                $horario_jueves, $horario_viernes, $horario_sabado, $horario_domingo,
                $color_primario, $color_secundario, $color_texto, $color_fondo, $datos_bancarios_json
            ]);
            
            $mensaje = "Configuración actualizada correctamente.";
            $tipo_mensaje = 'success';
            
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
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#tab-estilos">
                                    <i class="fas fa-palette"></i> Estilos
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
                                            <div class="col-md-6 mb-3">
                                                <label>Fecha de Fundación</label>
                                                <input type="date" name="fecha_fundacion" class="form-control" 
                                                    value="<?= htmlspecialchars($config['fecha_fundacion'] ?? '') ?>">
                                                <small class="form-text text-muted">Se usará para calcular los años de experiencia</small>
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

                            <!-- TAB 2: IDENTIDAD CORPORATIVA -->
                            <div class="tab-pane fade" id="tab-identidad">
                                <div class="callout callout-info">
                                    <h5><i class="fas fa-info-circle"></i> Sección "Quiénes Somos"</h5>
                                    <p class="mb-0">Esta información se mostrará en la página <code>nosotros.php</code>.</p>
                                </div>

                                <div class="card card-outline card-secondary mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-book text-primary"></i> Historia de la Empresa</h3>
                                    </div>
                                    <div class="card-body">
                                        <textarea name="historia" class="form-control" rows="6" 
                                                  placeholder="Cuenta cómo inició la empresa..."><?= htmlspecialchars($config['historia'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <div class="card card-outline card-danger mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-bullseye text-danger"></i> Misión</h3>
                                    </div>
                                    <div class="card-body">
                                        <textarea name="mision" class="form-control" rows="3" 
                                                  placeholder="¿Cuál es el propósito de la empresa?"><?= htmlspecialchars($config['mision'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <div class="card card-outline card-primary mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-eye text-primary"></i> Visión</h3>
                                    </div>
                                    <div class="card-body">
                                        <textarea name="vision" class="form-control" rows="3" 
                                                  placeholder="¿Hacia dónde se dirige la empresa?"><?= htmlspecialchars($config['vision'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <div class="card card-outline card-warning mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-heart text-warning"></i> Valores</h3>
                                    </div>
                                    <div class="card-body">
                                        <textarea name="valores" class="form-control" rows="4" 
                                                  placeholder="Honestidad, Innovación, Calidad..."><?= htmlspecialchars($config['valores'] ?? '') ?></textarea>
                                        <small class="form-text text-muted">Separa los valores con comas o saltos de línea.</small>
                                    </div>
                                </div>

                                <div class="card card-outline card-success">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-flag text-success"></i> Objetivos Estratégicos</h3>
                                    </div>
                                    <div class="card-body">
                                        <textarea name="objetivos" class="form-control" rows="6" 
                                                  placeholder="- Ser referentes en el mercado..."><?= htmlspecialchars($config['objetivos'] ?? '') ?></textarea>
                                        <small class="form-text text-muted">Usa el símbolo <code>-</code> al inicio de cada línea.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB: IMAGEN QUIÉNES SOMOS -->
                            <div class="tab-pane fade" id="tab-nosotros-img">
                                <div class="callout callout-info">
                                    <h5><i class="fas fa-image"></i> Imagen de Portada</h5>
                                    <p class="mb-0">Esta imagen se usará como fondo del encabezado en la página <code>nosotros.php</code>.</p>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Imagen de Portada</label>
                                            <div class="custom-file">
                                                <input type="file" name="imagen_nosotros" class="custom-file-input" id="imgNosotros" accept="image/*">
                                                <label class="custom-file-label" for="imgNosotros">Seleccionar imagen...</label>
                                            </div>
                                            <small class="form-text text-muted">Recomendado: 1920x600px. JPG, PNG o WEBP. Máx. 5MB.</small>
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
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: CONTACTO + DATOS BANCARIOS -->
                            <div class="tab-pane fade" id="tab-contacto">
                                
                                <h5 class="mb-3"><i class="fas fa-address-book text-primary"></i> Información de Contacto</h5>
                                
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
                                        <small class="form-text text-muted">Incluir código de país</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label><i class="fas fa-envelope text-info"></i> Correo</label>
                                        <input type="email" name="correo" class="form-control" 
                                            value="<?= htmlspecialchars($config['correo'] ?? '') ?>"
                                            placeholder="contacto@empresa.com">
                                    </div>
                                </div>

                                <!-- DATOS BANCARIOS -->
                                <hr class="my-4">
                                
                                <?php 
                                $datos_bancarios = [
                                    'beneficiario' => '',
                                    'banco' => '',
                                    'clabe' => '',
                                    'tarjeta' => '',
                                    'link_pago' => ''
                                ];

                                if (!empty($config['datos_bancarios'])) {
                                    $decoded = json_decode($config['datos_bancarios'], true);
                                    
                                    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                                        $clean_json = html_entity_decode($config['datos_bancarios'], ENT_QUOTES, 'UTF-8');
                                        $decoded = json_decode($clean_json, true);
                                    }
                                    
                                    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                                        $clean_json = stripslashes(html_entity_decode($config['datos_bancarios'], ENT_QUOTES, 'UTF-8'));
                                        $decoded = json_decode($clean_json, true);
                                    }
                                    
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $datos_bancarios = array_merge($datos_bancarios, $decoded);
                                    }
                                }
                                ?>
                                
                                <h5 class="mb-3"><i class="fas fa-university text-info"></i> Datos Bancarios para Donaciones</h5>
                                <p class="text-muted small mb-3">Estos datos se mostrarán cuando los usuarios hagan clic en "Hacer una donación"</p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Nombre del Beneficiario</label>
                                            <input type="text" name="banco_beneficiario" class="form-control" 
                                                value="<?= htmlspecialchars($datos_bancarios['beneficiario'] ?? '') ?>"
                                                placeholder="Ej. Asociación Protectora de Animales AC">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Banco</label>
                                            <input type="text" name="banco_nombre" class="form-control" 
                                                value="<?= htmlspecialchars($datos_bancarios['banco'] ?? '') ?>"
                                                placeholder="Ej. BBVA México">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>CLABE / Número de Cuenta</label>
                                            <input type="text" name="banco_clabe" class="form-control" 
                                                value="<?= htmlspecialchars($datos_bancarios['clabe'] ?? '') ?>"
                                                placeholder="Ej. 012345678901234567">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label>Número de Tarjeta (opcional)</label>
                                            <input type="text" name="banco_tarjeta" class="form-control" 
                                                value="<?= htmlspecialchars($datos_bancarios['tarjeta'] ?? '') ?>"
                                                placeholder="Ej. **** **** **** 1234">
                                            <small class="text-muted">Últimos 4 dígitos para referencia</small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label>URL de PayPal / MercadoPago / Stripe (opcional)</label>
                                            <input type="url" name="banco_link_pago" class="form-control" 
                                                value="<?= htmlspecialchars($datos_bancarios['link_pago'] ?? '') ?>"
                                                placeholder="https://paypal.me/tu-organizacion">
                                            <small class="text-muted">Enlace directo a tu página de pagos</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i> Estos datos se mostrarán en un modal cuando los usuarios hagan clic en "Hacer una donación" en la página de Causas Sociales.
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
                                    <div class="col-md-6 mb-3">
                                        <label><i class="fab fa-linkedin text-primary"></i> LinkedIn</label>
                                        <input type="url" name="linkedin" class="form-control" 
                                               value="<?= htmlspecialchars($config['linkedin'] ?? '') ?>"
                                               placeholder="https://linkedin.com/company/tuempresa">
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 5: UBICACIÓN -->
                            <div class="tab-pane fade" id="tab-ubicacion">
                                <div class="callout callout-warning">
                                    <h5><i class="fab fa-google"></i> Cómo obtener el código de Google Maps</h5>
                                    <ol class="mb-0">
                                        <li>Ve a <a href="https://www.google.com/maps" target="_blank">Google Maps</a></li>
                                        <li>Busca la dirección de tu empresa</li>
                                        <li>Haz clic en <strong>"Compartir"</strong> → <strong>"Insertar mapa"</strong></li>
                                        <li>Copia el código HTML (empieza con <code>&lt;iframe</code>)</li>
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
                                    <p class="mb-0">Usa el formato <code>9:00 - 18:00</code> o escribe <code>Cerrado</code>.</p>
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

                            <!-- TAB 7: ESTILOS / COLORES -->
                            <div class="tab-pane fade" id="tab-estilos">
                                <div class="callout callout-info">
                                    <h5><i class="fas fa-palette"></i> Personalización de Colores</h5>
                                    <p class="mb-0">Estos colores se aplicarán automáticamente a todo el sitio público (botones, enlaces, fondos, etc.). Los cambios se verán reflejados inmediatamente.</p>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label><i class="fas fa-circle text-success"></i> Color Primario</label>
                                            <div class="input-group">
                                                <input type="color" name="color_primario" class="form-control form-control-color" 
                                                    value="<?= htmlspecialchars($config['color_primario'] ?? '#28a745') ?>" title="Elige un color">
                                                <input type="text" class="form-control" value="<?= htmlspecialchars($config['color_primario'] ?? '#28a745') ?>" readonly>
                                            </div>
                                            <small class="text-muted">Botones, enlaces, destacados, iconos principales</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label><i class="fas fa-circle text-info"></i> Color Secundario</label>
                                            <div class="input-group">
                                                <input type="color" name="color_secundario" class="form-control form-control-color" 
                                                    value="<?= htmlspecialchars($config['color_secundario'] ?? '#20c997') ?>" title="Elige un color">
                                                <input type="text" class="form-control" value="<?= htmlspecialchars($config['color_secundario'] ?? '#20c997') ?>" readonly>
                                            </div>
                                            <small class="text-muted">Hover, degradados, detalles secundarios</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label><i class="fas fa-font"></i> Color de Texto Principal</label>
                                            <div class="input-group">
                                                <input type="color" name="color_texto" class="form-control form-control-color" 
                                                    value="<?= htmlspecialchars($config['color_texto'] ?? '#333333') ?>" title="Elige un color">
                                                <input type="text" class="form-control" value="<?= htmlspecialchars($config['color_texto'] ?? '#333333') ?>" readonly>
                                            </div>
                                            <small class="text-muted">Color del texto general del sitio</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label><i class="fas fa-fill-drip"></i> Color de Fondo General</label>
                                            <div class="input-group">
                                                <input type="color" name="color_fondo" class="form-control form-control-color" 
                                                    value="<?= htmlspecialchars($config['color_fondo'] ?? '#ffffff') ?>" title="Elige un color">
                                                <input type="text" class="form-control" value="<?= htmlspecialchars($config['color_fondo'] ?? '#ffffff') ?>" readonly>
                                            </div>
                                            <small class="text-muted">Fondo principal de la página</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-success mt-3">
                                    <i class="fas fa-lightbulb"></i> <strong>Consejo:</strong> Usa colores contrastantes para mejor legibilidad. El color primario debe destacar sobre el fondo.
                                </div>

                                <!-- Vista previa -->
                                <div class="card mt-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-eye"></i> Vista Previa</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-md-3 mb-3">
                                                <button class="btn btn-success btn-lg w-100">Botón Primario</button>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <button class="btn btn-outline-success btn-lg w-100">Botón Outline</button>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <span class="badge bg-success p-3">Badge de Ejemplo</span>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <a href="#" class="text-success h4">Enlace de Ejemplo</a>
                                            </div>
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

// Previsualización en tiempo real del código hex
document.querySelectorAll('input[type="color"]').forEach(input => {
    input.addEventListener('input', function() {
        this.nextElementSibling.value = this.value;
    });
});
</script>

<?php include '../../includes/footer.php'; ?>