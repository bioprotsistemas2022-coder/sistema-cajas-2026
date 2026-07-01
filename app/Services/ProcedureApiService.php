<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ProcedureApiService
{
    public function search(array $filters): array
    {
        $url = config('services.procedure_api.url');
        $token = config('services.procedure_api.token');

        if (!$url || !$token) {
            return [];
        }

        $response = Http::withToken($token)
            ->timeout(15)
            ->get($url, $filters);

        if ($response->successful()) {
            return $response->json()['data'] ?? [];
        }

        return [];
    }
}
