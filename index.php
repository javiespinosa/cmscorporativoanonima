<?php
include 'includes/web_header.php';
include 'includes/web_menu.php';

if (!isset($config)) {
    require_once 'includes/config_loader.php';
}

$productos = $pdo->query(
    "SELECT * FROM productos ORDER BY id DESC LIMIT 6"
)->fetchAll(PDO::FETCH_ASSOC);

$banners = $pdo->query(
    "SELECT * FROM banners WHERE activo=1 ORDER BY orden_banner"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- 🔥 MENSAJE DE ÉXITO DE COTIZACIÓN 🔥 -->
<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'cotizacion_enviada'): ?>
<div class="container mt-4">
    <div class="alert alert-success text-center shadow-sm border-0" role="alert" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-radius: 15px;">
        <i class="fas fa-check-circle fa-4x mb-3 text-success"></i>
        <h3 class="alert-heading fw-bold">¡Cotización Enviada con Éxito!</h3>
        <p class="mb-3 fs-5">Hemos recibido tu solicitud correctamente.</p>
        <p class="mb-4 text-muted">Un asesor se pondrá en contacto contigo a la brevedad para enviarte tu cotización formal.</p>
        <hr class="my-4">
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="productos.php" class="btn btn-success btn-lg px-4" style="border-radius: 50px;">
                <i class="fas fa-shopping-bag me-2"></i> Seguir viendo productos
            </a>
            <a href="contacto.php" class="btn btn-outline-success btn-lg px-4" style="border-radius: 50px;">
                <i class="fas fa-envelope me-2"></i> Contactar ahora
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- 🔥 Estilos personalizados para un look profesional -->
<style>
 /* <!-- 🔥 ESTILOS MEJORADOS PARA EL CARRUSEL -->
    ====== CARRUSEL PROFESIONAL CON EFECTOS ====== */
    #sliderPrincipal {
        position: relative;
        overflow: hidden;
        background: #000;
    }
    
    #sliderPrincipal .carousel-item {
        height: 90vh;
        min-height: 600px;
        max-height: 900px;
        position: relative;
        transition: transform 1.2s cubic-bezier(0.645, 0.045, 0.355, 1);
    }
    
    /* Efecto Ken Burns (zoom lento) */
    #sliderPrincipal .carousel-item img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        object-position: center;
        animation: kenBurns 20s ease infinite;
    }
    
    @keyframes kenBurns {
        0% { transform: scale(1) translate(0, 0); }
        50% { transform: scale(1.1) translate(-2%, -2%); }
        100% { transform: scale(1) translate(0, 0); }
    }
    
    /* Overlay con gradiente para mejor legibilidad */
    #sliderPrincipal .carousel-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            135deg,
            rgba(0, 0, 0, 0.7) 0%,
            rgba(40, 167, 69, 0.3) 50%,
            rgba(0, 0, 0, 0.6) 100%
        );
        z-index: 1;
    }
    
    /* Partículas animadas de fondo */
    #sliderPrincipal .carousel-item::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: 
            radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
            radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
            radial-gradient(circle at 40% 20%, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
        background-size: 100px 100px, 150px 150px, 120px 120px;
        animation: particleFloat 30s linear infinite;
        z-index: 1;
        opacity: 0.5;
    }
    
    @keyframes particleFloat {
        0% { transform: translate(0, 0); }
        100% { transform: translate(100px, 100px); }
    }
    
    /* Contenido del caption */
    #sliderPrincipal .carousel-caption {
        bottom: 50%;
        transform: translateY(50%);
        text-align: center;
        max-width: 900px;
        margin: 0 auto;
        z-index: 2;
        padding: 0 20px;
    }
    
    /* Badge superior */
    .banner-badge {
        display: inline-block;
        background: rgba(40, 167, 69, 0.9);
        color: white;
        padding: 8px 25px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 20px;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.2);
        animation: fadeInDown 1s ease 0.2s both;
    }
    
    /* Título principal */
    #sliderPrincipal .carousel-caption h2 {
        font-size: 4rem;
        font-weight: 900;
        text-shadow: 3px 3px 15px rgba(0,0,0,0.8);
        margin-bottom: 25px;
        letter-spacing: 2px;
        line-height: 1.1;
        animation: fadeInUp 1s ease 0.4s both;
        background: linear-gradient(135deg, #ffffff 0%, #28a745 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Subtítulo */
    #sliderPrincipal .carousel-caption p {
        font-size: 1.5rem;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.8);
        margin-bottom: 40px;
        font-weight: 300;
        letter-spacing: 1px;
        animation: fadeInUp 1s ease 0.6s both;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }
    
    /* Botones CTA mejorados */
    .btn-hero-group {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        animation: fadeInUp 1s ease 0.8s both;
    }
    
    .btn-hero {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 15px 45px;
        border-radius: 50px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        border: none;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        box-shadow: 0 8px 25px rgba(40,167,69,0.4);
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-hero::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .btn-hero:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-hero:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 15px 40px rgba(40,167,69,0.6);
        color: white;
    }
    
    .btn-hero-outline {
        background: transparent;
        color: white;
        padding: 15px 45px;
        border-radius: 50px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        border: 3px solid white;
        transition: all 0.4s ease;
        backdrop-filter: blur(10px);
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-hero-outline:hover {
        background: white;
        color: #28a745;
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(255,255,255,0.3);
    }
    
    /* Indicadores personalizados */
    #sliderPrincipal .carousel-indicators {
        bottom: 40px;
        margin: 0;
        z-index: 3;
    }
    
    #sliderPrincipal .carousel-indicators button {
        width: 50px;
        height: 5px;
        border-radius: 3px;
        background: rgba(255, 255, 255, 0.5);
        border: none;
        margin: 0 5px;
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
    }
    
    #sliderPrincipal .carousel-indicators button.active {
        background: #28a745;
        width: 80px;
    }
    
    #sliderPrincipal .carousel-indicators button.active::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        background: white;
        animation: progress 5s linear;
    }
    
    @keyframes progress {
        from { width: 0; }
        to { width: 100%; }
    }
    
    /* Controles de navegación */
    #sliderPrincipal .carousel-control-prev,
    #sliderPrincipal .carousel-control-next {
        width: 80px;
        height: 80px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(40, 167, 69, 0.8);
        border-radius: 50%;
        margin: 0 30px;
        opacity: 0;
        transition: all 0.4s ease;
        z-index: 3;
        backdrop-filter: blur(10px);
    }
    
    #sliderPrincipal:hover .carousel-control-prev,
    #sliderPrincipal:hover .carousel-control-next {
        opacity: 1;
    }
    
    #sliderPrincipal .carousel-control-prev:hover,
    #sliderPrincipal .carousel-control-next:hover {
        background: #28a745;
        transform: translateY(-50%) scale(1.1);
    }
    
    #sliderPrincipal .carousel-control-prev-icon,
    #sliderPrincipal .carousel-control-next-icon {
        width: 30px;
        height: 30px;
    }
    
    /* Contador de slides */
    .slide-counter {
        position: absolute;
        top: 30px;
        right: 30px;
        background: rgba(0, 0, 0, 0.6);
        color: white;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        z-index: 3;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.2);
    }
    
    .slide-counter .current {
        color: #28a745;
        font-size: 1.2rem;
        font-weight: 700;
    }
    
    /* Barra de progreso superior */
    .carousel-progress {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: rgba(255, 255, 255, 0.2);
        z-index: 4;
    }
    
    .carousel-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #28a745, #20c997);
        width: 0;
        animation: progressBar 5s linear infinite;
    }
    
    @keyframes progressBar {
        from { width: 0; }
        to { width: 100%; }
    }
    
    /* Animaciones de entrada */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Responsive */
    @media (max-width: 992px) {
        #sliderPrincipal .carousel-item {
            height: 70vh;
            min-height: 500px;
        }
        #sliderPrincipal .carousel-caption h2 {
            font-size: 2.8rem;
        }
        #sliderPrincipal .carousel-caption p {
            font-size: 1.2rem;
        }
        .btn-hero, .btn-hero-outline {
            padding: 12px 35px;
            font-size: 0.9rem;
        }
    }
    
    @media (max-width: 768px) {
        #sliderPrincipal .carousel-item {
            height: 60vh;
            min-height: 400px;
        }
        #sliderPrincipal .carousel-caption h2 {
            font-size: 2rem;
            letter-spacing: 1px;
        }
        #sliderPrincipal .carousel-caption p {
            font-size: 1rem;
            margin-bottom: 25px;
        }
        .btn-hero-group {
            flex-direction: column;
            gap: 10px;
        }
        .btn-hero, .btn-hero-outline {
            padding: 12px 30px;
            font-size: 0.85rem;
        }
        #sliderPrincipal .carousel-control-prev,
        #sliderPrincipal .carousel-control-next {
            width: 50px;
            height: 50px;
            margin: 0 15px;
        }
        .slide-counter {
            top: 15px;
            right: 15px;
            padding: 8px 15px;
            font-size: 0.8rem;
        }
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

    /* ====== PRODUCTOS PREMIUM ====== */
