<?php

declare(strict_types=1);

/**
 * Gera arquivo CSV com dados de rifas
 * Colunas: numero, vendedor, vendido
 */

// Configurações
$totalRifas = 110;
$inicio = 1;
$filename = 'rifas_' . date('Y-m-d_His') . '.csv';

// Criar arquivo CSV
$file = fopen($filename, 'w');

// Definir encoding UTF-8 com BOM
fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

// Cabeçalho
fputcsv($file, ['numero', 'vendedor', 'vendido'], ';');

// Dados
for ($i = $inicio; $i <= ($inicio + $totalRifas - 1); $i++) {
    fputcsv($file, [
        str_pad((string)$i, 3, '0', STR_PAD_LEFT),
        '', // vendedor vazio (será preenchido depois)
        'nao' // inicialmente não vendido
    ], ';');
}

fclose($file);

echo "Arquivo CSV gerado com sucesso: <strong>{$filename}</strong>\n";
echo "Total de rifas: <strong>{$totalRifas}</strong>\n";
echo "Baixar: <a href='{$filename}' download>Clique aqui</a>";
