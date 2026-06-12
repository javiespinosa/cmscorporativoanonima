<?php
include 'includes/web_header.php';
include 'includes/web_menu.php';

if (!isset($config)) {
    require_once 'includes/config_loader.php';
}

// Función para verificar si está abierto ahora
function estaAbiertoAhora($config) {
    $dia_semana = date('N');
    $hora_actual = date('H:i');
    
    $dias_map = [
        1 => 'horario_lunes', 2 => 'horario_martes', 3 => 'horario_miercoles',
        4 => 'horario_jueves', 5 => 'horario_viernes', 6 => 'horario_sabado', 7 => 'horario_domingo'
    ];
    
    $campo = $dias_map[$dia_semana];
    $horario_hoy = $config[$campo] ?? 'Cerrado';
    
    if (strtolower($horario_hoy) === 'cerrado' || empty($horario_hoy)) {
        return false;
    }
    
    if (preg_match('/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/', $horario_hoy, $matches)) {
        $apertura = $matches[1];
        $cierre = $matches[2];
        $hora_actual_fmt = date('H:i');
        $apertura_fmt = str_pad(str_replace(':', '', $apertura), 4, '0', STR_PAD_LEFT);
        $cierre_fmt = str_pad(str_replace(':', '', $cierre), 4, '0', STR_PAD_LEFT);
        $actual_fmt = str_pad(str_replace(':', '', $hora_actual_fmt), 4, '0', STR_PAD_LEFT);
        
        return ($actual_fmt >= $apertura_fmt && $actual_fmt <= $cierre_fmt);
    }
    
    return false;
}

$abierto_ahora = estaAbiertoAhora($config);
$mensaje_enviado = false;
$error_envio = '';

// PROCESAR FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_contacto'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono_form'] ?? '');
    $empresa = trim($_POST['empresa_form'] ?? '');
    $asunto = trim($_POST['asunto'] ?? '');
    $mensaje_texto = trim($_POST['mensaje'] ?? '');
    
    // Validaciones
    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje_texto)) {
        $error_envio = 'Por favor completa todos los campos obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_envio = 'Por favor ingresa un correo electrónico válido.';
    } else {
        try {
            // Guardar en base de datos
            $stmt = $pdo->prepare("
                INSERT INTO mensajes_contacto 
                (nombre, email, telefono, empresa, asunto, mensaje, leido) 
                VALUES (?, ?, ?, ?, ?, ?, 0)
            ");
            
            $stmt->execute([
                $nombre,
                $email,
                $telefono,
                $empresa,
                $asunto,
                $mensaje_texto
            ]);
            
            // Opcional: Enviar email de notificación al administrador
            $para = $config['correo']; // Tu correo desde configuración
            $asunto_email = "Nuevo mensaje de contacto - " . $nombre;
            $cuerpo = "
                <h2>Nuevo mensaje desde el sitio web</h2>
                <p><strong>Nombre:</strong> $nombre</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Teléfono:</strong> " . ($telefono ?: 'No especificado') . "</p>
                <p><strong>Empresa:</strong> " . ($empresa ?: 'No especificada') . "</p>
                <p><strong>Asunto:</strong> $asunto</p>
                <p><strong>Mensaje:</strong><br>$mensaje_texto</p>
            ";
            
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: no-reply@tudominio.com\r\n";
            $headers .= "Reply-To: $email\r\n";
            
            // Descomenta la siguiente línea para enviar email real
            // mail($para, $asunto_email, $cuerpo, $headers);
            
            $mensaje_enviado = true;
            
            // Limpiar POST para que no se muestren los datos al recargar
            $_POST = [];
            
        } catch (PDOException $e) {
            $error_envio = 'Error al guardar el mensaje. Por favor intenta de nuevo.';
            // Para debugging: error_log($e->getMessage());
        }
    }
}
?>

