<?php

// Configurar zona horaria de México
date_default_timezone_set('America/Mexico_City');

include 'includes/web_header.php';
include 'includes/web_menu.php';

if (!isset($config)) {
    require_once 'includes/config_loader.php';
}

// Función para verificar si la EMPRESA PRINCIPAL está abierta ahora
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

// NUEVA FUNCIÓN: Verificar si una SUCURSAL específica está abierta ahora
function sucursalAbiertaAhora($horarios_json) {
    $horarios = json_decode($horarios_json ?? '{}', true);
    if (!is_array($horarios) || empty($horarios)) return false;
    
    $dia_actual_num = (int)date('N'); // 1=Lunes, 7=Domingo
    $dias_map = [
        1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
        4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
    ];
    
    $dia_nombre = $dias_map[$dia_actual_num];
    $horario_hoy = trim($horarios[$dia_nombre] ?? 'Cerrado');
    
    if (strtolower($horario_hoy) === 'cerrado' || empty($horario_hoy)) {
        return false;
    }
    
    // Buscar patrón de horario (ej: "9:00 - 18:00" o "09:00-18:00")
    if (preg_match('/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/', $horario_hoy, $matches)) {
        $apertura = $matches[1];
        $cierre = $matches[2];
        $hora_actual = date('H:i');
        
        $apertura_fmt = str_pad(str_replace(':', '', $apertura), 4, '0', STR_PAD_LEFT);
        $cierre_fmt = str_pad(str_replace(':', '', $cierre), 4, '0', STR_PAD_LEFT);
        $actual_fmt = str_pad(str_replace(':', '', $hora_actual), 4, '0', STR_PAD_LEFT);
        
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
    
    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje_texto)) {
        $error_envio = 'Por favor completa todos los campos obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_envio = 'Por favor ingresa un correo electrónico válido.';
    } else {
        try {
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
            
            $mensaje_enviado = true;
            $_POST = [];
            
        } catch (PDOException $e) {
            $error_envio = 'Error al guardar el mensaje. Por favor intenta de nuevo.';
        }
    }
}
?>

