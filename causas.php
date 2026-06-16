<?php
include 'includes/web_header.php';
include 'includes/web_menu.php';

if (!isset($config)) {
    require_once 'includes/config_loader.php';
}

// Obtener todas las causas activas
$causas = $pdo->query("
    SELECT * FROM causas_sociales 
    WHERE activo = 1 
    ORDER BY destacado DESC, fecha_creacion DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Calcular estadísticas totales
$total_stats = ['rescatados' => 0, 'alimentados' => 0, 'voluntarios' => 0, 'donaciones' => 0];
foreach ($causas as $c) {
    $stats = json_decode($c['estadisticas'] ?? '{}', true) ?: [];
    foreach ($total_stats as $key => $val) {
        $total_stats[$key] += (int)($stats[$key] ?? 0);
    }
}
?>

<style>
/* ====== HERO CAUSAS ====== */
.causas-hero {
    position: relative;
    height: 450px;
    background: linear-gradient(135deg, rgba(231, 76, 60, 0.9), rgba(192, 57, 43, 0.85)),
                url('https://images.unsplash.com/photo-1450778869180-41d0601e046e?w=1600') center/cover;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
    overflow: hidden;
}
.causas-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-image: 
        radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
    background-size: 100px 100px, 150px 150px;
    animation: particleFloat 30s linear infinite;
}
.causas-hero-content {
    position: relative;
    z-index: 2;
    max-width: 800px;
    padding: 0 20px;
}
.causas-hero h1 {
    font-size: 3.5rem;
    font-weight: 900;
    margin-bottom: 15px;
    text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
}
.causas-hero p {
    font-size: 1.3rem;
    opacity: 0.95;
}
.causas-hero .heart-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    animation: heartbeat 1.5s infinite;
}
@keyframes heartbeat {
    0%, 100% { transform: scale(1); }
    25% { transform: scale(1.1); }
    50% { transform: scale(1); }
    75% { transform: scale(1.15); }
}

/* ====== ESTADÍSTICAS TOTALES ====== */
.stats-causas-section {
    background: white;
    padding: 60px 0;
    margin-top: -60px;
    position: relative;
    z-index: 3;
}
.stats-causas-card {
    background: white;
    border-radius: 20px;
    padding: 40px 20px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    text-align: center;
    transition: all 0.4s ease;
    height: 100%;
}
.stats-causas-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 50px rgba(231, 76, 60, 0.2);
}
.stats-causas-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    box-shadow: 0 10px 25px rgba(231, 76, 60, 0.3);
}
.stats-causas-number {
    font-size: 2.8rem;
    font-weight: 900;
    color: #e74c3c;
    line-height: 1;
    margin-bottom: 10px;
}
.stats-causas-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-weight: 600;
}

/* ====== TARJETA DE CAUSA ====== */
.causa-card-full {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    transition: all 0.4s ease;
    margin-bottom: 40px;
}
.causa-card-full:hover {
    box-shadow: 0 20px 50px rgba(231, 76, 60, 0.15);
}
.causa-header-full {
    position: relative;
    height: 350px;
    overflow: hidden;
}
.causa-header-full img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}
.causa-card-full:hover .causa-header-full img {
    transform: scale(1.05);
}
.causa-header-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 40px 30px 30px;
    background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
    color: white;
}
.causa-header-overlay h2 {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 5px;
}
.causa-header-overlay p {
    opacity: 0.9;
    margin: 0;
}
.causa-tipo-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    color: white;
    padding: 8px 20px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}
.causa-body-full {
    padding: 40px;
}
.causa-body-full .descripcion {
    font-size: 1.05rem;
    line-height: 1.8;
    color: #555;
    margin-bottom: 30px;
    text-align: justify;
}

