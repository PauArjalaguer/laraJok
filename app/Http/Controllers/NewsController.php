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
    public function index(Request $request)
    {
        $search = $request->input('q');
        $source = $request->input('source');

        $query = News::orderBy('newsDatetime', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('newsTitle', 'like', "%{$search}%")
                  ->orWhere('newsSubtitle', 'like', "%{$search}%")
                  ->orWhere('newsContent', 'like', "%{$search}%");
            });
        }

        if (!empty($source)) {
            switch ($source) {
                case 'fcb':
                    $query->where('externalLink', 'like', '%fcbarcelona.cat%');
                    break;
                case 'reus':
                    $query->where('externalLink', 'like', '%reusdeportiu.org%');
                    break;
                case 'palau':
                    $query->where('externalLink', 'like', '%hcpalau.com%');
                    break;
                case 'cerdanyola':
                    $query->where('externalLink', 'like', '%cerdanyola.info%');
                    break;
                case 'regio':
                    $query->where('externalLink', 'like', '%regio7.cat%');
                    break;
                case 'noia':
                    $query->where('externalLink', 'like', '%cenoia.com%');
                    break;
                case 'caldes':
                    $query->where('externalLink', 'like', '%clubhoqueicaldes.com%');
                    break;
                case 'shum':
                    $query->where('externalLink', 'like', '%shummassanet.com%');
                    break;
                case 'amunt':
                    $query->where('externalLink', 'like', '%amunthoquei.cat%');
                    break;
                case 'jokcat':
                    $query->where(function ($q) {
                        $q->whereNull('externalLink')->orWhere('externalLink', '');
                    });
                    break;
            }
        }

        $page = (int) $request->input('page', 1);
        if ($page < 1) { $page = 1; }
        $perPage = ($page === 1) ? 10 : 9;
        $offset = ($page === 1) ? 0 : 10 + (($page - 2) * 9);

        $total = $query->count();
        $items = $query->offset($offset)->limit($perPage)->get();

        $newsListTop = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $sourcesMap = [
            ''           => 'Totes les fonts',
            'fcb'        => 'FC Barcelona',
            'reus'       => 'Reus Deportiu',
            'palau'      => 'HC Palau',
            'cerdanyola' => 'Cerdanyola CH',
            'regio'      => 'Regió 7',
            'noia'       => 'CE Noia Freixenet',
            'caldes'     => 'CH Caldes',
            'shum'       => 'SHUM Maçanet',
            'amunt'      => 'CE Arenys de Munt',
            'jokcat'     => 'JOK.cat',
        ];

        return view(
            'news',
            [
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),
                'newsListTop' => $newsListTop,
                'search' => $search,
                'source' => $source,
                'sourcesMap' => $sourcesMap,
            ]
        );
    }

    public static function detall(Request $request)
    {
        $id = $request->id;

        return view(
            'news_detail',
            [
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' => User::userSavedData(),
                'newsDetail' => News::where('idNew', $id)->get()
            ]
        );
    }
    public function create()
    {
        $query = DB::table('news')->insertGetId([

            'newsTitle' => '.',


        ]);
        $idNews = $query;
        return to_route('dashboard.news.edit',$idNews);
    }
    public function edit($new)

    {
        return view('dashboard_news_edit', ['news' => News::where('idNew', $new)->get()]);
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
                'idNew' => $request->idNew, 'website' => $request->website,'newsTitle' => $request->newsTitle, 'newsSubtitle' => $request->newsSubtitle, 'newsContent' => $request->newsContent, 'newsImage' => $request->newsImage
            ],
            ['idNew'],
            ['website','newsTitle', 'newsSubtitle', 'newsContent', 'newsImage']
        );
        return to_route('dashboard.news.edit', $request->idNew)->with('status', 'Noticia actualitzada');
    }
    public function delete($id)
    {
        News::where('idNew', $id)->delete();
        return to_route('dashboard.news')->with('status', 'Noticia eliminada');
    }
}