<!-- ====== ESTILOS PERSONALIZADOS ====== -->
<style>
    /* ====== HERO DE CONTACTO ====== */
    .contact-hero {
        background: linear-gradient(135deg, var(--color-primario), var(--color-secundario)),
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
        background: linear-gradient(90deg, var(--color-primario), var(--color-secundario));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }
    .contact-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(var(--rgb-primario), 0.15);
    }
    .contact-card:hover::before {
        transform: scaleX(1);
    }
    .contact-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, var(--color-primario), var(--color-secundario));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        transition: all 0.4s ease;
        box-shadow: 0 10px 25px rgba(var(--rgb-primario), 0.3);
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
        color: var(--color-primario);
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
        border-color: var(--color-primario);
        box-shadow: 0 0 0 0.2rem rgba(var(--rgb-primario), 0.25);
    }
    .form-label {
        font-weight: 600;
        color: #495057;
    }
    .btn-submit {
        background: linear-gradient(135deg, var(--color-primario), var(--color-secundario));
        border: none;
        color: white;
        padding: 14px 40px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(var(--rgb-primario), 0.3);
    }
    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(var(--rgb-primario), 0.5);
        color: white;
    }

    /* ====== BADGE DE ESTADO EN SUCURSALES ====== */
    .sucursal-status-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 5;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .sucursal-status-badge.abierta {
        background: #28a745;
        color: white;
    }
    .sucursal-status-badge.cerrada {
        background: #dc3545;
        color: white;
    }
    .sucursal-status-badge .status-dot-mini {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: white;
        animation: pulse 2s infinite;
    }

    /* ====== TARJETAS DE SUCURSALES ====== */
    .sucursal-card { 
        transition: transform 0.3s ease, box-shadow 0.3s ease; 
        position: relative;
    }
    .sucursal-card:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important; 
    }
    .horario-item { 
        display: flex; 
        justify-content: space-between; 
        font-size: 0.85rem; 
        padding: 2px 0; 
        border-bottom: 1px dashed #e9ecef; 
    }
    .horario-item:last-child { border-bottom: none; }
    .horario-dia { font-weight: 600; color: #495057; }
    .horario-hora { color: #28a745; font-weight: 500; }
    .horario-cerrado { color: #dc3545; font-weight: 500; }
    .horario-item.hoy {
        background: rgba(40, 167, 69, 0.08);
        padding: 4px 8px;
        border-radius: 5px;
        margin: 0 -8px;
        border-bottom: none;
    }
    .horario-item.hoy .horario-dia {
        color: var(--color-primario);
        font-weight: 700;
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
        background: linear-gradient(135deg, rgba(var(--rgb-primario), 0.1), rgba(var(--rgb-secundario), 0.1));
        color: #2c3e50;
        font-weight: 600;
    }
    .accordion-button:focus {
        box-shadow: 0 0 0 0.2rem rgba(var(--rgb-primario), 0.25);
    }

    /* ====== RESPONSIVE ====== */
    @media (max-width: 768px) {
        .contact-hero h1 { font-size: 2.2rem; }
        .contact-hero p { font-size: 1rem; }
        .form-container { padding: 30px 20px; }
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
                    Abierto (revisar los horarios de nuestras sucursales)
                </span>
            <?php else: ?>
                <span class="status-badge cerrado">
                    <span class="status-dot cerrado"></span>
                    Cerrado ((revisar los horarios de nuestras sucursales))
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
                    <a href="#sucursales">Ver sucursales</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== FORMULARIO (AHORA A ANCHO COMPLETO) ====== -->
<section class="form-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10" data-aos="fade-up">
                <div class="form-container">
                    <div class="text-center mb-4">
                        <h3><i class="fas fa-paper-plane text-success me-2"></i>Envíanos un mensaje</h3>
                        <p class="text-muted">Completa el formulario y te responderemos a la brevedad.</p>
                    </div>

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
        </div>
    </div>
</section>

<!-- ====== SUCURSALES (CON ESTADO ABIERTO/CERRADO) ====== -->
<section class="py-5" id="sucursales" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold" style="color: #2c3e50;">
                <i class="fas fa-map-marked-alt text-success me-2"></i>Nuestras Sucursales
            </h2>
            <p class="text-muted">Visítanos o contáctanos en cualquiera de nuestras ubicaciones</p>
        </div>

        <?php
        $stmt_suc = $pdo->query("
            SELECT * FROM sucursales 
            WHERE activo = 1 
            ORDER BY es_principal DESC, orden ASC
        ");
        $sucursales = $stmt_suc->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (empty($sucursales)): ?>
            <div class="text-center py-5 bg-white rounded-3 shadow-sm" data-aos="fade-up">
                <i class="fas fa-store-slash fa-4x text-muted mb-3"></i>
                <h3 class="text-muted">No hay sucursales registradas en este momento</h3>
                <p class="text-muted">Estamos actualizando nuestra información. ¡Vuelve pronto!</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($sucursales as $s): 
                    $tel_limpio = preg_replace('/[^0-9+]/', '', $s['telefono']);
                    $wa_limpio = preg_replace('/[^0-9]/', '', $s['whatsapp']);
                    $wa_link = !empty($wa_limpio) ? "https://wa.me/52{$wa_limpio}" : (!empty($tel_limpio) ? "https://wa.me/52{$tel_limpio}" : "#");
                    
                    // Decodificar el horario semanal
                    $horarios = json_decode($s['horarios_semanales'] ?? '{}', true) ?: [];
                    $dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                    
                    // Verificar si esta sucursal está abierta ahora
                    $sucursal_abierta = sucursalAbiertaAhora($s['horarios_semanales']);
                    $dia_actual_num = (int)date('N');

                ?>


                    <div class="col-md-6 col-lg-4" data-aos="fade-up">
                        <div class="card h-100 border-0 shadow-sm sucursal-card" style="border-radius: 15px; overflow: hidden;">
                            
                            <!-- BADGE DE ESTADO (ABIERTO/CERRADO) -->
                            <?php if ($sucursal_abierta): ?>
                                <span class="sucursal-status-badge abierta">
                                    <span class="status-dot-mini"></span>
                                    Abierto ahora
                                </span>
                            <?php else: ?>
                                <span class="sucursal-status-badge cerrada">
                                    <span class="status-dot-mini"></span>
                                    Cerrado ahora
                                </span>
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column p-4">
                                
                                <!-- Encabezado -->
                                <div class="d-flex justify-content-between align-items-start mb-3" style="padding-right: 110px;">
                                    <h4 class="card-title fw-bold mb-0 text-dark" style="font-size: 1.2rem;">
                                        <i class="fas fa-store text-success me-2"></i>
                                        <?= htmlspecialchars($s['nombre']) ?>
                                    </h4>
                                    <?php if ($s['es_principal']): ?>
                                        <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Principal</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Dirección -->
                                <div class="mb-3">
                                    <p class="mb-1 text-muted small text-uppercase fw-bold" style="letter-spacing: 1px; font-size: 0.75rem;">Dirección</p>
                                    <p class="mb-0 text-dark" style="font-size: 0.9rem;">
                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                        <?= nl2br(htmlspecialchars($s['direccion'])) ?>
                                    </p>
                                </div>

                                <!-- Horario Semanal CON DÍA ACTUAL RESALTADO -->
                                <div class="mb-3 bg-light p-3 rounded">
                                    <p class="mb-2 text-muted small text-uppercase fw-bold" style="letter-spacing: 1px; font-size: 0.75rem;">
                                        <i class="fas fa-clock text-warning me-1"></i> Horario
                                    </p>
                                    <div class="horarios-lista">
                                        <?php foreach ($dias_semana as $index => $dia): 
                                            $hora = trim($horarios[$dia] ?? 'Cerrado');
                                            $es_cerrado = (strtolower($hora) === 'cerrado' || empty($hora));
                                            $es_hoy = (($index + 1) === $dia_actual_num);
                                        ?>
                                            <div class="horario-item <?= $es_hoy ? 'hoy' : '' ?>">
                                                <span class="horario-dia">
                                                    <?= $dia ?>
                                                    <?php if ($es_hoy): ?>
                                                        <small class="text-muted">(Hoy)</small>
                                                    <?php endif; ?>
                                                </span>
                                                <span class="<?= $es_cerrado ? 'horario-cerrado' : 'horario-hora' ?>">
                                                    <?= $es_cerrado ? 'Cerrado' : htmlspecialchars($hora) ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Contacto -->
                                <div class="mb-3">
                                    <?php if (!empty($s['telefono'])): ?>
                                        <p class="mb-2" style="font-size: 0.9rem;">
                                            <i class="fas fa-phone text-success me-2"></i>
                                            <a href="tel:<?= $tel_limpio ?>" class="text-decoration-none text-dark fw-medium"><?= htmlspecialchars($s['telefono']) ?></a>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($s['whatsapp'])): ?>
                                        <p class="mb-0" style="font-size: 0.9rem;">
                                            <i class="fab fa-whatsapp text-success me-2"></i>
                                            <a href="<?= $wa_link ?>" target="_blank" class="text-decoration-none text-success fw-medium"><?= htmlspecialchars($s['whatsapp']) ?></a>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Separador visual -->
                                <hr class="my-3">

                                <!-- Mapa o Botón de respaldo -->
                                <div class="mt-auto">
                                    <?php if (!empty($s['google_maps'])): ?>
                                        <div class="ratio ratio-16x9 rounded overflow-hidden border">
                                            <?= $s['google_maps'] ?>
                                        </div>
                                    <?php else: ?>
                                        <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($s['direccion']) ?>" 
                                           target="_blank" 
                                           class="btn btn-outline-success w-100 btn-sm" 
                                           style="border-radius: 50px; padding: 10px; font-size: 0.85rem;">
                                            <i class="fas fa-directions me-2"></i> Ver en Google Maps
                                        </a>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
<section class="py-5" style="background: linear-gradient(135deg, var(--color-primario), var(--color-secundario)); color: white;">
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