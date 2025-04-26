<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Anuncis extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'titol',
        'descripcio',
        'preu',
        'id_estat',
        'id_marca',
        'id_tipus',
        'id_mida',
        'id_usuari'
    ];
    public $timestamps = true;


    public static function llista($id_usuari=0)
    {       
        $firstPhotoSub = DB::table('anuncis_fotos as af')
            ->select('af.foto_ruta')
            ->whereColumn('af.id_anunci', 'anuncis.id')
            ->orderBy('af.id') // o per data si tens timestamp
            ->limit(1);

        return Anuncis::join('anuncis_estats', 'anuncis_estats.id_estat', '=', 'anuncis.id_estat')
            ->leftJoin('anuncis_tipus', 'anuncis_tipus.id_tipus', '=', 'anuncis.id_tipus')
            ->leftJoin('anuncis_marques', 'anuncis_marques.id_marca', '=', 'anuncis.id_marca')
            ->leftJoin('anuncis_mides', 'anuncis_mides.id_mida', '=', 'anuncis.id_mida')
            ->select(
                'anuncis.*',
                'mida',
                'marca',
                'tipus',
                'estat',
                DB::raw("({$firstPhotoSub->toSql()}) as foto_ruta")
            )
            ->mergeBindings($firstPhotoSub) // molt important per evitar errors de bindings
            ->when($id_usuari != 0, function ($query) use ($id_usuari) {
                return $query->where(function ($subQuery) use ($id_usuari) {
                    $subQuery->where('anuncis.id_usuari', $id_usuari);
                });
            })
            ->orderBy('anuncis.id', 'desc')
            ->limit(10)
            ->get();
    }

    public static function anunci($id)
    {
        $llista_estats = self::llista_estats();
        $llista_marques = self::llista_marques();
        $llista_tipus = self::llista_tipus();
        $llista_mides = self::llista_mides();
        
        $anunci =  Anuncis::join('anuncis_estats', 'anuncis_estats.id_estat', '=', 'anuncis.id_estat')
            ->join('anuncis_tipus', 'anuncis_tipus.id_tipus', '=', 'anuncis.id_tipus')
            ->join('anuncis_marques', 'anuncis_marques.id_marca', '=', 'anuncis.id_marca')
            ->join('anuncis_mides', 'anuncis_mides.id_mida', '=', 'anuncis.id_mida')
            ->select('anuncis.*', 'mida', 'marca', 'tipus', 'estat')
            ->whereId($id)
            ->first();
        $anunci_fotos = AnuncisFotos::where('id_anunci', $id)->get();

        if ($anunci == null) {
            $anunci = new Anuncis([
                'id' => 0,
                'titol' => '',
                'descripcio' => '',
            ]);
        }

        return [
            'anunci' => $anunci,
            'llista_estats' => $llista_estats,
            'llista_marques' => $llista_marques,
            'llista_tipus' => $llista_tipus,
            'llista_mides' => $llista_mides,
            'anunci_fotos' => $anunci_fotos
        ];
    }
    public function primeraFoto()
    {
        return $this->hasOne(AnuncisFotos::class, 'id_anunci')->orderBy('id'); // o created_at
    }
    public static function llista_estats()
    {
        return DB::table('anuncis_estats')
            ->select('id_estat', 'estat')
            ->get();
    }
    public static function llista_marques()
    {
        return DB::table('anuncis_marques')
            ->select('id_marca', 'marca')
            ->get();
    }
    public static function llista_tipus()
    {
        return DB::table('anuncis_tipus')
            ->select('id_tipus', 'tipus')
            ->get();
    }
    public static function llista_mides()
    {
        return DB::table('anuncis_mides')
            ->select('id_mida', 'mida')
            ->get();
    }
}
