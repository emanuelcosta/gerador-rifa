<?php

declare(strict_types=1);

ob_start();
require_once __DIR__ . '/vendor/autoload.php';


/* ================= HTML PDF ================= 
<h3>ADTC SGA ABJ - EIXO DO CARRO</h3>
<p>Rua Professora Nely Caúla, S/N - Alto Bom Jesus</p>

<p>
Rifa em prol do Projeto Missionário na localidade do EIXO DO CARRO.
</p>

<b>Prêmios:</b>
<ul>
  <li>1º Caixa de som portátil</li>
  <li>2º Pix de R$ 100,00</li>
  <li>3º Kit Natura</li>
</ul>

<p><b>Valor:</b> R$ 10,00</p>
*/


/*
<h3>Carnê de Contribuição Missionária</h3>

<p>
Contribuição voluntária para apoio ao campo missionário.
</p>

<p>
<b>Finalidade:</b> Sustento missionário, ações sociais e evangelismo.
</p>

<p>
<b>Valor sugerido:</b> R$ 50,00
</p>
*/


class PDF extends TCPDF
{
    // Marcas de corte profissionais
    public function MarcasDeCorte(float $x, float $y, float $w, float $h): void
    {
        $t = 4;
        $this->SetLineWidth(0.3);

        // Superior esquerda
        $this->Line($x - $t, $y, $x, $y);
        $this->Line($x, $y - $t, $x, $y);

        // Superior direita
        $this->Line($x + $w, $y - $t, $x + $w, $y);
        $this->Line($x + $w, $y, $x + $w + $t, $y);

        // Inferior esquerda
        $this->Line($x - $t, $y + $h, $x, $y + $h);
        $this->Line($x, $y + $h, $x, $y + $h + $t);

        // Inferior direita
        $this->Line($x + $w, $y + $h, $x + $w + $t, $y + $h);
        $this->Line($x + $w, $y + $h, $x + $w, $y + $h + $t);
    }
}

/* ================= CONFIGURAÇÕES ================= */

$totalRifas = 110;
$inicio     = 1;

$alturaRifa = 37;
$canhotoW   = 60;

$margemX = 3;
$margemY = 5;

