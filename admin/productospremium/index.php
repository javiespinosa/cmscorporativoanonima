<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php'; // Base de datos local del CMS

$mensaje = '';
$tipo_mensaje = '';

// ==========================================
// PROCESAR ELIMINACIÓN
// ==========================================
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    
    // Obtener imagen para borrarla del servidor
    $stmt = $pdo->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($prod && !empty($prod['imagen'])) {
        $ruta = __DIR__ . '/../../uploads/productos/' . $prod['imagen'];
        if (file_exists($ruta)) unlink($ruta);
    }
    
    $pdo->prepare("DELETE FROM productos WHERE id = ?")->execute([$id]);
    $mensaje = "Producto premium eliminado correctamente.";
    $tipo_mensaje = "success";
}

// ==========================================
// PROCESAR GUARDADO (AGREGAR / EDITAR)
// ==========================================
if (isset($_POST['guardar'])) {
    $id_editar = isset($_POST['id_editar']) ? (int)$_POST['id_editar'] : 0;
    $categoria_id = (int)$_POST['categoria_id'];
    $nombre = trim($_POST['nombre']);
    $descripcion_corta = trim($_POST['descripcion_corta']);
    $descripcion_larga = trim($_POST['descripcion_larga']);
    $imagen_actual = '';

    // Si es edición, obtener la imagen actual
    if ($id_editar > 0) {
        $stmt = $pdo->prepare("SELECT imagen FROM productos WHERE id = ?");
        $stmt->execute([$id_editar]);
        $imagen_actual = $stmt->fetchColumn();
    }

    $nueva_imagen = $imagen_actual;

    // Procesar nueva imagen si se subió
    if (!empty($_FILES['imagen']['name'])) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($ext, $allowed_ext)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
            
            if (in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
                if ($_FILES['imagen']['size'] <= 5 * 1024 * 1024) { // 5MB
                    $upload_dir = __DIR__ . '/../../uploads/productos/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    
                    $nombreArchivo = time() . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_dir . $nombreArchivo)) {
                        $nueva_imagen = $nombreArchivo;
                        
                        // Borrar imagen anterior si existe y es diferente
                        if (!empty($imagen_actual) && $imagen_actual !== $nueva_imagen) {
                            $ruta_vieja = $upload_dir . $imagen_actual;
                            if (file_exists($ruta_vieja)) unlink($ruta_vieja);
                        }
                    } else {
                        $mensaje = "Error al guardar la imagen en el servidor.";
                        $tipo_mensaje = "danger";
                    }
                } else {
                    $mensaje = "La imagen no debe superar los 5MB.";
                    $tipo_mensaje = "danger";
                }
            } else {
                $mensaje = "El archivo no es una imagen válida.";
                $tipo_mensaje = "danger";
            }
        } else {
            $mensaje = "Formato no permitido. Use JPG, PNG o WEBP.";
            $tipo_mensaje = "danger";
        }
    }

    // Guardar en BD si no hubo errores
    if (empty($tipo_mensaje) || $tipo_mensaje !== 'danger') {
        try {
            if ($id_editar > 0) {
                // Actualizar
                $stmt = $pdo->prepare("
                    UPDATE productos SET categoria_id=?, nombre=?, descripcion_corta=?, descripcion_larga=?, imagen=?
                    WHERE id=?
                ");
                $stmt->execute([$categoria_id, $nombre, $descripcion_corta, $descripcion_larga, $nueva_imagen, $id_editar]);
                $mensaje = "Producto premium actualizado correctamente.";
            } else {
                // Insertar
                $stmt = $pdo->prepare("
                    INSERT INTO productos (categoria_id, nombre, descripcion_corta, descripcion_larga, imagen)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$categoria_id, $nombre, $descripcion_corta, $descripcion_larga, $nueva_imagen]);
                $mensaje = "Producto premium agregado correctamente.";
            }
            $tipo_mensaje = "success";
        } catch (PDOException $e) {
            $mensaje = "Error en la base de datos: " . $e->getMessage();
            $tipo_mensaje = "danger";
        }
    }
}

// ==========================================
// OBTENER DATOS
// ==========================================
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

