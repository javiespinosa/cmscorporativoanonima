<?php

require_once '../../includes/auth.php';
require_once '../../config/database.php';
require_once '../../fpdf/fpdf.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
"SELECT *
 FROM cotizaciones
 WHERE id=?");

$stmt->execute([$id]);

$cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$cotizacion)
{
    die('Cotización no encontrada');
}

$stmt = $pdo->prepare(
"SELECT
d.cantidad,
p.nombre,
p.descripcion_corta
FROM cotizacion_detalle d
INNER JOIN productos p
ON p.id=d.producto_id
WHERE d.cotizacion_id=?");

$stmt->execute([$id]);

$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF();

$pdf->AddPage();

$config = $pdo
->query("SELECT * FROM configuracion WHERE id=1")
->fetch(PDO::FETCH_ASSOC);

if (!empty($config['logo'])) {

    $logo = __DIR__ . '/../../' . $config['logo'];

    if (file_exists($logo)) {
        $pdf->Image($logo, 10, 10, 35);
    } else {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, 'Logo no disponible', 0, 1);
    }
}
$pdf->SetFont('Arial','B',18);

$pdf->SetFont('Arial','B',18);

$pdf->Cell(
0,
10,
utf8_decode($config['empresa']),
0,
1,
'C'
);

$pdf->SetFont('Arial','',10);

$pdf->Cell(
0,
6,
utf8_decode($config['giro']),
0,
1,
'C'
);

$pdf->Ln(5);

$pdf->SetFont('Arial','B',16);

$pdf->Cell(
0,
10,
'COTIZACION',
0,
1,
'C'
);

$pdf->Ln(5);

$pdf->SetFont('Arial','',10);

$pdf->Cell(
0,
6,
utf8_decode('Folio: COT-'.str_pad($id,6,'0',STR_PAD_LEFT)),
0,
1
);

$pdf->Cell(
0,
6,
utf8_decode('Fecha: '.date('d/m/Y')),
0,
1
);

$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);

$pdf->Cell(
0,
8,
utf8_decode('DATOS DEL CLIENTE'),
0,
1
);

$pdf->SetFont('Arial','',10);

$pdf->Cell(
0,
6,
utf8_decode('Nombre: '.$cotizacion['nombre']),
0,
1
);

$pdf->Cell(
0,
6,
utf8_decode('Empresa: '.$cotizacion['empresa']),
0,
1
);

$pdf->Cell(
0,
6,
utf8_decode('Teléfono: '.$cotizacion['telefono']),
0,
1
);

$pdf->Cell(
0,
6,
utf8_decode('Correo: '.$cotizacion['correo']),
0,
1
);

$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);

$pdf->Cell(
120,
8,
'Producto',
1
);

$pdf->Cell(
30,
8,
'Cantidad',
1,
1
);

$pdf->SetFont('Arial','',10);

foreach($productos as $p)
{
    $pdf->Cell(
        120,
        8,
        utf8_decode($p['nombre']),
        1
    );

    $pdf->Cell(
        30,
        8,
        $p['cantidad'],
        1,
        1
    );
}

$pdf->Ln(10);

$pdf->SetFont('Arial','B',12);

$pdf->Cell(
0,
8,
utf8_decode('Comentarios'),
0,
1
);

$pdf->SetFont('Arial','',10);

$pdf->MultiCell(
0,
6,
utf8_decode($cotizacion['comentarios'])
);

$pdf->Ln(10);

$pdf->SetFont('Arial','I',9);

$pdf->Cell(
0,
6,
utf8_decode('Documento generado por CMS Corporativo'),
0,
1,
'C'
);

$pdf->Output(
'I',
'Cotizacion_'.$id.'.pdf'
);