/* ====== ESTADÍSTICAS DE CAUSA ====== */
.causa-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    margin: 30px 0;
    padding: 30px;
    background: linear-gradient(135deg, #fff5f5, #ffe5e5);
    border-radius: 15px;
}
.causa-stat-item {
    text-align: center;
}
.causa-stat-item i {
    font-size: 2rem;
    color: #e74c3c;
    margin-bottom: 10px;
}
.causa-stat-number {
    font-size: 2.2rem;
    font-weight: 900;
    color: #e74c3c;
    line-height: 1;
    margin-bottom: 5px;
}
.causa-stat-label {
    font-size: 0.85rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

/* ====== GALERÍA ====== */
.causa-galeria {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    margin-top: 30px;
}
.causa-galeria-item {
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.causa-galeria-item:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.causa-galeria-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.causa-galeria-item:hover img {
    transform: scale(1.1);
}
.causa-galeria-overlay {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(231, 76, 60, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.causa-galeria-item:hover .causa-galeria-overlay {
    opacity: 1;
}

/* ====== MODAL LIGHTBOX ====== */
.modal-lightbox-causas .modal-content {
    background: transparent;
    border: none;
}
.modal-lightbox-causas .modal-body {
    padding: 0;
    text-align: center;
}
.modal-lightbox-causas img {
    max-width: 100%;
    max-height: 85vh;
    border-radius: 10px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}
.btn-close-lightbox-causas {
    position: absolute;
    top: -50px;
    right: 0;
    background: white;
    border: none;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    color: #e74c3c;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    z-index: 10;
    transition: all 0.3s ease;
}
.btn-close-lightbox-causas:hover {
    background: #e74c3c;
    color: white;
    transform: rotate(90deg);
}

/* ====== CTA FINAL ====== */
.cta-causas {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    padding: 80px 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.cta-causas::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-image: 
        radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
    background-size: 100px 100px, 150px 150px;
    animation: particleFloat 30s linear infinite;
}
.cta-causas-content {
    position: relative;
    z-index: 2;
}
.cta-causas h2 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 20px;
}
.cta-causas p {
    font-size: 1.2rem;
    opacity: 0.95;
    margin-bottom: 30px;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}
.btn-cta-causas {
    background: white;
    color: #e74c3c;
    padding: 15px 40px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1.1rem;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    margin: 0 10px;
}
.btn-cta-causas:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    color: #e74c3c;
}
.btn-cta-causas.outline {
    background: transparent;
    color: white;
    border: 2px solid white;
}
.btn-cta-causas.outline:hover {
    background: white;
    color: #e74c3c;
}

@media (max-width: 768px) {
    .causas-hero { height: 350px; }
    .causas-hero h1 { font-size: 2.2rem; }
    .causa-header-full { height: 250px; }
    .causa-header-overlay h2 { font-size: 1.5rem; }
    .causa-body-full { padding: 25px; }
}
</style>

<!-- ====== HERO ====== -->
<section class="causas-hero">
    <div class="causas-hero-content">
        <div class="heart-icon" data-aos="zoom-in">
            <i class="fas fa-heart"></i>
        </div>
        <h1 data-aos="fade-down">Nuestras Causas Sociales</h1>
        <p data-aos="fade-up" data-aos-delay="200">
            Porque creemos que juntos podemos hacer la diferencia
        </p>
    </div>
</section>

<!-- ====== ESTADÍSTICAS TOTALES ====== -->
<section class="stats-causas-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6" data-aos="fade-up">
                <div class="stats-causas-card">
                    <div class="stats-causas-icon">
                        <i class="fas fa-paw"></i>
                    </div>
                    <div class="stats-causas-number" data-count="<?= $total_stats['rescatados'] ?>">0</div>
                    <div class="stats-causas-label">Rescatados</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stats-causas-card">
                    <div class="stats-causas-icon">
                        <i class="fas fa-drumstick-bite"></i>
                    </div>
                    <div class="stats-causas-number" data-count="<?= $total_stats['alimentados'] ?>">0</div>
                    <div class="stats-causas-label">Alimentados</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stats-causas-card">
                    <div class="stats-causas-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-causas-number" data-count="<?= $total_stats['voluntarios'] ?>">0</div>
                    <div class="stats-causas-label">Voluntarios</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stats-causas-card">
                    <div class="stats-causas-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="stats-causas-number" data-count="<?= $total_stats['donaciones'] ?>">0</div>
                    <div class="stats-causas-label">Donaciones</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== LISTADO DE CAUSAS ====== -->
<section style="padding: 80px 0; background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span style="color: #e74c3c; font-weight: 700; text-transform: uppercase; letter-spacing: 3px; font-size: 0.9rem;">
                Conoce nuestras historias
            </span>
            <h2 class="fw-bold mt-2" style="color: #2c3e50;">Causas que nos inspiran</h2>
            <p class="text-muted">Cada historia representa vidas transformadas gracias a tu apoyo</p>
        </div>

        <?php if (empty($causas)): ?>
            <div class="text-center py-5 bg-white rounded-3" data-aos="fade-up">
                <i class="fas fa-heart-broken fa-4x text-muted mb-3"></i>
                <h3 class="text-muted">No hay causas activas en este momento</h3>
                <p class="text-muted">Pronto compartiremos nuevas historias contigo.</p>
            </div>
        <?php else: ?>
            <?php 
            $iconos_tipo = [
                'ANIMAL' => ['icon' => 'fa-paw', 'color' => '#e74c3c'],
                'AMBIENTAL' => ['icon' => 'fa-leaf', 'color' => '#27ae60'],
                'COMUNITARIA' => ['icon' => 'fa-hands-helping', 'color' => '#3498db'],
                'EDUCATIVA' => ['icon' => 'fa-graduation-cap', 'color' => '#9b59b6'],
                'OTRA' => ['icon' => 'fa-heart', 'color' => '#f39c12']
            ];
            
            foreach ($causas as $causa): 
                $stats = json_decode($causa['estadisticas'] ?? '{}', true) ?: [];
                $tipo_info = $iconos_tipo[$causa['tipo']] ?? $iconos_tipo['OTRA'];
                
                // Obtener galería de esta causa
                $stmtGal = $pdo->prepare("SELECT * FROM causas_galeria WHERE causa_id = ? ORDER BY orden");
                $stmtGal->execute([$causa['id']]);
                $galeria = $stmtGal->fetchAll(PDO::FETCH_ASSOC);
            ?>
                <div class="causa-card-full" data-aos="fade-up">
                    <!-- HEADER CON IMAGEN -->
                    <div class="causa-header-full">
                        <?php if (!empty($causa['imagen_principal'])): ?>
                            <img src="uploads/causas/<?= htmlspecialchars($causa['imagen_principal']) ?>" 
                                 alt="<?= htmlspecialchars($causa['titulo']) ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; background: <?= $tipo_info['color'] ?>; display: flex; align-items: center; justify-content: center; color: white; font-size: 6rem;">
                                <i class="fas <?= $tipo_info['icon'] ?>"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="causa-tipo-badge" style="background: <?= $tipo_info['color'] ?>;">
                            <i class="fas <?= $tipo_info['icon'] ?> me-1"></i>
                            <?= $causa['tipo'] ?>
                        </div>
                        
                        <div class="causa-header-overlay">
                            <h2><?= htmlspecialchars($causa['titulo']) ?></h2>
                            <?php if (!empty($causa['subtitulo'])): ?>
                                <p><i class="fas fa-quote-left me-2"></i><?= htmlspecialchars($causa['subtitulo']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- BODY -->
                    <div class="causa-body-full">
                        <div class="descripcion">
                            <?= nl2br(htmlspecialchars($causa['descripcion'])) ?>
                        </div>

                        <!-- ESTADÍSTICAS -->
                        <?php if (array_sum($stats) > 0): ?>
                            <div class="causa-stats-grid">
                                <?php if (!empty($stats['rescatados']) && $stats['rescatados'] > 0): ?>
                                    <div class="causa-stat-item">
                                        <i class="fas fa-paw"></i>
                                        <div class="causa-stat-number" data-count="<?= $stats['rescatados'] ?>">0</div>
                                        <div class="causa-stat-label">Rescatados</div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($stats['alimentados']) && $stats['alimentados'] > 0): ?>
                                    <div class="causa-stat-item">
                                        <i class="fas fa-drumstick-bite"></i>
                                        <div class="causa-stat-number" data-count="<?= $stats['alimentados'] ?>">0</div>
                                        <div class="causa-stat-label">Alimentados</div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($stats['voluntarios']) && $stats['voluntarios'] > 0): ?>
                                    <div class="causa-stat-item">
                                        <i class="fas fa-users"></i>
                                        <div class="causa-stat-number" data-count="<?= $stats['voluntarios'] ?>">0</div>
                                        <div class="causa-stat-label">Voluntarios</div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($stats['donaciones']) && $stats['donaciones'] > 0): ?>
                                    <div class="causa-stat-item">
                                        <i class="fas fa-hand-holding-heart"></i>
                                        <div class="causa-stat-number" data-count="<?= $stats['donaciones'] ?>">0</div>
                                        <div class="causa-stat-label">Donaciones</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- GALERÍA -->
                        <?php if (!empty($galeria)): ?>
                            <h4 class="fw-bold mt-4 mb-3" style="color: #2c3e50;">
                                <i class="fas fa-images me-2" style="color: #e74c3c;"></i>
                                Galería de momentos
                            </h4>
                            <div class="causa-galeria">
                                <?php foreach ($galeria as $img): ?>
                                    <div class="causa-galeria-item" 
                                         onclick="abrirLightboxCausa('uploads/causas/<?= htmlspecialchars($img['imagen']) ?>')">
                                        <img src="uploads/causas/<?= htmlspecialchars($img['imagen']) ?>" 
                                             alt="<?= htmlspecialchars($img['descripcion'] ?? 'Momento') ?>">
                                        <div class="causa-galeria-overlay">
                                            <i class="fas fa-search-plus"></i>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- ====== CTA FINAL ====== -->
