<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';
require_once '../../config/database_sativa.php';

$mensaje = '';
$tipo_mensaje = '';

// ==========================================
// PROCESAR ACTUALIZACIÓN DE IMÁGENES
// ==========================================
if (isset($_POST['actualizar_imagenes'])) {
    $idProducto = isset($_POST['idProducto']) ? (int)$_POST['idProducto'] : 0;
    
    // Validación estricta: El producto DEBE existir en Sativa antes de guardar imágenes
    $stmtCheckProd = $pdo_sativa->prepare("SELECT id FROM producto WHERE id = ? AND Deleted = 0");
    $stmtCheckProd->execute([$idProducto]);
    if (!$stmtCheckProd->fetch()) {
        $mensaje = "Error: El producto ID $idProducto no existe o está eliminado en Sativa.";
        $tipo_mensaje = "danger";
    } else {
        // Función auxiliar para procesar cada imagen
        $procesarImagen = function($inputName) {
            if (!empty($_FILES[$inputName]['name'])) {
                $ext = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $nombreArchivo = time() . '_' . $inputName . '_' . uniqid() . '.' . $ext;
                    $upload_dir = '../../uploads/productos/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    
                    if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $upload_dir . $nombreArchivo)) {
                        return 'uploads/productos/' . $nombreArchivo;
                    }
                }
            }
            return null;
        };

        $path1 = $procesarImagen('path1');
        $path2 = $procesarImagen('path2');
        $path3 = $procesarImagen('path3');
        $path4 = $procesarImagen('path4');

        // Verificar si ya existe registro de imágenes
        $stmtCheckImg = $pdo_sativa->prepare("SELECT id FROM producto_imagenes WHERE idProducto = ?");
        $stmtCheckImg->execute([$idProducto]);
        $existe = $stmtCheckImg->fetch(PDO::FETCH_ASSOC);

        try {
            $pdo_sativa->beginTransaction();

            if ($existe) {
                $setClauses = [];
                $params = [];
                
                if ($path1) { $setClauses[] = "Path1 = ?"; $params[] = $path1; }
                if ($path2) { $setClauses[] = "Path2 = ?"; $params[] = $path2; }
                if ($path3) { $setClauses[] = "Path3 = ?"; $params[] = $path3; }
                if ($path4) { $setClauses[] = "Path4 = ?"; $params[] = $path4; }

                if (!empty($setClauses)) {
                    $params[] = $idProducto;
                    $sql = "UPDATE producto_imagenes SET " . implode(", ", $setClauses) . " WHERE idProducto = ?";
                    $stmt = $pdo_sativa->prepare($sql);
                    $stmt->execute($params);
                }
            } else {
                // Solo insertamos si al menos hay una imagen nueva
                if ($path1 || $path2 || $path3 || $path4) {
                    $stmt = $pdo_sativa->prepare("
                        INSERT INTO producto_imagenes (idProducto, Path1, Path2, Path3, Path4, Deleted)
                        VALUES (?, ?, ?, ?, ?, 0)
                    ");
                    $stmt->execute([$idProducto, $path1, $path2, $path3, $path4]);
                }
            }

            $pdo_sativa->commit();
            $mensaje = "Imágenes actualizadas correctamente.";
            $tipo_mensaje = "success";
        } catch (PDOException $e) {
            $pdo_sativa->rollBack();
            $mensaje = "Error de base de datos: " . $e->getMessage();
            $tipo_mensaje = "danger";
            // Para depuración: error_log("Error SQL: " . $e->getMessage() . " | ID Producto: " . $idProducto);
        }
    }
}

