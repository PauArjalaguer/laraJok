<?php

namespace App\Http\Controllers;

use App\Models\Anunci;
use App\Models\AnunciMarca;
use App\Models\AnunciEstat;
use App\Models\AnunciMida;
use App\Models\AnunciTipus;
use App\Models\Merchandisings;
use App\Models\User;
use Illuminate\Http\Request;

class AnuncisController extends Controller
{
    public function index(Request $request)
    {
        // ── Filtres lookup ────────────────────────────────────────────────────
        $marques = AnunciMarca::orderBy('nom_marca')->get();
        $estats  = AnunciEstat::all();
        $mides   = AnunciMida::orderByRaw("FIELD(tipus_mida,'samarreta','calcat'), nom_mida")->get();
        $tipus  = AnunciTipus::all();

        // ── Query base ────────────────────────────────────────────────────────
        $query = Anunci::with(['marca', 'estat', 'mida', 'tipus', 'fotos'])
            ->join('anuncismarques', 'anuncis.id_marca', '=', 'anuncismarques.id')
            ->select('anuncis.*');

        // ── Cerca de text (títol, descripció, marca) ──────────────────────────
        if ($request->filled('cerca')) {
            $cerca = '%' . $request->cerca . '%';
            $query->where(function ($q) use ($cerca) {
                $q->where('anuncis.titol', 'like', $cerca)
                  ->orWhere('anuncis.descripcio', 'like', $cerca)
                  ->orWhere('anuncismarques.nom_marca', 'like', $cerca);
            });
        }

        // ── Filtre estat ──────────────────────────────────────────────────────
        if ($request->filled('estat')) {
            $query->where('anuncis.id_estat', $request->estat);
        }

        // ── Filtre mida (múltiple) ────────────────────────────────────────────
        if ($request->filled('mides') && is_array($request->mides)) {
            $query->whereIn('anuncis.id_mida', $request->mides);
        }

        // ── Filtre marca (múltiple) ───────────────────────────────────────────
        if ($request->filled('marques') && is_array($request->marques)) {
            $query->whereIn('anuncis.id_marca', $request->marques);
        }

        // ── Filtre tipus (múltiple) ───────────────────────────────────────────
        if ($request->filled('tipus') && is_array($request->tipus)) {
            $query->whereIn('anuncis.id_tipus', $request->tipus);
        }

        // ── Ordenació ─────────────────────────────────────────────────────────
        $query->orderBy('anuncis.created_at', 'desc');

        // ── Paginació (15 per pàgina) ─────────────────────────────────────────
        $anuncis = $query->paginate(15)->withQueryString();

        // ── Nombre de filtres actius ──────────────────────────────────────────
        $filtresActius = collect([
            $request->filled('cerca'),
            $request->filled('estat'),
            $request->filled('mides'),
            $request->filled('marques'),
            $request->filled('tipus'),
        ])->filter()->count();

        return view('anuncis.index', compact(
            'anuncis',
            'marques',
            'estats',
            'mides',
            'tipus',
            'filtresActius'
        ) + [
            'userSavedData'    => User::userSavedData(),
            'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
        ]);
    }
}
