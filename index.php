<?php
include 'includes/web_header.php';
include 'includes/web_menu.php';

$productos = $pdo->query(
    "SELECT * FROM productos ORDER BY id DESC LIMIT 6"
)->fetchAll(PDO::FETCH_ASSOC);

$banners = $pdo->query(
    "SELECT * FROM banners WHERE activo=1 ORDER BY orden_banner"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- 🔥 Estilos personalizados para un look profesional -->
<style>
    /* ====== CARRUSEL MEJORADO ====== */
    #sliderPrincipal .carousel-item {
        height: 85vh;
        min-height: 500px;
        background: #000;
        position: relative;
    }
    #sliderPrincipal .carousel-item img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        filter: brightness(0.55);
    }
    #sliderPrincipal .carousel-caption {
        bottom: 50%;
        transform: translateY(50%);
        text-align: center;
        max-width: 800px;
        margin: 0 auto;
        animation: fadeInUp 1s ease;
    }
    #sliderPrincipal .carousel-caption h2 {
        font-size: 3.5rem;
        font-weight: 800;
        text-shadow: 2px 2px 10px rgba(0,0,0,0.7);
        margin-bottom: 20px;
        letter-spacing: 1px;
    }
    #sliderPrincipal .carousel-caption p {
        font-size: 1.4rem;
        text-shadow: 1px 1px 5px rgba(0,0,0,0.7);
        margin-bottom: 30px;
    }
    #sliderPrincipal .carousel-caption .btn-hero {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 12px 35px;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(40,167,69,0.4);
    }
    #sliderPrincipal .carousel-caption .btn-hero:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(40,167,69,0.6);
    }

    /* ====== SECCIÓN QUIÉNES SOMOS ====== */
    .section-title {
        text-align: center;
        margin-bottom: 60px;
        position: relative;
    }
    .section-title h2 {
        font-size: 2.8rem;
        font-weight: 800;
        color: #2c3e50;
        display: inline-block;
        position: relative;
        padding-bottom: 15px;
    }
    .section-title h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #28a745, #20c997);
        border-radius: 2px;
    }
    .section-title p {
        color: #6c757d;
        font-size: 1.1rem;
        margin-top: 15px;
    }

    /* ====== TARJETAS MISIÓN/VISIÓN ====== */
    .info-card {
        background: white;
        border-radius: 20px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.4s ease;
        height: 100%;
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }
    .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, #28a745, #20c997);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }
    .info-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }
    .info-card:hover::before {
        transform: scaleX(1);
    }
    .info-card .icon-box {
        width: 90px;
        height: 90px;
        margin: 0 auto 25px;
        background: linear-gradient(135deg, #28a745, #20c997);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        transition: all 0.4s ease;
        box-shadow: 0 10px 25px rgba(40,167,69,0.3);
    }
    .info-card:hover .icon-box {
        transform: rotateY(360deg);
        background: linear-gradient(135deg, #20c997, #28a745);
    }
    .info-card h3 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
    }
    .info-card p {
        color: #6c757d;
        line-height: 1.7;
        font-size: 1rem;
    }

    /* ====== SECCIÓN QUIÉNES SOMOS (TEXTO) ====== */
    .about-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 80px 0;
    }
    .about-text h2 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #2c3e50;
        margin-bottom: 20px;
    }
    .about-text p {
        color: #555;
        line-height: 1.8;
        font-size: 1.05rem;
    }
    .about-image {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        position: relative;
    }
    .about-image img {
        width: 100%;
        transition: transform 0.6s ease;
    }
    .about-image:hover img {
        transform: scale(1.05);
    }

    /* ====== PRODUCTOS DESTACADOS ====== */
    .productos-section {
        padding: 80px 0;
        background: white;
    }
    .producto-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.4s ease;
        height: 100%;
        background: white;
        display: flex;
        flex-direction: column;
    }
    .producto-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    .producto-img-wrapper {
        position: relative;
        overflow: hidden;
        height: 250px;
    }
    .producto-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }
    .producto-card:hover .producto-img-wrapper img {
        transform: scale(1.1);
    }
    .producto-img-wrapper .overlay {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(40,167,69,0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    .producto-card:hover .producto-img-wrapper .overlay {
        opacity: 1;
    }
    .producto-card .card-body {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .producto-card h5 {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    .producto-card p {
        color: #6c757d;
        font-size: 0.95rem;
        margin-bottom: 20px;
        flex-grow: 1;
    }
    .producto-card .btn-group {
        display: flex;
        gap: 10px;
        margin-top: auto;
    }
    .btn-ver-mas {
        background: transparent;
        color: #28a745;
        border: 2px solid #28a745;
        border-radius: 50px;
        padding: 8px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        flex: 1;
        text-align: center;
        text-decoration: none;
        display: inline-block;
    }
    .btn-ver-mas:hover {
        background: #28a745;
        color: white;
        transform: translateX(5px);
    }
    .btn-agregar {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border: 2px solid #28a745;
        border-radius: 50px;
        padding: 8px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        flex: 1;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        box-shadow: 0 3px 10px rgba(40,167,69,0.3);
    }
    .btn-agregar:hover {
        background: linear-gradient(135deg, #20c997, #28a745);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40,167,69,0.5);
        color: white;
    }

    /* ====== ANIMACIONES ====== */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .float-animation {
        animation: float 3s ease-in-out infinite;
    }

    /* ====== RESPONSIVE ====== */
    @media (max-width: 768px) {
        #sliderPrincipal .carousel-caption h2 { font-size: 2rem; }
        #sliderPrincipal .carousel-caption p { font-size: 1rem; }
        .section-title h2 { font-size: 2rem; }
        .producto-card .btn-group {
            flex-direction: column;
        }
    }
</style>

<!-- 🔥 AOS - Animate On Scroll (librería para animaciones al hacer scroll) -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<!-- ====== CARRUSEL MEJORADO ====== -->
<div id="sliderPrincipal" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php foreach($banners as $i => $banner): ?>
            <div class="carousel-item <?= $i==0?'active':'' ?>">
                <img src="uploads/banners/<?= htmlspecialchars($banner['imagen']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($banner['titulo']) ?>">
                <div class="carousel-caption">
                    <h2 data-aos="fade-down"><?= htmlspecialchars($banner['titulo']) ?></h2>
                    <p data-aos="fade-up" data-aos-delay="200"><?= htmlspecialchars($banner['subtitulo']) ?></p>
                    <a href="#productos" class="btn btn-hero" data-aos="zoom-in" data-aos-delay="400">
                        Ver Productos <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#sliderPrincipal" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#sliderPrincipal" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- ====== SECCIÓN QUIÉNES SOMOS ====== -->
<section class="about-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-md-6" data-aos="fade-right">
                <div class="about-text">
                    <span class="text-success fw-bold text-uppercase" style="letter-spacing: 2px;">Conócenos</span>
                    <h2 class="mt-2"><?= htmlspecialchars($config['empresa'] ?? 'Nuestra Empresa') ?></h2>
                    <div class="mt-3">
                        <?= nl2br(htmlspecialchars($config['quienes_somos'] ?? '')) ?>
                    </div>
                    <a href="nosotros.php" class="btn btn-success mt-4" style="border-radius: 50px; padding: 12px 30px;">
                        Saber más <i class="fas fa-chevron-right ms-2"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <div class="about-image">
                    <?php if(!empty($config['logo'])): ?>
                        <img src="<?= htmlspecialchars($config['logo']) ?>" alt="Nosotros">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/600x400/28a745/ffffff?text=Nuestra+Empresa" alt="Nosotros">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== MISIÓN / VISIÓN / VALORES ====== -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container py-5">
        <div class="section-title" data-aos="fade-up">
            <h2>Nuestra Esencia</h2>
            <p>Los pilares que nos definen y guían cada día</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="info-card">
                    <div class="icon-box float-animation">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Misión</h3>
                    <p><?= nl2br(htmlspecialchars($config['mision'] ?? '')) ?></p>
                </div>
            </div>

            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="info-card">
                    <div class="icon-box float-animation" style="animation-delay: 0.5s;">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Visión</h3>
                    <p><?= nl2br(htmlspecialchars($config['vision'] ?? '')) ?></p>
                </div>
            </div>

            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="info-card">
                    <div class="icon-box float-animation" style="animation-delay: 1s;">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Valores</h3>
                    <p>Compromiso, calidad, innovación y pasión por ofrecer lo mejor a nuestros clientes en cada producto y servicio.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== PRODUCTOS DESTACADOS ====== -->
<section class="productos-section" id="productos">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Productos Destacados</h2>
            <p>Descubre nuestra selección de productos más populares</p>
        </div>

        <div class="row g-4">
            <?php foreach($productos as $p): ?>
                <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="producto-card">
                        <div class="producto-img-wrapper">
                            <?php if(!empty($p['imagen'])): ?>
                                <img src="uploads/productos/<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/400x300?text=Sin+Imagen" alt="Sin imagen">
                            <?php endif; ?>
                            <div class="overlay">
                                <a href="producto.php?id=<?= $p['id'] ?>" class="btn btn-light" style="border-radius: 50px; padding: 10px 25px; font-weight: 600;">
                                    <i class="fas fa-eye me-2"></i> Ver detalles
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5><?= htmlspecialchars($p['nombre']) ?></h5>
                            <p><?= htmlspecialchars($p['descripcion_corta'] ?? '') ?></p>
                            
                            <!-- 🔥 BOTONES RESTAURADOS -->
                            <div class="btn-group">
                                <a href="producto.php?id=<?= $p['id'] ?>" class="btn-ver-mas">
                                    <i class="fas fa-info-circle me-1"></i> Ver detalle
                                </a>
                                <a href="cotizacion_agregar.php?id=<?= $p['id'] ?>" class="btn-agregar">
                                    <i class="fas fa-plus-circle me-1"></i> Agregar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="productos.php" class="btn btn-success" style="border-radius: 50px; padding: 12px 40px; font-weight: 600;">
                Ver todos los productos <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- 🔥 Script de AOS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });
</script>

<?php include 'includes/web_footer.php'; ?>