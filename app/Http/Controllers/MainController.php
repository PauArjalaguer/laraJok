<?php

namespace App\Http\Controllers;

use App\Models\Anunci;
use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Matches;
use App\Models\Merchandisings;
use App\Models\News;
use App\Models\User;

use Illuminate\Http\Request;

use App\Models\Video;

class MainController extends Controller
{
    public function index()
    {
        $userSavedData = User::userSavedData();
        return view(
            'main',
            [
                'matchesListNext' => Matches::matchesListNext( $userSavedData),
                'matchesListLastWithResults' => Matches::matchesListLastWithResults($userSavedData),
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' =>  $userSavedData,
                'newsListTop'=>News::orderBy('newsDateTime','desc')->where('website','jokcat')->limit(4)->get(),
                'recentAds' => Anunci::with(['marca', 'estat', 'fotos'])->where('conforme_usuari_enviament_mail', 1)->latest()->take(5)->get(),
                'latestVideos' => Video::with('channel')->orderBy('published_at', 'desc')->limit(8)->get(),
                'userAgent' => request()->userAgent()

            ]
        );
    }
}
