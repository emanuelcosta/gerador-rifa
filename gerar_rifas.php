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

// JÁ FOI IMPRESSO ATÉ O NÚMERO 160
$totalRifas = 50;
$inicio     = 111;

$alturaRifa = 37;
$canhotoW   = 50;

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

$csv = fopen('rifas.csv','r');
$header = fgetcsv($csv, 0, ';'); // Pular cabeçalho
$header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

while(feof($csv) === false) {
    $valores = fgetcsv($csv, 0, ';');
    $dados = array_combine($header,$valores);
    if($dados['vendedor'] !== ''){
        continue;
    }

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
    $numero = str_pad((string)$dados['numero'], 4, '0', STR_PAD_LEFT);

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
    $pdf->Image('imagens/watermark_canhoto.jpg', $xBase + 5, $y + 8, $canhotoW - 10);
    $pdf->Image('imagens/watermark_canhoto.jpg', $x_rifa + 30, $y + 10, 40);
    $pdf->SetAlpha(1);

    /* ===== CANHOTO ===== */
    $pdf->SetFont('helvetica', 'BU', 10);
    $pdf->SetXY($xBase + 2, $y + 1);
    $pdf->MultiCell($canhotoW - 4, 6, 'ADTC SGA ABJ - EIXO DO CARRO', 0, 'C');

    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetXY($xBase + 2, $y + 10);
    $pdf->MultiCell(
        $canhotoW - 4,
        4,
        "Nome:\n\nTelefone:\n\nEndereço:"
    );

    $pdf->SetXY($xBase + 2, $y + 31);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell($canhotoW - 4, 4, "Nº " . $numero, 0, 1, 'R');


    // /* ===== RIFA ===== */
    $pdf->SetFont('helvetica', 'BU', 11);
    $pdf->SetXY($x_rifa_margin, $y + 1);
    $pdf->MultiCell($width_rifa, 6, 'ADTC SGA ABJ - EIXO DO CARRO', 0, 'C');

    $pdf->SetXY($x_rifa_margin, $y + 6);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell($width_rifa, 4, 'Rua Professora Nely Caúla, S/N - Alto Bom Jesus', 0, 1, 'C');
    $pdf->SetFont('helvetica', 'b', 11);
    $pdf->SetX($x_rifa_margin);
    $pdf->MultiCell(
        $width_rifa,
        4,
        "Rifa em prol do TRABALHO MISSIONÁRIO na localidade do EIXO DO CARRO. Valor: R$ 10,00\n",
        0,
        'J'
    );

    $pdf->SetX($x_rifa_margin);
    $pdf->SetFont('helvetica', 'b', '10');
    $pdf->Cell(15, 4, "Prêmios:");
    $x_depois_de_premios = $pdf->GetX();
    $y_depois_de_premios = $pdf->GetY();

    $pdf->SetXY($x_depois_de_premios + 2, $y_depois_de_premios);
    $pdf->SetFont('helvetica', '', 10);
    $texto_prêmios = "1º Caixa de som portátil / " .
        "2º Pix de 100,00\n" .
        "3º Kit Natura / 4º Cafeteira";
    $pdf->MultiCell($width_rifa, 5, $texto_prêmios, 0, 'j');

    // $x_depois_de_premios = $x_rifa - $x_depois_de_premios;
    $x_valor = $x_depois_de_premios + ($width_rifa - ($x_depois_de_premios - $x_rifa_margin))- 26;
    $pdf->SetXY($x_valor, $y_depois_de_premios);
    $pdf->SetFont('helvetica', 'BU', 10);
    // $pdf->WriteHTML("<strong>Valor:</strong> R$ 10,00");

    /* ===== NUMERAÇÃO ===== */
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetXY($x_rifa_margin, $y + $alturaRifa - 10);
    // $pdf->Cell(26, 6, 'Nº ' . $numero, 0, 0, 'R');
    $pdf->Cell($width_rifa, 6, 'Nº ' . $numero, 0, 0, 'R', '', 1);
    /* ===== PIX ===== */
    $texto_pix = 'Data do sorteio: 12/03/2026 / Pix: (85)9 9650-0294';
    $pdf->SetXY($x_rifa_margin, $y + $alturaRifa - 6);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell($width_rifa, 6, $texto_pix, 0, 0, 'R');

    $y += $alturaRifa + 1;
}

// gerar pdf
$pdf->Output('rifas_duas_colunas_paisagem.pdf', 'I');
ob_end_flush();
