<?php
include 'includes/web_header.php';
include 'includes/web_menu.php';

// Cargar conexión a Sativa
require_once 'config/database_sativa.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. Obtener datos del producto
$stmt = $pdo_sativa->prepare("
    SELECT * FROM producto 
    WHERE id = ? AND Deleted = 0 AND Activo = 1
");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Obtener imágenes del producto (hasta 4)
$imagenes = ['Path1' => null, 'Path2' => null, 'Path3' => null, 'Path4' => null];
if ($producto) {
    $stmtImg = $pdo_sativa->prepare("
        SELECT Path1, Path2, Path3, Path4 
        FROM producto_imagenes 
        WHERE idProducto = ? AND Deleted = 0
    ");
    $stmtImg->execute([$id]);
    $imgData = $stmtImg->fetch(PDO::FETCH_ASSOC);
    if ($imgData) {
        $imagenes = $imgData;
    }
}

// Función auxiliar para construir URL de imagen
function getUrlImagen($path) {
    if (empty($path)) return '';
    if (strpos($path, 'http') === 0 || strpos($path, '//') === 0) return $path;
    return (defined('BASE_URL') ? BASE_URL : '/') . ltrim($path, '/');
}
?>

<div class="container py-5">
    
    <?php if (!$producto): ?>
        <!-- Estado: Producto no encontrado o inactivo -->
        <div class="text-center py-5">
            <h2 class="text-muted">Producto no disponible</h2>
            <p class="text-muted">El producto que buscas no existe, está inactivo o ha sido retirado.</p>
            <a href="productos.php" class="btn btn-success mt-3">
                ← Volver al catálogo
            </a>
        </div>
    <?php else: ?>
        
        <!-- Migas de pan -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Inicio</a></li>
                <li class="breadcrumb-item"><a href="productos.php" class="text-decoration-none">Productos</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($producto['Codigo']) ?></li>
            </ol>
        </nav>

        <div class="row g-5">
            <!-- Columna de Imágenes (Galería) -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body p-4 text-center">
                        <!-- Imagen Principal -->
                        <?php 
                        $imgPrincipal = !empty($imagenes['Path1']) ? getUrlImagen($imagenes['Path1']) : 'https://placehold.co/600x600/e9ecef/6c757d?text=Sin+Imagen';
                        ?>
                        <img id="mainImage" src="<?= htmlspecialchars($imgPrincipal) ?>" 
                             class="img-fluid rounded mb-3" 
                             style="max-height: 400px; object-fit: contain;" 
                             alt="<?= htmlspecialchars($producto['Descripcion']) ?>">
                        
                        <!-- Miniaturas (si existen Path2, Path3, Path4) -->
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <?php 
                            $thumbs = [$imagenes['Path1'], $imagenes['Path2'], $imagenes['Path3'], $imagenes['Path4']];
                            foreach ($thumbs as $thumb): 
                                if (!empty($thumb)): 
                            ?>
                                <img src="<?= htmlspecialchars(getUrlImagen($thumb)) ?>" 
                                     class="img-thumbnail" 
                                     style="width: 70px; height: 70px; object-fit: contain; cursor: pointer; border: 2px solid transparent;"
                                     onclick="cambiarImagen(this)"
                                     alt="Vista">
                            <?php 
                                endif; 
                            endforeach; 
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna de Información -->
            <div class="col-lg-6 d-flex flex-column justify-content-center">
                <span class="badge bg-secondary mb-2" style="width: fit-content;">
                    Código: <?= htmlspecialchars($producto['Codigo']) ?>
                </span>
                
                <h1 class="display-6 fw-bold mb-3">
                    <?= htmlspecialchars($producto['Descripcion']) ?>
                </h1>
                
                <div class="mb-4">
                    <h2 class="text-success fw-bold">
                        $<?= number_format($producto['Precio1'], 2) ?>
                    </h2>
                    <?php if (!empty($producto['Notas'])): ?>
                        <p class="text-muted small mt-2">
                            <i class="fas fa-info-circle me-1"></i> <?= htmlspecialchars($producto['Notas']) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <h5 class="text-muted mb-3">Descripción:</h5>
                    <div class="bg-light p-3 rounded" style="line-height: 1.8; color: #495057;">
                        <?= nl2br(htmlspecialchars($producto['Descripcion'])) ?>
                    </div>
                </div>

                <div class="d-grid gap-3 d-md-flex">
                    <a href="cotizacion_agregar.php?id=<?= $producto['id'] ?>" class="btn btn-success btn-lg px-4">
                        <i class="fas fa-cart-plus me-2"></i> Agregar a Cotización
                    </a>
                    <a href="productos.php" class="btn btn-outline-secondary btn-lg px-4">
                        ← Seguir viendo
                    </a>
                </div>
                
                <!-- Datos técnicos opcionales (si los usas) -->
                <?php if ($producto['Existencia'] > 0): ?>
                    <div class="mt-4 text-success small">
                        <i class="fas fa-check-circle me-1"></i> Producto disponible en inventario
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
// Función para cambiar imagen principal y actualizar borde dinámicamente
function cambiarImagen(thumb) {
    // Cambiar imagen principal
    document.getElementById('mainImage').src = thumb.src;
    
    // Quitar borde de todas las miniaturas
    document.querySelectorAll('.img-thumbnail').forEach(i => {
        i.style.borderColor = 'transparent';
    });
    
    // Obtener el color primario de las variables CSS
    const colorPrimario = getComputedStyle(document.documentElement).getPropertyValue('--color-primario').trim();
    
    // Aplicar el color dinámico a la miniatura seleccionada
    thumb.style.borderColor = colorPrimario;
}
</script>

<?php include 'includes/web_footer.php'; ?>