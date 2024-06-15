<?php

namespace App\Http\Controllers;

use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Merchandisings;
use App\Models\News;
use App\Models\User;
use Illuminate\Support\Facades\DB;



use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        return view(
            'news',
            [
                'clubsList' => Clubs::clubsList(),
                'leaguesList' => Leagues::leaguesList(),
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),
                'newsListTop' => News::orderBy('newsDateTime', 'desc')->simplePaginate(5)
            ]
        );
    }

    public static function detall(Request $request)
    {
        $id = $request->id;

        return view(
            'news-detail',
            [
                'clubsList' => Clubs::clubsList(),
                'leaguesList' => Leagues::leaguesList(),
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),
                'newsDetail' => News::where('idNew', $id)->get()
            ]
        );
    }
    public function create()
    {
    }
    public function edit($new)

    {
        return view('dashboardNewsEdit', ['news' => News::where('idNew', $new)->get()]);
    }

    public function update(Request $request, News $new)
    {
        $validated = $request->validate([
            'newsTitle' => 'required',
            'newsImage' => 'required',
            'newsContent' => 'required'
        ]);

        DB::table('news')->upsert(
            [
                'idNew' => $request->idNew, 'newsTitle' => $request->newsTitle, 'newsSubtitle' => $request->newsSubtitle, 'newsContent' => $request->newsContent, 'newsImage' => $request->newsImage
            ],
            ['idNew'],
            ['newsTitle', 'newsSubtitle', 'newsContent', 'newsImage']
        );
        return to_route('dashboard.news.edit', $request->idNew)->with('status', 'Noticia actualitzada');
    }
    public function delete($id)
    {

        
         News::where('idNew', $id)->delete();
        return to_route('dashboard.news')->with('status', 'Noticia eliminada');
    }
}
