<?php
include 'includes/web_header.php';
include 'includes/web_menu.php';

if (!isset($config)) {
    require_once 'includes/config_loader.php';
}

$hoy = date('Y-m-d');
$stmt = $pdo->prepare("
    SELECT * FROM promociones 
    WHERE activo = 1 
      AND fecha_inicio <= ? 
      AND fecha_fin >= ? 
    ORDER BY fecha_inicio DESC
");
$stmt->execute([$hoy, $hoy]);
$promociones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    /* ====== TARJETA DE PROMOCIÓN ====== */
    .promo-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.4s ease;
        height: 100%;
        background: white;
    }
    .promo-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(220, 53, 69, 0.2);
    }

    /* ====== CONTENEDOR DE IMAGEN CON EFECTO ZOOM ====== */
    .promo-img-wrapper {
        position: relative;
        height: 280px;
        overflow: hidden;
        cursor: pointer;
        background: #f8f9fa;
    }
    .promo-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }
    .promo-card:hover .promo-img-wrapper img {
        transform: scale(1.08);
    }

    /* ====== OVERLAY CON ICONO DE ZOOM ====== */
    .promo-img-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.4s ease;
        z-index: 2;
    }
    .promo-img-wrapper:hover .promo-img-overlay {
        opacity: 1;
    }
    .zoom-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #dc3545;
        font-size: 1.5rem;
        transform: scale(0.5);
        transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    }
    .promo-img-wrapper:hover .zoom-icon {
        transform: scale(1);
    }
    .zoom-text {
        position: absolute;
        bottom: 15px;
        left: 0;
        right: 0;
        text-align: center;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.4s ease 0.1s;
    }
    .promo-img-wrapper:hover .zoom-text {
        opacity: 1;
        transform: translateY(0);
    }

    /* ====== BADGE Y FECHA ====== */
    .promo-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #dc3545;
        color: white;
        padding: 8px 15px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.4);
        z-index: 3;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    /* ====== MODAL LIGHTBOX PERSONALIZADO ====== */
    .promo-lightbox .modal-content {
        background: transparent;
        border: none;
        box-shadow: none;
    }
    .promo-lightbox .modal-header {
        border: none;
        padding: 0;
        position: absolute;
        top: -50px;
        right: 0;
        z-index: 10;
    }
    .promo-lightbox .btn-close-lightbox {
        background: white;
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        font-size: 1.5rem;
        color: #dc3545;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
    }
    .promo-lightbox .btn-close-lightbox:hover {
        background: #dc3545;
        color: white;
        transform: rotate(90deg);
    }
    .promo-lightbox .modal-body {
        padding: 0;
        text-align: center;
    }
    .promo-lightbox .modal-body img {
        max-width: 100%;
        max-height: 85vh;
        border-radius: 10px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        animation: zoomInLightbox 0.4s ease;
    }
    .promo-lightbox .modal-backdrop.show {
        opacity: 0.9;
        background: #000;
    }
    .promo-lightbox-info {
        background: rgba(255, 255, 255, 0.95);
        padding: 20px;
        border-radius: 10px;
        margin-top: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        animation: slideUpLightbox 0.5s ease 0.2s both;
    }
    .promo-lightbox-info h3 {
        color: #2c3e50;
        font-weight: 700;
        margin-bottom: 10px;
    }
    .promo-lightbox-info p {
        color: #6c757d;
        margin-bottom: 0;
    }

    @keyframes zoomInLightbox {
        from { opacity: 0; transform: scale(0.8); }
        to { opacity: 1; transform: scale(1); }
    }
    @keyframes slideUpLightbox {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ====== CONTADOR REGRESIVO ====== */
    .countdown-container {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin: 15px 0;
    }
    .countdown-box {
        background: #ffffff;
        border: 2px solid #dc3545;
        border-radius: 8px;
        padding: 8px 5px;
        text-align: center;
        min-width: 55px;
        box-shadow: 0 4px 6px rgba(220, 53, 69, 0.15);
        transition: transform 0.3s ease;
    }
    .countdown-box:hover {
        transform: translateY(-3px);
    }
    .countdown-number {
        font-size: 1.4rem;
        font-weight: 800;
        color: #dc3545;
        line-height: 1;
        display: block;
        font-variant-numeric: tabular-nums;
    }
    .countdown-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 700;
        margin-top: 4px;
        display: block;
        letter-spacing: 0.5px;
    }
    .countdown-expired {
        background: #6c757d;
        color: white;
        padding: 8px 15px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
        display: inline-block;
        margin: 15px 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .promo-img-wrapper { height: 220px; }
        .promo-lightbox .modal-body img { max-height: 60vh; }
    }
</style>

