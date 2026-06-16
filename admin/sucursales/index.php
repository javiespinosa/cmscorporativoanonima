<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';

$mensaje = '';
$tipo_mensaje = '';

// PROCESAR ELIMINACIÓN
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $pdo->prepare("DELETE FROM sucursales WHERE id = ?")->execute([(int)$_GET['eliminar']]);
    $mensaje = "Sucursal eliminada correctamente.";
    $tipo_mensaje = "success";
}

// PROCESAR GUARDADO (AGREGAR / EDITAR)
if (isset($_POST['guardar'])) {
    $id_editar = isset($_POST['id_editar']) ? (int)$_POST['id_editar'] : 0;
    $nombre = trim($_POST['nombre']);
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);
    $whatsapp = trim($_POST['whatsapp']);
    $google_maps = trim($_POST['google_maps']);
    $es_principal = isset($_POST['es_principal']) ? 1 : 0;
    $activo = isset($_POST['activo']) ? 1 : 0;
    $orden = (int)($_POST['orden'] ?? 0);

    // Capturar horarios día por día y convertirlos a JSON
    $horarios_array = [
        'Lunes'       => trim($_POST['h_lunes'] ?? 'Cerrado'),
        'Martes'      => trim($_POST['h_martes'] ?? 'Cerrado'),
        'Miércoles'   => trim($_POST['h_miercoles'] ?? 'Cerrado'),
        'Jueves'      => trim($_POST['h_jueves'] ?? 'Cerrado'),
        'Viernes'     => trim($_POST['h_viernes'] ?? 'Cerrado'),
        'Sábado'      => trim($_POST['h_sabado'] ?? 'Cerrado'),
        'Domingo'     => trim($_POST['h_domingo'] ?? 'Cerrado')
    ];
    $horarios_json = json_encode($horarios_array);

    try {
        if ($id_editar > 0) {
            $stmt = $pdo->prepare("UPDATE sucursales SET nombre=?, direccion=?, telefono=?, whatsapp=?, horarios_semanales=?, google_maps=?, es_principal=?, activo=?, orden=? WHERE id=?");
            $stmt->execute([$nombre, $direccion, $telefono, $whatsapp, $horarios_json, $google_maps, $es_principal, $activo, $orden, $id_editar]);
            $mensaje = "Sucursal actualizada correctamente.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO sucursales (nombre, direccion, telefono, whatsapp, horarios_semanales, google_maps, es_principal, activo, orden) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $direccion, $telefono, $whatsapp, $horarios_json, $google_maps, $es_principal, $activo, $orden]);
            $mensaje = "Sucursal agregada correctamente.";
        }
        $tipo_mensaje = "success";
    } catch (PDOException $e) {
        $mensaje = "Error: " . $e->getMessage();
        $tipo_mensaje = "danger";
    }
}

