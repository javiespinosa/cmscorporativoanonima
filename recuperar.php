<?php
session_start();
require_once 'config/database.php';
require_once 'config/mail_config.php';
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

// Cargar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/PHPMailer/src/PHPMailer.php')) {
    require __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require __DIR__ . '/PHPMailer/src/SMTP.php';
    require __DIR__ . '/PHPMailer/src/Exception.php';
} else {
    die('Error: PHPMailer no está instalado. Instala con: composer require phpmailer/phpmailer');
}

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    
    if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Por favor ingresa un correo válido.";
        $tipo_mensaje = "danger";
    } else {
        $stmt = $pdo->prepare("SELECT id, nombre FROM usuarios WHERE correo = ? AND activo = 1");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            $token = bin2hex(random_bytes(32));
            $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $pdo->prepare("UPDATE password_resets SET usado = 1 WHERE usuario_id = ? AND usado = 0")
                ->execute([$usuario['id']]);
            
            $stmt = $pdo->prepare("
                INSERT INTO password_resets (usuario_id, token, fecha_expiracion) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$usuario['id'], $token, $fecha_expiracion]);
            
            $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
                        "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
            $enlace = $base_url . "/recuperar_cambiar.php?token=" . $token;
            
            try {
                $mail = new PHPMailer(true);
                
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USERNAME;
                $mail->Password = SMTP_PASSWORD;
                $mail->SMTPSecure = SMTP_SECURE;
                $mail->Port = SMTP_PORT;
                $mail->CharSet = 'UTF-8';
                
                $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                $mail->addAddress($correo, $usuario['nombre']);
                
                $mail->isHTML(true);
                $mail->Subject = 'Recuperación de Contraseña - ' . SMTP_FROM_NAME;
                
                // ============================================
                // CORREO CON COLORES DINÁMICOS
                // ============================================
                $mail->Body = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset='UTF-8'>
                        <style>
                            body { 
                                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                                background: #f4f4f4; 
                                padding: 20px; 
                                margin: 0;
                            }
                            .container { 
                                max-width: 600px; 
                                margin: 0 auto; 
                                background: white; 
                                border-radius: 15px; 
                                overflow: hidden; 
                                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                            }
                            .header { 
                                background: linear-gradient(135deg, {$color_primario} 0%, {$color_secundario} 100%); 
                                color: white; 
                                padding: 40px 30px; 
                                text-align: center;
                            }
                            .header h1 { 
                                margin: 0; 
                                font-size: 28px; 
                                font-weight: 700;
                            }
                            .header p { 
                                margin: 10px 0 0; 
                                opacity: 0.9;
                            }
                            .content { 
                                padding: 40px 30px; 
                                color: #333;
                            }
                            .content h2 { 
                                color: {$color_primario}; 
                                margin-top: 0;
                            }
                            .btn { 
                                display: inline-block; 
                                background: linear-gradient(135deg, {$color_primario}, {$color_secundario}); 
                                color: white !important; 
                                padding: 15px 40px; 
                                text-decoration: none; 
                                border-radius: 50px; 
                                font-weight: bold; 
                                margin: 25px 0;
                                font-size: 16px;
                            }
                            .btn:hover {
                                background: linear-gradient(135deg, {$color_secundario}, {$color_primario});
                            }
                            .warning { 
                                background: #fff3cd; 
                                border-left: 4px solid #ffc107; 
                                padding: 15px; 
                                margin: 20px 0;
                                border-radius: 5px;
                            }
                            .footer { 
                                background: #f8f9fa; 
                                padding: 25px; 
                                text-align: center; 
                                font-size: 13px; 
                                color: #6c757d;
                                border-top: 1px solid #e9ecef;
                            }
                            .link-alt {
                                background: #f8f9fa;
                                padding: 15px;
                                border-radius: 8px;
                                margin-top: 20px;
                                font-size: 12px;
                                word-break: break-all;
                            }
                            .link-alt a {
                                color: {$color_primario};
                                text-decoration: none;
                            }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <h1>🔐 Recuperación de Contraseña</h1>
                                <p>Sistema de Gestión Empresarial</p>
                            </div>
                            <div class='content'>
                                <h2>Hola {$usuario['nombre']},</h2>
                                <p>Hemos recibido una solicitud para restablecer tu contraseña. Si fuiste tú quien realizó esta solicitud, haz clic en el siguiente botón:</p>
                                
                                <div style='text-align: center;'>
                                    <a href='$enlace' class='btn'>Restablecer Mi Contraseña</a>
                                </div>
                                
                                <div class='warning'>
                                    <strong>⚠️ Importante:</strong>
                                    <ul style='margin: 10px 0 0; padding-left: 20px;'>
                                        <li>Este enlace es válido por <strong>1 hora</strong></li>
                                        <li>Solo puede usarse <strong>una vez</strong></li>
                                        <li>Si no solicitaste este cambio, ignora este correo</li>
                                    </ul>
                                </div>
                                
                                <div class='link-alt'>
                                    <strong>¿El botón no funciona?</strong><br>
                                    Copia y pega este enlace en tu navegador:<br>
                                    <a href='$enlace'>$enlace</a>
                                </div>
                            </div>
                            <div class='footer'>
                                <p>© " . date('Y') . " " . SMTP_FROM_NAME . ". Todos los derechos reservados.</p>
                                <p style='margin: 5px 0 0; font-size: 11px;'>
                                    Este es un correo automático, por favor no respondas a este mensaje.
                                </p>
                            </div>
                        </div>
                    </body>
                    </html>
                ";
                
                $mail->AltBody = "Hola {$usuario['nombre']},\n\n" .
                                "Para restablecer tu contraseña, visita este enlace:\n" .
                                "$enlace\n\n" .
                                "Este enlace es válido por 1 hora y solo puede usarse una vez.\n\n" .
                                "Si no solicitaste este cambio, ignora este correo.\n\n" .
                                "© " . date('Y') . " " . SMTP_FROM_NAME;
                
                $mail->send();
                
                $mensaje = "Se ha enviado un enlace de recuperación a tu correo. Revisa tu bandeja de entrada (y la carpeta de spam si no lo encuentras).";
                $tipo_mensaje = "success";
                
            } catch (Exception $e) {
                error_log("Error PHPMailer: " . $mail->ErrorInfo);
                
                $mensaje = "⚠️ Error al enviar el correo: " . $mail->ErrorInfo . "<br><br>
                           <strong>MODO PRUEBA:</strong> Usa este enlace directamente:<br>
                           <a href='$enlace' class='alert-link' style='word-break: break-all;'>$enlace</a>";
                $tipo_mensaje = "warning";
            }
            
        } else {
            $mensaje = "Si el correo está registrado, recibirás un enlace de recuperación en los próximos minutos.";
            $tipo_mensaje = "success";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
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
            padding: 20px;
        }
        .recovery-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            animation: slideIn 0.6s ease-out;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .recovery-header {
            background: linear-gradient(135deg, var(--color-primario) 0%, var(--color-secundario) 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .recovery-header i {
            font-size: 4rem;
            margin-bottom: 15px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .recovery-body {
            padding: 40px 30px;
        }
        .form-control {
            padding: 14px 15px 14px 45px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: var(--color-primario);
            box-shadow: 0 0 0 4px rgba(var(--rgb-primario), 0.1);
        }
        .input-wrapper {
            position: relative;
        }
        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 2;
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
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: var(--color-primario);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .back-link a:hover {
            color: var(--color-secundario);
        }
    </style>
</head>
<body>
    <div class="recovery-card">
        <div class="recovery-header">
            <i class="fas fa-key"></i>
            <h3>¿Olvidaste tu contraseña?</h3>
            <p class="mb-0">No te preocupes, te ayudaremos a recuperarla</p>
        </div>
        <div class="recovery-body">
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipo_mensaje ?>">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>
            
            <p class="text-muted text-center mb-4">
                Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
            </p>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <input type="email" name="correo" class="form-control" 
                               placeholder="tucorreo@ejemplo.com" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
                <button type="submit" class="btn btn-recovery">
                    <i class="fas fa-paper-plane me-2"></i> Enviar Enlace de Recuperación
                </button>
            </form>
            
            <div class="back-link">
                <a href="login.php">
                    <i class="fas fa-arrow-left me-1"></i> Volver al Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>