$productos = $pdo->query("
    SELECT p.*, c.nombre as categoria_nombre 
    FROM productos p 
    LEFT JOIN categorias c ON p.categoria_id = c.id 
    ORDER BY p.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Si estamos editando, cargar los datos del producto
$editando = null;
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch(PDO::FETCH_ASSOC);
}

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-crown text-warning"></i> Productos Premium</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Productos Premium</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-<?= $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- FORMULARIO -->
                <div class="col-md-4">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-<?= $editando ? 'edit' : 'plus-circle' ?>"></i> 
                                <?= $editando ? 'Editar Producto' : 'Nuevo Producto Premium' ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id_editar" value="<?= $editando['id'] ?? 0 ?>">
                                
                                <div class="form-group">
                                    <label>Categoría</label>
                                    <select name="categoria_id" class="form-control" required>
                                        <option value="">Seleccione</option>
                                        <?php foreach ($categorias as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= ($editando['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Nombre del Producto</label>
                                    <input type="text" name="nombre" class="form-control" required 
                                           value="<?= htmlspecialchars($editando['nombre'] ?? '') ?>">
                                </div>

                                <div class="form-group">
                                    <label>Descripción Corta</label>
                                    <textarea name="descripcion_corta" class="form-control" rows="2"><?= htmlspecialchars($editando['descripcion_corta'] ?? '') ?></textarea>
                                    <small class="text-muted">Se muestra en la tarjeta del inicio.</small>
                                </div>

                                <div class="form-group">
                                    <label>Descripción Larga</label>
                                    <textarea name="descripcion_larga" class="form-control" rows="4"><?= htmlspecialchars($editando['descripcion_larga'] ?? '') ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Imagen</label>
                                    <?php if (!empty($editando['imagen'])): ?>
                                        <div class="mb-2">
                                            <img src="<?= BASE_URL ?>uploads/productos/<?= htmlspecialchars($editando['imagen']) ?>" 
                                                 class="img-thumbnail" style="max-height: 100px;">
                                            <p class="text-muted small mb-0">Imagen actual</p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="custom-file">
                                        <input type="file" name="imagen" class="custom-file-input" id="imagenInput" accept="image/*">
                                        <label class="custom-file-label" for="imagenInput"><?= $editando ? 'Cambiar imagen...' : 'Seleccionar imagen...' ?></label>
                                    </div>
                                    <small class="text-muted">JPG, PNG o WEBP. Máx. 5MB.</small>
                                </div>

                                <button type="submit" name="guardar" class="btn btn-warning btn-block">
                                    <i class="fas fa-save"></i> <?= $editando ? 'Actualizar' : 'Guardar' ?>
                                </button>
                                <?php if ($editando): ?>
                                    <a href="index.php" class="btn btn-secondary btn-block mt-2">
                                        <i class="fas fa-times"></i> Cancelar Edición
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- LISTADO -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h3 class="card-title"><i class="fas fa-list"></i> Listado de Productos Premium</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="tablaPremium" class="table table-bordered table-striped mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Imagen</th>
                                            <th>Producto</th>
                                            <th>Categoría</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($productos)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    No hay productos premium registrados.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($productos as $prod): ?>
                                                <tr>
                                                    <td><?= $prod['id'] ?></td>
                                                    <td>
                                                        <?php if (!empty($prod['imagen'])): ?>
                                                            <img src="<?= BASE_URL ?>uploads/productos/<?= htmlspecialchars($prod['imagen']) ?>" 
                                                                 class="img-thumbnail" style="width: 70px; height: 70px; object-fit: contain;">
                                                        <?php else: ?>
                                                            <span class="text-muted small">Sin imagen</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($prod['nombre']) ?></strong>
                                                        <br><small class="text-muted"><?= htmlspecialchars(substr($prod['descripcion_corta'], 0, 50)) ?>...</small>
                                                    </td>
                                                    <td><?= htmlspecialchars($prod['categoria_nombre'] ?? 'Sin categoría') ?></td>
                                                    <td class="text-center">
                                                        <a href="?editar=<?= $prod['id'] ?>" class="btn btn-sm btn-info" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="?eliminar=<?= $prod['id'] ?>" class="btn btn-sm btn-danger" 
                                                           onclick="return confirm('¿Estás seguro de eliminar este producto? La imagen también se borrará.')" title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(function(){
    $('#tablaPremium').DataTable({
        pageLength: 10,
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
        },
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [4] }
        ]
    });

    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Seleccionar imagen...');
    });
});
</script>

<?php include '../../includes/footer.php'; ?>