// OBTENER DATOS
$sucursales = $pdo->query("SELECT * FROM sucursales ORDER BY orden ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);

$editando = null;
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM sucursales WHERE id = ?");
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
                    <h1><i class="fas fa-store text-primary"></i> Gestión de Sucursales</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Sucursales</li>
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
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- FORMULARIO -->
                <div class="col-md-5">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-<?= $editando ? 'edit' : 'plus-circle' ?>"></i> <?= $editando ? 'Editar' : 'Nueva' ?> Sucursal</h3>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <input type="hidden" name="id_editar" value="<?= $editando['id'] ?? 0 ?>">
                                
                                <div class="form-group">
                                    <label>Nombre de la Sucursal <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($editando['nombre'] ?? '') ?>" placeholder="Ej. Sucursal Centro">
                                </div>

                                <div class="form-group">
                                    <label>Dirección Completa <span class="text-danger">*</span></label>
                                    <textarea name="direccion" class="form-control" rows="2" required placeholder="Calle, número, colonia, ciudad..."><?= htmlspecialchars($editando['direccion'] ?? '') ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Teléfono</label>
                                            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($editando['telefono'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>WhatsApp</label>
                                            <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($editando['whatsapp'] ?? '') ?>" placeholder="52 1 55...">
                                        </div>
                                    </div>
                                </div>

                                <!-- HORARIO SEMANAL ESTRUCTURADO -->
                                <div class="form-group mb-3">
                                    <label><i class="fas fa-clock text-warning"></i> Horario de Atención (Día por día)</label>
                                    <?php 
                                    // Decodificar horarios existentes para editar
                                    $h_edit = json_decode($editando['horarios_semanales'] ?? '{}', true) ?: [];
                                    $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                                    ?>
                                    <div class="row g-2">
                                        <?php foreach ($dias as $dia): 
                                            $val = $h_edit[$dia] ?? 'Cerrado';
                                        ?>
                                            <div class="col-6 col-md-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text" style="width: 85px; font-size: 0.8rem;"><?= $dia ?></span>
                                                    <input type="text" name="h_<?= strtolower($dia) ?>" class="form-control" 
                                                           value="<?= htmlspecialchars($val) ?>" placeholder="Ej. 9:00 - 18:00">
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <small class="text-muted">Escribe "Cerrado" si no hay atención ese día.</small>
                                </div>


                                                               <!-- CÓDIGO DE GOOGLE MAPS -->
                                <div class="form-group mb-3">
                                    <label><i class="fab fa-google text-danger"></i> Código iframe de Google Maps</label>
                                    <!-- AQUÍ ESTABA EL ERROR: Faltaba imprimir el valor guardado entre las etiquetas -->
                                    <textarea name="google_maps" class="form-control" rows="4" placeholder='<iframe src="https://www.google.com/maps/embed?...'><?= htmlspecialchars($editando['google_maps'] ?? '') ?></textarea>
                                    
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle"></i> Ve a Google Maps → Compartir → Insertar mapa → Copiar HTML
                                    </small>
                                    
                                    <!-- Vista previa del mapa actual (solo si existe) -->
                                    <?php if (!empty($editando['google_maps'])): ?>
                                        <div class="mt-3 p-3 bg-light border rounded">
                                            <small class="text-muted d-block mb-2 fw-bold">Vista previa del mapa guardado:</small>
                                            <div class="ratio ratio-16x9">
                                                <?= $editando['google_maps'] ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Orden de aparición</label>
                                            <input type="number" name="orden" class="form-control" value="<?= $editando['orden'] ?? 0 ?>" min="0">
                                            <small class="text-muted">Menor número = aparece primero</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check mb-2">
                                    <input type="checkbox" name="es_principal" class="form-check-input" id="es_principal" <?= ($editando['es_principal'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="es_principal">⭐ Marcar como Sucursal Principal</label>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" name="activo" class="form-check-input" id="activo" <?= ($editando['activo'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="activo">Activa (Visible en el sitio)</label>
                                </div>

                                <button type="submit" name="guardar" class="btn btn-primary btn-block">
                                    <i class="fas fa-save"></i> <?= $editando ? 'Actualizar' : 'Guardar' ?>
                                </button>
                                <?php if ($editando): ?>
                                    <a href="index.php" class="btn btn-secondary btn-block mt-2"><i class="fas fa-times"></i> Cancelar</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- LISTADO -->
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fas fa-list"></i> Sucursales Registradas</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Orden</th>
                                            <th>Nombre</th>
                                            <th>Dirección</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($sucursales)): ?>
                                            <tr><td colspan="5" class="text-center text-muted py-4">No hay sucursales registradas.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($sucursales as $s): ?>
                                                <tr>
                                                    <td class="text-center"><?= $s['orden'] ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($s['nombre']) ?></strong>
                                                        <?php if ($s['es_principal']): ?><span class="badge badge-warning">Principal</span><?php endif; ?>
                                                    </td>
                                                    <td><small><?= htmlspecialchars(substr($s['direccion'], 0, 50)) ?>...</small></td>
                                                    <td class="text-center">
                                                        <?= $s['activo'] ? '<span class="badge badge-success">Activa</span>' : '<span class="badge badge-secondary">Inactiva</span>' ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="?editar=<?= $s['id'] ?>" class="btn btn-sm btn-info" title="Editar"><i class="fas fa-edit"></i></a>
                                                        <a href="?eliminar=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta sucursal?')" title="Eliminar"><i class="fas fa-trash"></i></a>
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

<?php include '../../includes/footer.php'; ?>