<section class="cta-causas">
    <div class="container cta-causas-content" data-aos="zoom-in">
        <i class="fas fa-hands-helping fa-3x mb-3"></i>
        <h2>¿Quieres ser parte del cambio?</h2>
        <p>Tu apoyo puede transformar vidas. Únete como voluntario o realiza una donación.</p>
        <div class="mt-4">
            <a href="contacto.php?asunto=voluntariado" class="btn-cta-causas">
                <i class="fas fa-hand-holding-heart me-2"></i> Quiero ser voluntario
            </a>
            <button type="button" class="btn-cta-causas outline" onclick="abrirModalDonaciones()">
                <i class="fas fa-donate me-2"></i> Hacer una donación
            </button>
        </div>
    </div>
</section>

<!-- ====== MODAL DATOS BANCARIOS ====== -->
<div class="modal fade" id="modalDonaciones" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border-radius: 20px 20px 0 0;">
                <h3 class="modal-title fw-bold">
                    <i class="fas fa-heart me-2"></i>Realizar una Donación
                </h3>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-center mb-4" style="font-size: 1.1rem; color: #6c757d;">
                    Tu donación ayuda a continuar con nuestra labor social. Gracias por tu apoyo.
                </p>

                <?php 
                $datos_bancarios = json_decode($config['datos_bancarios'] ?? '{}', true) ?: [];
                $tiene_datos = !empty($datos_bancarios['beneficiario']) || !empty($datos_bancarios['clabe']);
                ?>

                <?php if ($tiene_datos): ?>
                    <!-- DATOS BANCARIOS -->
                    <div class="card border-0 shadow-sm mb-4" style="background: #fff5f5;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3" style="color: #e74c3c;">
                                <i class="fas fa-university me-2"></i>Transferencia Bancaria
                            </h5>
                            
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase fw-bold">Beneficiario</label>
                                <p class="fw-bold mb-0"><?= htmlspecialchars($datos_bancarios['beneficiario'] ?? 'N/A') ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase fw-bold">Banco</label>
                                <p class="fw-bold mb-0"><?= htmlspecialchars($datos_bancarios['banco'] ?? 'N/A') ?></p>
                            </div>
                            
                            <?php if (!empty($datos_bancarios['clabe'])): ?>
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase fw-bold">CLABE</label>
                                <div class="d-flex align-items-center gap-2">
                                    <code class="bg-white p-2 rounded border" style="font-size: 1.1rem; letter-spacing: 2px;">
                                        <?= htmlspecialchars($datos_bancarios['clabe']) ?>
                                    </code>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                                            onclick="copiarAlPortapapeles('<?= htmlspecialchars($datos_bancarios['clabe']) ?>')">
                                        <i class="fas fa-copy"></i> Copiar
                                    </button>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($datos_bancarios['tarjeta'])): ?>
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase fw-bold">Tarjeta</label>
                                <p class="fw-bold mb-0"><?= htmlspecialchars($datos_bancarios['tarjeta']) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- LINK DE PAGO -->
                    <?php if (!empty($datos_bancarios['link_pago'])): ?>
                    <div class="text-center mb-4">
                        <p class="text-muted mb-3">O realiza tu donación de forma segura con:</p>
                        <a href="<?= htmlspecialchars($datos_bancarios['link_pago']) ?>" 
                           target="_blank" 
                           class="btn btn-lg" 
                           style="background: #0070ba; color: white; border-radius: 50px; padding: 12px 40px;">
                            <i class="fab fa-paypal me-2"></i> Donar con PayPal / Tarjeta
                        </a>
                    </div>
                    <?php endif; ?>

                    <!-- CONFIRMACIÓN -->
                    <!-- CONFIRMACIÓN -->
                    <div class="alert alert-info" style="background: #e3f2fd; border: none; border-left: 4px solid #0dcaf0;">
                        <p class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Importante:</strong> Después de realizar tu donación, envía el comprobante a nuestro correo 
                            <a href="mailto:<?= htmlspecialchars($config['correo'] ?? '') ?>" class="fw-bold text-decoration-underline">
                                <?= htmlspecialchars($config['correo'] ?? 'nuestro correo') ?>
                            </a> 
                            o también a nuestro WhatsApp: 
                            <a href="https://wa.me/52<?= preg_replace('/[^0-9]/', '', $config['whatsapp'] ?? '') ?>" target="_blank" class="fw-bold text-success text-decoration-underline">
                                <i class="fab fa-whatsapp"></i> <?= htmlspecialchars($config['whatsapp'] ?? 'nuestro WhatsApp') ?>
                            </a>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">
                            Los datos bancarios se están actualizando.<br>
                            Por favor contáctanos directamente para más información.
                        </p>
                        <a href="contacto.php?asunto=donacion" class="btn btn-danger btn-lg px-4" style="border-radius: 50px;">
                            <i class="fas fa-envelope me-2"></i> Contactar
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Función para abrir el modal
function abrirModalDonaciones() {
    const modal = new bootstrap.Modal(document.getElementById('modalDonaciones'));
    modal.show();
}

