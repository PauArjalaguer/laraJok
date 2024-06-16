<?php

namespace App\Http\Controllers;

use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Merchandisings;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class MerchandisingsController extends Controller
{
   public function index(){
    return view(
        'merchandising',
        [
            'clubsList' => Clubs::clubsList(),
            'leaguesList' => Leagues::leaguesList(),                  
            'merchandisingListAll' => Merchandisings::whereNotNull('assetCategory')->orderBy('assetName','asc')->get(),
            'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
            'userSavedData' => User::userSavedData(),
            'merchandisingReturnCategories' => Merchandisings::merchandisingReturnCategories()
        ]
    );
   }

   public function create()
   {
       $query = DB::table('merchandisings')->insertGetId([          
           'assetName' => '.',         
       ]);
       $idMerch = $query;
       
       return to_route('dashboard.merchandising.edit',$idMerch);
   }
   public function edit($idMerch)

   {
       return view('dashboard_merchandising_edit', ['merch' => Merchandisings::where('idAsset', $idMerch)->get()]);
   }

   public function update(Request $request,  $idMerch)
   {
       $validated = $request->validate([
           'assetName' => 'required',
           'assetThumbnail' => 'required',
           'assetCategory' => 'required'
       ]);

       DB::table('merchandisings')->upsert(
           [
               'idAsset' => $request->idAsset, 'assetName' => $request->assetName, 'assetThumbnail' => $request->assetThumbnail, 'assetCategory' => $request->assetCategory, 'assetUrl' => $request->assetUrl,'assetPrice' => $request->assetPrice,
           ],
           ['idAsset'],
           ['assetName', 'assetThumbnail', 'assetCategory','assetUrl','assetPrice']
       );
       return to_route('dashboard.merchandising.edit', $request->idAsset)->with('status', 'Producte actualitzat');
   }
   public function delete($id)
   {
       Merchandisings::where('idAsset', $id)->delete();
       return to_route('dashboard.merchandising')->with('status', 'Producte eliminat');
   }
}
