<?php
// Configuración SMTP para envío de correos
define('SMTP_HOST', 'smtp.gmail.com');        // Servidor SMTP
define('SMTP_PORT', 587);                     // Puerto (587 para TLS, 465 para SSL)
define('SMTP_USERNAME', 'javi.martinez9008@gmail.com'); // Tu correo
define('SMTP_PASSWORD', 'tnjs oaoo tvvc kagr');      // Contraseña o App Password
define('SMTP_FROM_NAME', 'CMS Corporativo');   // Nombre del remitente
define('SMTP_FROM_EMAIL', 'javi.martinez9008@gmail.com'); // Correo del remitente
define('SMTP_SECURE', 'tls');                 // 'tls' o 'ssl'

// Para Gmail, necesitas crear una "App Password":
// 1. Ve a: https://myaccount.google.com/apppasswords
// 2. Genera una contraseña de aplicación
// 3. Úsala en SMTP_PASSWORD (NO uses tu contraseña normal)
?>