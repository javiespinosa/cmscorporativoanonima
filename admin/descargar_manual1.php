<?php
header("Content-Type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=Manual_de_Usuario_CMS_Corporativo.doc");
header("Cache-Control: max-age=0");
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="UTF-8">
<title>Manual de Usuario - CMS Corporativo</title>
<style>
    body { font-family: 'Calibri', 'Arial', sans-serif; font-size: 11pt; color: #333; line-height: 1.5; margin: 40px; }
    h1 { font-size: 24pt; color: #28a745; text-align: center; border-bottom: 3px solid #28a745; padding-bottom: 15px; margin-top: 0; }
    h2 { font-size: 16pt; color: #2c3e50; border-bottom: 2px solid #e9ecef; padding-bottom: 5px; margin-top: 30px; page-break-before: always; }
    h3 { font-size: 13pt; color: #28a745; margin-top: 20px; }
    p { margin-bottom: 10px; text-align: justify; }
    ul, ol { margin-bottom: 15px; }
    li { margin-bottom: 8px; }
    .step { background: #f8f9fa; padding: 10px 15px; border-left: 4px solid #28a745; margin: 10px 0; }
    .note { background: #fff3cd; padding: 10px 15px; border-left: 4px solid #ffc107; margin: 15px 0; font-size: 10pt; }
    .btn { font-weight: bold; color: #0d6efd; background: #e7f1ff; padding: 2px 6px; border-radius: 4px; }
    .field { font-weight: bold; color: #6c757d; }
    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    th { background-color: #28a745; color: white; padding: 8px; text-align: left; border: 1px solid #ccc; }
    td { padding: 8px; border: 1px solid #ccc; }
    .footer { text-align: center; font-size: 9pt; color: #666; margin-top: 50px; border-top: 1px solid #ccc; padding-top: 10px; }
</style>
</head>
<body>

<h1>MANUAL DE USUARIO<br><span style="font-size:14pt; color:#666;">CMS Corporativo - Sistema de Gestión Empresarial</span></h1>
<p style="text-align:center;"><strong>Dirigido a:</strong> Personal Administrativo, Comercial y de Contenido<br>
<strong>Versión:</strong> 1.0 | <strong>Fecha:</strong> Junio 2026</p>

<br clear="all" style="page-break-before:always;">

<h2>1. CÓMO ACCEDER AL SISTEMA</h2>
<div class="step">
👉 <strong>Paso 1:</strong> Abre tu navegador y escribe la dirección de acceso (ej: <code>http://tudominio.com/login.php</code>)<br>
👉 <strong>Paso 2:</strong> Ingresa tu <span class="field">Usuario</span> y <span class="field">Contraseña</span>.<br>
👉 <strong>Paso 3:</strong> Haz clic en el botón <span class="btn">Iniciar Sesión</span>.
</div>
<p class="note">💡 <strong>¿Olvidaste tu contraseña?</strong> Haz clic en <span class="btn">¿Olvidaste tu contraseña?</span> debajo del formulario. Ingresa tu correo registrado y recibirás un enlace seguro por email para crear una nueva contraseña.</p>

<h2>2. PANEL PRINCIPAL (DASHBOARD)</h2>
<p>Al entrar, verás el <strong>Dashboard</strong>. Es tu centro de control. Aquí encontrarás:</p>
<ul>
    <li><strong>Tarjetas de colores:</strong> Muestran números importantes en tiempo real (productos totales, cotizaciones nuevas, mensajes sin leer, etc.).</li>
    <li><strong>Gráfica de líneas:</strong> Muestra cuántas cotizaciones se recibieron en los últimos 6 meses.</li>
    <li><strong>Panel "Atención Requerida":</strong> Te alerta si hay cotizaciones pendientes, mensajes nuevos o productos sin foto.</li>
    <li><strong>Accesos rápidos:</strong> Haz clic en cualquier tarjeta o enlace <span class="btn">Ver todos</span> para ir directamente a esa sección.</li>
</ul>

<h2>3. GESTIÓN DE PRODUCTOS (CATÁLOGO)</h2>
<p>⚠️ <strong>Importante:</strong> Los productos se crean y gestionan desde el sistema contable (Sativa). En este CMS <strong>solo administramos las fotografías</strong> para que se vean bien en la página web.</p>

<h3>3.1 Subir o Cambiar Fotos de un Producto</h3>
<div class="step">
👉 <strong>Paso 1:</strong> En el menú lateral, haz clic en <span class="btn">Productos</span>.<br>
👉 <strong>Paso 2:</strong> Usa el buscador para encontrar el producto por nombre o código.<br>
👉 <strong>Paso 3:</strong> Haz clic en el botón <span class="btn">Gestionar</span> (ícono de cámara/imágenes).<br>
👉 <strong>Paso 4:</strong> Se abrirá una ventana con 4 espacios. Haz clic en <span class="btn">Seleccionar archivo...</span> solo en los espacios que quieras actualizar.<br>
👉 <strong>Paso 5:</strong> Haz clic en <span class="btn">Guardar Cambios</span>. ✅ ¡Listo!
</div>
<p class="note">💡 <strong>Consejo:</strong> Si no subes una imagen nueva, el sistema mantiene la que ya existe. Solo se reemplaza lo que selecciones.</p>

<h2>4. PRODUCTOS PREMIUM (PORTADA)</h2>
<p>Estos son los productos que aparecen destacados en la página de inicio con un diseño especial.</p>

<h3>4.1 Agregar un Producto Premium</h3>
<div class="step">
👉 <strong>Paso 1:</strong> Ve a <span class="btn">Productos Premium</span> en el menú.<br>
👉 <strong>Paso 2:</strong> Llena el formulario:<br>
&nbsp;&nbsp;&nbsp;• <span class="field">Categoría:</span> Selecciona a qué grupo pertenece.<br>
&nbsp;&nbsp;&nbsp;• <span class="field">Nombre:</span> Escribe el título exacto.<br>
&nbsp;&nbsp;&nbsp;• <span class="field">Descripción Corta:</span> Frase breve que aparece en la tarjeta.<br>
&nbsp;&nbsp;&nbsp;• <span class="field">Imagen:</span> Sube una foto clara y sin fondo recargado.<br>
👉 <strong>Paso 3:</strong> Haz clic en <span class="btn">Guardar</span>.
</div>

<h3>4.2 Editar o Eliminar</h3>
<p>En la tabla de la derecha encontrarás los botones:<br>
✏️ <span class="btn">Editar</span>: Modifica texto o cambia la foto.<br>
🗑️ <span class="btn">Eliminar</span>: Borra el producto premium y su imagen del servidor.</p>

<h2>5. SISTEMA DE COTIZACIONES</h2>
<p>Aquí gestionas las solicitudes que envían los clientes desde la página web.</p>

<h3>5.1 Ver una Cotización</h3>
<div class="step">
👉 <strong>Paso 1:</strong> Ve a <span class="btn">Cotizaciones</span> en el menú.<br>
👉 <strong>Paso 2:</strong> Haz clic en <span class="btn">Ver</span> (ícono de ojo) en la cotización deseada.<br>
👉 <strong>Paso 3:</strong> Revisa los datos del cliente, los productos solicitados y los comentarios.
</div>

<h3>5.2 Cambiar el Estado</h3>
<p>En la columna izquierda verás un desplegable <span class="field">Estatus</span>:</p>
<ul>
    <li><strong>NUEVA:</strong> Acaba de llegar, pendiente de revisión.</li>
    <li><strong>EN PROCESO:</strong> Ya la estás atendiendo o preparando.</li>
    <li><strong>ATENDIDA:</strong> Ya fue respondida al cliente.</li>
</ul>
<p>Selecciona el nuevo estado y haz clic en <span class="btn">Guardar Estatus</span>.</p>

<h3>5.3 Generar PDF y Contactar</h3>
<ul>
    <li><span class="btn">Generar PDF</span>: Descarga un documento profesional listo para enviar al cliente (incluye precios, IVA, totales y código QR).</li>
    <li><span class="btn">WhatsApp</span>: Abre WhatsApp automáticamente con el número del cliente para iniciar conversación.</li>
</ul>

<h2>6. BANNERS (CARRUSEL DE INICIO)</h2>
<p>Aquí controlas las imágenes o videos que aparecen al inicio de la página web.</p>

<h3>6.1 Crear un Banner</h3>
<div class="step">
👉 <strong>Paso 1:</strong> Ve a <span class="btn">Banners</span> y llena el formulario.<br>
👉 <strong>Paso 2:</strong> Elige el <span class="field">Tipo</span>: Imagen, Video Local o YouTube.<br>
👉 <strong>Paso 3:</span> Escribe Título, Subtítulo (opcional) y Enlace del botón.<br>
👉 <strong>Paso 4:</span> Sube el archivo o pega la URL.<br>
👉 <strong>Paso 5:</span> Configura el <span class="field">Orden</span> (1 = primero) y marca <span class="field">Activo</span>.<br>
👉 <strong>Paso 6:</span> Haz clic en <span class="btn">Guardar Banner</span>.
</div>
<p class="note">💡 <strong>Recomendación:</strong> Usa imágenes de 1920x1080 px. Para videos, sube archivos cortos (10-15 seg) y sin audio.</p>

<h2>7. PROMOCIONES</h2>
<p>Crea ofertas con fecha de inicio y fin. El sistema las muestra y oculta automáticamente.</p>

<h3>7.1 Crear una Promoción</h3>
<div class="step">
👉 <strong>Paso 1:</strong> Ve a <span class="btn">Promociones</span>.<br>
👉 <strong>Paso 2:</span> Escribe el <span class="field">Título</span> y la <span class="field">Descripción</span>.<br>
👉 <strong>Paso 3:</span> Selecciona <span class="field">Fecha Inicio</span> y <span class="field">Fecha Fin</span>.<br>
👉 <strong>Paso 4:</span> Sube una imagen llamativa (opcional).<br>
👉 <strong>Paso 5:</span> Marca <span class="field">Activar promoción</span> y haz clic en <span class="btn">Guardar</span>.
</div>
<p>✅ En la página pública aparecerá un <strong>contador regresivo</strong> (días, horas, minutos) para generar urgencia en el cliente.</p>

<h2>8. MENSAJES DE CONTACTO</h2>
<p>Aquí llegan los correos que escriben los visitantes desde el formulario web.</p>

<h3>8.1 Leer un Mensaje</h3>
<div class="step">
👉 <strong>Paso 1:</strong> Ve a <span class="btn">Contacto > Mensajes</span>.<br>
👉 <strong>Paso 2:</span> Haz clic en <span class="btn">Ver</span> (ícono de ojo).<br>
👉 <strong>Paso 3:</span> Se abrirá una ventana con el nombre, correo, teléfono y mensaje completo.
</div>

<h3>8.2 Responder y Organizar</h3>
<ul>
    <li><span class="btn">Responder por email</span>: Abre tu correo (Outlook, Gmail, etc.) con el asunto y destinatario listos.</li>
    <li><span class="btn">Marcar como leído</span>: Cambia el estado a "Leído" y el contador amarillo del menú lateral disminuye.</li>
    <li><span class="btn">Eliminar</span>: Borra mensajes spam o antiguos.</li>
</ul>

<h2>9. CONFIGURACIÓN GENERAL</h2>
<p>Desde aquí se controla toda la información que ve el público en la página web.</p>

<h3>9.1 Pestaña: Datos Generales</h3>
<ul>
    <li><span class="field">Nombre, Giro, Slogan:</span> Aparecen en el título y PDFs.</li>
    <li><span class="field">Logo y Favicon:</span> Sube tu logo oficial e icono de pestaña.</li>
    <li><span class="field">Contacto y Redes:</span> Teléfono, WhatsApp, Facebook, Instagram, etc.</li>
</ul>

<h3>9.2 Pestaña: Identidad Corporativa</h3>
<ul>
    <li>Aquí editas el texto de la página <strong>"Quiénes Somos"</strong>: Historia, Misión, Visión, Valores y Objetivos.</li>
    <li>También subes la <span class="field">Imagen de Portada</span> de esa sección.</li>
</ul>

<h3>9.3 Pestaña: Horarios</h3>
<ul>
    <li>Ingresa el horario de atención de Lunes a Domingo (ej: <code>09:00 - 18:00</code>).</li>
    <li>Escribe <code>Cerrado</code> en los días que no atiendan.</li>
    <li>Esto actualiza automáticamente el indicador de <strong>"Abierto Ahora"</strong> en la página de contacto.</li>
</ul>
<p class="note">💡 <strong>Importante:</strong> Después de cambiar cualquier configuración, siempre haz clic en el botón verde <span class="btn">Guardar Cambios</span> al final de la página.</p>

<h2>10. PREGUNTAS FRECUENTES (FAQ)</h2>
<table>
    <tr>
        <th>Pregunta</th>
        <th>Respuesta</th>
    </tr>
    <tr>
        <td>¿Puedo crear productos desde aquí?</td>
        <td>No. Los productos se crean en Sativa. Aquí solo subes sus fotografías para la web.</td>
    </tr>
    <tr>
        <td>¿Por qué no se ve una promoción que acabo de crear?</td>
        <td>Verifica que la <span class="field">Fecha Inicio</span> ya haya pasado y que la casilla <span class="field">Activo</span> esté marcada.</td>
    </tr>
    <tr>
        <td>¿Cómo descargo una cotización para enviarla?</td>
        <td>Abre la cotización y haz clic en <span class="btn">Generar PDF</span>. Se descargará automáticamente.</td>
    </tr>
    <tr>
        <td>¿Qué hago si un cliente no recibió el PDF por correo?</td>
        <td>Descárgalo del sistema, adjúntalo manualmente en tu correo y envíalo. El sistema no envía correos automáticos, solo genera el archivo.</td>
    </tr>
    <tr>
        <td>¿Puedo cambiar mi contraseña?</td>
        <td>Sí, desde tu perfil en la esquina superior derecha > <span class="btn">Cambiar Contraseña</span>.</td>
    </tr>
</table>

<h2>11. CONTACTO DE SOPORTE</h2>
<p>Si encuentras un error, necesitas capacitación adicional o deseas agregar una nueva función, contacta a:</p>
<ul>
    <li><strong>Departamento de Sistemas:</strong> [correo@empresa.com]</li>
    <li><strong>Teléfono / Extensión:</strong> [XXXX]</li>
    <li><strong>Horario de atención:</strong> Lunes a Viernes, 9:00 a 18:00 hrs.</li>
</ul>
<p class="note">📝 <strong>Al reportar un problema, indica:</strong> En qué módulo estabas, qué botón presionaste y qué mensaje de error apareció (si aplica). Adjunta una captura de pantalla para agilizar la solución.</p>

<div class="footer">
    <p>© 2026 CMS Corporativo. Todos los derechos reservados.<br>
    Manual elaborado para uso interno del personal autorizado.</p>
</div>

</body>
</html>