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
use Illuminate\Support\Str;

use App\Models\AnunciFoto;
use Illuminate\Support\Facades\File;

class AnuncisController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $query = Anunci::with(['marca', 'estat', 'mida', 'tipus', 'fotos']);

        // Si no és admin (role 1), només veu els seus
        if ($user->idRole != 1) {
            $query->where('id_usuari', $user->id);
        }

        $anuncis = $query->orderBy('created_at', 'desc')->get();

        return view('dashboard_anuncis', compact('anuncis'));
    }

    public function create()
    {
        $marques = AnunciMarca::orderBy('nom_marca')->get();
        $estats  = AnunciEstat::all();
        $tipus   = AnunciTipus::all();
        $mides   = AnunciMida::all();

        return view('dashboard_anunci_edit', [
            'anunci'  => new Anunci(),
            'marques' => $marques,
            'estats'  => $estats,
            'tipus'   => $tipus,
            'mides'   => $mides,
            'isEdit'  => false
        ]);
    }

    public function edit($id)
    {
        $anunci = Anunci::with('fotos')->findOrFail($id);

        // Autorització: només l'amo o l'admin
        if (auth()->user()->idRole != 1 && $anunci->id_usuari != auth()->id()) {
            abort(403);
        }

        $marques = AnunciMarca::orderBy('nom_marca')->get();
        $estats  = AnunciEstat::all();
        $tipus   = AnunciTipus::all();
        $mides   = AnunciMida::all();

        return view('dashboard_anunci_edit', [
            'anunci'  => $anunci,
            'marques' => $marques,
            'estats'  => $estats,
            'tipus'   => $tipus,
            'mides'   => $mides,
            'isEdit'  => true
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titol'      => 'required|string|max:200',
            'descripcio' => 'required|string',
            'preu'       => 'nullable|numeric',
            'id_marca'   => 'required|exists:anuncismarques,id',
            'id_estat'   => 'required|exists:anuncisestats,id',
            'id_tipus'   => 'required|exists:anuncistipus,id',
            'id_mida'    => 'required|exists:anuncismides,id',
        ]);

        $anunci = new Anunci($validated);
        $anunci->id_usuari = auth()->id();
        $anunci->save();

        // Gestionar fotos (si n'hi ha de temporals o enviades)
        if ($request->filled('fotos')) {
            foreach ($request->fotos as $index => $ruta) {
                AnunciFoto::create([
                    'id_anunci' => $anunci->id,
                    'foto_ruta' => $ruta,
                    'ordre'     => $index
                ]);
            }
        }

        return redirect()->route('dashboard.anuncis')->with('status', 'Anunci creat correctament.');
    }

    public function update(Request $request, $id)
    {
        $anunci = Anunci::findOrFail($id);

        // Autorització
        if (auth()->user()->idRole != 1 && $anunci->id_usuari != auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'titol'      => 'required|string|max:200',
            'descripcio' => 'required|string',
            'preu'       => 'nullable|numeric',
            'id_marca'   => 'required|exists:anuncismarques,id',
            'id_estat'   => 'required|exists:anuncisestats,id',
            'id_tipus'   => 'required|exists:anuncistipus,id',
            'id_mida'    => 'required|exists:anuncismides,id',
        ]);

        $anunci->update($validated);

        // Actualitzar fotos: primer esborrem les velles i posem les noves 
        // (simplificació per al prototip, millor seria comparar)
        if ($request->filled('fotos')) {
            // Esborrem relacions (però no els fitxers si es volen mantenir o si es gestionen a part)
            $anunci->fotos()->delete();
            foreach ($request->fotos as $index => $ruta) {
                AnunciFoto::create([
                    'id_anunci' => $anunci->id,
                    'foto_ruta' => $ruta,
                    'ordre'     => $index
                ]);
            }
        }

        return redirect()->route('dashboard.anuncis')->with('status', 'Anunci actualitzat.');
    }

    public function destroy($id)
    {
        $anunci = Anunci::with('fotos')->findOrFail($id);

        // Autorització
        if (auth()->user()->idRole != 1 && $anunci->id_usuari != auth()->id()) {
            abort(403);
        }

        // Esborrar fitxers físics
        foreach ($anunci->fotos as $foto) {
            $path = public_path($foto->foto_ruta);
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        $anunci->delete();

        return redirect()->route('dashboard.anuncis')->with('status', 'Anunci eliminat.');
    }

    public function moderacio()
    {
        if (auth()->user()->idRole != 1) {
            abort(403);
        }

        $fotos = AnunciFoto::with('anunci')->latest()->paginate(50);

        return view('dashboard_anuncis_moderacio', compact('fotos'));
    }

    public function destroyFoto($id)
    {
        if (auth()->user()->idRole != 1) {
            abort(403);
        }

        $foto = AnunciFoto::findOrFail($id);
        $path = public_path($foto->foto_ruta);
        
        if (File::exists($path)) {
            File::delete($path);
        }

        $foto->delete();

        return back()->with('status', 'Foto eliminada correctament.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $image = $request->file('file');
        $filename = time() . '_' . Str::random(10);
        $tempPath = public_path('uploads/temp');
        
        if (!File::isDirectory($tempPath)) {
            File::makeDirectory($tempPath, 0755, true);
        }

        // Resizing and WebP conversion using GD
        $img = null;
        $extension = strtolower($image->getClientOriginalExtension());
        
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                $img = imagecreatefromjpeg($image->getRealPath());
                break;
            case 'png':
                $img = imagecreatefrompng($image->getRealPath());
                break;
            case 'webp':
                $img = imagecreatefromwebp($image->getRealPath());
                break;
            case 'gif':
                $img = imagecreatefromgif($image->getRealPath());
                break;
        }

        if ($img) {
            $width = imagesx($img);
            $height = imagesy($img);
            $targetWidth = 800;
            
            if ($width > $targetWidth) {
                $targetHeight = ($height / $width) * $targetWidth;
                $newImg = imagecreatetruecolor($targetWidth, $targetHeight);
                
                // Mantenir transparència per PNG/WebP si cal, tot i que convertim a WebP opac sovint
                imagealphablending($newImg, false);
                imagesavealpha($newImg, true);
                
                imagecopyresampled($newImg, $img, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
                $img = $newImg;
            }

            $finalFilename = $filename . '.webp';
            $finalPath = 'uploads/anuncis/' . $finalFilename;
            
            if (!File::isDirectory(public_path('uploads/anuncis'))) {
                File::makeDirectory(public_path('uploads/anuncis'), 0755, true);
            }

            imagewebp($img, public_path($finalPath), 80);
            imagedestroy($img);

            return response()->json([
                'success' => true,
                'path' => '/' . $finalPath
            ]);
        }

        return response()->json(['success' => false], 400);
    }

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

    public function show($id, $slug = null)
    {
        $anunci = Anunci::with(['marca', 'estat', 'mida', 'tipus', 'fotos', 'usuari'])->findOrFail($id);

        // ── Redirect 301 cap a la URL canonònica si el slug és incorrecte o manca ──
        $correctSlug = $anunci->slug;
        if ($slug !== $correctSlug) {
            return redirect()->route('anuncis.show', ['id' => $id, 'slug' => $correctSlug], 301);
        }

        // Anuncis relacionats (mateix tipus, excloent l'actual)
        $relacionats = Anunci::with(['marca', 'estat', 'fotos'])
            ->where('id_tipus', $anunci->id_tipus)
            ->where('id', '!=', $anunci->id)
            ->latest()
            ->take(4)
            ->get();

        return view('anuncis.show', [
            'anunci'           => $anunci,
            'relacionats'      => $relacionats,
            'userSavedData'    => User::userSavedData(),
            'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
        ]);
    }
}
