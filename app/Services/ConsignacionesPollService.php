<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\Cirugia;
use App\Models\Consumo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConsignacionesPollService
{
    public function poll(int $limit = 50): array
    {
        if (!config('services.consignaciones.enabled')) {
            return ['ok' => false, 'skipped' => true, 'reason' => 'poll deshabilitado'];
        }

        $url = config('services.consignaciones.poll_url');
        if (!$url) {
            return ['ok' => false, 'error' => 'poll_url no configurado'];
        }

        $desde = cache()->get('consignaciones_poll_desde', now()->subDay()->toIso8601String());

        $response = Http::timeout(10)->get($url, ['desde' => $desde, 'limit' => $limit]);

        if (!$response->successful()) {
            Log::warning('Consignaciones poll fallo HTTP ' . $response->status() . ' ' . $response->body());
            return ['ok' => false, 'error' => 'HTTP ' . $response->status()];
        }

        $json = $response->json();
        if (!($json['ok'] ?? false)) {
            return ['ok' => false, 'error' => $json['error'] ?? 'respuesta no ok'];
        }

        $notas = $json['data'] ?? [];
        $procesadas = 0;
        $creadas = 0;

        foreach ($notas as $nota) {
            $plcCod = (string)($nota['NcoPlcCod'] ?? '');
            if ($plcCod === '' || $plcCod === '0') continue;
            $cacCod = (int)($nota['NcoCac'] ?? 0);
            $ncoCod = (int)($nota['NcoCod'] ?? 0);
            if ($cacCod <= 0 || $ncoCod <= 0) continue;

            $existe = Cirugia::where('external_nco_cod', $ncoCod)->where('plc_cod', $plcCod)->exists();
            if ($existe) {
                $procesadas++;
                continue;
            }

            $caja = Caja::where('codigo_interno', 'CAJA-' . $cacCod)->first();
            if (!$caja || $caja->estado !== 'CONSIGNADA') {
                continue;
            }

            try {
                $cirugia = Cirugia::create([
                    'plc_cod' => $plcCod,
                    'external_nco_cod' => $ncoCod,
                    'paciente' => $nota['NcoPac'] ?: ($nota['PlcFec'] ?? 'Paciente ' . $plcCod),
                    'medico' => $nota['NcoMed'] ?: ($nota['MedNom'] ?? 'Médico no especificado'),
                    'fecha_cx' => $nota['CxFec'] ?? $nota['NcoFec'] ?? now()->toDateString(),
                    'observaciones' => trim('Poll consignaciones NcoCod ' . $ncoCod . ($nota['HospDesc'] ? ' | Institución: ' . $nota['HospDesc'] : '')),
                    'external_implantes' => $nota['implantes'] ?? null,
                    'status' => 'PENDIENTE',
                ]);
                $cirugia->cajas()->attach($caja->id);
                if (!empty($nota['implantes']) || !empty($nota['detalles'])) {
                    Consumo::create([
                        'cirugia_id' => $cirugia->id,
                        'caja_id' => $caja->id,
                        'items' => $nota['implantes'] ?? [],
                        'observaciones' => !empty($nota['detalles']) ? json_encode($nota['detalles']) : null,
                    ]);
                }
                BoxStateService::transition($caja->fresh(), 'PENDIENTE_DESPACHO', null, [
                    'responsable_nombre' => 'Poll Consignaciones PlcCod ' . $plcCod,
                    'observaciones' => 'Poll vinculación NcoCod ' . $ncoCod,
                ]);
                $creadas++;
            } catch (\Throwable $e) {
                Log::error('Poll crear cirugia fallo NcoCod ' . $ncoCod . ': ' . $e->getMessage());
            }
            $procesadas++;
        }

        cache()->put('consignaciones_poll_desde', now()->toIso8601String(), 86400);

        return ['ok' => true, 'procesadas' => $procesadas, 'creadas' => $creadas, 'total_notas' => count($notas)];
    }
}