<div class="container py-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <span class="text-danger fw-bold text-uppercase" style="letter-spacing: 2px;">Ofertas Especiales</span>
        <h1 class="display-4 fw-bold mt-2">Promociones Vigentes</h1>
        <p class="lead text-muted">Aprovecha nuestras ofertas por tiempo limitado</p>
    </div>

    <?php if (empty($promociones)): ?>
        <div class="text-center py-5 bg-light rounded-3" data-aos="fade-up">
            <i class="fas fa-tags fa-4x text-muted mb-3"></i>
            <h3 class="text-muted">No hay promociones activas en este momento</h3>
            <p class="text-muted">¡Vuelve pronto para descubrir nuevas ofertas!</p>
            <a href="productos.php" class="btn btn-success mt-3" style="border-radius: 50px; padding: 10px 30px;">
                Ver Catálogo de Productos
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($promociones as $promo): 
                $fechaFinJS = date('c', strtotime($promo['fecha_fin'] . ' 23:59:59'));
            ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="promo-card">
                        
                        <!-- 🔥 IMAGEN CON LIGHTBOX 🔥 -->
                        <div class="promo-img-wrapper" 
                             onclick="abrirLightbox('<?= !empty($promo['imagen']) ? 'uploads/promociones/' . htmlspecialchars($promo['imagen']) : 'https://placehold.co/800x600/dc3545/ffffff?text=Promoción' ?>', '<?= htmlspecialchars(addslashes($promo['titulo'])) ?>', '<?= htmlspecialchars(addslashes($promo['descripcion'])) ?>')">
                            
                            <?php if (!empty($promo['imagen'])): ?>
                                <img src="uploads/promociones/<?= htmlspecialchars($promo['imagen']) ?>" 
                                     alt="<?= htmlspecialchars($promo['titulo']) ?>"
                                     onerror="this.src='https://placehold.co/600x400/dc3545/ffffff?text=Promoción'">
                            <?php else: ?>
                                <img src="https://placehold.co/600x400/dc3545/ffffff?text=Promoción" alt="Promoción">
                            <?php endif; ?>
                            
                            <!-- Overlay con icono de zoom -->
                            <div class="promo-img-overlay">
                                <div class="zoom-icon">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                                <div class="zoom-text">
                                    <i class="fas fa-expand me-1"></i> Click para ampliar
                                </div>
                            </div>
                            
                            <div class="promo-badge">
                                <i class="fas fa-fire me-1"></i> ¡OFERTA!
                            </div>
                        </div>
                        
                        <div class="card-body p-4 text-center">
                            <h3 class="h4 fw-bold mb-2" style="color: #2c3e50;">
                                <?= htmlspecialchars($promo['titulo']) ?>
                            </h3>
                            <p class="text-muted mb-3" style="line-height: 1.6; font-size: 0.95rem;">
                                <?= nl2br(htmlspecialchars($promo['descripcion'])) ?>
                            </p>
                            
                            <!-- CONTADOR REGRESIVO -->
                            <div class="countdown-container" data-end="<?= $fechaFinJS ?>">
                                <div class="countdown-box">
                                    <span class="countdown-number days">00</span>
                                    <span class="countdown-label">Días</span>
                                </div>
                                <div class="countdown-box">
                                    <span class="countdown-number hours">00</span>
                                    <span class="countdown-label">Horas</span>
                                </div>
                                <div class="countdown-box">
                                    <span class="countdown-number minutes">00</span>
                                    <span class="countdown-label">Min</span>
                                </div>
                                <div class="countdown-box">
                                    <span class="countdown-number seconds">00</span>
                                    <span class="countdown-label">Seg</span>
                                </div>
                            </div>

                            <a href="productos.php" class="btn btn-danger w-100 fw-bold mt-3" style="border-radius: 50px; padding: 12px;">
                                <i class="fas fa-shopping-cart me-2"></i> Aprovechar Oferta
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- 🔥 MODAL LIGHTBOX 🔥 -->
<div class="modal fade promo-lightbox" id="lightboxPromo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close-lightbox" data-bs-dismiss="modal" aria-label="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <img id="lightboxImage" src="" alt="Promoción">
                <div class="promo-lightbox-info">
                    <h3 id="lightboxTitle"></h3>
                    <p id="lightboxDescription"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script> AOS.init({ duration: 800, once: true }); </script>

<script>
// ====== FUNCIÓN PARA ABRIR LIGHTBOX ======
function abrirLightbox(imagen, titulo, descripcion) {
    document.getElementById('lightboxImage').src = imagen;
    document.getElementById('lightboxTitle').textContent = titulo;
    document.getElementById('lightboxDescription').innerHTML = descripcion.replace(/\n/g, '<br>');
    
    const modal = new bootstrap.Modal(document.getElementById('lightboxPromo'));
    modal.show();
}

// ====== CERRAR CON TECLA ESC ======
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = bootstrap.Modal.getInstance(document.getElementById('lightboxPromo'));
        if (modal) modal.hide();
    }
});

// ====== CONTADOR REGRESIVO ======
document.addEventListener('DOMContentLoaded', function() {
    function updateAllCountdowns() {
        const timers = document.querySelectorAll('.countdown-container');
        const now = new Date().getTime();

        timers.forEach(timer => {
            if (timer.classList.contains('expired')) return;

            const endDate = new Date(timer.dataset.end).getTime();
            const distance = endDate - now;

            if (distance < 0) {
                timer.innerHTML = '<span class="countdown-expired"><i class="fas fa-hourglass-end me-1"></i> ¡Promoción finalizada!</span>';
                timer.classList.add('expired');
                
                const btn = timer.parentElement.querySelector('a.btn');
                if (btn) {
                    btn.classList.remove('btn-danger');
                    btn.classList.add('btn-secondary');
                    btn.innerHTML = '<i class="fas fa-ban me-2"></i> Oferta Agotada';
                    btn.style.pointerEvents = 'none';
                }
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            timer.querySelector('.days').innerText = String(days).padStart(2, '0');
            timer.querySelector('.hours').innerText = String(hours).padStart(2, '0');
            timer.querySelector('.minutes').innerText = String(minutes).padStart(2, '0');
            timer.querySelector('.seconds').innerText = String(seconds).padStart(2, '0');
        });
    }

    updateAllCountdowns();
    setInterval(updateAllCountdowns, 1000);
});
</script>

<?php include 'includes/web_footer.php'; ?>