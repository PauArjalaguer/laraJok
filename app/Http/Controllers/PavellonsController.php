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
use Illuminate\Support\Facades\DB;

class PavellonsController extends Controller
{
    public function index()
    {
        // Última temporada: mateix criteri que l'Agenda (idSeason màxim de leagues)
        $maxSeason = DB::table('leagues')->max('idSeason');

        $pavellons = Pavellons::whereNotNull('lat')
            ->whereExists(function ($q) use ($maxSeason) {
                $q->select(DB::raw(1))
                    ->from('matches as m')
                    ->join('leagues as l', 'l.idLeague', '=', 'm.idLeague')
                    ->whereColumn('m.idPlace', 'places.idPlace')
                    ->where('l.idSeason', $maxSeason);
            })
            ->with('matches')
            ->get();

        return view(
            'pavellons',
            [
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),
                'pavellons' => $pavellons
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
