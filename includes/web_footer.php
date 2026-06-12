<?php
// Asegurar que $config esté disponible
if (!isset($config)) {
    require_once __DIR__ . '/config_loader.php';
}
?>

<footer class="bg-dark text-white pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row">
            
            <!-- Columna 1: Info de la empresa -->
            <div class="col-lg-4 col-md-6 mb-4">
                <?php if (!empty($config['logo'])): ?>
                    <img src="<?= htmlspecialchars($config['logo']) ?>" 
                         alt="<?= htmlspecialchars($config['empresa']) ?>" 
                         class="mb-3" style="max-height: 60px;">
                <?php else: ?>
                    <h4 class="text-success"><?= htmlspecialchars($config['empresa']) ?></h4>
                <?php endif; ?>
                
                <?php if (!empty($config['slogan'])): ?>
                    <p class="text-muted fst-italic"><?= htmlspecialchars($config['slogan']) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($config['quienes_somos'])): ?>
                    <p class="small text-muted">
                        <?= htmlspecialchars(substr($config['quienes_somos'], 0, 150)) ?>...
                    </p>
                <?php endif; ?>
            </div>

            <!-- Columna 2: Contacto -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-success mb-3">
                    <i class="fas fa-address-book me-2"></i>Contacto
                </h5>
                <ul class="list-unstyled">
                    <?php if (!empty($config['direccion'])): ?>
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt text-success me-2"></i>
                            <?= htmlspecialchars($config['direccion']) ?>
                        </li>
                    <?php endif; ?>
                    
                    <?php if (!empty($config['telefono'])): ?>
                        <li class="mb-2">
                            <i class="fas fa-phone text-success me-2"></i>
                            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $config['telefono']) ?>" 
                               class="text-white text-decoration-none">
                                <?= htmlspecialchars($config['telefono']) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if (!empty($config['whatsapp'])): ?>
                        <li class="mb-2">
                            <i class="fab fa-whatsapp text-success me-2"></i>
                            <a href="https://wa.me/52<?= preg_replace('/[^0-9]/', '', $config['whatsapp']) ?>" 
                               target="_blank"
                               class="text-white text-decoration-none">
                                <?= htmlspecialchars($config['whatsapp']) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if (!empty($config['correo'])): ?>
                        <li class="mb-2">
                            <i class="fas fa-envelope text-success me-2"></i>
                            <a href="mailto:<?= htmlspecialchars($config['correo']) ?>" 
                               class="text-white text-decoration-none">
                                <?= htmlspecialchars($config['correo']) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Columna 3: Horario y Redes -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-success mb-3">
                    <i class="fas fa-clock me-2"></i>Horario de Atención
                </h5>
                <ul class="list-unstyled small">
                    <?php
                    $dias = [
                        'Lunes' => $config['horario_lunes'] ?? 'Cerrado',
                        'Martes' => $config['horario_martes'] ?? 'Cerrado',
                        'Miércoles' => $config['horario_miercoles'] ?? 'Cerrado',
                        'Jueves' => $config['horario_jueves'] ?? 'Cerrado',
                        'Viernes' => $config['horario_viernes'] ?? 'Cerrado',
                        'Sábado' => $config['horario_sabado'] ?? 'Cerrado',
                        'Domingo' => $config['horario_domingo'] ?? 'Cerrado'
                    ];
                    
                    foreach ($dias as $dia => $horario):
                        $cerrado = strtolower($horario) === 'cerrado';
                    ?>
                        <li class="d-flex justify-content-between mb-1">
                            <span><?= $dia ?>:</span>
                            <span class="<?= $cerrado ? 'text-danger' : 'text-white' ?>">
                                <?= htmlspecialchars($horario) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <h5 class="text-success mt-4 mb-3">
                    <i class="fas fa-share-alt me-2"></i>Síguenos
                </h5>
                <div class="d-flex flex-wrap gap-2">
                    <?php
                    $redes = [
                        'facebook' => ['icon' => 'fab fa-facebook', 'color' => '#1877F2'],
                        'instagram' => ['icon' => 'fab fa-instagram', 'color' => '#E4405F'],
                        'tiktok' => ['icon' => 'fab fa-tiktok', 'color' => '#000000'],
                        'youtube' => ['icon' => 'fab fa-youtube', 'color' => '#FF0000'],
                        'linkedin' => ['icon' => 'fab fa-linkedin', 'color' => '#0A66C2']
                    ];
                    
                    foreach ($redes as $red => $info):
                        if (!empty($config[$red])):
                    ?>
                        <a href="<?= htmlspecialchars($config[$red]) ?>" 
                           target="_blank" 
                           class="btn btn-sm rounded-circle"
                           style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; background: <?= $info['color'] ?>; color: white; border: none;">
                            <i class="<?= $info['icon'] ?>"></i>
                        </a>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>

        <hr class="border-secondary">

        <!-- Copyright -->
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <small class="text-muted">
                    &copy; <?= date('Y') ?> <?= htmlspecialchars($config['empresa']) ?>. Todos los derechos reservados.
                </small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small class="text-muted">
                    Desarrollado con ❤️ por <?= htmlspecialchars($config['empresa']) ?>
                </small>
            </div>
        </div>
    </div>
</footer>

<!-- Botón flotante de WhatsApp -->
<?php if (!empty($config['whatsapp'])): ?>
    <a href="https://wa.me/52<?= preg_replace('/[^0-9]/', '', $config['whatsapp']) ?>" 
       target="_blank" 
       class="whatsapp"
       title="Contáctanos por WhatsApp">
        <i class="fab fa-whatsapp fa-2x text-white"></i>
    </a>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>