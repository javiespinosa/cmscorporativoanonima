<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';
require_once '../../config/database_sativa.php'; // ← Conexión a Sativa
require_once '../../fpdf/fpdf.php';

$id = (int)($_GET['id'] ?? 0);

// 1. Obtener cotización desde CMS
$stmt = $pdo->prepare("SELECT * FROM cotizaciones WHERE id = ?");
$stmt->execute([$id]);
$cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cotizacion) {
    die('Cotización no encontrada');
}

// 2. Obtener detalles (IDs y cantidades) desde CMS
$stmtDetalles = $pdo->prepare("SELECT producto_id, cantidad FROM cotizacion_detalle WHERE cotizacion_id = ?");
$stmtDetalles->execute([$id]);
$detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

// 3. Obtener productos desde SATIVA
$productos = [];
if (!empty($detalles)) {
    $ids = array_column($detalles, 'producto_id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $stmtProd = $pdo_sativa->prepare("
        SELECT 
            p.id,
            p.Codigo,
            p.Descripcion,
            p.Precio1,
            pi.Path1 as imagen
        FROM producto p
        LEFT JOIN producto_imagenes pi ON p.id = pi.idProducto AND pi.Deleted = 0
        WHERE p.id IN ($placeholders)
    ");
    $stmtProd->execute($ids);
    $productosSat = $stmtProd->fetchAll(PDO::FETCH_ASSOC);
    
    // Combinar: agregar la cantidad del detalle a cada producto
    $cantidades = array_column($detalles, 'cantidad', 'producto_id');
    foreach ($productosSat as $prod) {
        $prod['cantidad'] = $cantidades[$prod['id']] ?? 1;
        $productos[] = $prod;
    }
}

// 4. Obtener configuración
$config = $pdo->query("SELECT * FROM configuracion WHERE id=1")->fetch(PDO::FETCH_ASSOC);

// ============================================
// CLASE PDF PERSONALIZADA CON HEADER Y FOOTER
// ============================================
class PDF_Cotizacion extends FPDF {
    private $config;
    private $folio;
    private $fecha_cot;
    
    function __construct($config, $folio, $fecha) {
        parent::__construct();
        $this->config = $config;
        $this->folio = $folio;
        $this->fecha_cot = $fecha;
    }
    
    function Header() {
        $verde = [40, 167, 69];
        $verde_claro = [232, 245, 233];
        
        // Barra superior verde
        $this->SetFillColor($verde[0], $verde[1], $verde[2]);
        $this->Rect(0, 0, 210, 8, 'F');
        
        // Logo
        if (!empty($this->config['logo'])) {
            $logo = __DIR__ . '/../../' . $this->config['logo'];
            if (file_exists($logo)) $this->Image($logo, 12, 15, 35);
        }
        
        // Empresa
        $this->SetXY(52, 15);
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(0, 8, utf8_decode($this->config['empresa']), 0, 1);
        
        $this->SetX(52);
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(108, 117, 125);
        $this->Cell(0, 5, utf8_decode($this->config['giro'] ?? ''), 0, 1);
        
        if (!empty($this->config['slogan'])) {
            $this->SetX(52);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 5, utf8_decode('"' . $this->config['slogan'] . '"'), 0, 1);
        }
        
        $this->SetX(52);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(80, 80, 80);
        $contacto = [];
        if (!empty($this->config['telefono'])) $contacto[] = 'Tel: ' . $this->config['telefono'];
        if (!empty($this->config['correo'])) $contacto[] = $this->config['correo'];
        $this->Cell(0, 4, implode('  |  ', $contacto), 0, 1);
        
        // Línea divisoria
        $this->SetDrawColor($verde[0], $verde[1], $verde[2]);
        $this->SetLineWidth(0.5);
        $this->Line(12, 48, 198, 48);
        
        // Caja de Folio
        $this->SetFillColor($verde_claro[0], $verde_claro[1], $verde_claro[2]);
        $this->SetDrawColor($verde[0], $verde[1], $verde[2]);
        $this->Rect(145, 15, 53, 25, 'DF');
        
        $this->SetXY(145, 17);
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor($verde[0], $verde[1], $verde[2]);
        $this->Cell(53, 4, 'COTIZACION', 0, 1, 'C');
        
        $this->SetX(145);
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(53, 6, $this->folio, 0, 1, 'C');
        
        $this->SetX(145);
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(108, 117, 125);
        $this->Cell(53, 4, 'Fecha: ' . $this->fecha_cot, 0, 1, 'C');
        
        $this->SetY(55);
    }
    
    function Footer() {
        $verde = [40, 167, 69];
        $this->SetY(-35);
        $this->SetDrawColor($verde[0], $verde[1], $verde[2]);
        $this->SetLineWidth(0.3);
        $this->Line(12, $this->GetY(), 198, $this->GetY());
        $this->Ln(2);
        
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 3, utf8_decode('Esta cotización tiene una vigencia de 15 días a partir de su emisión.'), 0, 1, 'C');
        $this->Cell(0, 3, utf8_decode('Precios sujetos a disponibilidad. IVA incluido salvo indicación contraria.'), 0, 1, 'C');
        
        $redes = [];
        if (!empty($this->config['facebook'])) $redes[] = 'Facebook';
        if (!empty($this->config['instagram'])) $redes[] = 'Instagram';
        if (!empty($this->config['whatsapp'])) $redes[] = 'WhatsApp: ' . $this->config['whatsapp'];
        if (!empty($redes)) {
            $this->SetFont('Arial', '', 7);
            $this->SetTextColor($verde[0], $verde[1], $verde[2]);
            $this->Cell(0, 3, 'Síguenos: ' . implode(' | ', $redes), 0, 1, 'C');
        }
        
        $this->SetFont('Arial', 'B', 7);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(0, 3, utf8_decode('© ' . date('Y') . ' ' . $this->config['empresa'] . ' - Todos los derechos reservados'), 0, 1, 'C');
        
        $this->SetFillColor($verde[0], $verde[1], $verde[2]);
        $this->Rect(0, 289, 210, 8, 'F');
    }
}