// ==========================================
// OBTENER PRODUCTOS DE SATIVA
// ==========================================
$productos = $pdo_sativa->query("
    SELECT 
        p.id, p.Codigo, p.Descripcion, p.Precio1, p.Activo,
        l.Descripcion as LineaNombre,
        pi.Path1, pi.Path2, pi.Path3, pi.Path4
    FROM producto p
    LEFT JOIN linea l ON p.idLinea = l.id
    LEFT JOIN producto_imagenes pi ON p.id = pi.idProducto AND pi.Deleted = 0
    WHERE p.Deleted = 0 AND p.Activo = 1
    ORDER BY p.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Catálogo de Productos <small class="text-muted">(Sincronizado con Sativa)</small></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Productos</li>
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title">
                                <i class="fas fa-box-open"></i> Listado de Productos (<?= count($productos) ?>)
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="tablaProductos" class="table table-bordered table-striped mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th>Línea</th>
                                            <th>Precio</th>
                                            <th class="text-center">Imágenes</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($productos as $producto): ?>
                                            <?php 
                                            // Contar imágenes para el badge
                                            $total_imgs = 0;
                                            if(!empty($producto['Path1'])) $total_imgs++;
                                            if(!empty($producto['Path2'])) $total_imgs++;
                                            if(!empty($producto['Path3'])) $total_imgs++;
                                            if(!empty($producto['Path4'])) $total_imgs++;
                                            
                                            // Preparar datos para el modal usando JSON (más seguro que data-attributes)
                                            $imgData = json_encode([
                                                'id' => $producto['id'],
                                                'codigo' => $producto['Codigo'],
                                                'p1' => $producto['Path1'] ?? '',
                                                'p2' => $producto['Path2'] ?? '',
                                                'p3' => $producto['Path3'] ?? '',
                                                'p4' => $producto['Path4'] ?? ''
                                            ], JSON_HEX_QUOT | JSON_HEX_TAG);
                                            ?>
                                            <tr>
                                                <td><?= $producto['id'] ?></td>
                                                <td><strong><?= htmlspecialchars($producto['Codigo']) ?></strong></td>
                                                <td><?= htmlspecialchars(substr($producto['Descripcion'], 0, 60)) ?><?= strlen($producto['Descripcion']) > 60 ? '...' : '' ?></td>
                                                <td><?= htmlspecialchars($producto['LineaNombre'] ?? 'Sin línea') ?></td>
                                                <td class="text-success font-weight-bold">$<?= number_format($producto['Precio1'], 2) ?></td>
                                                <td class="text-center">
                                                    <span class="badge badge-info"><?= $total_imgs ?>/4</span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-primary btn-edit-img" 
                                                            data-toggle="modal" 
                                                            data-target="#modalImagenes"
                                                            data-json='<?= $imgData ?>'>
                                                        <i class="fas fa-images"></i> Gestionar
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
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

<!-- ========================================== -->
<!-- MODAL PARA GESTIONAR IMÁGENES -->
<!-- ========================================== -->
<div class="modal fade" id="modalImagenes" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">
                        <i class="fas fa-images"></i> Gestionar Imágenes
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="idProducto" id="modalIdProducto">
                    
                    <div class="alert alert-info py-2">
                        <i class="fas fa-info-circle"></i> Sube solo las imágenes que deseas <strong>reemplazar</strong>. Deja en blanco las que quieras mantener.
                    </div>

                    <div class="row">
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold">Imagen <?= $i ?> (Path<?= $i ?>)</label>
                                
                                <!-- Vista previa -->
                                <div class="mb-2 text-center bg-light p-2 rounded border" style="height: 130px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <img id="preview<?= $i ?>" src="" class="img-fluid" style="max-height: 110px; display: none;">
                                    <span id="no-img<?= $i ?>" class="text-muted small">Sin imagen asignada</span>
                                </div>

                                <div class="custom-file">
                                    <input type="file" name="path<?= $i ?>" class="custom-file-input" id="inputPath<?= $i ?>" accept="image/*" onchange="previewImage(<?= $i ?>)">
                                    <label class="custom-file-label" for="inputPath<?= $i ?>">Seleccionar archivo...</label>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="actualizar_imagenes" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ✅ INCLUIR EL FOOTER PRIMERO (Aquí se carga jQuery) -->
<?php include '../../includes/footer.php'; ?>

<!-- ✅ SCRIPTS PERSONALIZADOS DESPUÉS DEL FOOTER -->
<script>
// Inyectamos BASE_URL desde PHP de forma segura
const BASE_URL = "<?= defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/' : '/' ?>";

function construirUrlImagen(path) {
    if (!path || path.trim() === '') return '';
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('//')) {
        return path;
    }
    return BASE_URL + path.replace(/^\/+/, '');
}

$(function(){
    $('#tablaProductos').DataTable({
        pageLength: 25,
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
        },
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [5, 6] }
        ]
    });

    // Al abrir el modal, cargar los datos usando JSON
    $('#modalImagenes').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var data = button.data('json'); 
        
        if (!data) return;

        $('#modalIdProducto').val(data.id);
        $('.modal-title').html('<i class="fas fa-images"></i> Imágenes de: <strong>' + data.codigo + '</strong> (ID: ' + data.id + ')');

        for (var i = 1; i <= 4; i++) {
            var path = data['p' + i];
            var preview = $('#preview' + i);
            var noImg = $('#no-img' + i);
            var label = $('label[for="inputPath' + i + '"]');

            if (path && path.trim() !== '') {
                var imgUrl = construirUrlImagen(path);
                preview.attr('src', imgUrl).show();
                noImg.hide();
                label.text('Reemplazar imagen ' + i);
            } else {
                preview.hide();
                noImg.show();
                label.text('Subir imagen ' + i);
            }
        }
    });

    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Seleccionar archivo...');
    });
});

function previewImage(num) {
    const input = document.getElementById('inputPath' + num);
    const preview = document.getElementById('preview' + num);
    const noImg = document.getElementById('no-img' + num);
    const label = document.querySelector('label[for="inputPath' + num + '"]');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            noImg.style.display = 'none';
            label.textContent = 'Archivo: ' + input.files[0].name;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include '../../includes/footer.php'; ?>