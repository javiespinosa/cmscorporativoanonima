<?php
include 'includes/web_header.php';
include 'includes/web_menu.php';

if (!isset($config)) {
    require_once 'includes/config_loader.php';
}

// Procesar valores (separar por comas o saltos de línea)
$valores_array = [];
if (!empty($config['valores'])) {
    $temp = preg_split('/[\n,]+/', $config['valores']);
    foreach ($temp as $v) {
        $v = trim($v, " \t\n\r\0\x0B-•*·");
        if (!empty($v)) $valores_array[] = $v;
    }
}

// Procesar objetivos
$objetivos_array = [];
if (!empty($config['objetivos'])) {
    $temp = explode("\n", $config['objetivos']);
    foreach ($temp as $o) {
        $o = trim($o, " \t\n\r\0\x0B-•*·");
        if (!empty($o)) $objetivos_array[] = $o;
    }
}
?>

<style>
    /* ====== HERO DE QUIÉNES SOMOS ====== */
    .nosotros-hero {
        position: relative;
        height: 500px;
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        overflow: hidden;
    }
    .nosotros-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: linear-gradient(135deg, rgba(var(--rgb-primario), 0.85), rgba(var(--rgb-secundario), 0.75));
    }
    .nosotros-hero-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        padding: 0 20px;
    }
    .nosotros-hero h1 {
        font-size: 4rem;
        font-weight: 900;
        margin-bottom: 20px;
        text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
        animation: fadeInDown 1s ease;
    }
    .nosotros-hero p {
        font-size: 1.4rem;
        opacity: 0.95;
        animation: fadeInUp 1s ease 0.3s both;
    }
    .nosotros-hero .scroll-indicator {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        color: white;
        font-size: 2rem;
        animation: bounce 2s infinite;
        z-index: 2;
    }

    @keyframes bounce {
        0%, 100% { transform: translateX(-50%) translateY(0); }
        50% { transform: translateX(-50%) translateY(-15px); }
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ====== SECCIÓN HISTORIA ====== */
    .historia-section {
        padding: 100px 0;
        background: white;
        position: relative;
    }
    .section-title-pro {
        text-align: center;
        margin-bottom: 70px;
    }
    .section-title-pro span {
        color: var(--color-primario);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 3px;
        font-size: 0.9rem;
    }
    .section-title-pro h2 {
        font-size: 3rem;
        font-weight: 800;
        color: #2c3e50;
        margin-top: 10px;
        position: relative;
        display: inline-block;
        padding-bottom: 15px;
    }
    .section-title-pro h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, var(--color-primario), var(--color-secundario));
        border-radius: 2px;
    }
    .historia-texto {
        font-size: 1.1rem;
        line-height: 1.9;
        color: #555;
        max-width: 900px;
        margin: 0 auto;
        text-align: justify;
    }
    .historia-texto p {
        margin-bottom: 20px;
    }

    /* ====== MISIÓN / VISIÓN ====== */
    .filosofia-section {
        padding: 100px 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    .filosofia-card {
        background: white;
        border-radius: 20px;
        padding: 50px 40px;
        text-align: center;
        box-shadow: 0 15px 40px rgba(0,0,0,0.08);
        transition: all 0.4s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .filosofia-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 5px;
        background: linear-gradient(90deg, var(--color-primario), var(--color-secundario));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }
    .filosofia-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 25px 60px rgba(var(--rgb-primario), 0.15);
    }
    .filosofia-card:hover::before {
        transform: scaleX(1);
    }
    .filosofia-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto 30px;
        background: linear-gradient(135deg, var(--color-primario), var(--color-secundario));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.8rem;
        box-shadow: 0 10px 30px rgba(var(--rgb-primario), 0.3);
        transition: all 0.4s ease;
    }
    .filosofia-card:hover .filosofia-icon {
        transform: rotateY(360deg);
    }
    .filosofia-card h3 {
        font-size: 2rem;
        font-weight: 800;
        color: #2c3e50;
        margin-bottom: 20px;
    }
    .filosofia-card p {
        color: #6c757d;
        line-height: 1.8;
        font-size: 1.05rem;
    }

    /* ====== VALORES ====== */
    .valores-section {
        padding: 100px 0;
        background: white;
    }
    .valor-card {
        background: white;
        border-radius: 15px;
        padding: 30px 20px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.4s ease;
        height: 100%;
        border: 2px solid transparent;
    }
    .valor-card:hover {
        transform: translateY(-10px);
        border-color: var(--color-primario);
        box-shadow: 0 15px 40px rgba(var(--rgb-primario), 0.2);
    }
    .valor-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, rgba(var(--rgb-primario), 0.1), rgba(var(--rgb-secundario), 0.1));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-primario);
        font-size: 1.8rem;
        transition: all 0.4s ease;
    }
    .valor-card:hover .valor-icon {
        background: linear-gradient(135deg, var(--color-primario), var(--color-secundario));
        color: white;
        transform: scale(1.1);
    }
    .valor-card h4 {
        font-weight: 700;
        color: #2c3e50;
        font-size: 1.2rem;
    }

    /* ====== OBJETIVOS ====== */
    .objetivos-section {
        padding: 100px 0;
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
    }
    .objetivos-section .section-title-pro h2 {
        color: white;
    }
    .objetivos-section .section-title-pro span {
        color: var(--color-secundario);
    }
    .objetivo-item {
        background: rgba(255,255,255,0.05);
        border-left: 4px solid var(--color-primario);
        padding: 25px 30px;
        margin-bottom: 20px;
        border-radius: 10px;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }
    .objetivo-item:hover {
        background: rgba(255,255,255,0.1);
        transform: translateX(10px);
        border-left-width: 8px;
    }
    .objetivo-numero {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--color-primario), var(--color-secundario));
        border-radius: 50%;
        font-weight: 800;
        font-size: 1.1rem;
        margin-right: 15px;
        flex-shrink: 0;
    }
    .objetivo-texto {
        font-size: 1.1rem;
        line-height: 1.6;
        flex-grow: 1;
    }

    /* ====== CTA FINAL ====== */
    .cta-section {
        padding: 80px 0;
        background: linear-gradient(135deg, var(--color-primario), var(--color-secundario));
        color: white;
        text-align: center;
    }
    .cta-section h2 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 20px;
    }
    .cta-section p {
        font-size: 1.2rem;
        opacity: 0.95;
        margin-bottom: 30px;
    }
    .btn-cta {
        background: white;
        color: var(--color-primario);
        padding: 15px 40px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    .btn-cta:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        color: var(--color-primario);
    }

    /* ====== ESTADÍSTICAS ====== */
    .stats-section {
        padding: 80px 0;
        background: linear-gradient(135deg, var(--color-primario), var(--color-secundario));
        color: white;
    }
    .stat-item {
        text-align: center;
        padding: 20px;
    }
    .stat-number {
        font-size: 3.5rem;
        font-weight: 900;
        line-height: 1;
        margin-bottom: 10px;
        text-shadow: 2px 2px 10px rgba(0,0,0,0.2);
    }
    .stat-label {
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        opacity: 0.9;
    }
    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
        opacity: 0.8;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .nosotros-hero { height: 400px; }
        .nosotros-hero h1 { font-size: 2.5rem; }
        .nosotros-hero p { font-size: 1.1rem; }
        .section-title-pro h2 { font-size: 2rem; }
        .filosofia-card { padding: 30px 20px; }
        .filosofia-card h3 { font-size: 1.5rem; }
    }
