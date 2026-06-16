<?php
session_start();

include 'includes/web_header.php';
include 'includes/web_menu.php';
require_once 'config/database_sativa.php';

// 1. PROCESAR ELIMINACIÓN
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    if (isset($_SESSION['cotizacion'][$id_eliminar])) {
        unset($_SESSION['cotizacion'][$id_eliminar]);
        if (empty($_SESSION['cotizacion'])) {
            unset($_SESSION['cotizacion']);
        }
    }
    header("Location: cotizacion.php");
    exit;
}

// 2. PROCESAR ACTUALIZACIÓN DE CANTIDADES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_cantidades'])) {
    if (isset($_POST['cantidades']) && is_array($_POST['cantidades'])) {
        foreach ($_POST['cantidades'] as $pid => $cantidad) {
            $cantidad = (int)$cantidad;
            $pid = (int)$pid;
            if ($cantidad > 0) {
                $_SESSION['cotizacion'][$pid] = $cantidad;
            } else {
                unset($_SESSION['cotizacion'][$pid]);
            }
        }
        if (empty($_SESSION['cotizacion'])) {
            unset($_SESSION['cotizacion']);
        }
    }
    header("Location: cotizacion.php");
    exit;
}

// 3. OBTENER PRODUCTOS DEL CARRITO DESDE SATIVA
$productos_carrito = [];
$total_general = 0;

if (!empty($_SESSION['cotizacion'])) {
    $ids = array_keys($_SESSION['cotizacion']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $stmt = $pdo_sativa->prepare("
        SELECT p.id, p.Codigo, p.Descripcion, p.Precio1, pi.Path1 as imagen
        FROM producto p
        LEFT JOIN producto_imagenes pi ON p.id = pi.idProducto AND pi.Deleted = 0
        WHERE p.id IN ($placeholders) AND p.Activo = 1 AND p.Deleted = 0
    ");
    $stmt->execute($ids);
    $productos_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular total general
    foreach ($productos_carrito as $p) {
        $cantidad = $_SESSION['cotizacion'][$p['id']];
        $total_general += ($p['Precio1'] * $cantidad);
    }
}

// Función auxiliar para imágenes
function getUrlImg($path) {
    if (empty($path)) return 'https://placehold.co/80x80/e9ecef/6c757d?text=Sin+Img';
    if (strpos($path, 'http') === 0) return $path;
    return (defined('BASE_URL') ? BASE_URL : '/') . ltrim($path, '/');
}
?>

<style>
    .cart-table th { 
        background-color: #f8f9fa; 
        font-weight: 600; 
        color: #2c3e50; 
    }
    .cart-total { 
        font-size: 1.5rem; 
        font-weight: 800; 
        color: var(--color-primario); /* ← CAMBIADO de #28a745 */
    }
    .qty-input { 
        width: 70px; 
        text-align: center; 
    }
    /* Sombra dinámica al pasar el mouse sobre la tabla */
    .cart-table tbody tr:hover {
        box-shadow: 0 4px 12px rgba(var(--rgb-primario), 0.1);
    }
</style>

<div class="container py-5">
    <h1 class="h2 fw-bold mb-4 text-center">Tu Cotización</h1>

    <?php if (empty($productos_carrito)): ?>
        <!-- Carrito Vacío -->
        <div class="text-center py-5 bg-light rounded-3">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
            <h3 class="text-muted">Tu cotización está vacía</h3>
            <p class="text-muted mb-4">Agrega productos desde nuestro catálogo para comenzar.</p>
            <a href="productos.php" class="btn btn-success btn-lg" style="border-radius: 50px; padding: 12px 40px;">
                <i class="fas fa-arrow-left me-2"></i> Ver Catálogo de Productos
            </a>
        </div>
    <?php else: ?>
        
        <!-- Formulario para actualizar cantidades -->
        <form method="POST" action="cotizacion.php">
            <div class="table-responsive mb-4">
                <table class="table table-hover align-middle cart-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Imagen</th>
                            <th>Código</th>
                            <th>Producto</th>
                            <th class="text-center" style="width: 120px;">Cantidad</th>
                            <th class="text-end" style="width: 130px;">P. Unitario</th>
                            <th class="text-end" style="width: 130px;">Subtotal</th>
                            <th class="text-center" style="width: 80px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos_carrito as $p): 
                            $cantidad = $_SESSION['cotizacion'][$p['id']];
                            $subtotal = $p['Precio1'] * $cantidad;
                        ?>
                            <tr>
                                <td>
                                    <img src="<?= getUrlImg($p['imagen']) ?>" 
                                         class="img-thumbnail" 
                                         style="width: 60px; height: 60px; object-fit: contain;" 
                                         alt="<?= htmlspecialchars($p['Descripcion']) ?>">
                                </td>
                                <td><strong><?= htmlspecialchars($p['Codigo']) ?></strong></td>
                                <td><?= htmlspecialchars($p['Descripcion']) ?></td>
                                <td class="text-center">
                                    <input type="number" name="cantidades[<?= $p['id'] ?>]" 
                                           value="<?= $cantidad ?>" min="1" 
                                           class="form-control form-control-sm qty-input mx-auto">
                                </td>
                                <td class="text-end">$<?= number_format($p['Precio1'], 2) ?></td>
                                <td class="text-end fw-bold" style="color: var(--color-primario);">
                                    $<?= number_format($subtotal, 2) ?>
                                </td>
                                <td class="text-center">
                                    <a href="?eliminar=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="return confirm('¿Eliminar este producto?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="productos.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Seguir agregando productos
                </a>
                <button type="submit" name="actualizar_cantidades" class="btn btn-primary">
                    <i class="fas fa-sync me-2"></i> Actualizar Cantidades
                </button>
            </div>
        </form>

        <!-- Resumen y Formulario de Envío -->
        <div class="row g-4">
            <!-- Resumen de Total -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">Resumen de la Cotización</h4>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Productos distintos:</span>
                            <strong><?= count($productos_carrito) ?></strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">Total Estimado:</span>
                            <span class="cart-total mb-0">$<?= number_format($total_general, 2) ?></span>
                        </div>
                        <small class="text-muted d-block mt-2">* Precios sujetos a cambio sin previo aviso. IVA no incluido.</small>
                    </div>
                </div>
            </div>

            <!-- Formulario de Datos del Cliente -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Solicitar Cotización Formal</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted mb-4">Completa tus datos para enviarte la cotización oficial por correo electrónico.</p>
                        
                        <form method="POST" action="procesar_cotizacion.php">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Empresa</label>
                                    <input type="text" name="empresa" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Teléfono / WhatsApp <span class="text-danger">*</span></label>
                                    <input type="tel" name="telefono" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" name="correo" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Comentarios o Indicaciones Especiales</label>
                                    <textarea name="comentarios" class="form-control" rows="3" placeholder="Ej. Necesito factura, dirección de envío, etc."></textarea>
                                </div>
                                <div class="col-12 text-end mt-4">
                                    <button type="submit" class="btn btn-success btn-lg px-5" style="border-radius: 50px;">
                                        <i class="fas fa-check-circle me-2"></i> Enviar Solicitud
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<?php include 'includes/web_footer.php'; ?>