$pdf = new PDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetMargins($margemX, $margemY, $margemX);
$pdf->SetAutoPageBreak(false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

/* ================= LAYOUT COLUNAS ================= */

$larguraPagina = 297 - ($margemX * 2);
$colunaW = ($larguraPagina / 2) - 1;
$alturaMax = 200;

$colunaAtual = 1;
$xBase = $margemX;
$y = $margemY;
$width_rifa = $colunaW - $canhotoW - 1;

/* ================= LOOP PRINCIPAL ================= */

for ($i = $inicio; $i < ($inicio + $totalRifas); $i++) {

    if ($y + $alturaRifa > $alturaMax) {
        if ($colunaAtual === 1) {
            $colunaAtual = 2;
            $xBase = $margemX + $colunaW + 1;
            $y = $margemY;
        } else {
            $pdf->AddPage();
            $colunaAtual = 1;
            $xBase = $margemX;
            $y = $margemY;
        }
    }

    $x_rifa = $xBase + $canhotoW;
    $margin_rifa = 2;
    $x_rifa_margin = $x_rifa + $margin_rifa;
    $width_rifa = $colunaW - $canhotoW - ($margin_rifa * 2);
    $numero = str_pad((string)$i, 4, '0', STR_PAD_LEFT);

    /* ===== FUNDO ===== */
    $pdf->MarcasDeCorte($xBase, $y, $colunaW, $alturaRifa);
    $pdf->setLineStyle(['width' => 0.2, 'dash' => 0, 'color' => [0, 0, 0]]);
    $pdf->Rect($xBase, $y, $colunaW, $alturaRifa);

    /* ===== LINHA PONTILHADA ===== */
    $pdf->SetLineStyle(['width' => 0.2, 'dash' => '2,2']);
    $pdf->Line(
        $x_rifa,
        $y,
        $x_rifa,
        $y + $alturaRifa
    );

    /* ===== MARCA D’ÁGUA ===== */
    $pdf->SetAlpha(0.12);
    $pdf->Image('imagens/watermark_canhoto.jpg', $xBase + 5, $y + 1, $canhotoW - 10);
    $pdf->Image('imagens/watermark_canhoto.jpg', $x_rifa + 25, $y + 6, 40);
    $pdf->SetAlpha(1);

    /* ===== CANHOTO ===== */
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetXY($xBase + 2, $y + 1);
    $pdf->MultiCell($canhotoW - 4, 6, "ADTC SGA Alto Bom Jesus\nIDE E ANUNCIAI\nCarnê de Contribuição\n", 0, 'C');

    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetX($xBase + 2);
    $pdf->MultiCell(
        $canhotoW - 4,
        4,
        "(  ) PIX (  ) Dinheiro\nData:___/___/____\nValor:___________\n______________________\nAssinatura do Tesoureiro(a)",
        0,
        'C'
    );

    // /* ===== RIFA ===== */
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetXY($x_rifa_margin, $y + 1);
    $pdf->MultiCell($width_rifa, 6, "ADTC ALTO BOM JESUS\nIDE e ANUNCIAI - Carnê Contribuição", 0, 'C');

    $pdf->SetFont('helvetica', '', 11);
    $pdf->SetX($x_rifa_margin);
    $pdf->MultiCell(
        $width_rifa,
        4,
        "(  ) PIX (  ) Dinheiro\nContribuinte:______________________\nData:___/___/____  Valor:___________\n",
        'L'
    );


    /* ===== assinatura do tesoureiro ===== */
    $pdf->SetXY($x_rifa_margin, $y + $alturaRifa - 10);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell($width_rifa, 4, "_____________________________\nAssinatura do Tesoureiro(a)", 0, 'C');


    // /* ===== PIX ===== */
    // $texto_pix = 'Pix: (85)9 9650-0294';
    // $pdf->SetXY($x_rifa_margin, $y + $alturaRifa - 6);
    // $pdf->SetFont('helvetica', '', 9);
    // $pdf->Cell($width_rifa, 6, $texto_pix, 0, 0, 'R');

    $y += $alturaRifa + 1;
}


// capa do carnê - mesmo tamanho das rifas
$pdf->AddPage();

foreach (['left', 'right'] as $position) {
    if ($y + $alturaRifa > $alturaMax) {
        if ($colunaAtual === 1) {
            $colunaAtual = 2;
            $xBase = $margemX + $colunaW + 1;
            $y = $margemY;
        } else {
            $pdf->AddPage();
            $colunaAtual = 1;
            $xBase = $margemX;
            $y = $margemY;
        }
    }

    $x_rifa = $xBase + $canhotoW;
    $margin_rifa = 2;
    $x_rifa_margin = $x_rifa + $margin_rifa;
    $width_rifa = $colunaW - $canhotoW - ($margin_rifa * 2);
    $numero = str_pad((string)$i, 4, '0', STR_PAD_LEFT);

        /* ===== FUNDO ===== */
    $pdf->MarcasDeCorte($xBase, $y, $colunaW, $alturaRifa);
    $pdf->setLineStyle(['width' => 0.2, 'dash' => 0, 'color' => [0, 0, 0]]);
    $pdf->Rect($xBase, $y, $colunaW, $alturaRifa);

    // html/capa_carne.html
    $html = file_get_contents(__DIR__ . '/html/capa_carne.html');
   $pdf->SetXY($xBase + 2, $y);
    $pdf->writeHTMLCell(
        $colunaW,          // largura EXATA
        $alturaRifa,       // altura EXATA
        $xBase + 2,
        $y,
        $html,
        0,
        0,
        false,
        true,
        'C',
        true
    );
}

$pdf->Output('rifas_duas_colunas_paisagem.pdf', 'I');
ob_end_flush();