.producto-card-premium {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    transition: all 0.4s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.producto-card-premium:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}
.producto-img-premium {
    position: relative;
    overflow: hidden;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 300px;
}
.producto-img-premium img {
    width: 100%;
    height: 100%;
    object-fit: contain; /* Muestra la imagen completa sin recortar */
    padding: 20px;
    transition: transform 0.6s ease;
}
.producto-card-premium:hover .producto-img-premium img {
    transform: scale(1.05);
}
.producto-info-premium {
    padding: 25px;
    text-align: center;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.producto-info-premium h5 {
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 1.2rem;
}
.producto-info-premium p {
    color: #6c757d;
    font-size: 0.95rem;
    line-height: 1.6;
    margin: 0;
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

/* ====== CONTADOR REGRESIVO DE PROMOCIONES ====== */
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
    font-variant-numeric: tabular-nums; /* Evita que los números "salten" al cambiar */
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

/* Versión compacta para el index (horizontal) */
.countdown-compact .countdown-box {
    min-width: 45px;
    padding: 5px 3px;
}
.countdown-compact .countdown-number {
    font-size: 1.1rem;
}
.countdown-compact .countdown-label {
    font-size: 0.6rem;
}

/* Estado expirado */
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

</style>

<!-- 🔥 AOS - Animate On Scroll (librería para animaciones al hacer scroll) -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<!-- ====== CARRUSEL PROFESIONAL CON SOPORTE PARA VIDEOS ====== -->
<div id="sliderPrincipal" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
    
    <!-- Barra de progreso -->
    <div class="carousel-progress">
        <div class="carousel-progress-bar"></div>
    </div>
    
    <!-- Contador de slides -->
    <div class="slide-counter">
        <span class="current">01</span> / <span class="total"><?= str_pad(count($banners), 2, '0', STR_PAD_LEFT) ?></span>
    </div>
    
    <!-- Indicadores -->
    <div class="carousel-indicators">
        <?php foreach($banners as $i => $banner): ?>
            <button type="button" 
                    data-bs-target="#sliderPrincipal" 
                    data-bs-slide-to="<?= $i ?>" 
                    class="<?= $i==0?'active':'' ?>"
                    aria-current="<?= $i==0?'true':'false' ?>"
                    aria-label="Slide <?= $i + 1 ?>"></button>
        <?php endforeach; ?>
    </div>
    
    <!-- Slides -->
    <div class="carousel-inner">
        <?php foreach($banners as $i => $banner): ?>
            <div class="carousel-item <?= $i==0?'active':'' ?>">
                
                <!-- CONTENIDO SEGÚN EL TIPO -->
                <?php if ($banner['tipo'] === 'imagen'): ?>
                    <!-- IMAGEN -->
                    <img src="uploads/banners/<?= htmlspecialchars($banner['imagen']) ?>" 
                         class="d-block w-100" 
                         alt="<?= htmlspecialchars($banner['titulo']) ?>">
                
                <?php elseif ($banner['tipo'] === 'video'): ?>
                    <!-- VIDEO LOCAL -->
                    <video class="d-block w-100" 
                           autoplay 
                           muted 
                           loop 
                           playsinline
                           style="height: 100%; width: 100%; object-fit: cover;">
                        <source src="uploads/banners/<?= htmlspecialchars($banner['imagen']) ?>" type="video/mp4">
                        <source src="uploads/banners/<?= htmlspecialchars($banner['imagen']) ?>" type="video/webm">
                        Tu navegador no soporta videos HTML5.
                    </video>
                
                <?php elseif ($banner['tipo'] === 'youtube'): ?>
                    <!-- YOUTUBE / VIMEO -->
                    <div class="video-wrapper">
                        <iframe src="<?= htmlspecialchars($banner['video_url']) ?>" 
                                frameborder="0" 
                                allow="autoplay; encrypted-media" 
                                allowfullscreen
                                style="position: absolute; top: 50%; left: 50%; width: 100vw; height: 100vh; transform: translate(-50%, -50%); pointer-events: none;">
                        </iframe>
                    </div>
                <?php endif; ?>
                
                <!-- CAPTION (IGUAL PARA TODOS) -->
                <div class="carousel-caption">
                    <?php if (!empty($banner['subtitulo'])): ?>
                        <div class="banner-badge">
                            <i class="fas fa-star me-2"></i><?= htmlspecialchars($banner['subtitulo']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <h2><?= htmlspecialchars($banner['titulo']) ?></h2>
                    
                    <div class="btn-hero-group">
                        <a href="#productos" class="btn-hero">
                            <i class="fas fa-shopping-bag me-2"></i>Ver Productos
                        </a>
                        <?php if (!empty($banner['enlace'])): ?>
                            <a href="<?= htmlspecialchars($banner['enlace']) ?>" class="btn-hero-outline">
                                <i class="fas fa-info-circle me-2"></i>Más Información
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Controles -->
    <button class="carousel-control-prev" type="button" data-bs-target="#sliderPrincipal" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#sliderPrincipal" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>
</div>

<!-- Script para actualizar contador -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('sliderPrincipal');
    const counter = carousel.querySelector('.slide-counter .current');
    const progressBar = carousel.querySelector('.carousel-progress-bar');
    
    carousel.addEventListener('slide.bs.carousel', function (event) {
        counter.textContent = String(event.to + 1).padStart(2, '0');
        
        progressBar.style.animation = 'none';
        progressBar.offsetHeight;
        progressBar.style.animation = 'progressBar 5s linear';
    });
});
</script>

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
    <img src="<?= BASE_URL . htmlspecialchars($config['logo']) ?>" alt="Nosotros">
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
        <p><?= nl2br(htmlspecialchars($config['valores'] ?? 'Compromiso, calidad, innovación y pasión por ofrecer lo mejor a nuestros clientes.')) ?></p>
    </div>
</div>
        </div>
    </div>
</section>

<!-- ====== SECCIÓN PROMOCIONES VIGENTES (En Index) ====== -->
<?php
$hoy = date('Y-m-d');
$stmtPromo = $pdo->prepare("SELECT * FROM promociones WHERE activo = 1 AND fecha_inicio <= ? AND fecha_fin >= ? ORDER BY id DESC LIMIT 2");
$stmtPromo->execute([$hoy, $hoy]);
$promosIndex = $stmtPromo->fetchAll(PDO::FETCH_ASSOC);

if (!empty($promosIndex)):
?>
<section class="py-5" style="background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-right">
            <div>
                <span class="text-danger fw-bold text-uppercase" style="letter-spacing: 2px;">No te lo pierdas</span>
                <h2 class="fw-bold mt-1">Promociones del Momento 🔥</h2>
            </div>
            <a href="promociones.php" class="btn btn-outline-danger" style="border-radius: 50px;">
                Ver todas <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-4">
                        <?php foreach ($promosIndex as $promo): 
                $fechaFinJS = date('c', strtotime($promo['fecha_fin'] . ' 23:59:59'));
            ?>
                <div class="col-md-6" data-aos="fade-up">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; overflow: hidden;">
                        <div class="row g-0 h-100">
                            <div class="col-md-5">
                                <?php if (!empty($promo['imagen'])): ?>
                                    <img src="uploads/promociones/<?= htmlspecialchars($promo['imagen']) ?>" class="w-100 h-100" style="object-fit: cover; min-height: 200px;" alt="<?= htmlspecialchars($promo['titulo']) ?>">
                                <?php else: ?>
                                    <div class="w-100 h-100 bg-danger d-flex align-items-center justify-content-center text-white">
                                        <i class="fas fa-tags fa-3x"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-7">
                                <div class="card-body p-3 p-md-4 d-flex flex-column h-100">
                                    <span class="badge bg-danger mb-2" style="width: fit-content;">
                                        <i class="fas fa-fire me-1"></i> Válido hasta <?= date('d/m/Y', strtotime($promo['fecha_fin'])) ?>
                                    </span>
                                    <h4 class="fw-bold mb-2"><?= htmlspecialchars($promo['titulo']) ?></h4>
                                    <p class="text-muted flex-grow-1 small"><?= htmlspecialchars(substr($promo['descripcion'], 0, 100)) ?>...</p>
                                    
                                    <!-- 🔥 CONTADOR COMPACTO 🔥 -->
                                    <div class="countdown-container countdown-compact justify-content-start" data-end="<?= $fechaFinJS ?>">
                                        <div class="countdown-box">
                                            <span class="countdown-number days">00</span>
                                            <span class="countdown-label">Días</span>
                                        </div>
                                        <div class="countdown-box">
                                            <span class="countdown-number hours">00</span>
                                            <span class="countdown-label">Hrs</span>
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

                                    <a href="promociones.php" class="btn btn-danger mt-3" style="border-radius: 50px;">
                                        Ver detalles <i class="fas fa-chevron-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ====== PRODUCTOS PREMIUM ====== -->
<section class="productos-section" id="productos">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Productos Premium</h2>
            <p>Nuestra selección exclusiva de productos de la más alta calidad</p>
        </div>

        <div class="row g-4">
            <?php foreach($productos as $p): ?>
                <div class="col-md-6 col-lg-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="producto-card-premium">
                        <div class="producto-img-premium">
                            <?php if(!empty($p['imagen'])): ?>
                                <img src="uploads/productos/<?= htmlspecialchars($p['imagen']) ?>" 
                                     alt="<?= htmlspecialchars($p['nombre']) ?>">
                            <?php else: ?>
                                <img src="https://placehold.co/400x400/e9ecef/6c757d?text=Producto+Premium" 
                                     alt="Sin imagen">
                            <?php endif; ?>
                        </div>
                        <div class="producto-info-premium">
                            <h5><?= htmlspecialchars($p['nombre']) ?></h5>
                            <?php if(!empty($p['descripcion_corta'])): ?>
                                <p><?= htmlspecialchars($p['descripcion_corta']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para actualizar todos los contadores de la página
    function updateAllCountdowns() {
        const timers = document.querySelectorAll('.countdown-container');
        const now = new Date().getTime();

        timers.forEach(timer => {
            // Si ya fue marcado como expirado, lo saltamos
            if (timer.classList.contains('expired')) return;

            const endDate = new Date(timer.dataset.end).getTime();
            const distance = endDate - now;

            if (distance < 0) {
                // Tiempo agotado: Reemplazar el contador con un mensaje
                timer.innerHTML = '<span class="countdown-expired"><i class="fas fa-hourglass-end me-1"></i> ¡Promoción finalizada!</span>';
                timer.classList.add('expired');
                
                // Opcional: Deshabilitar el botón de acción
                const btn = timer.parentElement.querySelector('a.btn');
                if (btn) {
                    btn.classList.remove('btn-danger');
                    btn.classList.add('btn-secondary');
                    btn.innerHTML = '<i class="fas fa-ban me-2"></i> Oferta Agotada';
                    btn.style.pointerEvents = 'none';
                }
                return;
            }

            // Cálculos de tiempo
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Actualizar el DOM
            timer.querySelector('.days').innerText = String(days).padStart(2, '0');
            timer.querySelector('.hours').innerText = String(hours).padStart(2, '0');
            timer.querySelector('.minutes').innerText = String(minutes).padStart(2, '0');
            timer.querySelector('.seconds').innerText = String(seconds).padStart(2, '0');
        });
    }

    // Ejecutar inmediatamente para evitar el parpadeo de "00" al cargar
    updateAllCountdowns();
    
    // Actualizar cada segundo
    setInterval(updateAllCountdowns, 1000);
});
</script>

<script>
// Auto-ocultar el mensaje después de 10 segundos
setTimeout(function() {
    const alert = document.querySelector('.alert-success');
    if (alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }
}, 10000);
</script>

<?php include 'includes/web_footer.php'; ?>