<?php
header("Content-Type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=Manual_de_Usuario_CMS_Corporativo.doc");
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="UTF-8">
<title>Manual de Usuario - CMS Corporativo</title>
<style>
    body { font-family: 'Calibri', sans-serif; font-size: 12pt; color: #333; line-height: 1.6; }
    h1 { font-size: 28pt; color: #28a745; text-align: center; margin-bottom: 10px; }
    h2 { font-size: 20pt; color: #2c3e50; border-bottom: 3px solid #28a745; padding-bottom: 10px; margin-top: 40px; page-break-before: always; }
    h3 { font-size: 15pt; color: #28a745; margin-top: 25px; }
    p { margin-bottom: 12px; text-align: justify; }
    .step { background-color: #f8f9fa; border-left: 4px solid #28a745; padding: 15px; margin: 15px 0; }
    .step strong { color: #28a745; }
    .tip { background-color: #e7f3ff; border-left: 4px solid #2196F3; padding: 12px; margin: 15px 0; font-style: italic; }
    .warning { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin: 15px 0; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    th { background-color: #28a745; color: white; padding: 12px; text-align: left; border: 1px solid #ddd; }
    td { padding: 10px; border: 1px solid #ddd; }
    .footer { text-align: center; font-size: 10pt; color: #666; margin-top: 50px; border-top: 2px solid #28a745; padding-top: 15px; }
    .index { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
    .index a { color: #28a745; text-decoration: none; }
    .index a:hover { text-decoration: underline; }
</style>
</head>
<body>

<h1>MANUAL DE USUARIO<br><span style="font-size:16pt; color:#666;">CMS Corporativo</span></h1>
<p style="text-align:center; font-size:14pt;"><strong>Sistema de Gestión Empresarial</strong></p>
<p style="text-align:center;">Versión 1.0 - Junio 2026</p>

<br clear="all" style="page-break-before:always;">

<div class="index">
<h2 style="margin-top:0; border:none;">📋 CONTENIDO</h2>
<ol>
    <li><a href="#inicio">Primeros Pasos</a></li>
    <li><a href="#dashboard">El Panel Principal (Dashboard)</a></li>
    <li><a href="#productos">Gestionar Productos</a></li>
    <li><a href="#premium">Productos Premium (Destacados)</a></li>
    <li><a href="#cotizaciones">Ver y Atender Cotizaciones</a></li>
    <li><a href="#banners">Administrar Banners del Inicio</a></li>
    <li><a href="#promociones">Crear Promociones</a></li>
    <li><a href="#contacto">Revisar Mensajes de Contacto</a></li>
    <li><a href="#configuracion">Configurar la Empresa</a></li>
    <li><a href="#consejos">Consejos y Recomendaciones</a></li>
</ol>
</div>

<br clear="all" style="page-break-before:always;">

<h2 id="inicio">1. PRIMEROS PASOS</h2>

<h3>1.1 Cómo Ingresar al Sistema</h3>
<div class="step">
<strong>Paso 1:</strong> Abre tu navegador (Chrome, Firefox, Edge) y escribe:<br>
<code>http://tudominio.com/login.php</code>
</div>

<div class="step">
<strong>Paso 2:</strong> Ingresa tu usuario y contraseña que te proporcionó el administrador.
</div>

<div class="step">
<strong>Paso 3:</strong> Haz clic en el botón <strong>"Iniciar Sesión"</strong>.
</div>

<div class="tip">
💡 <strong>Consejo:</strong> Si olvidaste tu contraseña, haz clic en "Recuperar acceso" y sigue las instrucciones que llegarán a tu correo.
</div>

<h3>1.2 Conociendo el Menú</h3>
<p>Una vez dentro, verás un menú lateral a la izquierda con las siguientes opciones:</p>
<ul>
    <li><strong>Dashboard:</strong> Panel principal con estadísticas</li>
    <li><strong>Productos:</strong> Catálogo de productos (sincronizado con Sativa)</li>
    <li><strong>Productos Premium:</strong> Productos destacados del inicio</li>
    <li><strong>Cotizaciones:</strong> Solicitudes de los clientes</li>
    <li><strong>Banners:</strong> Imágenes y videos del carrusel principal</li>
    <li><strong>Promociones:</strong> Ofertas especiales</li>
    <li><strong>Contacto:</strong> Mensajes del formulario web</li>
    <li><strong>Configuración:</strong> Datos de la empresa</li>
    <li><strong>Cerrar Sesión:</strong> Salir del sistema de forma segura</li>
</ul>

<h2 id="dashboard">2. EL PANEL PRINCIPAL (DASHBOARD)</h2>
<p>El Dashboard es tu pantalla de inicio. Aquí verás de un vistazo toda la información importante de tu negocio.</p>

<h3>2.1 Tarjetas de Información</h3>
<p>En la parte superior verás tarjetas con números importantes:</p>
<ul>
    <li><strong>Productos en Catálogo:</strong> Total de productos activos</li>
    <li><strong>Productos Premium:</strong> Productos destacados en el inicio</li>
    <li><strong>Total Cotizaciones:</strong> Todas las solicitudes recibidas</li>
    <li><strong>Cotizaciones Nuevas:</strong> Solicitudes pendientes de atender (en rojo)</li>
    <li><strong>Cotizaciones Atendidas:</strong> Solicitudes ya respondidas</li>
    <li><strong>Mensajes Nuevos:</strong> Correos sin leer del formulario de contacto</li>
    <li><strong>Promociones Activas:</strong> Ofertas vigentes actualmente</li>
    <li><strong>Banners Activos:</strong> Imágenes/videos mostrándose en el inicio</li>
</ul>

<div class="tip">
💡 <strong>Consejo:</strong> Haz clic en cualquier tarjeta para ir directamente a esa sección.
</div>

<h3>2.2 Gráfica de Cotizaciones</h3>
<p>Verás una gráfica de línea que muestra cuántas cotizaciones has recibido en los últimos 6 meses. Esto te ayuda a identificar si tu negocio está creciendo.</p>

<h3>2.3 Alertas Importantes</h3>
<p>En el lado derecho verás un recuadro amarillo que te avisa sobre:</p>
<ul>
    <li>Cotizaciones nuevas que necesitan atención</li>
    <li>Mensajes de contacto sin leer</li>
    <li>Productos que no tienen imagen (para que los agregues)</li>
</ul>

<h2 id="productos">3. GESTIONAR PRODUCTOS</h2>
<div class="warning">
⚠️ <strong>Importante:</strong> Los productos se crean en el sistema Sativa (ERP). Desde este CMS solo puedes <strong>agregar o cambiar las imágenes</strong> de los productos que ya existen en Sativa.
</div>

<h3>3.1 Ver el Catálogo</h3>
<div class="step">
<strong>Paso 1:</strong> En el menú lateral, haz clic en <strong>"Productos"</strong>.
</div>

<div class="step">
<strong>Paso 2:</strong> Verás una tabla con todos los productos activos de Sativa, mostrando:
<ul>
    <li>ID del producto</li>
    <li>Código (SKU)</li>
    <li>Descripción del producto</li>
    <li>Línea/Categoría</li>
    <li>Precio</li>
    <li>Cuántas imágenes tiene (ej. "2/4" significa que tiene 2 de 4 imágenes posibles)</li>
</ul>
</div>

<h3>3.2 Agregar o Cambiar Imágenes</h3>
<div class="step">
<strong>Paso 1:</strong> En la tabla de productos, busca el producto al que quieres agregarle imágenes.
</div>

<div class="step">
<strong>Paso 2:</strong> Haz clic en el botón azul <strong>"Gestionar"</strong> (tiene un ícono de imágenes).
</div>

<div class="step">
<strong>Paso 3:</strong> Se abrirá una ventana con 4 espacios para imágenes (Path1, Path2, Path3, Path4).
</div>

<div class="step">
<strong>Paso 4:</strong> Para cada imagen:
<ul>
    <li>Si ya existe una imagen, verás la foto actual.</li>
    <li>Para cambiarla: Haz clic en "Seleccionar archivo" y elige la nueva imagen de tu computadora.</li>
    <li>Si no quieres cambiarla: Déjala en blanco y no se modificará.</li>
</ul>
</div>

<div class="step">
<strong>Paso 5:</strong> Haz clic en el botón verde <strong>"Guardar Cambios"</strong>.
</div>

<div class="tip">
💡 <strong>Consejos para las imágenes:</strong><br>
• Usa imágenes de buena calidad (mínimo 800x800 píxeles)<br>
• Formato recomendado: JPG o PNG<br>
• Tamaño máximo: 5 MB por imagen<br>
• Fondo blanco o transparente se ve mejor<br>
• Puedes subir hasta 4 imágenes por producto (diferentes ángulos)
</div>

<h2 id="premium">4. PRODUCTOS PREMIUM (DESTACADOS)</h2>
<p>Estos son productos especiales que aparecen en la página de inicio con un diseño elegante. A diferencia del catálogo normal, estos <strong>SÍ los puedes crear y editar</strong> desde aquí.</p>

<h3>4.1 Crear un Producto Premium</h3>
<div class="step">
<strong>Paso 1:</strong> En el menú lateral, haz clic en <strong>"Productos Premium"</strong>.
</div>

<div class="step">
<strong>Paso 2:</strong> En el formulario de la izquierda, llena los datos:
<ul>
    <li><strong>Categoría:</strong> Selecciona de la lista</li>
    <li><strong>Nombre:</strong> Nombre del producto (ej. "Laptop Dell XPS 15")</li>
    <li><strong>Descripción Corta:</strong> Un resumen breve (se verá en la tarjeta del producto)</li>
    <li><strong>Descripción Larga:</strong> Descripción completa con todas las características</li>
    <li><strong>Imagen:</strong> Haz clic en "Seleccionar archivo" y sube la foto del producto</li>
</ul>
</div>

<div class="step">
<strong>Paso 3:</strong> Haz clic en el botón verde <strong>"Guardar"</strong>.
</div>

<h3>4.2 Editar o Eliminar</h3>
<div class="step">
<strong>Para Editar:</strong> En la tabla de la derecha, haz clic en el botón azul con el ícono de lápiz ✏️. Modifica los datos y haz clic en "Actualizar".
</div>

<div class="step">
<strong>Para Eliminar:</strong> Haz clic en el botón rojo con el ícono de basura 🗑️. Confirma que realmente quieres borrarlo.
</div>

<div class="tip">
💡 <strong>Consejo:</strong> Los Productos Premium se muestran en la página de inicio sin botones de compra, solo como una vitrina elegante. Úsalos para destacar tus productos más importantes o nuevos.
</div>

<h2 id="cotizaciones">5. VER Y ATENDER COTIZACIONES</h2>
<p>Cuando un cliente llena el formulario de cotización en la página web, llega aquí para que lo atiendas.</p>

<h3>5.1 Ver la Lista de Cotizaciones</h3>
<div class="step">
<strong>Paso 1:</strong> En el menú lateral, haz clic en <strong>"Cotizaciones"</strong>.
</div>

<div class="step">
<strong>Paso 2:</strong> Verás una tabla con todas las cotizaciones recibidas, mostrando:
<ul>
    <li>Número de folio (ID)</li>
    <li>Nombre del cliente</li>
    <li>Empresa (si la proporcionó)</li>
    <li>Correo electrónico</li>
    <li>Estado: 
        <ul>
            <li><strong style="color: #ffc107;">NUEVA</strong> (amarillo) - Sin atender</li>
            <li><strong style="color: #17a2b8;">EN PROCESO</strong> (azul) - La estás atendiendo</li>
            <li><strong style="color: #28a745;">ATENDIDA</strong> (verde) - Ya la respondiste</li>
        </ul>
    </li>
    <li>Fecha en que llegó</li>
</ul>
</div>

<h3>5.2 Atender una Cotización</h3>
<div class="step">
<strong>Paso 1:</strong> En la tabla, haz clic en el botón <strong>"Ver"</strong> (ícono de ojo 👁️) en la cotización que quieras atender.
</div>

<div class="step">
<strong>Paso 2:</strong> Verás toda la información:
<ul>
    <li><strong>Lado izquierdo:</strong> Datos del cliente (nombre, empresa, teléfono, correo) y un selector para cambiar el estado.</li>
    <li><strong>Lado derecho:</strong> 
        <ul>
            <li>Tabla con los productos que el cliente solicitó (con fotos, código, descripción, precio y cantidad)</li>
            <li>Total de la cotización (con subtotal, IVA y total)</li>
            <li>Comentarios del cliente</li>
        </ul>
    </li>
</ul>
</div>

<div class="step">
<strong>Paso 3:</strong> Cambia el estado según corresponda:
<ul>
    <li>Si apenas la recibiste: Déjala en <strong>"NUEVA"</strong></li>
    <li>Si ya la estás trabajando: Selecciona <strong>"EN PROCESO"</strong></li>
    <li>Si ya le enviaste la cotización al cliente: Selecciona <strong>"ATENDIDA"</strong></li>
</ul>
</div>

<div class="step">
<strong>Paso 4:</strong> Haz clic en <strong>"Guardar Estatus"</strong>.
</div>

<h3>5.3 Generar PDF de la Cotización</h3>
<div class="step">
<strong>Paso 1:</strong> Dentro de la cotización (paso anterior), haz clic en el botón rojo <strong>"Generar PDF"</strong>.
</div>

<div class="step">
<strong>Paso 2:</strong> Se abrirá un PDF profesional con:
<ul>
    <li>Logo de tu empresa</li>
    <li>Datos del cliente</li>
    <li>Tabla de productos con precios</li>
    <li>Totales (subtotal, IVA, total)</li>
    <li>Código QR con tus datos de contacto</li>
    <li>Espacio para firma del cliente</li>
    <li>Vigencia de 15 días</li>
</ul>
</div>

<div class="step">
<strong>Paso 3:</strong> Descarga el PDF y envíalo al cliente por correo o WhatsApp.
</div>

<h3>5.4 Contactar al Cliente por WhatsApp</h3>
<div class="step">
<strong>Paso 1:</strong> Dentro de la cotización, haz clic en el botón verde <strong>"WhatsApp"</strong>.
</div>

<div class="step">
<strong>Paso 2:</strong> Se abrirá WhatsApp Web (o la app en tu celular) con el número del cliente ya cargado.
</div>

<div class="step">
<strong>Paso 3:</strong> Escribe tu mensaje y envía el PDF adjunto.
</div>

<div class="tip">
💡 <strong>Consejo:</strong> Revisa las cotizaciones "NUEVAS" todos los días y respóndelas en menos de 24 horas para dar un buen servicio.
</div>

<h2 id="banners">6. ADMINISTRAR BANNERS DEL INICIO</h2>
<p>Los banners son las imágenes o videos grandes que aparecen en la página de inicio, en la parte superior (carrusel).</p>

<h3>6.1 Crear un Banner Nuevo</h3>
<div class="step">
<strong>Paso 1:</strong> En el menú lateral, haz clic en <strong>"Banners"</strong>.
</div>

<div class="step">
<strong>Paso 2:</strong> En el formulario de la izquierda, selecciona el <strong>"Tipo de Banner"</strong>:
<ul>
    <li>🖼️ <strong>Imagen Estática:</strong> Una foto normal</li>
    <li>🎥 <strong>Video Local (MP4):</strong> Un video que tengas en tu computadora</li>
    <li>📺 <strong>YouTube / Vimeo:</strong> Un video de YouTube o Vimeo</li>
</ul>
</div>

<div class="step">
<strong>Paso 3:</strong> Llena los datos:
<ul>
    <li><strong>Título:</strong> Texto principal grande (ej. "OFERTA DE VERANO")</li>
    <li><strong>Subtítulo:</strong> Texto más pequeño (ej. "Hasta 50% de descuento")</li>
    <li><strong>Enlace del botón:</strong> A dónde va el cliente al hacer clic (opcional, puede ser #productos)</li>
</ul>
</div>

<div class="step">
<strong>Paso 4:</strong> Sube el contenido según el tipo:
<ul>
    <li><strong>Si es Imagen:</strong> Haz clic en "Seleccionar imagen" y sube tu foto (JPG, PNG o WEBP, máx. 5MB)</li>
    <li><strong>Si es Video Local:</strong> Haz clic en "Seleccionar video" y sube tu archivo MP4 (máx. 50MB, recomendado 10-15 segundos)</li>
    <li><strong>Si es YouTube:</strong> Pega la URL completa del video (ej. https://www.youtube.com/watch?v=CODIGO)</li>
</ul>
</div>

<div class="step">
<strong>Paso 5:</strong> Configura:
<ul>
    <li><strong>Orden de aparición:</strong> Número (el 1 aparece primero, el 2 después, etc.)</li>
    <li><strong>Mostrar en el sitio:</strong> Déjalo marcado para que se vea, desmárcalo si quieres ocultarlo temporalmente</li>
</ul>
</div>

<div class="step">
<strong>Paso 6:</strong> Haz clic en <strong>"Guardar Banner"</strong>.
</div>

<h3>6.2 Eliminar un Banner</h3>
<div class="step">
<strong>Paso 1:</strong> En la tabla de la derecha, busca el banner que quieres borrar.
</div>

<div class="step">
<strong>Paso 2:</strong> Haz clic en el botón rojo con el ícono de basura 🗑️.
</div>

<div class="step">
<strong>Paso 3:</strong> Confirma que sí quieres eliminarlo.
</div>

<div class="tip">
💡 <strong>Consejos para banners:</strong><br>
• Usa imágenes de alta calidad (1920x1080 píxeles)<br>
• Texto corto y grande (que se lea bien en celular)<br>
• Máximo 5 banners (más de eso aburre al usuario)<br>
• Cambia los banners cada 2-3 semanas para mantener el sitio fresco<br>
• Para videos: que sean cortos (10-15 segundos) y sin audio
</div>

<h2 id="promociones">7. CREAR PROMOCIONES</h2>
<p>Las promociones son ofertas especiales con fecha de inicio y fin. Aparecen en la página "Promociones" y en el inicio con un contador regresivo.</p>

<h3>7.1 Crear una Promoción</h3>
<div class="step">
<strong>Paso 1:</strong> En el menú lateral, haz clic en <strong>"Promociones"</strong>.
</div>

<div class="step">
<strong>Paso 2:</strong> En el formulario de la izquierda, llena los datos:
<ul>
    <li><strong>Título:</strong> Nombre de la promoción (ej. "2x1 en Laptops")</li>
    <li><strong>Descripción:</strong> Explica los detalles de la oferta</li>
    <li><strong>Fecha Inicio:</strong> Cuándo comienza (por defecto es hoy)</li>
    <li><strong>Fecha Fin:</strong> Cuándo termina (por defecto es dentro de 7 días)</li>
    <li><strong>Imagen:</strong> Sube una imagen atractiva de la promoción (opcional pero recomendado)</li>
    <li><strong>Activar promoción:</strong> Déjalo marcado para que se vea inmediatamente</li>
</ul>
</div>

<div class="step">
<strong>Paso 3:</strong> Haz clic en <strong>"Guardar Promoción"</strong>.
</div>

<h3>7.2 ¿Cuándo Aparece y Desaparece?</h3>
<p>El sistema es inteligente:</p>
<ul>
    <li>Si hoy es <strong>antes</strong> de la Fecha de Inicio: La promoción NO se muestra (está programada para el futuro)</li>
    <li>Si hoy está <strong>entre</strong> la Fecha de Inicio y Fecha de Fin: La promoción SE MUESTRA con el contador regresivo</li>
    <li>Si hoy es <strong>después</strong> de la Fecha de Fin: La promoción YA NO se muestra (expiró)</li>
</ul>

<div class="tip">
💡 <strong>Consejo:</strong> Crea promociones con al menos 3-7 días de vigencia. Si es muy corto, los clientes no alcanzan a verla. Si es muy largo, no genera urgencia.
</div>

<h3>7.3 Eliminar una Promoción</h3>
<div class="step">
<strong>Paso 1:</strong> En la tabla de la derecha, busca la promoción.
</div>

<div class="step">
<strong>Paso 2:</strong> Haz clic en el botón rojo de basura 🗑️.
</div>

<div class="step">
<strong>Paso 3:</strong> Confirma la eliminación.
</div>

<h2 id="contacto">8. REVISAR MENSAJES DE CONTACTO</h2>
<p>Cuando alguien llena el formulario de contacto en la página web, el mensaje llega aquí.</p>

<h3>8.1 Ver los Mensajes</h3>
<div class="step">
<strong>Paso 1:</strong> En el menú lateral, haz clic en <strong>"Contacto"</strong> o <strong>"Mensajes de Contacto"</strong>.
</div>

<div class="step">
<strong>Paso 2:</strong> Verás tarjetas arriba que dicen:
<ul>
    <li><strong>Todos:</strong> Total de mensajes</li>
    <li><strong>Nuevos:</strong> Mensajes sin leer (en amarillo)</li>
    <li><strong>Leídos:</strong> Mensajes que ya revisaste (en verde)</li>
</ul>
</div>

<div class="step">
<strong>Paso 3:</strong> Abajo verás una tabla con:
<ul>
    <li>ID del mensaje</li>
    <li>Estado (Nuevo o Leído)</li>
    <li>Nombre de quien escribió</li>
    <li>Correo electrónico</li>
    <li>Asunto (Solicitar cotización, Información, Soporte, Otro)</li>
    <li>Fecha</li>
</ul>
</div>

<h3>8.2 Leer un Mensaje Completo</h3>
<div class="step">
<strong>Paso 1:</strong> En la tabla, haz clic en el botón <strong>"Ver"</strong> (ícono de ojo 👁️).
</div>

<div class="step">
<strong>Paso 2:</strong> Se abrirá una ventana con:
<ul>
    <li>Nombre completo</li>
    <li>Correo (puedes hacer clic para abrir tu correo)</li>
    <li>Teléfono (si lo proporcionó)</li>
    <li>Empresa (si la proporcionó)</li>
    <li>Asunto</li>
    <li>Mensaje completo</li>
</ul>
</div>

<h3>8.3 Responder un Mensaje</h3>
<div class="step">
<strong>Paso 1:</strong> En la ventana del mensaje, haz clic en el botón <strong>"Responder por email"</strong>.
</div>

<div class="step">
<strong>Paso 2:</strong> Se abrirá tu programa de correo (Outlook, Gmail, etc.) con el correo del cliente ya puesto.
</div>

<div class="step">
<strong>Paso 3:</strong> Escribe tu respuesta y envíala.
</div>

<div class="step">
<strong>Paso 4:</strong> Regresa al sistema y haz clic en <strong>"Marcar como leído"</strong> para que el mensaje ya no aparezca como nuevo.
</div>

<h3>8.4 Eliminar Mensajes</h3>
<div class="step">
<strong>Paso 1:</strong> En la tabla, haz clic en el botón rojo de basura 🗑️.
</div>

<div class="step">
<strong>Paso 2:</strong> Confirma que quieres borrarlo (útil para spam o mensajes antiguos).
</div>

<div class="tip">
💡 <strong>Consejo:</strong> Revisa los mensajes nuevos todos los días y responde en menos de 24 horas. Un buen tiempo de respuesta genera confianza en los clientes.
</div>

<h2 id="configuracion">9. CONFIGURAR LA EMPRESA</h2>
<p>Aquí es donde actualizas los datos de tu empresa, redes sociales, horarios y contenido de "Quiénes Somos".</p>

<h3>9.1 Acceder a la Configuración</h3>
<div class="step">
<strong>Paso 1:</strong> En el menú lateral, haz clic en <strong>"Configuración"</strong>.
</div>

<div class="step">
<strong>Paso 2:</strong> Verás varias pestañas en la parte superior. Haz clic en la que necesites editar.
</div>

<h3>9.2 Pestaña: Datos Generales</h3>
<p>Aquí va la información básica de tu empresa:</p>
<ul>
    <li><strong>Nombre de la Empresa:</strong> Aparece en el título del sitio y en los PDFs</li>
    <li><strong>Giro/Actividad:</strong> A qué se dedica la empresa (ej. "Venta de computadoras y accesorios")</li>
    <li><strong>Slogan:</strong> Frase corta (ej. "Tecnología al alcance de todos")</li>
    <li><strong>Logo:</strong> Sube el logo de tu empresa (aparece en el menú y PDFs)</li>
    <li><strong>Favicon:</strong> Ícono pequeño que aparece en la pestaña del navegador</li>
    <li><strong>Dirección:</strong> Dirección física completa</li>
    <li><strong>Teléfono:</strong> Teléfono principal</li>
    <li><strong>WhatsApp:</strong> Número de WhatsApp (solo números, sin espacios ni guiones)</li>
    <li><strong>Correo:</strong> Correo de contacto</li>
    <li><strong>Redes Sociales:</strong> Pega las URLs completas de Facebook, Instagram, TikTok, YouTube, LinkedIn</li>
    <li><strong>Google Maps:</strong> Pega el código "iframe" de Google Maps (ve a Google Maps → Compartir → Insertar mapa → Copia el HTML)</li>
</ul>

<h3>9.3 Pestaña: Identidad Corporativa</h3>
<p>Aquí va el contenido de la página "Quiénes Somos":</p>
<ul>
    <li><strong>Historia:</strong> Escribe la historia de tu empresa (cuándo se fundó, cómo creció, logros importantes)</li>
    <li><strong>Misión:</strong> ¿Cuál es el propósito de tu empresa? (ej. "Ofrecer la mejor tecnología al mejor precio")</li>
    <li><strong>Visión:</strong> ¿Hacia dónde quieres llegar? (ej. "Ser líderes en tecnología en la región")</li>
    <li><strong>Valores:</strong> Escribe uno por línea o sepáralos con comas (ej. "Honestidad, Calidad, Innovación, Compromiso")</li>
    <li><strong>Objetivos:</strong> Escribe uno por línea (ej. "Expandirnos a 3 ciudades más", "Lanzar 5 nuevos productos al año")</li>
    <li><strong>Imagen de Portada:</strong> Sube una foto grande para el fondo de "Quiénes Somos" (recomendado 1920x600 píxeles)</li>
    <li><strong>Fecha de Fundación:</strong> Selecciona la fecha (se usa para calcular automáticamente los "Años de experiencia")</li>
</ul>

<h3>9.4 Pestaña: Horarios de Atención</h3>
<p>Escribe tus horarios de atención para cada día:</p>
<ul>
    <li><strong>Lunes a Viernes:</strong> Ej. "09:00 - 18:00"</li>
    <li><strong>Sábado:</strong> Ej. "09:00 - 14:00" o "Cerrado"</li>
    <li><strong>Domingo:</strong> Ej. "Cerrado"</li>
</ul>

<div class="tip">
💡 <strong>Consejo:</strong> La página de contacto mostrará automáticamente si estás "Abierto" o "Cerrado" en este momento, basado en estos horarios.
</div>

<h3>9.5 Guardar los Cambios</h3>
<div class="step">
<strong>Paso 1:</strong> Después de editar cualquier pestaña, baja hasta el final de la página.
</div>

<div class="step">
<strong>Paso 2:</strong> Haz clic en el botón verde grande <strong>"Guardar Cambios"</strong>.
</div>

<div class="step">
<strong>Paso 3:</strong> Verás un mensaje verde confirmando que se guardó correctamente.
</div>

<h2 id="consejos">10. CONSEJOS Y RECOMENDACIONES</h2>

<h3>10.1 Mantenimiento Semanal</h3>
<p>Para mantener tu sitio siempre actualizado:</p>
<ul>
    <li>✅ <strong>Lunes:</strong> Revisar cotizaciones nuevas y mensajes de contacto</li>
    <li>✅ <strong>Miércoles:</strong> Revisar si hay productos sin imágenes y agregarlas</li>
    <li>✅ <strong>Viernes:</strong> Revisar promociones vigentes y crear nuevas si es necesario</li>
</ul>

<h3>10.2 Mantenimiento Mensual</h3>
<ul>
    <li> Cambiar los banners del inicio (cada 2-4 semanas)</li>
    <li>📅 Revisar y eliminar promociones vencidas</li>
    <li>📅 Actualizar Productos Premium con novedades</li>
    <li>📅 Revisar que los datos de contacto estén actualizados</li>
</ul>

<h3>10.3 Buenas Prácticas</h3>
<ul>
    <li><strong>Imágenes:</strong> Siempre usa imágenes de buena calidad. Una mala imagen daña la percepción de tu marca.</li>
    <li><strong>Tiempos de respuesta:</strong> Responde cotizaciones y mensajes en menos de 24 horas.</li>
    <li><strong>Contenido fresco:</strong> Actualiza promociones y banners regularmente para que el sitio no se vea abandonado.</li>
    <li><strong>Revisar en celular:</strong> De vez en cuando, abre tu sitio en un celular para ver cómo se ve.</li>
    <li><strong>Backup:</strong> Pide a tu proveedor de hosting que haga copias de seguridad semanales de la base de datos.</li>
</ul>

<h3>10.4 Seguridad</h3>
<ul>
    <li>🔒 <strong>Cambia tu contraseña</strong> cada 3 meses</li>
    <li>🔒 <strong>Nunca compartas</strong> tu usuario y contraseña</li>
    <li>🔒 <strong>Cierra sesión</strong> cuando termines de usar el sistema</li>
    <li>🔒 <strong>Verifica</strong> que nadie más tenga acceso a tu computadora con la sesión abierta</li>
</ul>

<h3>10.5 Soporte Técnico</h3>
<p>Si tienes problemas técnicos o dudas que no están en este manual:</p>
<ul>
    <li>📧 Correo: soporte@tudominio.com</li>
    <li>📱 Teléfono: (XXX) XXX-XXXX</li>
    <li>🕐 Horario de soporte: Lunes a Viernes de 9:00 a 18:00</li>
</ul>

<div class="warning">
⚠️ <strong>Antes de contactar soporte:</strong><br>
1. Revisa si el problema ya está resuelto en este manual<br>
2. Toma una captura de pantalla del error<br>
3. Anota los pasos que seguiste antes del error<br>
4. Verifica tu conexión a internet
</div>

<div class="footer">
<p><strong>© 2026 CMS Corporativo - Todos los derechos reservados</strong></p>
<p>Documento generado el 12 de junio de 2026<br>
Versión 1.0 del Manual de Usuario</p>
<p style="margin-top:20px; font-style:italic;">Este manual es propiedad de la empresa y está destinado exclusivamente para uso interno y capacitación del personal autorizado.</p>
</div>

</body>
</html>