// ============================================
// GENERAR PDF
// ============================================
$folio = 'COT-' . date('Y') . '-' . str_pad($id, 6, '0', STR_PAD_LEFT);
$fecha_cot = date('d/m/Y');
$fecha_venc = date('d/m/Y', strtotime('+15 days'));

// QR
$qr_data = "Cotizacion: $folio\nEmpresa: " . $config['empresa'] . "\nCliente: " . $cotizacion['nombre'] . "\nTel: " . ($config['telefono'] ?? '');
$qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qr_data) . '&color=28a745';
$qr_temp = __DIR__ . '/../../uploads/temp/qr_' . $id . '.png';
if (!is_dir(__DIR__ . '/../../uploads/temp')) mkdir(__DIR__ . '/../../uploads/temp', 0755, true);

$qr_image = @file_get_contents($qr_url);
$tiene_qr = false;
if ($qr_image) {
    file_put_contents($qr_temp, $qr_image);
    $tiene_qr = true;
}

// Helper para formato de moneda
function formato_moneda($valor) {
    return '$' . number_format(floatval($valor), 2, '.', ',');
}

$pdf = new PDF_Cotizacion($config, $folio, $fecha_cot);
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 45);

$verde = [40, 167, 69];

// ===== 1. DATOS DEL CLIENTE =====
$pdf->SetFillColor($verde[0], $verde[1], $verde[2]);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, '  DATOS DEL CLIENTE', 0, 1, 'L', true);

$pdf->SetFillColor(248, 249, 250);
$pdf->SetTextColor(44, 62, 80);
$y_cli = $pdf->GetY();
$pdf->Rect(12, $y_cli, 186, 32, 'DF');

$pdf->SetXY(15, $y_cli + 3);
$datos = [
    'NOMBRE:' => $cotizacion['nombre'],
    'EMPRESA:' => $cotizacion['empresa'] ?: 'N/A',
    'TELÉFONO:' => $cotizacion['telefono'],
    'CORREO:' => $cotizacion['correo']
];
foreach ($datos as $etiq => $val) {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor(108, 117, 125);
    $pdf->Cell(35, 5, utf8_decode($etiq), 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(44, 62, 80);
    $pdf->Cell(0, 5, utf8_decode($val), 0, 1);
}
$pdf->Ln(8);

// ===== 2. TABLA DE PRODUCTOS =====
$pdf->SetFillColor($verde[0], $verde[1], $verde[2]);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, '  DETALLE DE PRODUCTOS', 0, 1, 'L', true);
$pdf->Ln(2);

// Encabezados de tabla
$pdf->SetFillColor(44, 62, 80);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 8, '#', 1, 0, 'C', true);
$pdf->Cell(65, 8, 'PRODUCTO', 1, 0, 'C', true);
$pdf->Cell(45, 8, 'DESCRIPCION', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'P. UNITARIO', 1, 0, 'C', true);
$pdf->Cell(18, 8, 'CANT.', 1, 0, 'C', true);
$pdf->Cell(23, 8, 'SUBTOTAL', 1, 1, 'C', true);

// Filas de productos
$pdf->SetTextColor(44, 62, 80);
$pdf->SetFont('Arial', '', 9);

$subtotal_gral = 0;
$fill = false;
$i = 1;

