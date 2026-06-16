<?php
// Asegurar que $config esté disponible
if (!isset($config)) {
    require_once __DIR__ . '/config_loader.php';
}

// Asegurar zona horaria correcta
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('America/Mexico_City');
}

// Función para verificar si la sucursal principal está abierta
function sucursalPrincipalAbierta($pdo) {
    // Verificar que $pdo exista
    if (!$pdo) {
        return null; // null = no se puede determinar
    }
    
    try {
        // Buscar sucursal principal
        $stmt = $pdo->query("
            SELECT horarios_semanales, nombre FROM sucursales 
            WHERE activo = 1 
            ORDER BY es_principal DESC, orden ASC
            LIMIT 1
        ");
        $sucursal = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$sucursal) {
            return null; // No hay sucursales
        }
        
        if (empty($sucursal['horarios_semanales'])) {
            return null; // No tiene horarios configurados
        }
        
        $horarios = json_decode($sucursal['horarios_semanales'], true);
        if (!is_array($horarios)) {
            return null; // JSON inválido
        }
        
        $dia_actual_num = (int)date('N');
        $dias_map = [
            1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
            4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
        ];
        
        $dia_nombre = $dias_map[$dia_actual_num];
        $horario_hoy = trim($horarios[$dia_nombre] ?? 'Cerrado');
        
        if (strtolower($horario_hoy) === 'cerrado' || empty($horario_hoy)) {
            return false;
        }
        
        if (preg_match('/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/', $horario_hoy, $matches)) {
            $apertura = $matches[1];
            $cierre = $matches[2];
            $hora_actual = date('H:i');
            
            $apertura_fmt = str_pad(str_replace(':', '', $apertura), 4, '0', STR_PAD_LEFT);
            $cierre_fmt = str_pad(str_replace(':', '', $cierre), 4, '0', STR_PAD_LEFT);
            $actual_fmt = str_pad(str_replace(':', '', $hora_actual), 4, '0', STR_PAD_LEFT);
            
            return ($actual_fmt >= $apertura_fmt && $actual_fmt <= $cierre_fmt);
        }
        
        return null; // No se pudo parsear el horario
    } catch (Exception $e) {
        return null; // Error en la consulta
    }
}

// Obtener el estado
$abierto_ahora = sucursalPrincipalAbierta($pdo);


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

            <!-- Columna 3: ESTADO ACTUAL Y REDES -->
            <div class="col-lg-4 col-md-6 mb-4">
    
            <!-- Indicador de Estado (MEJORADO) -->
            <h5 class="mb-3" style="color: var(--color-primario);">
                <i class="fas fa-store me-2"></i>Estado Actual
            </h5>
            
            <div class="mb-3 p-3" style="background: rgba(255,255,255,0.1); border-radius: 10px; border-left: 4px solid var(--color-primario);">
                <div class="d-flex align-items-center mb-2">
                    <?php if ($abierto_ahora): ?>
                        <span class="badge me-2" style="background: #28a745; font-size: 0.95rem; padding: 10px 18px; border-radius: 50px; font-weight: 600;">
                            <i class="fas fa-check-circle me-1"></i> Abierto ahora
                        </span>
                    <?php else: ?>
                        <span class="badge me-2" style="background: #dc3545; font-size: 0.95rem; padding: 10px 18px; border-radius: 50px; font-weight: 600;">
                            <i class="fas fa-times-circle me-1"></i> Cerrado ahora
                        </span>
                    <?php endif; ?>
                </div>
                <p class="small mb-2" style="color: rgba(255,255,255,0.7);">
                    <i class="fas fa-info-circle me-1"></i>
                    Horario basado en nuestra sucursal principal
                </p>
                <a href="<?= BASE_URL ?>contacto.php#sucursales" 
                class="btn btn-sm" 
                style="background: var(--color-primario); color: white; border-radius: 50px; font-size: 0.85rem; padding: 8px 20px; font-weight: 600; border: none; transition: all 0.3s ease;"
                onmouseover="this.style.background='var(--color-secundario)'; this.style.transform='translateY(-2px)'"
                onmouseout="this.style.background='var(--color-primario)'; this.style.transform='translateY(0)'">
                    <i class="fas fa-map-marked-alt me-1"></i> Ver todas las sucursales
                </a>
            </div>

            <!-- Redes Sociales -->
            <h5 class="mt-4 mb-3" style="color: var(--color-primario);">
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
                    style="width: 45px; height: 45px; display: inline-flex; align-items: center; justify-content: center; background: <?= $info['color'] ?>; color: white; border: none; transition: all 0.3s ease; box-shadow: 0 3px 10px rgba(0,0,0,0.3);"
                    onmouseover="this.style.transform='translateY(-5px) scale(1.1)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.4)'"
                    onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 3px 10px rgba(0,0,0,0.3)'">
                        <i class="<?= $info['icon'] ?>" style="font-size: 1.2rem;"></i>
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