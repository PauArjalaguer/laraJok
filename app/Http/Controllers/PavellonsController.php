<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Merchandisings;
use App\Models\User;
use App\Models\Matches;
use App\Models\Pavellons;
use App\Services\WeatherService;

class PavellonsController extends Controller
{
    public function index()
    {
        return view(
            'pavellons',
            [
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),
                'pavellons' => Pavellons::whereNotNull('lat')->with('matches')->get()
            ]
        );
    }

    public function detall($idPavello, $label = null)
    {
        $pavello = Pavellons::findOrFail($idPavello);
        $weatherService = app(WeatherService::class);

        $weatherForecast = null;
        if (!empty($pavello->lat) && !empty($pavello->lon)) {
            $weatherForecast = $weatherService->getForecastForMatch(
                (float)$pavello->lat,
                (float)$pavello->lon,
                date('Y-m-d'),
                '18:00:00'
            );
        }

        return view(
            'pavello',
            [
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),
                'pavello' => $pavello,
                'partits_pavello' => Matches::matchesListFromIdPavello($idPavello),
                'weatherForecast' => $weatherForecast
            ]
        );
    }
}