</style>

<!-- ====== HERO ====== -->
<section class="nosotros-hero" <?php if (!empty($config['imagen_nosotros'])): ?>style="background-image: url('<?= htmlspecialchars($config['imagen_nosotros']) ?>');"<?php endif; ?>>
    <div class="nosotros-hero-content">
        <h1 data-aos="fade-down">Quiénes Somos</h1>
        <p data-aos="fade-up" data-aos-delay="200">
            Conoce nuestra historia, valores y el compromiso que nos define
        </p>
    </div>
    <a href="#historia" class="scroll-indicator">
        <i class="fas fa-chevron-down"></i>
    </a>
</section>

<!-- ====== HISTORIA ====== -->
<?php if (!empty($config['historia'])): ?>
<section class="historia-section" id="historia">
    <div class="container">
        <div class="section-title-pro" data-aos="fade-up">
            <span>Nuestra Trayectoria</span>
            <h2>Nuestra Historia</h2>
        </div>
        <div class="historia-texto" data-aos="fade-up" data-aos-delay="200">
            <?= nl2br(htmlspecialchars($config['historia'])) ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ====== ESTADÍSTICAS DINÁMICAS ====== -->
<?php
// Calcular años de experiencia
$anos_experiencia = 0;
if (!empty($config['fecha_fundacion'])) {
    $fundacion = new DateTime($config['fecha_fundacion']);
    $hoy = new DateTime();
    $anos_experiencia = $hoy->diff($fundacion)->y;
}

