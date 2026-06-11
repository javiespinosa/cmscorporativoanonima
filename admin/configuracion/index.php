<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';

$mensaje = '';
$tipo_mensaje = 'success';

// 1. OBTENER CONFIGURACIÓN ACTUAL AL INICIO (Necesario para no perder el logo)
$config = $pdo->query("SELECT * FROM configuracion WHERE id=1")->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger datos usando null coalescing (??) para evitar warnings si falta algún campo
    $empresa = $_POST['empresa'] ?? '';
    $giro = $_POST['giro'] ?? '';
    $slogan = $_POST['slogan'] ?? '';
    $quienes_somos = $_POST['quienes_somos'] ?? '';
    $mision = $_POST['mision'] ?? '';
    $vision = $_POST['vision'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $whatsapp = $_POST['whatsapp'] ?? '';
    $correo = $_POST['correo'] ?? '';
    
    // 2. MANTENER EL LOGO ACTUAL POR DEFECTO
    $logo = $config['logo']; 

    // 3. PROCESAR SUBIDA DE LOGO (Solo si se envió uno nuevo)
    if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));

        // Validar extensión
        if (in_array($ext, $allowed_ext)) {
            // 🔥 Validación de MIME type por seguridad (evita subir PHP disfrazado)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['logo']['tmp_name']);
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (in_array($mime, $allowed_mimes)) {
                $new_filename = time() . '_logo.' . $ext;
                $upload_dir = __DIR__ . '/../../uploads/logos/';
                $upload_path = $upload_dir . $new_filename;
                
                // Asegurar que el directorio existe
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                // Mover archivo y verificar éxito
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                    $logo = 'uploads/logos/' . $new_filename;
                } else {
                    $mensaje = "Error al guardar la imagen en el servidor (permisos).";
                    $tipo_mensaje = 'danger';
                }
            } else {
                $mensaje = "El tipo de archivo no es una imagen válida.";
                $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = "Extensión no permitida. Solo: " . implode(', ', $allowed_ext);
            $tipo_mensaje = 'danger';
        }
    }

    // 4. GUARDAR EN BASE DE DATOS (Solo si no hubo errores en la subida)
    if (empty($mensaje)) {
        try {
            $sql = "UPDATE configuracion
                    SET empresa=?, giro=?, slogan=?, quienes_somos=?, mision=?, 
                        vision=?, direccion=?, telefono=?, whatsapp=?, correo=?, logo=?
                    WHERE id=1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $empresa, $giro, $slogan, $quienes_somos, $mision, $vision,
                $direccion, $telefono, $whatsapp, $correo, $logo
            ]);
            
            $mensaje = "Información actualizada correctamente.";
            $tipo_mensaje = 'success';
            
            // Actualizar la variable en memoria para que el formulario muestre los nuevos datos al recargar
            $config['empresa'] = $empresa;
            $config['giro'] = $giro;
            $config['slogan'] = $slogan;
            $config['quienes_somos'] = $quienes_somos;
            $config['mision'] = $mision;
            $config['vision'] = $vision;
            $config['direccion'] = $direccion;
            $config['telefono'] = $telefono;
            $config['whatsapp'] = $whatsapp;
            $config['correo'] = $correo;
            $config['logo'] = $logo;

        } catch (PDOException $e) {
            $mensaje = "Error en base de datos: " . $e->getMessage();
            $tipo_mensaje = 'danger';
        }
    }
}

include '../../includes/header.php';
include '../../includes/navbar.php';
include '../../includes/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content p-3">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h3>Configuración General</h3>
            </div>
            <div class="card-body">
                <?php if(!empty($mensaje)): ?>
                    <div class="alert alert-<?= $tipo_mensaje ?>">
                        <?= htmlspecialchars($mensaje) ?>
                    </div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Empresa</label>
                            <input type="text" name="empresa" class="form-control" value="<?= htmlspecialchars($config['empresa'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Giro</label>
                            <input type="text" name="giro" class="form-control" value="<?= htmlspecialchars($config['giro'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Slogan</label>
                        <input type="text" name="slogan" class="form-control" value="<?= htmlspecialchars($config['slogan'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label>Quiénes Somos</label>
                        <textarea name="quienes_somos" class="form-control" rows="5"><?= htmlspecialchars($config['quienes_somos'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Misión</label>
                        <textarea name="mision" class="form-control" rows="4"><?= htmlspecialchars($config['mision'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Visión</label>
                        <textarea name="vision" class="form-control" rows="4"><?= htmlspecialchars($config['vision'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($config['direccion'] ?? '') ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($config['telefono'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>WhatsApp</label>
                            <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($config['whatsapp'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Correo</label>
                            <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($config['correo'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- 🔥 MEJORA: Previsualización del logo actual -->
                    <div class="mb-3">
                        <label>Logo Actual</label><br>
                        <?php if (!empty($config['logo'])): ?>
                            <img src="../../<?= htmlspecialchars($config['logo']) ?>" alt="Logo" style="max-height: 100px; margin-bottom: 10px; border: 1px solid #ddd; padding: 5px;" class="img-thumbnail">
                        <?php else: ?>
                            <p class="text-muted">No hay logo configurado.</p>
                        <?php endif; ?>
                        
                        <label class="mt-2">Cambiar Logo (dejar en blanco para mantener el actual)</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-success">Guardar</button>
                </form>
            </div>
        </div>
    </section>
</div>

<?php include '../../includes/footer.php'; ?>