// Función para copiar al portapapeles
function copiarAlPortapapeles(texto) {
    navigator.clipboard.writeText(texto).then(() => {
        // Mostrar notificación temporal
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> ¡Copiado!';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    }).catch(err => {
        console.error('Error al copiar:', err);
        alert('Copia manualmente: ' + texto);
    });
}
</script>
<!-- ====== MODAL LIGHTBOX ====== -->
<div class="modal fade modal-lightbox-causas" id="lightboxCausas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <button type="button" class="btn-close-lightbox-causas" data-bs-dismiss="modal">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-body">
                <img id="lightboxImageCausa" src="" alt="Imagen">
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ duration: 800, once: true });

// ====== LIGHTBOX ======
function abrirLightboxCausa(imagen) {
    document.getElementById('lightboxImageCausa').src = imagen;
    const modal = new bootstrap.Modal(document.getElementById('lightboxCausas'));
    modal.show();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = bootstrap.Modal.getInstance(document.getElementById('lightboxCausas'));
        if (modal) modal.hide();
    }
});

// ====== CONTADORES ANIMADOS ======
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('[data-count]');
    const speed = 150;

    const animateCounter = (counter) => {
        const target = +counter.getAttribute('data-count');
        const count = +counter.innerText.replace(/,/g, '') || 0;
        const inc = target / speed;

        if (count < target) {
            counter.innerText = Math.ceil(count + inc).toLocaleString();
            setTimeout(() => animateCounter(counter), 20);
        } else {
            counter.innerText = target.toLocaleString();
        }
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
});
</script>

<?php include 'includes/web_footer.php'; ?>