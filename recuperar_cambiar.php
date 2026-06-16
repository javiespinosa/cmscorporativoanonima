<?php
session_start();
require_once 'config/database.php';
require_once 'includes/config_loader.php';

// Función para convertir HEX a RGB
if (!function_exists('hexToRgb')) {
    function hexToRgb($hex) {
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return "$r, $g, $b";
    }
}

// Obtener colores de la configuración
$color_primario = $config['color_primario'] ?? '#28a745';
$color_secundario = $config['color_secundario'] ?? '#20c997';
$rgb_primario = hexToRgb($color_primario);
$rgb_secundario = hexToRgb($color_secundario);

$token = $_GET['token'] ?? '';
$mensaje = '';
$tipo_mensaje = '';
$token_valido = false;
$usuario_nombre = '';

if (empty($token)) {
    $mensaje = "Token no válido.";
    $tipo_mensaje = "danger";
} else {
    // Validar token
    $stmt = $pdo->prepare("
        SELECT pr.*, u.nombre 
        FROM password_resets pr
        INNER JOIN usuarios u ON u.id = pr.usuario_id
        WHERE pr.token = ? AND pr.usado = 0 AND pr.fecha_expiracion > NOW()
    ");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($reset) {
        $token_valido = true;
        $usuario_nombre = $reset['nombre'];
        $usuario_id = $reset['usuario_id'];
    } else {
        $mensaje = "Este enlace ha expirado o ya fue utilizado. Solicita uno nuevo.";
        $tipo_mensaje = "warning";
    }
}

// Procesar cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_valido) {
    $nueva_password = $_POST['nueva_password'] ?? '';
    $confirmar_password = $_POST['confirmar_password'] ?? '';
    
    if (strlen($nueva_password) < 6) {
        $mensaje = "La contraseña debe tener al menos 6 caracteres.";
        $tipo_mensaje = "danger";
    } elseif ($nueva_password !== $confirmar_password) {
        $mensaje = "Las contraseñas no coinciden.";
        $tipo_mensaje = "danger";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Actualizar contraseña
            $hash = password_hash($nueva_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmt->execute([$hash, $usuario_id]);
            
            // Marcar token como usado
            $stmt = $pdo->prepare("UPDATE password_resets SET usado = 1 WHERE token = ?");
            $stmt->execute([$token]);
            
            $pdo->commit();
            
            $mensaje = "¡Contraseña actualizada correctamente! Ya puedes iniciar sesión.";
            $tipo_mensaje = "success";
            $token_valido = false; // Ocultar formulario
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $mensaje = "Error al actualizar la contraseña. Intenta de nuevo.";
            $tipo_mensaje = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Variables CSS dinámicas */
        :root {
            --color-primario: <?= $color_primario ?>;
            --color-secundario: <?= $color_secundario ?>;
            --rgb-primario: <?= $rgb_primario ?>;
            --rgb-secundario: <?= $rgb_secundario ?>;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', sans-serif;
        }
        .recovery-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }
        .recovery-header {
            background: linear-gradient(135deg, var(--color-primario) 0%, var(--color-secundario) 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .recovery-header i { font-size: 4rem; margin-bottom: 15px; }
        .recovery-body { padding: 40px 30px; }
        .form-control {
            padding: 14px 15px 14px 45px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
        }
        .form-control:focus {
            border-color: var(--color-primario);
            box-shadow: 0 0 0 4px rgba(var(--rgb-primario), 0.1);
        }
        .input-wrapper { position: relative; }
        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 2;
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 2;
            transition: color 0.3s ease;
        }
        .toggle-password:hover {
            color: var(--color-primario);
        }
        .btn-recovery {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--color-primario), var(--color-secundario));
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-recovery:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(var(--rgb-primario), 0.4);
            color: white;
        }
        .password-strength {
            height: 5px;
            background: #e9ecef;
            border-radius: 3px;
            margin-top: 8px;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="recovery-card">
        <div class="recovery-header">
            <i class="fas fa-lock"></i>
            <h3>Nueva Contraseña</h3>
            <?php if ($usuario_nombre): ?>
                <p class="mb-0">Hola <?= htmlspecialchars($usuario_nombre) ?></p>
            <?php endif; ?>
        </div>
        <div class="recovery-body">
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?>">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>
            
            <?php if ($token_valido): ?>
                <p class="text-muted text-center mb-4">
                    Ingresa tu nueva contraseña. Debe tener al menos 6 caracteres.
                </p>
                
                <form method="POST" id="formPassword">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nueva Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" name="nueva_password" id="nueva_password" 
                                   class="form-control" placeholder="Mínimo 6 caracteres" required minlength="6">
                            <i class="fas fa-lock"></i>
                            <span class="toggle-password" onclick="togglePass('nueva_password', 'icon1')">
                                <i class="fas fa-eye" id="icon1"></i>
                            </span>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <small class="text-muted" id="strengthText">Fortaleza de la contraseña</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Confirmar Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" name="confirmar_password" id="confirmar_password" 
                                   class="form-control" placeholder="Repite la contraseña" required minlength="6">
                            <i class="fas fa-lock"></i>
                            <span class="toggle-password" onclick="togglePass('confirmar_password', 'icon2')">
                                <i class="fas fa-eye" id="icon2"></i>
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-recovery">
                        <i class="fas fa-save me-2"></i> Guardar Nueva Contraseña
                    </button>
                </form>
            <?php else: ?>
                <div class="text-center">
                    <a href="recuperar.php" class="btn btn-recovery">
                        <i class="fas fa-redo me-2"></i> Solicitar Nuevo Enlace
                    </a>
                    <br><br>
                    <a href="login.php" class="text-muted">
                        <i class="fas fa-arrow-left me-1"></i> Volver al Login
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function togglePass(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Indicador de fortaleza de contraseña
        document.getElementById('nueva_password')?.addEventListener('input', function(e) {
            const password = e.target.value;
            const bar = document.getElementById('strengthBar');
            const text = document.getElementById('strengthText');
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            const levels = [
                { width: '0%', color: '#e9ecef', text: 'Fortaleza de la contraseña' },
                { width: '20%', color: '#dc3545', text: 'Muy débil' },
                { width: '40%', color: '#fd7e14', text: 'Débil' },
                { width: '60%', color: '#ffc107', text: 'Regular' },
                { width: '80%', color: '#20c997', text: 'Fuerte' },
                { width: '100%', color: '#28a745', text: 'Muy fuerte' }
            ];
            
            bar.style.width = levels[strength].width;
            bar.style.background = levels[strength].color;
            text.textContent = levels[strength].text;
            text.style.color = levels[strength].color;
        });
    </script>
</body>
</html>