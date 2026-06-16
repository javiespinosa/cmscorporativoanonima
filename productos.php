<?php
include 'includes/web_header.php';
include 'includes/web_menu.php';

// Cargar conexión a Sativa
require_once 'config/database_sativa.php';

// Función auxiliar para construir URL de imagen
function getUrlImagen($path) {
    if (empty($path)) return '';
    if (strpos($path, 'http') === 0 || strpos($path, '//') === 0) return $path;
    return (defined('BASE_URL') ? BASE_URL : '/') . ltrim($path, '/');
}

// Obtener productos activos y no eliminados de Sativa
$productos = $pdo_sativa->query("
    SELECT 
        p.id, 
        p.Codigo, 
        p.Descripcion, 
        p.Precio1, 
        pi.Path1 as imagen_principal
    FROM producto p
    LEFT JOIN producto_imagenes pi ON p.id = pi.idProducto AND pi.Deleted = 0
    WHERE p.Deleted = 0 AND p.Activo = 1
    ORDER BY p.Descripcion ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Contar productos para el badge
$totalProductos = count($productos);
?>

<!-- Estilos específicos para el catálogo -->
<style>
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
        box-shadow: 0 15px 40px rgba(var(--rgb-primario), 0.15);
    }
    .producto-img-wrapper {
        position: relative;
        overflow: hidden;
        height: 250px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .producto-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.6s ease;
        padding: 10px;
    }
    .producto-card:hover .producto-img-wrapper img {
        transform: scale(1.05);
    }
    .producto-card .card-body {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .producto-precio {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--color-primario);
        margin-bottom: 10px;
    }
    .producto-codigo {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 5px;
    }
</style>

<div class="container py-5">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h1 class="h2 fw-bold mb-1">Catálogo de Productos</h1>
            <p class="text-muted mb-0">Explora nuestra selección y solicita tu cotización.</p>
        </div>
        <div class="d-none d-md-block">
            <span class="badge bg-success fs-6 p-2">
                <?= $totalProductos ?> productos disponibles
            </span>
        </div>
    </div>

    <?php if (empty($productos)): ?>
        <!-- Estado: Catálogo vacío -->
        <div class="text-center py-5 bg-light rounded-3">
            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
            <h3 class="text-muted">No hay productos disponibles en este momento</h3>
            <p class="text-muted">Estamos actualizando nuestro inventario. ¡Vuelve pronto!</p>
        </div>
    <?php else: ?>
        <!-- Estado: Grid de productos -->
        <div class="row g-4">
            <?php foreach ($productos as $p): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="producto-card">
                        
                        <!-- Imagen del producto -->
                        <a href="producto.php?id=<?= $p['id'] ?>" class="text-decoration-none">
                            <div class="producto-img-wrapper">
                                <?php if (!empty($p['imagen_principal'])): ?>
                                    <img src="<?= htmlspecialchars(getUrlImagen($p['imagen_principal'])) ?>" 
                                         alt="<?= htmlspecialchars($p['Descripcion']) ?>"
                                         onerror="this.src='https://placehold.co/300x300/e9ecef/6c757d?text=Sin+Imagen'">
                                <?php else: ?>
                                    <img src="https://placehold.co/300x300/e9ecef/6c757d?text=Sin+Imagen" alt="Sin imagen">
                                <?php endif; ?>
                            </div>
                        </a>

                        <!-- Cuerpo de la tarjeta -->
                        <div class="card-body">
                            <div class="producto-codigo">Código: <?= htmlspecialchars($p['Codigo']) ?></div>
                            <h5 class="card-title fw-bold mb-2" style="font-size: 1rem; line-height: 1.3; min-height: 40px;">
                                <a href="producto.php?id=<?= $p['id'] ?>" class="text-dark text-decoration-none">
                                    <?= htmlspecialchars($p['Descripcion']) ?>
                                </a>
                            </h5>
                            
                            <div class="producto-precio">
                                $<?= number_format($p['Precio1'], 2) ?>
                            </div>
                            
                            <!-- Espaciador para empujar los botones al fondo -->
                            <div class="mt-auto pt-3">
                                <div class="d-grid gap-2">
                                    <a href="producto.php?id=<?= $p['id'] ?>" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-eye me-1"></i> Ver detalle
                                    </a>
                                    <a href="cotizacion_agregar.php?id=<?= $p['id'] ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-cart-plus me-1"></i> Cotizar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/web_footer.php'; ?>