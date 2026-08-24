<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * Obté la previsió meteorològica per a un partit si és a 2 dies vista o menys.
     */
    public function getForecastForMatch(?float $lat, ?float $lon, ?string $matchDate, ?string $matchHour = null): ?array
    {
        if (empty($lat) || empty($lon) || empty($matchDate)) {
            return null;
        }

        try {
            $hour = !empty($matchHour) ? Carbon::parse($matchHour)->format('H:00:00') : '12:00:00';
            $matchDateTime = Carbon::parse($matchDate . ' ' . $hour);
            $now = Carbon::now();

            // Comprovem si el partit està a 2 dies vista (avui, demà o màxim 2 dies endavant)
            $diffHours = $now->diffInHours($matchDateTime, false);
            $diffDays = $now->diffInDays($matchDateTime, false);

            // Si falten més de 2 dies (48h) o el partit és de fa dies passats, no mostrem meteo
            if ($diffDays > 2 || $diffHours < -6) {
                return null;
            }

            // Cache de 30 minuts per a la combinació lat/lon
            $cacheKey = sprintf('weather_%s_%s', round($lat, 3), round($lon, 3));

            $weatherData = Cache::remember($cacheKey, 1800, function () use ($lat, $lon) {
                $url = "https://api.open-meteo.com/v1/forecast";
                $response = Http::timeout(8)->get($url, [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'hourly' => 'temperature_2m,precipitation_probability,weathercode',
                    'timezone' => 'auto',
                    'forecast_days' => 4
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                return null;
            });

            if (!$weatherData || empty($weatherData['hourly']['time'])) {
                return null;
            }

            // Busquem l'índex de l'hora més propera
            $targetTimeKey = $matchDateTime->format('Y-m-d\TH:00');
            $times = $weatherData['hourly']['time'];
            $index = array_search($targetTimeKey, $times);

            // Si no coincideix l'hora exacta, busquem la primera hora del dia
            if ($index === false) {
                $targetDay = $matchDateTime->format('Y-m-d');
                foreach ($times as $i => $t) {
                    if (str_starts_with($t, $targetDay)) {
                        $index = $i;
                        break;
                    }
                }
            }

            if ($index === false) {
                return null;
            }

            $temp = isset($weatherData['hourly']['temperature_2m'][$index]) ? round($weatherData['hourly']['temperature_2m'][$index]) : null;
            $rainProb = isset($weatherData['hourly']['precipitation_probability'][$index]) ? (int)$weatherData['hourly']['precipitation_probability'][$index] : 0;
            $code = isset($weatherData['hourly']['weathercode'][$index]) ? (int)$weatherData['hourly']['weathercode'][$index] : 0;

            $meta = $this->interpretWmoCode($code);

            return [
                'temperature' => $temp,
                'rain_probability' => $rainProb,
                'condition' => $meta['desc'],
                'icon' => $meta['icon'],
                'is_rainy' => $rainProb >= 40 || in_array($code, [51, 53, 55, 61, 63, 65, 80, 81, 82, 95, 96, 99]),
                'date_formatted' => $matchDateTime->format('d/m/Y'),
                'hour_formatted' => $matchDateTime->format('H:i'),
            ];

        } catch (\Exception $e) {
            Log::warning("Error consultant WeatherService: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tradueix els codis WMO a text en català i icones de FontAwesome.
     */
    protected function interpretWmoCode(int $code): array
    {
        return match ($code) {
            0 => ['desc' => 'Cel clar i assolellat', 'icon' => 'fa-solid fa-sun text-amber-500'],
            1, 2 => ['desc' => 'Poc ennuvolat', 'icon' => 'fa-solid fa-cloud-sun text-amber-500'],
            3 => ['desc' => 'Ennuvolat', 'icon' => 'fa-solid fa-cloud text-stone-400'],
            45, 48 => ['desc' => 'Boira', 'icon' => 'fa-solid fa-smog text-stone-400'],
            51, 53, 55 => ['desc' => 'Plugim feble', 'icon' => 'fa-solid fa-cloud-rain text-blue-400'],
            61, 63 => ['desc' => 'Pluja moderada', 'icon' => 'fa-solid fa-cloud-rain text-blue-500'],
            65 => ['desc' => 'Pluja forta', 'icon' => 'fa-solid fa-cloud-showers-heavy text-blue-600'],
            71, 73, 75, 77 => ['desc' => 'Nevada', 'icon' => 'fa-solid fa-snowflake text-sky-300'],
            80, 81, 82 => ['desc' => 'Ruixats', 'icon' => 'fa-solid fa-cloud-showers-heavy text-blue-500'],
            95, 96, 99 => ['desc' => 'Tempesta', 'icon' => 'fa-solid fa-bolt text-amber-500'],
            default => ['desc' => 'Temps variable', 'icon' => 'fa-solid fa-cloud-sun text-amber-500'],
        };
    }
}