foreach ($productos as $p) {
    $precio = floatval($p['Precio1']);
    $subtotal_linea = $precio * intval($p['cantidad']);
    $subtotal_gral += $subtotal_linea;
    
    $pdf->SetFillColor($fill ? 248 : 255, $fill ? 249 : 255, $fill ? 250 : 255);
    $pdf->SetDrawColor(220, 220, 220);
    
    $pdf->Cell(10, 8, $i, 1, 0, 'C', true);
    $pdf->Cell(65, 8, utf8_decode($p['Codigo']), 1, 0, 'L', true);
    $pdf->Cell(45, 8, utf8_decode(substr($p['Descripcion'] ?? '', 0, 40)), 1, 0, 'L', true);
    $pdf->Cell(25, 8, formato_moneda($precio), 1, 0, 'R', true);
    $pdf->Cell(18, 8, $p['cantidad'], 1, 0, 'C', true);
    
    // Subtotal de línea en negrita y verde
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor($verde[0], $verde[1], $verde[2]);
    $pdf->Cell(23, 8, formato_moneda($subtotal_linea), 1, 1, 'R', true);
    
    // Resetear estilos
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(44, 62, 80);
    $fill = !$fill;
    $i++;
}

// ===== 3. TOTALES (Subtotal, IVA, Total) =====
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(44, 62, 80);

// Subtotal
$pdf->Cell(163, 8, 'SUBTOTAL:', 'LTR', 0, 'R');
$pdf->Cell(23, 8, formato_moneda($subtotal_gral), 'LTR', 1, 'R');

// IVA (16%)
$iva = $subtotal_gral * 0.16;
$pdf->Cell(163, 8, 'IVA (16%):', 'LR', 0, 'R');
$pdf->Cell(23, 8, formato_moneda($iva), 'LR', 1, 'R');

// Total General (Destacado)
$total_final = $subtotal_gral + $iva;
$pdf->SetFillColor(44, 62, 80);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(163, 10, 'TOTAL:', 'LRB', 0, 'R', true);

$pdf->SetFillColor($verde[0], $verde[1], $verde[2]);
$pdf->Cell(23, 10, formato_moneda($total_final), 'LRB', 1, 'R', true);

$pdf->Ln(3);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 4, 'Moneda: MXN (Pesos Mexicanos)', 0, 1, 'L');

// ===== 4. OBSERVACIONES =====
if (!empty($cotizacion['comentarios'])) {
    $pdf->Ln(3);
    $pdf->SetFillColor($verde[0], $verde[1], $verde[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, '  OBSERVACIONES', 0, 1, 'L', true);
    
    $pdf->SetFillColor(248, 249, 250);
    $pdf->SetTextColor(44, 62, 80);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(186, 5, utf8_decode($cotizacion['comentarios']), 1, 'L', true);
}

// ===== 5. FIRMA Y QR =====
$pdf->Ln(8);
if ($tiene_qr) {
    $pdf->Image($qr_temp, 15, $pdf->GetY(), 30);
    $pdf->SetXY(50, $pdf->GetY() + 5);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->SetTextColor(108, 117, 125);
    $pdf->Cell(0, 4, 'Escanea el QR para', 0, 1);
    $pdf->SetX(50);
    $pdf->Cell(0, 4, 'contactarnos directamente', 0, 1);
    $pdf->SetXY(50, $pdf->GetY() + 3);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetTextColor($verde[0], $verde[1], $verde[2]);
    $pdf->Cell(0, 4, utf8_decode($config['empresa']), 0, 1);
}

$y_firma = $pdf->GetY() + 5;
$pdf->SetXY(120, $y_firma);
$pdf->SetDrawColor(44, 62, 80);
$pdf->SetLineWidth(0.3);
$pdf->Line(120, $y_firma + 25, 195, $y_firma + 25);
$pdf->SetXY(120, $y_firma + 27);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(44, 62, 80);
$pdf->Cell(75, 4, 'Firma de Conformidad', 0, 1, 'C');
$pdf->SetX(120);
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(108, 117, 125);
$pdf->Cell(75, 4, utf8_decode($cotizacion['nombre']), 0, 1, 'C');
$pdf->SetX(120);
$pdf->Cell(75, 4, 'Cliente', 0, 1, 'C');

// ===== 6. VIGENCIA =====
$pdf->SetY(-55);
$pdf->SetFillColor(255, 243, 205);
$pdf->SetDrawColor(255, 193, 7);
$pdf->SetTextColor(133, 100, 4);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Rect(12, $pdf->GetY(), 186, 12, 'DF');
$pdf->SetXY(15, $pdf->GetY() + 2);
$pdf->Cell(0, 4, 'VIGENCIA DE ESTA COTIZACION: ' . $fecha_venc, 0, 1);
$pdf->SetX(15);
$pdf->SetFont('Arial', 'I', 7);
$pdf->Cell(0, 4, 'Despues de esta fecha, los precios y disponibilidad pueden variar.', 0, 1);

// ===== 7. SALIDA =====
$pdf->Output('I', 'Cotizacion_' . $folio . '.pdf');

if ($tiene_qr && file_exists($qr_temp)) @unlink($qr_temp);