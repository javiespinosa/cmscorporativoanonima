<aside class="main-sidebar sidebar-dark-success elevation-4">

    <a href="<?= BASE_URL ?>admin/dashboard.php" class="brand-link">

        <span class="brand-text font-weight-light">
            CMS Corporativo
        </span>

    </a>

    <div class="sidebar">

        <nav class="mt-2">

            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">

                <li class="nav-item">

                    <a href="<?= BASE_URL ?>admin/dashboard.php" class="nav-link">

                        <i class="nav-icon fas fa-home"></i>

                        <p>Dashboard</p>

                    </a>

                </li>

                <li class="nav-item">

                    <a href="<?= BASE_URL ?>admin/configuracion/index.php" class="nav-link">

                        <i class="nav-icon fas fa-cogs"></i>

                        <p>Configuración General</p>

                    </a>

                </li>

                <li class="nav-item">

                    <a href="<?= BASE_URL ?>admin/categorias/index.php" class="nav-link">

                        <i class="nav-icon fas fa-list"></i>

                        <p>Categorías</p>

                    </a>

                </li>
                
                <li class="nav-item">

    <a href="<?= BASE_URL ?>admin/banners/index.php" class="nav-link">

        <i class="nav-icon fas fa-images"></i>

        <p>Banners</p>

    </a>

</li>

                <li class="nav-item">

                    <a href="<?= BASE_URL ?>admin/productos/index.php" class="nav-link">

                        <i class="nav-icon fas fa-box"></i>

                        <p>Productos</p>

                    </a>

                </li>

                <li class="nav-item">
    <a href="<?= BASE_URL ?>admin/productospremium/index.php" class="nav-link">
        <i class="nav-icon fas fa-crown text-warning"></i>
        <p>Productos Premium</p>
    </a>
</li>

                <li class="nav-item">

<a href="<?= BASE_URL ?>admin/cotizaciones/index.php"
class="nav-link">

<i class="nav-icon fas fa-file-alt"></i>

<p>Cotizaciones</p>

</a>

</li>

<li class="nav-item">
    <a href="<?= BASE_URL ?>admin/contacto/mensajes.php" class="nav-link">
        <i class="nav-icon fas fa-envelope"></i>
        <p>
            Mensajes de Contacto
            <?php
            // Caché de 60 segundos para no saturar la BD
            $cache_key = 'total_mensajes_nuevos';
            $cache_time = 60; // segundos
            
            if (!isset($_SESSION[$cache_key]) || $_SESSION[$cache_key]['time'] < time() - $cache_time) {
                try {
                    if (!isset($pdo)) {
                        require_once __DIR__ . '/../config/database.php';
                    }
                    $totalNuevos = $pdo->query("SELECT COUNT(*) FROM mensajes_contacto WHERE leido = 0")->fetchColumn();
                    $_SESSION[$cache_key] = ['value' => $totalNuevos, 'time' => time()];
                } catch (Exception $e) {
                    $totalNuevos = 0;
                }
            } else {
                $totalNuevos = $_SESSION[$cache_key]['value'];
            }
            
            if ($totalNuevos > 0):
            ?>
                <span class="badge badge-warning right"><?= $totalNuevos ?></span>
            <?php endif; ?>
        </p>
    </a>
</li>

<li class="nav-item">
    <a href="<?= BASE_URL ?>admin/promociones/index.php" class="nav-link">
        <i class="nav-icon fas fa-tags"></i>
        <p>Promociones</p>
    </a>
</li>

                <li class="nav-item">

                    <a href="<?= BASE_URL ?>logout.php" class="nav-link">

                        <i class="nav-icon fas fa-sign-out-alt"></i>

                        <p>Salir</p>

                    </a>

                </li>

            </ul>

        </nav>

    </div>

</aside>