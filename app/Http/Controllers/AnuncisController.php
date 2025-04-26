<?php

namespace App\Http\Controllers;

use App\Models\Clubs;
use App\Models\Leagues;
use App\Models\Matches;
use App\Models\Merchandisings;
use App\Models\Anuncis;
use App\Models\AnuncisFotos;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



class AnuncisController extends Controller
{
    protected $userSavedData;
    public function __construct(User $user)
    {
        $this->userSavedData = $user::userSavedData();
    }
    public function index()
    {
      
        return view(
            'anuncis',
            [
                'clubsList' => Clubs::clubsList(),
                'leaguesList' => Leagues::leaguesList(),
                'matchesListNext' => Matches::matchesListNext($this->userSavedData),
                'matchesListLastWithResults' => Matches::matchesListLastWithResults($this->userSavedData),
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' =>  $this->userSavedData,
                'anuncis_llista' => Anuncis::llista(0)
            ]
        );
    }

    public function show(Request $request)
    {       
        return view(
            'anunci',
            [
                'clubsList' => Clubs::clubsList(),
                'leaguesList' => Leagues::leaguesList(),
                'matchesListNext' => Matches::matchesListNext($this->userSavedData),
                'matchesListLastWithResults' => Matches::matchesListLastWithResults($this->userSavedData),
                'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
                'userSavedData' =>  $this->userSavedData,
                'anunci' => Anuncis::anunci($request->id)
            ]
        );
    }
    public function anunci($id)
    {
        return view('dashboard_anuncis_formulari', Anuncis::anunci($id));
    }
    public static function update()
    {
        Log::info(request()->all());
        $id = request()->input('id_anunci');
        $titol = request()->input('anunci_title');
        $descripcio = request()->input('anunci_descripcio');
        $preu = request()->input('anunci_preu', '0');
        $id_estat = request()->input('anunci_estat', 1);
        $id_marca = request()->input('anunci_marca', 1);
        $id_tipus = request()->input('anunci_tipus', 1);
        $id_mida = request()->input('anunci_mida', 1);
        $id_usuari = auth()->user()->id;
        if ($id == 0) {
            Anuncis::create([
                'titol' => $titol,
                'descripcio' => $descripcio,
                'preu' => $preu,
                'id_estat' => $id_estat,
                'id_marca' => $id_marca,
                'id_tipus' => $id_tipus,
                'id_mida' => $id_mida,
                'id_usuari' => $id_usuari,
            ]);
            $id = DB::getPdo()->lastInsertId();
        } else {
            Anuncis::where('id', $id)->update([
                'titol' => $titol,
                'descripcio' => $descripcio,
                'preu' => $preu,
                'id_estat' => $id_estat,
                'id_marca' => $id_marca,
                'id_tipus' => $id_tipus,
                'id_mida' => $id_mida,
            ]);
        }

        return redirect()->route('dashboard.anuncis.formulari', ['id' => $id])->with('status', 'Anunci actualitzat correctament');
    }
    public static function esborra_foto($foto)
    {

        AnuncisFotos::where('foto_ruta', 'images/anuncis/' . $foto)->delete();

        $image_folder = public_path("images\anuncis\\");

        unlink($image_folder . $foto);
        return "Eliminada correctament";
    }
}
