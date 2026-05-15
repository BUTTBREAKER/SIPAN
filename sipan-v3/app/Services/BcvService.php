<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BcvService
{
    /**
     * Obtener la tasa BCV actual (USD → VES).
     * Se cachea por 1 hora para evitar consultas excesivas a la API.
     */
    public function getTasa(): float
    {
        return Cache::remember('tasa_bcv', 3600, function () {
            $rate = $this->fetchFromApi();
            return $rate ?? $this->getFallback();
        });
    }

    /**
     * Forzar actualización de la tasa BCV desde la API.
     */
    public function refreshTasa(): float
    {
        Cache::forget('tasa_bcv');
        $rate = $this->fetchFromApi();

        if ($rate) {
            Cache::put('tasa_bcv', $rate, 3600);
            return $rate;
        }

        return $this->getFallback();
    }

    /**
     * Convertir USD a Bolívares usando la tasa actual.
     */
    public function usdToVes(float $usd): float
    {
        return round($usd * $this->getTasa(), 2);
    }

    /**
     * Convertir Bolívares a USD usando la tasa actual.
     */
    public function vesToUsd(float $ves): float
    {
        $tasa = $this->getTasa();
        return $tasa > 0 ? round($ves / $tasa, 2) : 0;
    }

    /**
     * Consultar la API de tasas BCV.
     */
    private function fetchFromApi(): ?float
    {
        try {
            $response = Http::timeout(3)
                ->withoutVerifying()
                ->get('https://bcv-api.rafnixg.dev/rates/');

            if ($response->successful()) {
                $data = $response->json();

                // Estructura: {"dollar": 325.38, "date": "2026-01-08"}
                if (isset($data['dollar'])) {
                    return (float) $data['dollar'];
                }

                if (isset($data['USD'])) {
                    return (float) $data['USD'];
                }

                if (isset($data['usd'])) {
                    return (float) $data['usd'];
                }
            }
        } catch (\Exception $e) {
            Log::warning('BCV API error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Tasa de respaldo cuando la API no responde.
     */
    private function getFallback(): float
    {
        return 50.00;
    }
}
