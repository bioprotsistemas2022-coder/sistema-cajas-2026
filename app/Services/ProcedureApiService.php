<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ProcedureApiService
{
    public function search(array $filters): array
    {
        $url = $this->resolveUrl(config('services.procedure_api.url'));
        $token = config('services.procedure_api.token');

        if (!$url || !$token) {
            return [];
        }

        try {
            $response = Http::withToken($token)
                ->timeout(15)
                ->get($url, $filters);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
        } catch (\Throwable $e) {
            // No romper la búsqueda si la API externa no responde.
            report($e);
        }

        return [];
    }

    /**
     * Cuando el servidor corre dentro de WSL, "localhost" apunta al propio WSL
     * y no al host Windows (donde está Apache/XAMPP). Detecta la IP del host
     * Windows desde /etc/resolv.conf y la usa en lugar de localhost.
     */
    private function resolveUrl(?string $url): ?string
    {
        if (!$url || !str_contains($url, 'localhost')) {
            return $url;
        }

        $wslHost = $this->wslHostIp();
        if ($wslHost) {
            return str_replace('localhost', $wslHost, $url);
        }

        return $url;
    }

    private function wslHostIp(): ?string
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            return null;
        }

        $resolv = @file_get_contents('/etc/resolv.conf');
        if ($resolv === false) {
            return null;
        }

        foreach (explode("\n", $resolv) as $line) {
            if (preg_match('/^\s*nameserver\s+([0-9.]+)\s*$/', $line, $m)) {
                return $m[1];
            }
        }

        return null;
    }
}
