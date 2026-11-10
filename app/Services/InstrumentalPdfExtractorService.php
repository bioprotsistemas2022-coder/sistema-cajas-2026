<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

/**
 * Puerto de includes/extraer_instrumental.php del módulo de Consignaciones
 * (C:\xampp\htdocs\consignaciones). Mismo algoritmo: reconstruye líneas de
 * texto a partir de las posiciones x/y del PDF, filtra cabecera/pie de
 * página del remito por prefijo, detecta ítems "<cantidad> <descripción>"
 * y agrupa el resto de líneas largas como encabezados de sección.
 */
class InstrumentalPdfExtractorService
{
    private const PREFIJOS = [
        'BIOPROT', 'C.U.I.T.', 'MONTEVIDEO', 'E MAIL',
        'REMITO EN CONSIGNACI', 'CONDICIONES', 'EMERGENTES', 'DE LOS',
        'PAGINA', 'PÁGINA', 'INSTITUCION', 'FECHA', 'DOCTOR', 'PACIENTE',
        'CONTROL', 'INGRESO', 'SALIDA', 'ACONDICIONAMIENTO',
        'REMITO', 'RECIBIDO POR', 'FIRMA', 'ACLARACION',
    ];

    /**
     * @return array<int, array{nombre: string, items: array<int, array{cantidad: int, descripcion: string}>}>
     */
    public static function extraer(string $rutaAbsoluta): array
    {
        $secciones = [];

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($rutaAbsoluta);
            $pages = $pdf->getPages();
        } catch (\Exception $e) {
            return $secciones;
        }

        foreach ($pages as $page) {
            $dataTm = $page->getDataTm();
            $items = [];
            foreach ($dataTm as $d) {
                $str = trim((string) $d[1]);
                if ($str === '') {
                    continue;
                }
                $items[] = ['x' => (float) $d[0][4], 'y' => (float) $d[0][5], 'str' => $str];
            }

            usort($items, function ($a, $b) {
                $ya = (int) round($a['y']);
                $yb = (int) round($b['y']);
                if ($ya !== $yb) {
                    return $yb - $ya;
                }
                return $a['x'] <=> $b['x'];
            });

            $lines = [];
            $curIdx = -1;
            foreach ($items as $it) {
                $y = (int) round($it['y']);
                if ($curIdx === -1 || abs($y - $lines[$curIdx]['y']) > 3) {
                    $lines[] = ['y' => $y, 'parts' => []];
                    $curIdx = count($lines) - 1;
                }
                $lines[$curIdx]['parts'][] = ['x' => $it['x'], 'str' => $it['str']];
            }

            foreach ($lines as &$l) {
                usort($l['parts'], fn ($a, $b) => $a['x'] <=> $b['x']);
                $texts = array_map(fn ($p) => $p['str'], $l['parts']);
                $l['text'] = trim(implode(' ', $texts));
            }
            unset($l);

            $curSecIdx = -1;
            foreach ($lines as $l) {
                $line = $l['text'];
                if ($line === '') {
                    continue;
                }

                $up = mb_strtoupper($line, 'UTF-8');
                $up = preg_replace('/^[^A-Z0-9]+/', '', $up);
                $skip = false;
                foreach (self::PREFIJOS as $p) {
                    if (str_starts_with($up, $p)) {
                        $skip = true;
                        break;
                    }
                }
                if ($skip) {
                    continue;
                }

                if (preg_match('/^(\d+)\s+(.+)$/', $line, $m)) {
                    if ($curSecIdx === -1) {
                        $secciones[] = ['nombre' => 'Instrumental', 'items' => []];
                        $curSecIdx = count($secciones) - 1;
                    }
                    $secciones[$curSecIdx]['items'][] = ['cantidad' => (int) $m[1], 'descripcion' => trim($m[2])];
                    continue;
                }

                if (mb_strlen($line, 'UTF-8') <= 5) {
                    continue;
                }
                $secciones[] = ['nombre' => $line, 'items' => []];
                $curSecIdx = count($secciones) - 1;
            }
        }

        return self::deduplicar($secciones);
    }

    /**
     * @param array<int, array{nombre: string, items: array<int, array{cantidad: int, descripcion: string}>}> $secciones
     * @return array<int, array{nombre: string, items: array<int, array{cantidad: int, descripcion: string}>}>
     */
    private static function deduplicar(array $secciones): array
    {
        $resultado = [];
        foreach ($secciones as $s) {
            if (count($s['items']) === 0) {
                continue;
            }
            $found = false;
            foreach ($resultado as &$r) {
                if ($r['nombre'] === $s['nombre']) {
                    $existing = [];
                    foreach ($r['items'] as $it) {
                        $existing[] = $it['cantidad'] . '|' . $it['descripcion'];
                    }
                    foreach ($s['items'] as $it) {
                        $key = $it['cantidad'] . '|' . $it['descripcion'];
                        if (!in_array($key, $existing, true)) {
                            $r['items'][] = $it;
                            $existing[] = $key;
                        }
                    }
                    $found = true;
                    break;
                }
            }
            unset($r);
            if (!$found) {
                $existing = [];
                $uniqueItems = [];
                foreach ($s['items'] as $it) {
                    $key = $it['cantidad'] . '|' . $it['descripcion'];
                    if (!in_array($key, $existing, true)) {
                        $uniqueItems[] = $it;
                        $existing[] = $key;
                    }
                }
                $s['items'] = $uniqueItems;
                $resultado[] = $s;
            }
        }
        return $resultado;
    }
}
