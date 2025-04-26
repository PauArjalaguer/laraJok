<?php
namespace App\Http\Controllers;

use App\Models\AnuncisFotos;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
  
    public  function dropzoneUi()
    {
        return view('upload-view');
    }
  
    public  function dropzoneFileUpload(Request $request)
    {
        $image_folder="images/";
        $image = $request->file('file');
        $image_name = time().'.'.$image->extension();
        $image->move(public_path('images'),$image_name);
        $image_toconvert = imagecreatefromstring(file_get_contents($image_folder . $image_name));
        imagewebp($image_toconvert , $image_folder.$image_name.'.webp', 80);
        unlink($image_folder.$image_name);

        return response()->json(['success'=>$image_name.".webp"]);
    }

    public  function anuncis_file_upload(Request $request)
    {
        $image_folder="images/anuncis/";
        $image = $request->file('file');
        $image_name = time().'.'.$image->extension();
        $image->move(public_path($image_folder),$image_name);
        $image_toconvert = imagecreatefromstring(file_get_contents($image_folder . $image_name));
        imagewebp($image_toconvert , $image_folder.$image_name.'.webp', 80);
        unlink($image_folder.$image_name);

        AnuncisFotos::create([
            'id_anunci' => $request->input('id_anunci'),
            'foto_ruta' => $image_folder.$image_name.".webp",
        ]);

        return response()->json(['success'=>$image_name.".webp"]);
    }
}
