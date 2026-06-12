<?php
header("Content-Type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=Manual_Completo_CMS_Corporativo.doc");
header("Cache-Control: max-age=0");
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="UTF-8">
<title>Manual Completo - CMS Corporativo</title>
<style>
    body { font-family: 'Calibri', 'Arial', sans-serif; font-size: 11pt; color: #333; line-height: 1.5; }
    h1 { font-size: 22pt; color: #28a745; text-align: center; border-bottom: 3px solid #28a745; padding-bottom: 10px; margin-top: 30px; }
    h2 { font-size: 16pt; color: #2c3e50; border-bottom: 2px solid #28a745; padding-bottom: 5px; margin-top: 30px; page-break-before: always; }
    h3 { font-size: 13pt; color: #28a745; margin-top: 20px; }
    p { margin-bottom: 10px; text-align: justify; }
    ul, ol { margin-bottom: 15px; }
    li { margin-bottom: 5px; }
    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    th { background-color: #28a745; color: white; padding: 8px; text-align: left; border: 1px solid #ddd; }
    td { padding: 8px; border: 1px solid #ddd; }
    .note { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; margin: 15px 0; }
    .code { background-color: #f4f4f4; padding: 10px; font-family: 'Consolas', monospace; font-size: 9pt; border: 1px solid #ddd; white-space: pre-wrap; }
    .footer { text-align: center; font-size: 9pt; color: #666; margin-top: 40px; border-top: 1px solid #ccc; padding-top: 10px; }
</style>
</head>
<body>

<h1>MANUAL DE USUARIO<br><span style="font-size:14pt; color:#666;">CMS Corporativo - Sistema de Gestión Empresarial</span></h1>
<p style="text-align:center;"><strong>Versión:</strong> 1.0 | <strong>Fecha:</strong> Junio 2026</p>

<br clear="all" style="page-break-before:always;">

<h2>1. INTRODUCCIÓN</h2>
<p>El <strong>CMS Corporativo</strong> es un sistema integral diseñado para administrar catálogos de productos, generar cotizaciones profesionales con PDF, gestionar contenido web dinámico y mantener sincronización con sistemas ERP externos (como <strong>Sativa</strong>).</p>
<h3>1.1 Módulos Principales</h3>
<ul>
    <li><strong>Dashboard:</strong> Panel ejecutivo con métricas en tiempo real.</li>
    <li><strong>Productos:</strong> Catálogo sincronizado de solo lectura con gestión de imágenes.</li>
    <li><strong>Productos Premium:</strong> Gestión independiente de productos destacados para el inicio.</li>
    <li><strong>Cotizaciones:</strong> Carrito de compras, procesamiento y generación de PDFs profesionales.</li>
    <li><strong>Banners:</strong> Carrusel principal con soporte para imágenes, videos locales y YouTube.</li>
    <li><strong>Promociones:</strong> Ofertas con fechas de vigencia y contadores regresivos automáticos.</li>
    <li><strong>Quiénes Somos:</strong> Página corporativa con estadísticas dinámicas.</li>
    <li><strong>Contacto:</strong> Bandeja de entrada para mensajes del formulario público.</li>
    <li><strong>Configuración:</strong> Centralización de datos de la empresa, redes sociales y horarios.</li>
</ul>

<h2>2. REQUISITOS DEL SISTEMA</h2>
<ul>
    <li><strong>PHP:</strong> 7.4 o superior (Recomendado 8.x)</li>
    <li><strong>Base de Datos:</strong> MySQL 5.7+ o MariaDB 10.3+</li>
    <li><strong>Extensiones PHP:</strong> PDO, PDO_MySQL, GD, cURL, mbstring, fileinfo</li>
    <li><strong>Servidor Web:</strong> Apache o Nginx con mod_rewrite habilitado</li>
    <li><strong>Espacio en disco:</strong> Mínimo 2 GB para imágenes y archivos subidos.</li>
</ul>

<h2>3. INSTALACIÓN Y CONFIGURACIÓN INICIAL</h2>
<h3>3.1 Configuración de Base de Datos</h3>
<p>Edita el archivo <code>config/database.php</code> con las credenciales de tu base de datos local del CMS.</p>
<p>Edita el archivo <code>config/database_sativa.php</code> con las credenciales de la base de datos del sistema ERP Sativa.</p>
<h3>3.2 Configuración de Correo (PHPMailer)</h3>
<p>Edita el archivo <code>config/mail_config.php</code>:</p>
<div class="code">
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'tucorreo@gmail.com');
define('SMTP_PASSWORD', 'tu_app_password_de_16_caracteres');
define('SMTP_FROM_NAME', 'CMS Corporativo');
define('SMTP_FROM_EMAIL', 'tucorreo@gmail.com');
define('SMTP_SECURE', 'tls');
</div>
<p class="note"><strong>Nota importante:</strong> Para Gmail, NO uses tu contraseña normal. Debes generar una "App Password" en la configuración de seguridad de tu cuenta de Google.</p>
<h3>3.3 Permisos de Carpetas</h3>
<p>Asegúrate de que la carpeta <code>uploads/</code> y sus subcarpetas (<code>banners/</code>, <code>productos/</code>, <code>promociones/</code>, <code>nosotros/</code>, <code>temp/</code>) tengan permisos de escritura (755 en Linux o Control Total en Windows).</p>

<h2>4. ACCESO AL SISTEMA</h2>
<h3>4.1 Inicio de Sesión</h3>
<p>Accede a <code>http://tudominio.com/login.php</code>. El sistema utiliza hash bcrypt para las contraseñas y regenera el ID de sesión para prevenir secuestro de sesiones.</p>
<h3>4.2 Cerrar Sesión</h3>
<p>Haz clic en tu nombre en la esquina superior derecha y selecciona "Cerrar Sesión".</p>

<h2>5. DASHBOARD EJECUTIVO</h2>
<p>El panel principal muestra métricas clave:</p>
<ul>
    <li><strong>Productos en Catálogo:</strong> Total de productos activos en Sativa.</li>
    <li><strong>Productos Premium:</strong> Productos destacados en la página de inicio.</li>
    <li><strong>Total Cotizaciones:</strong> Número total de solicitudes recibidas.</li>
    <li><strong>Cotizaciones Nuevas/Atendidas:</strong> Estado de las solicitudes.</li>
    <li><strong>Mensajes Nuevos:</strong> Correos del formulario de contacto sin leer.</li>
    <li><strong>Promociones/Banners Activos:</strong> Contenido vigente en el sitio.</li>
</ul>
<p>Incluye una gráfica de cotizaciones mensuales, un panel de "Atención Requerida" y tablas de productos recientes y últimas cotizaciones.</p>

<h2>6. GESTIÓN DE PRODUCTOS (INTEGRACIÓN SATIVA)</h2>
<p>El catálogo principal se sincroniza directamente con la base de datos de <strong>Sativa</strong>. Por seguridad e integridad de datos, <strong>no se pueden crear ni eliminar productos desde este CMS</strong>.</p>
<h3>6.1 Gestión de Imágenes</h3>
<ol>
    <li>Ve al menú <strong>Productos</strong> en el panel de administración.</li>
    <li>Localiza el producto deseado y haz clic en el botón <strong>"Gestionar"</strong>.</li>
    <li>Se abrirá un modal con 4 espacios para imágenes (Path1 a Path4).</li>
    <li>Sube solo las imágenes que desees agregar o reemplazar. Las que dejes en blanco no se modificarán.</li>
    <li>Haz clic en <strong>Guardar Cambios</strong>.</li>
</ol>

<h2>7. PRODUCTOS PREMIUM</h2>
<p>Esta sección permite crear productos exclusivos que se mostrarán en la página de inicio (<code>index.php</code>) con un diseño de vitrina elegante, sin botones de acción y con la imagen completa.</p>
<ul>
    <li><strong>Crear:</strong> Ve a "Productos Premium" > Llena el formulario (Categoría, Nombre, Descripción Corta, Descripción Larga, Imagen) > Guardar.</li>
    <li><strong>Editar/Eliminar:</strong> Usa los botones de acción en la tabla de listado. Al eliminar, la imagen asociada se borra del servidor.</li>
</ul>

<h2>8. SISTEMA DE COTIZACIONES</h2>
<h3>8.1 Flujo del Cliente</h3>
<ol>
    <li>El cliente navega el catálogo y hace clic en "Cotizar".</li>
    <li>Revisa su carrito en <code>cotizacion.php</code> y ajusta cantidades.</li>
    <li>Llena el formulario con sus datos (Nombre, Empresa, Teléfono, Correo, Comentarios).</li>
    <li>Al enviar, el sistema valida que los productos sigan activos en Sativa y guarda la solicitud.</li>
</ol>
<h3>8.2 Gestión desde el Admin</h3>
<p>En <strong>Cotizaciones > Ver</strong>, podrás:</p>
<ul>
    <li>Ver el detalle completo del cliente y los productos solicitados.</li>
    <li>Cambiar el estatus: <strong>NUEVA</strong> (amarillo), <strong>EN_PROCESO</strong> (azul), <strong>ATENDIDA</strong> (verde).</li>
    <li>Hacer clic en <strong>"Generar PDF"</strong> para descargar la cotización formal.</li>
    <li>Hacer clic en <strong>"WhatsApp"</strong> para contactar al cliente directamente.</li>
</ul>
<h3>8.3 El PDF Generado</h3>
<p>El documento incluye: Logo corporativo, datos del cliente, tabla detallada con Código, Descripción, Precio Unitario, Cantidad y Subtotal, cálculo automático de <strong>Subtotal, IVA (16%) y Total</strong>, código QR de contacto, espacio para firma y aviso de vigencia de 15 días.</p>

<h2>9. BANNERS Y PROMOCIONES</h2>
<h3>9.1 Banners (Carrusel Principal)</h3>
<p>Soporta 3 tipos de contenido:</p>
<ul>
    <li><strong>Imagen:</strong> JPG, PNG o WEBP (Máx. 5MB, recomendado 1920x1080px).</li>
    <li><strong>Video Local:</strong> MP4 o WEBM (Máx. 50MB, sin audio, loop automático).</li>
    <li><strong>YouTube/Vimeo:</strong> Solo pega la URL completa. El sistema la adapta para autoplay sin controles.</li>
</ul>
<h3>9.2 Promociones</h3>
<p>Al crear una promoción, establece la <strong>Fecha de Inicio</strong> y <strong>Fecha de Fin</strong>. El sistema la mostrará u ocultará automáticamente en el sitio público según la fecha actual. Incluye un contador regresivo en tiempo real (Días, Horas, Minutos, Segundos) para generar urgencia.</p>

<h2>10. QUIÉNES SOMOS Y CONFIGURACIÓN</h2>
<h3>10.1 Edición de Contenido</h3>
<p>Toda la información corporativa se edita desde <strong>Configuración > Pestaña "Identidad Corporativa"</strong>. Aquí podrás llenar: Historia, Misión, Visión, Valores (separados por comas o saltos de línea) y Objetivos.</p>
<h3>10.2 Estadísticas Dinámicas</h3>
<p>La página "Quiénes Somos" calcula automáticamente:</p>
<ul>
    <li><strong>Años de experiencia:</strong> Basado en la "Fecha de Fundación" configurada.</li>
    <li><strong>Clientes atendidos:</strong> Correos únicos en la tabla de cotizaciones.</li>
    <li><strong>Productos entregados:</strong> Suma de cantidades de cotizaciones con estatus "ATENDIDA".</li>
</ul>

<h2>11. MENSAJES DE CONTACTO</h2>
<p>Los mensajes del formulario público llegan a <strong>Contacto > Mensajes</strong>. Puedes:</p>
<ul>
    <li>Ver el mensaje completo en un popup modal.</li>
    <li>Hacer clic en "Responder por email" (abre tu cliente de correo predeterminado).</li>
    <li>Marcar como "Leído" para que el badge amarillo del menú lateral disminuya.</li>
    <li>Eliminar mensajes de spam o antiguos.</li>
</ul>

<h2>12. RECUPERACIÓN DE CONTRASEÑA</h2>
<p>Si olvidas tu contraseña, haz clic en "Recuperar acceso" en el login. El sistema enviará un correo con un <strong>token de un solo uso</strong> válido por 1 hora. Este token se invalida automáticamente tras su uso o al solicitar uno nuevo.</p>

<h2>13. SOLUCIÓN DE PROBLEMAS COMUNES</h2>
<table>
    <tr>
        <th>Problema</th>
        <th>Solución</th>
    </tr>
    <tr>
        <td>Error "Column not found: correo"</td>
        <td>Ejecuta en phpMyAdmin: <code>ALTER TABLE usuarios ADD COLUMN correo VARCHAR(200) NULL;</code></td>
    </tr>
    <tr>
        <td>Las imágenes no se muestran</td>
        <td>Verifica que la carpeta <code>uploads/</code> tenga permisos de escritura (755 en Linux).</td>
    </tr>
    <tr>
        <td>Error al guardar cotización (Foreign Key)</td>
        <td>Ejecuta en phpMyAdmin: <code>ALTER TABLE cotizacion_detalle DROP FOREIGN KEY cotizacion_detalle_ibfk_2;</code></td>
    </tr>
    <tr>
        <td>No llegan los correos de recuperación</td>
        <td>Verifica <code>config/mail_config.php</code>. Para Gmail, usa una "App Password", no tu contraseña normal.</td>
    </tr>
    <tr>
        <td>Error "$ is not defined"</td>
        <td>Asegúrate de que los scripts de jQuery estén cargados en el footer y tus scripts personalizados vayan después.</td>
    </tr>
</table>

<br clear="all" style="page-break-before:always;">

<h2>APÉNDICE A: ESTRUCTURAS SQL COMPLETAS</h2>
<p>Copia y pega este código en la pestaña "SQL" de phpMyAdmin para crear o reparar las tablas del CMS:</p>
<div class="code">
-- Tabla: usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    correo VARCHAR(100) NULL,
    rol ENUM('admin', 'editor', 'usuario') DEFAULT 'usuario',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: password_resets
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion DATETIME NOT NULL,
    usado TINYINT(1) DEFAULT 0,
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: configuracion
CREATE TABLE IF NOT EXISTS configuracion (
    id INT PRIMARY KEY DEFAULT 1,
    empresa VARCHAR(200), giro VARCHAR(200), slogan VARCHAR(255),
    quienes_somos TEXT, mision TEXT, vision TEXT, valores TEXT,
    historia TEXT, objetivos TEXT, direccion TEXT, telefono VARCHAR(50),
    whatsapp VARCHAR(50), correo VARCHAR(100), logo VARCHAR(255),
    favicon VARCHAR(255), facebook VARCHAR(255), instagram VARCHAR(255),
    tiktok VARCHAR(255), youtube VARCHAR(255), linkedin VARCHAR(255),
    google_maps TEXT, imagen_nosotros VARCHAR(255), fecha_fundacion DATE,
    horario_lunes VARCHAR(50), horario_martes VARCHAR(50), horario_miercoles VARCHAR(50),
    horario_jueves VARCHAR(50), horario_viernes VARCHAR(50), horario_sabado VARCHAR(50),
    horario_domingo VARCHAR(50), actualizado TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: categorias
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255),
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: productos (Premium local)
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT,
    nombre VARCHAR(200) NOT NULL,
    descripcion_corta VARCHAR(255),
    descripcion_larga TEXT,
    imagen VARCHAR(255),
    precio DECIMAL(10,2) DEFAULT 0.00,
    destacado TINYINT(1) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: cotizaciones
CREATE TABLE IF NOT EXISTS cotizaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    empresa VARCHAR(200),
    telefono VARCHAR(100) NOT NULL,
    correo VARCHAR(200) NOT NULL,
    comentarios TEXT,
    estatus ENUM('NUEVA','EN_PROCESO','ATENDIDA') DEFAULT 'NUEVA',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: cotizacion_detalle
CREATE TABLE IF NOT EXISTS cotizacion_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cotizacion_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    INDEX idx_cotizacion (cotizacion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: banners
CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    subtitulo VARCHAR(200),
    imagen VARCHAR(255),
    enlace VARCHAR(500),
    tipo ENUM('imagen','video','youtube') DEFAULT 'imagen',
    video_url VARCHAR(500),
    orden_banner INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: promociones
CREATE TABLE IF NOT EXISTS promociones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255),
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: mensajes_contacto
CREATE TABLE IF NOT EXISTS mensajes_contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    empresa VARCHAR(100),
    asunto VARCHAR(50) NOT NULL,
    mensaje TEXT NOT NULL,
    leido TINYINT(1) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
</div>

<h2>APÉNDICE B: CHECKLIST DE INSTALACIÓN</h2>
<ul>
    <li>[ ] Servidor con PHP 7.4+ y MySQL 5.7+ instalado.</li>
    <li>[ ] Base de datos <code>cmscorporativo</code> creada.</li>
    <li>[ ] Tablas creadas (ejecutar script SQL del Apéndice A).</li>
    <li>[ ] Archivos <code>config/database.php</code> y <code>database_sativa.php</code> configurados.</li>
    <li>[ ] PHPMailer instalado y <code>config/mail_config.php</code> configurado.</li>
    <li>[ ] Permisos de escritura en carpeta <code>uploads/</code>.</li>
    <li>[ ] Usuario administrador creado y contraseña cambiada.</li>
    <li>[ ] Datos de empresa, logo y horarios configurados en el panel.</li>
    <li>[ ] Conexión a Sativa verificada y productos visibles.</li>
</ul>

<div class="footer">
    <p>© 2026 CMS Corporativo. Todos los derechos reservados.<br>
    Documento generado automáticamente por el sistema.</p>
</div>

</body>
</html>