// Contar clientes únicos (por correo en cotizaciones)
$total_clientes = $pdo->query("SELECT COUNT(DISTINCT correo) FROM cotizaciones")->fetchColumn();

// Contar productos en cotizaciones atendidas
$total_productos_entregados = $pdo->query("
    SELECT COALESCE(SUM(cd.cantidad), 0)
    FROM cotizacion_detalle cd
    INNER JOIN cotizaciones c ON cd.cotizacion_id = c.id
    WHERE c.estatus = 'ATENDIDA'
")->fetchColumn();

// Contar productos totales en el catálogo
$total_productos_catalogo = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
?>

<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6" data-aos="fade-up">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-number" data-count="<?= $anos_experiencia ?>">0</div>
                    <div class="stat-label">Años de Experiencia</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number" data-count="<?= $total_clientes ?>">0</div>
                    <div class="stat-label">Clientes Atendidos</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-number" data-count="<?= $total_productos_entregados ?>">0</div>
                    <div class="stat-label">Productos Entregados</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="stat-number" data-count="<?= $total_productos_catalogo ?>">0</div>
                    <div class="stat-label">Productos en Catálogo</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== MISIÓN / VISIÓN ====== -->
<section class="filosofia-section">
    <div class="container">
        <div class="section-title-pro" data-aos="fade-up">
            <span>Nuestra Esencia</span>
            <h2>Filosofía Corporativa</h2>
        </div>

        <div class="row g-4">
            <?php if (!empty($config['mision'])): ?>
                <div class="col-md-6" data-aos="fade-right">
                    <div class="filosofia-card">
                        <div class="filosofia-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3>Nuestra Misión</h3>
                        <p><?= nl2br(htmlspecialchars($config['mision'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($config['vision'])): ?>
                <div class="col-md-6" data-aos="fade-left">
                    <div class="filosofia-card">
                        <div class="filosofia-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3>Nuestra Visión</h3>
                        <p><?= nl2br(htmlspecialchars($config['vision'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ====== VALORES ====== -->
<?php if (!empty($valores_array)): ?>
<section class="valores-section">
    <div class="container">
        <div class="section-title-pro" data-aos="fade-up">
            <span>Lo que nos define</span>
            <h2>Nuestros Valores</h2>
        </div>

        <div class="row g-4">
            <?php 
            $iconos_valores = ['fa-heart', 'fa-star', 'fa-handshake', 'fa-lightbulb', 'fa-shield-alt', 'fa-users', 'fa-award', 'fa-gem'];
            foreach ($valores_array as $i => $valor): 
                $icono = $iconos_valores[$i % count($iconos_valores)];
            ?>
                <div class="col-6 col-md-4 col-lg-3" data-aos="zoom-in" data-aos-delay="<?= $i * 100 ?>">
                    <div class="valor-card">
                        <div class="valor-icon">
                            <i class="fas <?= $icono ?>"></i>
                        </div>
                        <h4><?= htmlspecialchars($valor) ?></h4>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ====== OBJETIVOS ====== -->
<?php if (!empty($objetivos_array)): ?>
<section class="objetivos-section">
    <div class="container">
        <div class="section-title-pro" data-aos="fade-up">
            <span>Hacia dónde vamos</span>
            <h2>Nuestros Objetivos</h2>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php foreach ($objetivos_array as $i => $objetivo): ?>
                    <div class="objetivo-item d-flex align-items-center" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                        <div class="objetivo-numero"><?= $i + 1 ?></div>
                        <div class="objetivo-texto"><?= htmlspecialchars($objetivo) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ====== CTA FINAL ====== -->
<section class="cta-section">
    <div class="container" data-aos="zoom-in">
        <h2>¿Quieres ser parte de nuestra historia?</h2>
        <p>Contáctanos y descubre cómo podemos trabajar juntos</p>
        <a href="contacto.php" class="btn-cta">
            <i class="fas fa-envelope me-2"></i> Contáctanos Ahora
        </a>
    </div>
</section>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script> AOS.init({ duration: 1000, once: true, offset: 100 }); </script>

<script>
// Contador animado para estadísticas
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.stat-number');
    const speed = 200;

    const animateCounter = (counter) => {
        const target = +counter.getAttribute('data-count');
        const count = +counter.innerText;
        const inc = target / speed;

        if (count < target) {
            counter.innerText = Math.ceil(count + inc);
            setTimeout(() => animateCounter(counter), 20);
        } else {
            counter.innerText = target;
        }
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                animateCounter(counter);
                observer.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
});
</script>

<?php include 'includes/web_footer.php'; ?>