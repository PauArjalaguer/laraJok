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
use Illuminate\Support\Facades\Mail;

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
            'latitud'    => 'nullable|numeric|between:-90,90',
            'longitud'   => 'nullable|numeric|between:-180,180',
            'nom_ubicacio' => 'nullable|string|max:200',
            'conforme_usuari_enviament_mail' => 'required|accepted',
        ]);

        $anunci = new Anunci($validated);
        $anunci->id_usuari = auth()->id();
        $anunci->visites = 0;
        $anunci->enviaments = 0;
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
            'latitud'    => 'nullable|numeric|between:-90,90',
            'longitud'   => 'nullable|numeric|between:-180,180',
            'nom_ubicacio' => 'nullable|string|max:200',
            'conforme_usuari_enviament_mail' => 'required|accepted',
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
        $userLat = $request->filled('lat') ? (float) $request->lat : null;
        $userLng = $request->filled('lng') ? (float) $request->lng : null;
        $proximitatActiva = $userLat !== null && $userLng !== null;

        if ($userLat !== null && $userLng !== null) {
            session(['anuncis_lat' => $userLat, 'anuncis_lng' => $userLng]);
        }

        $marques = AnunciMarca::orderBy('nom_marca')->get();
        $estats  = AnunciEstat::all();
        $mides   = AnunciMida::orderByRaw("FIELD(tipus_mida,'samarreta','calcat'), nom_mida")->get();
        $tipus  = AnunciTipus::all();

        $query = Anunci::with(['marca', 'estat', 'mida', 'tipus', 'fotos'])
            ->join('anuncismarques', 'anuncis.id_marca', '=', 'anuncismarques.id')
            ->select('anuncis.*')
            ->where('anuncis.conforme_usuari_enviament_mail', 1);

        if ($request->filled('cerca')) {
            $cerca = '%' . $request->cerca . '%';
            $query->where(function ($q) use ($cerca) {
                $q->where('anuncis.titol', 'like', $cerca)
                  ->orWhere('anuncis.descripcio', 'like', $cerca)
                  ->orWhere('anuncismarques.nom_marca', 'like', $cerca);
            });
        }

        if ($request->filled('estat')) {
            $query->where('anuncis.id_estat', $request->estat);
        }

        if ($request->filled('mides') && is_array($request->mides)) {
            $query->whereIn('anuncis.id_mida', $request->mides);
        }

        if ($request->filled('marques') && is_array($request->marques)) {
            $query->whereIn('anuncis.id_marca', $request->marques);
        }

        if ($request->filled('tipus') && is_array($request->tipus)) {
            $query->whereIn('anuncis.id_tipus', $request->tipus);
        }

        if ($proximitatActiva) {
            $query->selectRaw("
                anuncis.*,
                (6371 * acos(
                    LEAST(1, GREATEST(-1,
                        cos(radians(?)) * cos(radians(anuncis.latitud)) *
                        cos(radians(anuncis.longitud) - radians(?)) +
                        sin(radians(?)) * sin(radians(anuncis.latitud))
                    ))
                )) AS distancia
            ", [$userLat, $userLng, $userLat])
            ->whereNotNull('anuncis.latitud')
            ->whereNotNull('anuncis.longitud')
            ->orderByRaw("distancia ASC");

            $anuncisAmbUbicacio = $query->get();

            $querySenseUbicacio = Anunci::with(['marca', 'estat', 'mida', 'tipus', 'fotos'])
                ->join('anuncismarques', 'anuncis.id_marca', '=', 'anuncismarques.id')
                ->select('anuncis.*')
                ->where('anuncis.conforme_usuari_enviament_mail', 1)
                ->where(function ($q) use ($request) {
                    $q->whereNull('anuncis.latitud')->orWhereNull('anuncis.longitud');
                    if ($request->filled('cerca')) {
                        $cerca = '%' . $request->cerca . '%';
                        $q->where(function ($q2) use ($cerca) {
                            $q2->where('anuncis.titol', 'like', $cerca)
                              ->orWhere('anuncis.descripcio', 'like', $cerca)
                              ->orWhere('anuncismarques.nom_marca', 'like', $cerca);
                        });
                    }
                    if ($request->filled('estat')) {
                        $q->where('anuncis.id_estat', $request->estat);
                    }
                    if ($request->filled('mides') && is_array($request->mides)) {
                        $q->whereIn('anuncis.id_mida', $request->mides);
                    }
                    if ($request->filled('marques') && is_array($request->marques)) {
                        $q->whereIn('anuncis.id_marca', $request->marques);
                    }
                    if ($request->filled('tipus') && is_array($request->tipus)) {
                        $q->whereIn('anuncis.id_tipus', $request->tipus);
                    }
                })
                ->orderBy('anuncis.created_at', 'desc');

            $anuncisSenseUbicacio = $querySenseUbicacio->get();

            $anuncis = $anuncisAmbUbicacio->merge($anuncisSenseUbicacio)->paginate(15)->withQueryString();
        } else {
            $query->orderBy('anuncis.created_at', 'desc');
            $anuncis = $query->paginate(15)->withQueryString();
        }

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
            'filtresActius',
            'proximitatActiva',
            'userLat',
            'userLng'
        ) + [
            'userSavedData'    => User::userSavedData(),
            'merchandisingList' => Merchandisings::merchandisingReturnFiveRandomItems(),
        ]);
    }

    public function show($id, $slug = null)
    {
        $anunci = Anunci::with(['marca', 'estat', 'mida', 'tipus', 'fotos', 'usuari'])->findOrFail($id);

        // Redirect 301 cap a la URL canonònica si el slug és incorrecte o manca
        $correctSlug = $anunci->slug;
        if ($slug !== $correctSlug) {
            return redirect()->route('anuncis.show', ['id' => $id, 'slug' => $correctSlug], 301);
        }

        // Incrementar visites
        $anunci->increment('visites');

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

    public function contact(Request $request, $id)
    {
        $anunci = Anunci::with(['marca', 'estat', 'mida', 'tipus', 'fotos', 'usuari'])->findOrFail($id);

        $user = auth()->user();
        if (!$user) {
            return back()->with('error', 'Has d\'iniciar sessió per contactar amb el venedor.');
        }

        $emailVenedor = $anunci->usuari->email ?? 'info@jok.cat';
        $emailInteressat = $user->email;

        $fotoUrl = $anunci->fotos->first()?->foto_ruta 
            ? asset($anunci->fotos->first()->foto_ruta) 
            : 'https://picsum.photos/seed/'.$anunci->id.'/400/300';

        $html = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <h2 style="margin: 0 0 10px 0; color: #1f2937;">Nou interessat al teu anunci</h2>
                <p style="color: #6b7280; margin: 0;">Algú s\'ha posat en contacte amb tu respecte l\'anunci:</p>
            </div>
            
            <div style="display: flex; gap: 20px; margin-bottom: 20px; padding: 15px; border: 1px solid #e5e7eb; border-radius: 10px;">
                <img src="'.$fotoUrl.'" alt="'.$anunci->titol.'" style="width: 120px; height: 90px; object-fit: cover; border-radius: 8px; padding:10px;">
                <div>
                    <h3 style="margin: 0 0 5px 0; color: #1f2937; font-size: 16px;">'.$anunci->titol.'</h3>
                    <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 14px;">'.$anunci->marca->nom_marca.' · '.$anunci->tipus->nom_tipus.'</p>
                    <p style="margin: 0; color: #059669; font-size: 18px; font-weight: bold;">'.($anunci->preu ? number_format($anunci->preu, 0, ',', '.').' €' : 'A consultar').'</p>
                </div>
            </div>

            <div style="background: #eff6ff; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <p style="margin: 0 0 10px 0; font-weight: bold; color: #1e40af;">Dades de contacte:</p>
                <p style="margin: 0; color: #1f2937;"><strong>Nom:</strong> '.$user->name.'</p>
                <p style="margin: 5px 0 0 0; color: #1f2937;"><strong>Email:</strong> <a href="mailto:'.$emailInteressat.'" style="color: #2563eb;">'.$emailInteressat.'</a></p>
            </div>

            <p style="color: #9ca3af; font-size: 12px; text-align: center; margin: 0;">
                Jok.cat només fa de mitjancer. La comunicació entre comprador i venedor és directa.
            </p>
        </div>';

        try {
            Mail::html($html, function ($message) use ($emailVenedor, $anunci) {
                $message->to($emailVenedor)
                    ->subject('Nou interessat: ' . $anunci->titol);
            });

            $anunci->increment('enviaments');

            return back()->with('status', 'S\'ha enviat el correu al venedor correctament.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error en enviar el correu. Torna a intentar-ho.');
        }
    }
}