<!-- ====== ESTILOS PERSONALIZADOS ====== -->
<style>
    /* ====== HERO DE CONTACTO ====== */
    .contact-hero {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.9), rgba(32, 201, 151, 0.85)),
                    url('https://images.unsplash.com/photo-1423666639041-f56000c27a9a?w=1600') center/cover;
        color: white;
        padding: 100px 0 80px;
        position: relative;
        overflow: hidden;
    }
    .contact-hero::before {
        content: '';
        position: absolute;
        bottom: -50px;
        left: 0;
        right: 0;
        height: 100px;
        background: white;
        transform: skewY(-3deg);
        transform-origin: bottom left;
    }
    .contact-hero h1 {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 15px;
        text-shadow: 2px 2px 10px rgba(0,0,0,0.2);
    }
    .contact-hero p {
        font-size: 1.3rem;
        opacity: 0.95;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 10px 25px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        margin-top: 20px;
        backdrop-filter: blur(10px);
    }
    .status-badge.abierto {
        background: rgba(255,255,255,0.25);
        border: 2px solid #28a745;
    }
    .status-badge.cerrado {
        background: rgba(255,255,255,0.25);
        border: 2px solid #dc3545;
    }
    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 10px;
        animation: pulse 2s infinite;
    }
    .status-dot.abierto { background: #28a745; }
    .status-dot.cerrado { background: #dc3545; }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(1.2); }
    }

    /* ====== TARJETAS DE CONTACTO ====== */
    .contact-card {
        background: white;
        border-radius: 20px;
        padding: 35px 25px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.4s ease;
        height: 100%;
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }
    .contact-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #28a745, #20c997);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }
    .contact-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }
    .contact-card:hover::before {
        transform: scaleX(1);
    }
    .contact-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #28a745, #20c997);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        transition: all 0.4s ease;
        box-shadow: 0 10px 25px rgba(40,167,69,0.3);
    }
    .contact-card:hover .contact-icon {
        transform: rotateY(360deg);
    }
    .contact-card h5 {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    .contact-card a {
        color: #6c757d;
        text-decoration: none;
        transition: color 0.3s;
        word-break: break-word;
    }
    .contact-card a:hover {
        color: #28a745;
    }

    /* ====== FORMULARIO ====== */
    .form-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 80px 0;
    }
    .form-container {
        background: white;
        border-radius: 20px;
        padding: 50px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    }
    .form-container h3 {
        font-weight: 800;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40,167,69,0.25);
    }
    .form-label {
        font-weight: 600;
        color: #495057;
    }
    .btn-submit {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        color: white;
        padding: 14px 40px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(40,167,69,0.3);
    }
    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(40,167,69,0.5);
        color: white;
    }

    /* ====== HORARIOS VISUALES ====== */
    .schedule-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        height: 100%;
    }
    .schedule-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        border-radius: 10px;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }
    .schedule-item:hover {
        background: #f8f9fa;
        transform: translateX(5px);
    }
    .schedule-item.hoy {
        background: linear-gradient(135deg, rgba(40,167,69,0.1), rgba(32,201,151,0.1));
        border: 2px solid #28a745;
        font-weight: 700;
    }
    .schedule-item.cerrado-dia {
        opacity: 0.6;
    }
    .schedule-day {
        color: #2c3e50;
        font-weight: 600;
    }
    .schedule-time {
        color: #6c757d;
    }
    .schedule-item.hoy .schedule-day,
    .schedule-item.hoy .schedule-time {
        color: #28a745;
    }
    .schedule-item.cerrado-dia .schedule-time {
        color: #dc3545;
    }

    /* ====== MAPA ====== */
    .map-container {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        height: 100%;
        min-height: 400px;
    }
    .map-container iframe {
        width: 100%;
        height: 100%;
        min-height: 400px;
        border: 0;
    }

    /* ====== REDES SOCIALES ====== */
    .social-section {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        padding: 60px 0;
        color: white;
    }
    .social-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        color: white;
        font-size: 1.5rem;
        margin: 0 8px;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    .social-btn:hover {
        transform: translateY(-5px) scale(1.1);
        color: white;
    }
    .social-btn.facebook { background: #1877F2; box-shadow: 0 5px 15px rgba(24,119,242,0.4); }
    .social-btn.instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); box-shadow: 0 5px 15px rgba(225,48,108,0.4); }
    .social-btn.tiktok { background: #000000; box-shadow: 0 5px 15px rgba(0,0,0,0.4); }
    .social-btn.youtube { background: #FF0000; box-shadow: 0 5px 15px rgba(255,0,0,0.4); }
    .social-btn.linkedin { background: #0A66C2; box-shadow: 0 5px 15px rgba(10,102,194,0.4); }

    /* ====== FAQ ====== */
    .faq-section {
        padding: 80px 0;
        background: white;
    }
    .accordion-button:not(.collapsed) {
        background: linear-gradient(135deg, rgba(40,167,69,0.1), rgba(32,201,151,0.1));
        color: #2c3e50;
        font-weight: 600;
    }
    .accordion-button:focus {
        box-shadow: 0 0 0 0.2rem rgba(40,167,69,0.25);
    }

    /* ====== RESPONSIVE ====== */
    @media (max-width: 768px) {
        .contact-hero h1 { font-size: 2.2rem; }
        .contact-hero p { font-size: 1rem; }
        .form-container { padding: 30px 20px; }
        .schedule-card, .map-container { margin-bottom: 30px; }
    }
</style>

<!-- ====== HERO DE CONTACTO ====== -->
<section class="contact-hero text-center">
    <div class="container position-relative" style="z-index: 2;">
        <h1 data-aos="fade-down">Contáctanos</h1>
        <p data-aos="fade-up" data-aos-delay="200">
            Estamos listos para atenderte y resolver todas tus dudas
        </p>
        <div data-aos="zoom-in" data-aos-delay="400">
            <?php if ($abierto_ahora): ?>
                <span class="status-badge abierto">
                    <span class="status-dot abierto"></span>
                    Abierto ahora
                </span>
            <?php else: ?>
                <span class="status-badge cerrado">
                    <span class="status-dot cerrado"></span>
                    Cerrado ahora
                </span>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ====== TARJETAS DE CONTACTO RÁPIDO ====== -->
<section class="py-5" style="margin-top: -40px; position: relative; z-index: 3;">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="100">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h5>Teléfono</h5>
                    <?php if (!empty($config['telefono'])): ?>
                        <a href="tel:<?= preg_replace('/[^0-9+]/', '', $config['telefono']) ?>">
                            <?= htmlspecialchars($config['telefono']) ?>
                        </a>
                    <?php else: ?>
                        <p class="text-muted mb-0">No disponible</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="200">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h5>WhatsApp</h5>
                    <?php if (!empty($config['whatsapp'])): ?>
                        <a href="https://wa.me/52<?= preg_replace('/[^0-9]/', '', $config['whatsapp']) ?>" target="_blank">
                            <?= htmlspecialchars($config['whatsapp']) ?>
                        </a>
                    <?php else: ?>
                        <p class="text-muted mb-0">No disponible</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="300">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h5>Correo</h5>
                    <?php if (!empty($config['correo'])): ?>
                        <a href="mailto:<?= htmlspecialchars($config['correo']) ?>">
                            <?= htmlspecialchars($config['correo']) ?>
                        </a>
                    <?php else: ?>
                        <p class="text-muted mb-0">No disponible</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="400">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h5>Ubicación</h5>
                    <a href="#mapa">Ver en mapa</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== FORMULARIO + HORARIO ====== -->
<section class="form-section">
    <div class="container">
        <div class="row g-5 align-items-stretch">
            
            <!-- FORMULARIO DE CONTACTO -->
            <div class="col-lg-7" data-aos="fade-right">
                <div class="form-container h-100">
                    <h3><i class="fas fa-paper-plane text-success me-2"></i>Envíanos un mensaje</h3>
                    <p class="text-muted mb-4">Completa el formulario y te responderemos a la brevedad.</p>

                    <?php if ($mensaje_enviado): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>¡Mensaje enviado con éxito!</strong> Nos pondremos en contacto contigo pronto.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_envio)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars($error_envio) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="contacto.php">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control form-control-lg" 
                                       placeholder="Ej. Juan Pérez" required
                                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-lg" 
                                       placeholder="ejemplo@correo.com" required
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" name="telefono_form" class="form-control form-control-lg" 
                                       placeholder="55 1234 5678"
                                       value="<?= htmlspecialchars($_POST['telefono_form'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Empresa</label>
                                <input type="text" name="empresa_form" class="form-control form-control-lg" 
                                       placeholder="Nombre de tu empresa"
                                       value="<?= htmlspecialchars($_POST['empresa_form'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Asunto <span class="text-danger">*</span></label>
                                <select name="asunto" class="form-select form-select-lg" required>
                                    <option value="">Selecciona una opción</option>
                                    <option value="cotizacion" <?= ($_POST['asunto'] ?? '') === 'cotizacion' ? 'selected' : '' ?>>Solicitar cotización</option>
                                    <option value="informacion" <?= ($_POST['asunto'] ?? '') === 'informacion' ? 'selected' : '' ?>>Información de productos</option>
                                    <option value="soporte" <?= ($_POST['asunto'] ?? '') === 'soporte' ? 'selected' : '' ?>>Soporte técnico</option>
                                    <option value="otro" <?= ($_POST['asunto'] ?? '') === 'otro' ? 'selected' : '' ?>>Otro</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Mensaje <span class="text-danger">*</span></label>
                                <textarea name="mensaje" class="form-control" rows="5" 
                                          placeholder="Escribe tu mensaje aquí..." required><?= htmlspecialchars($_POST['mensaje'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="privacidad" required>
                                    <label class="form-check-label small text-muted" for="privacidad">
                                        Acepto la política de privacidad y el tratamiento de mis datos.
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 text-center mt-4">
                                <button type="submit" name="enviar_contacto" class="btn btn-submit">
                                    <i class="fas fa-paper-plane me-2"></i> Enviar mensaje
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- HORARIO -->
            <div class="col-lg-5" data-aos="fade-left">
                <div class="schedule-card">
                    <h3 class="mb-4 text-center">
                        <i class="fas fa-clock text-success me-2"></i>Horario de Atención
                    </h3>

                    <?php
                    $dias_completos = [
                        1 => ['nombre' => 'Lunes', 'campo' => 'horario_lunes'],
                        2 => ['nombre' => 'Martes', 'campo' => 'horario_martes'],
                        3 => ['nombre' => 'Miércoles', 'campo' => 'horario_miercoles'],
                        4 => ['nombre' => 'Jueves', 'campo' => 'horario_jueves'],
                        5 => ['nombre' => 'Viernes', 'campo' => 'horario_viernes'],
                        6 => ['nombre' => 'Sábado', 'campo' => 'horario_sabado'],
                        7 => ['nombre' => 'Domingo', 'campo' => 'horario_domingo']
                    ];
                    
                    $dia_actual = (int)date('N');
                    
                    foreach ($dias_completos as $num => $info):
                        $horario = $config[$info['campo']] ?? 'Cerrado';
                        $es_hoy = ($num === $dia_actual);
                        $es_cerrado = (strtolower($horario) === 'cerrado' || empty($horario));
                        
                        $clases = 'schedule-item';
                        if ($es_hoy) $clases .= ' hoy';
                        if ($es_cerrado) $clases .= ' cerrado-dia';
                    ?>
                        <div class="<?= $clases ?>">
                            <span class="schedule-day">
                                <?php if ($es_hoy): ?>
                                    <i class="fas fa-calendar-check text-success me-2"></i>
                                <?php endif; ?>
                                <?= $info['nombre'] ?>
                                <?php if ($es_hoy): ?>
                                    <span class="badge bg-success ms-2">HOY</span>
                                <?php endif; ?>
                            </span>
                            <span class="schedule-time">
                                <?php if ($es_cerrado): ?>
                                    <i class="fas fa-times-circle text-danger me-1"></i> Cerrado
                                <?php else: ?>
                                    <i class="fas fa-clock text-success me-1"></i> <?= htmlspecialchars($horario) ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-2 text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Los horarios pueden variar en días festivos
                        </p>
                        <?php if (!empty($config['whatsapp'])): ?>
                            <a href="https://wa.me/52<?= preg_replace('/[^0-9]/', '', $config['whatsapp']) ?>" 
                               target="_blank" 
                               class="btn btn-success mt-2" 
                               style="border-radius: 50px; padding: 10px 25px;">
                                <i class="fab fa-whatsapp me-2"></i> Consultar disponibilidad
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== MAPA ====== -->
<section class="py-5" id="mapa">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold" style="color: #2c3e50;">
                <i class="fas fa-map-marked-alt text-success me-2"></i>Encuéntranos
            </h2>
            <p class="text-muted">Visítanos en nuestras instalaciones</p>
        </div>

        <div class="row g-4 align-items-stretch">
            <div class="col-lg-8" data-aos="fade-right">
                <div class="map-container">
                    <?php if (!empty($config['google_maps'])): ?>
                        <?= $config['google_maps'] ?>
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                            <div class="text-center text-muted p-5">
                                <i class="fas fa-map-marked-alt fa-4x mb-3"></i>
                                <h5>Ubicación no configurada</h5>
                                <p>El administrador debe configurar el mapa desde el panel de control.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-left">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">
                            <i class="fas fa-building text-success me-2"></i><?= htmlspecialchars($config['empresa']) ?>
                        </h4>

                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase small mb-2">
                                <i class="fas fa-map-pin me-1"></i> Dirección
                            </h6>
                            <p class="mb-0">
                                <?= nl2br(htmlspecialchars($config['direccion'] ?? 'No especificada')) ?>
                            </p>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase small mb-2">
                                <i class="fas fa-phone me-1"></i> Teléfonos
                            </h6>
                            <?php if (!empty($config['telefono'])): ?>
                                <p class="mb-1">
                                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $config['telefono']) ?>" 
                                       class="text-decoration-none">
                                        <?= htmlspecialchars($config['telefono']) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($config['whatsapp'])): ?>
                                <p class="mb-0">
                                    <a href="https://wa.me/52<?= preg_replace('/[^0-9]/', '', $config['whatsapp']) ?>" 
                                       target="_blank"
                                       class="text-decoration-none text-success">
                                        <i class="fab fa-whatsapp me-1"></i>
                                        <?= htmlspecialchars($config['whatsapp']) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase small mb-2">
                                <i class="fas fa-envelope me-1"></i> Correo
                            </h6>
                            <p class="mb-0">
                                <a href="mailto:<?= htmlspecialchars($config['correo'] ?? '') ?>" 
                                   class="text-decoration-none">
                                    <?= htmlspecialchars($config['correo'] ?? 'No especificado') ?>
                                </a>
                            </p>
                        </div>

                        <?php if (!empty($config['direccion'])): ?>
                            <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($config['direccion']) ?>" 
                               target="_blank" 
                               class="btn btn-outline-success w-100 mt-3" 
                               style="border-radius: 50px;">
                                <i class="fas fa-directions me-2"></i> Cómo llegar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== REDES SOCIALES ====== -->
<section class="social-section text-center">
    <div class="container" data-aos="fade-up">
        <h2 class="fw-bold mb-3">Síguenos en Redes Sociales</h2>
        <p class="mb-4 opacity-75">Mantente conectado con nosotros a través de nuestras redes</p>
        
        <div class="mt-4">
            <?php if (!empty($config['facebook'])): ?>
                <a href="<?= htmlspecialchars($config['facebook']) ?>" target="_blank" class="social-btn facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
            <?php endif; ?>
            
            <?php if (!empty($config['instagram'])): ?>
                <a href="<?= htmlspecialchars($config['instagram']) ?>" target="_blank" class="social-btn instagram">
                    <i class="fab fa-instagram"></i>
                </a>
            <?php endif; ?>
            
            <?php if (!empty($config['tiktok'])): ?>
                <a href="<?= htmlspecialchars($config['tiktok']) ?>" target="_blank" class="social-btn tiktok">
                    <i class="fab fa-tiktok"></i>
                </a>
            <?php endif; ?>
            
            <?php if (!empty($config['youtube'])): ?>
                <a href="<?= htmlspecialchars($config['youtube']) ?>" target="_blank" class="social-btn youtube">
                    <i class="fab fa-youtube"></i>
                </a>
            <?php endif; ?>
            
            <?php if (!empty($config['linkedin'])): ?>
                <a href="<?= htmlspecialchars($config['linkedin']) ?>" target="_blank" class="social-btn linkedin">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ====== PREGUNTAS FRECUENTES ====== -->
<section class="faq-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold" style="color: #2c3e50;">
                <i class="fas fa-question-circle text-success me-2"></i>Preguntas Frecuentes
            </h2>
            <p class="text-muted">Respuestas a las dudas más comunes</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="accordion" id="accordionFAQ">
                    
                    <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: 10px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                <i class="fas fa-file-invoice text-success me-2"></i>
                                ¿Cómo solicito una cotización?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted">
                                Puedes solicitar una cotización de dos formas: navegando por nuestro <a href="productos.php">catálogo de productos</a> y agregando los que te interesen a tu cotización, o llenando el formulario de contacto en esta página con los detalles de lo que necesitas.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: 10px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                <i class="fas fa-clock text-success me-2"></i>
                                ¿Cuánto tiempo tarda en llegar una cotización?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted">
                                Normalmente enviamos las cotizaciones en un plazo de 24 a 48 horas hábiles después de recibir tu solicitud. Para casos urgentes, te recomendamos contactarnos directamente por WhatsApp.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: 10px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                <i class="fas fa-truck text-success me-2"></i>
                                ¿Realizan envíos a todo el país?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted">
                                Sí, realizamos envíos a toda la República Mexicana a través de paqueterías certificadas. Los costos y tiempos de entrega varían según el destino. Consulta con nuestros asesores para más detalles.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: 10px; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                <i class="fas fa-credit-card text-success me-2"></i>
                                ¿Qué métodos de pago aceptan?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted">
                                Aceptamos transferencia bancaria, depósito en efectivo, tarjetas de crédito/débito y pagos en tiendas de conveniencia. Los detalles específicos se incluyen en cada cotización.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== CTA FINAL ====== -->
<section class="py-5" style="background: linear-gradient(135deg, #28a745, #20c997); color: white;">
    <div class="container text-center" data-aos="zoom-in">
        <h2 class="fw-bold mb-3">¿Listo para comenzar tu proyecto?</h2>
        <p class="lead mb-4 opacity-90">Contáctanos hoy y recibe asesoría personalizada sin compromiso</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <?php if (!empty($config['whatsapp'])): ?>
                <a href="https://wa.me/52<?= preg_replace('/[^0-9]/', '', $config['whatsapp']) ?>" 
                   target="_blank" 
                   class="btn btn-light btn-lg px-4" 
                   style="border-radius: 50px; font-weight: 600;">
                    <i class="fab fa-whatsapp me-2 text-success"></i> WhatsApp
                </a>
            <?php endif; ?>
            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $config['telefono'] ?? '') ?>" 
               class="btn btn-outline-light btn-lg px-4" 
               style="border-radius: 50px; font-weight: 600; border-width: 2px;">
                <i class="fas fa-phone me-2"></i> Llamar ahora
            </a>
        </div>
    </div>
</section>

<!-- AOS Script -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });
</script>

<?php include 'includes/web_footer.php'; ?>